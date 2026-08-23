<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cita;
use App\Models\Agenda;
use App\Models\Auditoria;
use Carbon\Carbon;

class MedicoController extends Controller
{
    public function agendaPersonal(Request $request)
    {
        $user = Auth::user();
        $medico = $user->medico;

        if (!$medico) {
            return redirect()->route('login')->with('error', 'No se encontró perfil médico asociado.');
        }

        $fechaSeleccionada = $request->get('fecha', Carbon::today()->format('Y-m-d'));

        $citas = Cita::with(['paciente.usuario', 'agenda'])
            ->where('id_medico', $medico->id_medico)
            ->where('fecha_cita', $fechaSeleccionada)
            ->orderBy('hora_cita', 'asc')
            ->get();

        $statsHoy = [
            'total' => $citas->count(),
            'atendidas' => $citas->where('estado', 'ATENDIDA')->count(),
            'pendientes' => $citas->whereIn('estado', ['SOLICITADA', 'CONFIRMADA'])->count(),
            'canceladas' => $citas->where('estado', 'CANCELADA')->count(),
        ];

        return view('medico.agenda', compact('medico', 'citas', 'fechaSeleccionada', 'statsHoy'));
    }

    public function cambiarEstadoCita(Request $request, $id)
    {
        $data = $request->validate([
            'estado' => 'required|in:ATENDIDA,NO_ASISTIO,CONFIRMADA',
            'observaciones' => 'nullable|string|max:300',
        ]);

        $cita = Cita::findOrFail($id);

        $cita->update([
            'estado' => $data['estado'],
            'observaciones' => $data['observaciones'] ?? $cita->observaciones,
        ]);

        Auditoria::create([
            'id_usuario' => Auth::id(),
            'accion' => 'CAMBIAR_ESTADO_CITA',
            'tabla_afectada' => 'citas',
            'registro_afectado' => $cita->id_cita,
            'detalle' => json_encode(['nuevo_estado' => $data['estado']]),
            'ip_origen' => $request->ip(),
        ]);

        return back()->with('success', "El estado de la cita Ref #{$cita->id_cita} se actualizó a '{$data['estado']}'.");
    }
}
