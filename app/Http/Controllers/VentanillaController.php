<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Usuario;
use App\Models\Role;
use App\Models\Consultorio;
use App\Models\Pago;
use App\Models\Notificacion;
use App\Models\Auditoria;
use Carbon\Carbon;

class VentanillaController extends Controller
{
    public function salaEspera()
    {
        $today = Carbon::today()->format('Y-m-d');
        
        $citasHoy = Cita::with(['paciente.usuario', 'medico.usuario', 'medico.especialidad', 'consultorio', 'pago'])
            ->where('fecha_cita', $today)
            ->orderBy('hora_cita')
            ->get();

        $enEspera = $citasHoy->whereIn('estado', ['EN_ESPERA', 'EN_CONSULTA']);
        $confirmadas = $citasHoy->where('estado', 'CONFIRMADA');
        $atendidas = $citasHoy->where('estado', 'ATENDIDA');
        $solicitadas = $citasHoy->where('estado', 'SOLICITADA');

        $consultorios = Consultorio::with(['medico.usuario', 'especialidad'])->get();

        return view('ventanilla.sala_espera', compact(
            'citasHoy', 'enEspera', 'confirmadas', 'atendidas', 'solicitadas', 'consultorios'
        ));
    }

    public function cambiarEstadoLlegada(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:CONFIRMADA,EN_ESPERA,EN_CONSULTA,ATENDIDA,CANCELADA,NO_ASISTIO',
            'id_consultorio' => 'nullable|exists:consultorios,id_consultorio',
            'motivo_cancelacion' => 'nullable|string'
        ]);

        $cita = Cita::findOrFail($id);

        $updateData = ['estado' => $request->estado];

        if ($request->estado === 'EN_ESPERA') {
            $updateData['hora_llegada'] = now();
        } elseif ($request->estado === 'EN_CONSULTA') {
            $updateData['hora_atencion'] = now();
        }

        if ($request->id_consultorio) {
            $updateData['id_consultorio'] = $request->id_consultorio;
        }

        if ($request->motivo_cancelacion) {
            $updateData['motivo_cancelacion'] = $request->motivo_cancelacion;
        }

        $cita->update($updateData);

        // Notificación simulada
        Notificacion::create([
            'id_cita' => $cita->id_cita,
            'id_paciente' => $cita->id_paciente,
            'tipo' => 'ESTADO_CAMBIO',
            'canal' => 'WHATSAPP',
            'mensaje' => 'Hospital Plan 3000: Su cita ha cambiado de estado a ' . $request->estado . '.',
            'estado' => 'ENVIADO',
        ]);

        return redirect()->back()->with('success', 'Estado de recepción actualizado correctamente a ' . $request->estado);
    }

    public function registroWalkin()
    {
        return view('ventanilla.registro_walkin');
    }

    public function guardarPacienteWalkin(Request $request)
    {
        $request->validate([
            'ci' => 'required|string|max:15',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|string',
            'sexo' => 'required|string',
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string',
        ]);

        // Detección de duplicado por CI
        $pacienteExistente = Paciente::where('ci', $request->ci)->first();
        if ($pacienteExistente) {
            return redirect()->back()->with('warning', 'ALERTA DUPLICADO: Ya existe un paciente registrado con la Cédula de Identidad CI: ' . $request->ci . ' (' . $pacienteExistente->usuario->nombre . ' ' . $pacienteExistente->usuario->apellido . ').');
        }

        $rolPaciente = Role::where('nombre_rol', 'PACIENTE')->firstOrFail();

        $user = Usuario::create([
            'id_rol' => $rolPaciente->id_rol,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => strtolower($request->ci) . '@paciente.plan3000.gob.bo',
            'password' => Hash::make('paciente123'),
            'telefono' => $request->telefono ?? '00000000',
            'estado' => 'ACTIVO',
            'fecha_registro' => now(),
        ]);

        $paciente = Paciente::create([
            'id_usuario' => $user->id_usuario,
            'ci' => $request->ci,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero' => $request->genero,
            'sexo' => $request->sexo,
            'direccion' => $request->direccion ?? 'Plan 3000',
        ]);

        return redirect()->route('ventanilla.sala_espera')->with('success', 'Paciente presencial registrado exitosamente: ' . $user->nombre . ' ' . $user->apellido);
    }

    public function cajaIndex()
    {
        $citasSinPago = Cita::with(['paciente.usuario', 'medico.especialidad', 'pago'])
            ->whereIn('estado', ['CONFIRMADA', 'EN_ESPERA', 'EN_CONSULTA', 'ATENDIDA'])
            ->orderBy('fecha_cita', 'desc')
            ->get();

        $pagosHoy = Pago::with(['cita.paciente.usuario', 'cita.medico.especialidad', 'cajero'])
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();

        $totalEfectivo = $pagosHoy->where('metodo_pago', 'EFECTIVO')->sum('monto_pagado');
        $totalTarjeta = $pagosHoy->where('metodo_pago', 'TARJETA')->sum('monto_pagado');
        $totalTransferencia = $pagosHoy->where('metodo_pago', 'TRANSFERENCIA')->sum('monto_pagado');
        $totalQR = $pagosHoy->where('metodo_pago', 'QR')->sum('monto_pagado');
        $totalGeneral = $pagosHoy->sum('monto_pagado');

        return view('ventanilla.caja', compact(
            'citasSinPago', 'pagosHoy', 'totalEfectivo', 'totalTarjeta',
            'totalTransferencia', 'totalQR', 'totalGeneral'
        ));
    }

    public function procesarPago(Request $request)
    {
        $request->validate([
            'id_cita' => 'required|exists:citas,id_cita',
            'monto_total' => 'required|numeric|min:0',
            'monto_pagado' => 'required|numeric|min:0',
            'metodo_pago' => 'required|in:EFECTIVO,TARJETA,TRANSFERENCIA,QR',
        ]);

        $cita = Cita::with('paciente')->findOrFail($request->id_cita);
        $montoPendiente = max(0, $request->monto_total - $request->monto_pagado);
        $estadoPago = $montoPendiente > 0 ? 'PARCIAL' : 'PAGADO';
        $numeroComprobante = 'REC-' . strtoupper(uniqid());

        $pago = Pago::create([
            'id_cita' => $cita->id_cita,
            'id_paciente' => $cita->id_paciente,
            'id_usuario_caja' => Auth::user()->id_usuario,
            'monto_total' => $request->monto_total,
            'monto_pagado' => $request->monto_pagado,
            'monto_pendiente' => $montoPendiente,
            'metodo_pago' => $request->metodo_pago,
            'estado_pago' => $estadoPago,
            'numero_comprobante' => $numeroComprobante,
            'observaciones' => 'Cobro registrado en ventanilla por ' . Auth::user()->nombre,
        ]);

        Auditoria::create([
            'id_usuario' => Auth::user()->id_usuario,
            'accion' => 'PAGO_REGISTRADO',
            'tabla_afectada' => 'pagos',
            'registro_afectado' => $pago->id_pago,
            'detalle' => json_encode(['comprobante' => $numeroComprobante, 'monto' => $request->monto_pagado]),
            'fecha_hora' => now(),
            'ip_origen' => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'Pago registrado exitosamente. Comprobante: ' . $numeroComprobante);
    }
}
