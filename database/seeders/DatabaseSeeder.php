<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Usuario;
use App\Models\Paciente;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Agenda;
use App\Models\Cita;
use App\Models\Notificacion;
use App\Models\Configuracion;
use App\Models\Auditoria;
use App\Models\Consultorio;
use App\Models\ExpedienteMedico;
use App\Models\Consulta;
use App\Models\ConsultaCardiologia;
use App\Models\Pago;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $rolAdmin = Role::create([
            'nombre_rol' => 'ADMIN',
            'descripcion' => 'Administrador del sistema',
            'estado' => 'ACTIVO',
        ]);

        $rolCallCenter = Role::create([
            'nombre_rol' => 'CALL_CENTER',
            'descripcion' => 'Personal Administrativo y Ventanilla',
            'estado' => 'ACTIVO',
        ]);

        $rolMedico = Role::create([
            'nombre_rol' => 'MEDICO',
            'descripcion' => 'Médico especialista u odontólogo',
            'estado' => 'ACTIVO',
        ]);

        $rolPaciente = Role::create([
            'nombre_rol' => 'PACIENTE',
            'descripcion' => 'Paciente del Hospital Municipal Plan 3000',
            'estado' => 'ACTIVO',
        ]);

        // 2. Especialidades
        $espCardio = Especialidad::create([
            'nombre' => 'Cardiología',
            'descripcion' => 'Atención especializada del sistema cardiovascular',
            'duracion_turno' => 20,
            'estado' => 'ACTIVO',
        ]);

        $espPedia = Especialidad::create([
            'nombre' => 'Pediatría',
            'descripcion' => 'Atención médica integral a niños y adolescentes',
            'duracion_turno' => 20,
            'estado' => 'ACTIVO',
        ]);

        $espMedGen = Especialidad::create([
            'nombre' => 'Medicina General',
            'descripcion' => 'Consulta ambulatoria de atención primaria',
            'duracion_turno' => 15,
            'estado' => 'ACTIVO',
        ]);

        $espGineco = Especialidad::create([
            'nombre' => 'Ginecología',
            'descripcion' => 'Salud reproductiva y ginecológica',
            'duracion_turno' => 20,
            'estado' => 'ACTIVO',
        ]);

        $espTrauma = Especialidad::create([
            'nombre' => 'Traumatología',
            'descripcion' => 'Evaluación y tratamiento de lesiones óseas y musculares',
            'duracion_turno' => 20,
            'estado' => 'ACTIVO',
        ]);

        // 3. Usuarios y Perfiles

        // Administrador
        $userAdmin = Usuario::create([
            'id_rol' => $rolAdmin->id_rol,
            'nombre' => 'Olver',
            'apellido' => 'Alvarez',
            'email' => 'admin@plan3000.gob.bo',
            'password' => Hash::make('admin123'),
            'telefono' => '70012345',
            'estado' => 'ACTIVO',
            'fecha_registro' => now(),
        ]);

        // Personal Ventanilla / Call Center
        $userVentanilla = Usuario::create([
            'id_rol' => $rolCallCenter->id_rol,
            'nombre' => 'Mijail',
            'apellido' => 'Galarza',
            'email' => 'ventanilla@plan3000.gob.bo',
            'password' => Hash::make('ventanilla123'),
            'telefono' => '3494008',
            'estado' => 'ACTIVO',
            'fecha_registro' => now(),
        ]);

        // Médicos
        $userDoc1 = Usuario::create([
            'id_rol' => $rolMedico->id_rol,
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'jperez@plan3000.gob.bo',
            'password' => Hash::make('medico123'),
            'telefono' => '71000001',
            'estado' => 'ACTIVO',
        ]);

        $medico1 = Medico::create([
            'id_usuario' => $userDoc1->id_usuario,
            'id_especialidad' => $espCardio->id_especialidad,
            'titulo' => 'Especialista en Cardiología',
            'numero_colegiatura' => 'MP-10452',
            'estado' => 'ACTIVO',
        ]);

        $userDoc2 = Usuario::create([
            'id_rol' => $rolMedico->id_rol,
            'nombre' => 'María',
            'apellido' => 'López',
            'email' => 'mlopez@plan3000.gob.bo',
            'password' => Hash::make('medico123'),
            'telefono' => '71000002',
            'estado' => 'ACTIVO',
        ]);

        $medico2 = Medico::create([
            'id_usuario' => $userDoc2->id_usuario,
            'id_especialidad' => $espPedia->id_especialidad,
            'titulo' => 'Especialista en Pediatría Clínica',
            'numero_colegiatura' => 'MP-10884',
            'estado' => 'ACTIVO',
        ]);

        $userDoc3 = Usuario::create([
            'id_rol' => $rolMedico->id_rol,
            'nombre' => 'Carlos',
            'apellido' => 'Roca',
            'email' => 'croca@plan3000.gob.bo',
            'password' => Hash::make('medico123'),
            'telefono' => '71000003',
            'estado' => 'ACTIVO',
        ]);

        $medico3 = Medico::create([
            'id_usuario' => $userDoc3->id_usuario,
            'id_especialidad' => $espMedGen->id_especialidad,
            'titulo' => 'Médico Cirujano General',
            'numero_colegiatura' => 'MP-11201',
            'estado' => 'ACTIVO',
        ]);

        // Pacientes
        $userPac1 = Usuario::create([
            'id_rol' => $rolPaciente->id_rol,
            'nombre' => 'Juan',
            'apellido' => 'Pérez García',
            'email' => 'juan.perez@mail.com',
            'password' => Hash::make('paciente123'),
            'telefono' => '76543210',
            'estado' => 'ACTIVO',
        ]);

        $paciente1 = Paciente::create([
            'id_usuario' => $userPac1->id_usuario,
            'ci' => '12345678',
            'fecha_nacimiento' => '1990-05-15',
            'genero' => 'MASCULINO',
            'direccion' => 'Av. San Aurelio #123, Plan 3000',
            'telefono_emergencia' => '76543211',
            'contacto_emergencia' => 'María García (Esposa)',
        ]);

        $userPac2 = Usuario::create([
            'id_rol' => $rolPaciente->id_rol,
            'nombre' => 'Ana',
            'apellido' => 'García Torres',
            'email' => 'ana.garcia@mail.com',
            'password' => Hash::make('paciente123'),
            'telefono' => '77123456',
            'estado' => 'ACTIVO',
        ]);

        $paciente2 = Paciente::create([
            'id_usuario' => $userPac2->id_usuario,
            'ci' => '87654321',
            'fecha_nacimiento' => '1985-11-20',
            'genero' => 'FEMENINO',
            'direccion' => 'Barrio 8 de Diciembre, Manzana 4',
            'telefono_emergencia' => '77123450',
            'contacto_emergencia' => 'Roberto Torres (Hermano)',
        ]);

        $userPac3 = Usuario::create([
            'id_rol' => $rolPaciente->id_rol,
            'nombre' => 'Pedro',
            'apellido' => 'Quiroz Mamani',
            'email' => 'pedro.quiroz@mail.com',
            'password' => Hash::make('paciente123'),
            'telefono' => '78901234',
            'estado' => 'BLOQUEADO',
        ]);

        $paciente3 = Paciente::create([
            'id_usuario' => $userPac3->id_usuario,
            'ci' => '45678901',
            'fecha_nacimiento' => '1972-03-08',
            'genero' => 'MASCULINO',
            'direccion' => 'Av. Che Guevara esq. Calle 3',
            'telefono_emergencia' => '78901200',
            'contacto_emergencia' => 'Lucía Quiroz (Hija)',
        ]);

        // 4. Agendas Médicas
        $today = Carbon::today();

        // Agendas para Dr. Juan Pérez (Cardiología)
        $agendasMed1 = [];
        for ($i = 0; $i < 7; $i++) {
            $fechaSlot = $today->copy()->addDays($i);
            if ($fechaSlot->isSunday()) continue;

            $horarios = ['08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00'];
            foreach ($horarios as $h) {
                $ag = Agenda::create([
                    'id_medico' => $medico1->id_medico,
                    'fecha' => $fechaSlot->format('Y-m-d'),
                    'hora_inicio' => $h,
                    'hora_fin' => Carbon::createFromFormat('H:i', $h)->addMinutes(20)->format('H:i'),
                    'capacidad' => 1,
                    'disponibles' => 1,
                    'estado' => 'DISPONIBLE',
                ]);
                $agendasMed1[] = $ag;
            }
        }

        // Agendas para Dra. María López (Pediatría)
        $agendasMed2 = [];
        for ($i = 0; $i < 7; $i++) {
            $fechaSlot = $today->copy()->addDays($i);
            if ($fechaSlot->isSunday()) continue;

            $horarios = ['14:00', '14:30', '15:00', '15:30', '16:00', '16:30'];
            foreach ($horarios as $h) {
                $ag = Agenda::create([
                    'id_medico' => $medico2->id_medico,
                    'fecha' => $fechaSlot->format('Y-m-d'),
                    'hora_inicio' => $h,
                    'hora_fin' => Carbon::createFromFormat('H:i', $h)->addMinutes(20)->format('H:i'),
                    'capacidad' => 1,
                    'disponibles' => 1,
                    'estado' => 'DISPONIBLE',
                ]);
                $agendasMed2[] = $ag;
            }
        }

        // Agendas para Dr. Carlos Roca (Medicina General)
        for ($i = 0; $i < 7; $i++) {
            $fechaSlot = $today->copy()->addDays($i);
            if ($fechaSlot->isSunday()) continue;

            $horarios = ['08:00', '08:20', '08:40', '09:00', '09:20', '09:40', '10:00'];
            foreach ($horarios as $h) {
                Agenda::create([
                    'id_medico' => $medico3->id_medico,
                    'fecha' => $fechaSlot->format('Y-m-d'),
                    'hora_inicio' => $h,
                    'hora_fin' => Carbon::createFromFormat('H:i', $h)->addMinutes(15)->format('H:i'),
                    'capacidad' => 1,
                    'disponibles' => 1,
                    'estado' => 'DISPONIBLE',
                ]);
            }
        }

        // 5. Citas de Ejemplo
        if (count($agendasMed1) > 2) {
            // Cita 1: Paciente 1 con Dr. Juan Pérez
            $agendaRes1 = $agendasMed1[0];
            $agendaRes1->update(['disponibles' => 0, 'estado' => 'COMPLETO']);

            $cita1 = Cita::create([
                'id_paciente' => $paciente1->id_paciente,
                'id_medico' => $medico1->id_medico,
                'id_agenda' => $agendaRes1->id_agenda,
                'fecha_solicitud' => now()->subDays(1),
                'fecha_cita' => $agendaRes1->fecha,
                'hora_cita' => $agendaRes1->hora_inicio,
                'estado' => 'CONFIRMADA',
                'observaciones' => 'Consulta de control de presión arterial',
            ]);

            Notificacion::create([
                'id_cita' => $cita1->id_cita,
                'id_paciente' => $paciente1->id_paciente,
                'tipo' => 'CONFIRMACION',
                'canal' => 'WHATSAPP',
                'mensaje' => 'Hospital Plan 3000: Su cita de Cardiología con el Dr. Juan Pérez ha sido CONFIRMADA para el ' . $cita1->fecha_cita . ' a las ' . $cita1->hora_cita . '.',
                'estado' => 'ENVIADO',
            ]);

            Notificacion::create([
                'id_cita' => $cita1->id_cita,
                'id_paciente' => $paciente1->id_paciente,
                'tipo' => 'RECORDATORIO',
                'canal' => 'SMS',
                'mensaje' => 'Recordatorio: Mañana tiene cita médica en Hospital Plan 3000 a las ' . $cita1->hora_cita . ' en Consultorio de Cardiología.',
                'estado' => 'ENVIADO',
            ]);
        }

        if (count($agendasMed2) > 2) {
            // Cita 2: Paciente 2 con Dra. María López
            $agendaRes2 = $agendasMed2[1];
            $agendaRes2->update(['disponibles' => 0, 'estado' => 'COMPLETO']);

            $cita2 = Cita::create([
                'id_paciente' => $paciente2->id_paciente,
                'id_medico' => $medico2->id_medico,
                'id_agenda' => $agendaRes2->id_agenda,
                'fecha_solicitud' => now(),
                'fecha_cita' => $agendaRes2->fecha,
                'hora_cita' => $agendaRes2->hora_inicio,
                'estado' => 'SOLICITADA',
                'observaciones' => 'Control de desarrollo pediátrico',
            ]);

            Notificacion::create([
                'id_cita' => $cita2->id_cita,
                'id_paciente' => $paciente2->id_paciente,
                'tipo' => 'CONFIRMACION',
                'canal' => 'CORREO',
                'mensaje' => 'Estimada Ana García, su reserva de cita para Pediatría ha sido solicitada exitosamente para el ' . $cita2->fecha_cita . ' ' . $cita2->hora_cita . '.',
                'estado' => 'ENVIADO',
            ]);
        }

        // Consultorios
        $cons1 = Consultorio::create([
            'nombre_numero' => 'Consultorio 101 - Bloque A',
            'id_especialidad' => $espCardio->id_especialidad,
            'id_medico' => $medico1->id_medico,
            'estado' => 'DISPONIBLE',
            'equipamiento' => 'Electrocardiógrafo, Monitor de Presión Arterial, Ecocardiógrafo Portable',
        ]);

        $cons2 = Consultorio::create([
            'nombre_numero' => 'Consultorio 102 - Bloque A',
            'id_especialidad' => $espPedia->id_especialidad,
            'id_medico' => $medico2->id_medico,
            'estado' => 'DISPONIBLE',
            'equipamiento' => 'Báscula pediátrica, Cinta de perímetro cefálico, Camilla de exploración',
        ]);

        $cons3 = Consultorio::create([
            'nombre_numero' => 'Consultorio 103 - Bloque B',
            'id_especialidad' => $espMedGen->id_especialidad,
            'id_medico' => $medico3->id_medico,
            'estado' => 'DISPONIBLE',
            'equipamiento' => 'Estetoscopio, Tensiómetro, Termómetro digital, Oftalmoscopio',
        ]);

        // Expedientes Médicos Iniciales
        ExpedienteMedico::create([
            'id_paciente' => $paciente1->id_paciente,
            'tipo_sangre' => 'O+',
            'alergias' => 'Penicilina',
            'alergias_medicamentosas' => 'Aspirina a dosis altas',
            'enfermedades_cronicas' => 'Hipertensión Arterial Grado I',
            'antecedentes_personales' => 'Fumador ocasional',
            'antecedentes_familiares' => 'Padre con diabetes tipo 2',
            'cirugias_previas' => 'Apendicectomía (2015)',
            'hospitalizaciones' => 'Ninguna reciente',
            'medicamentos_actuales' => 'Losartán 50mg cada 24h',
            'habitos' => 'Ejercicio 2 veces por semana',
            'observaciones_medicas' => 'Paciente adherente al tratamiento antihipertensivo',
        ]);

        ExpedienteMedico::create([
            'id_paciente' => $paciente2->id_paciente,
            'tipo_sangre' => 'A+',
            'alergias' => 'Polen, Polvo',
            'alergias_medicamentosas' => 'Ninguna conocida',
            'enfermedades_cronicas' => 'Rinitis alérgica',
            'antecedentes_personales' => 'Ninguno',
            'antecedentes_familiares' => 'Madre con hipertensión arterial',
            'cirugias_previas' => 'Ninguna',
            'hospitalizaciones' => 'Ninguna',
            'medicamentos_actuales' => 'Cetirizina 10mg en crisis',
            'habitos' => 'No fuma, no consume alcohol',
            'observaciones_medicas' => 'Sin complicaciones agudas',
        ]);

        // 6. Configuraciones del Sistema
        Configuracion::create(['clave' => 'NOMBRE_HOSPITAL', 'valor' => 'Hospital Municipal Plan 3000', 'descripcion' => 'Nombre institucional']);
        Configuracion::create(['clave' => 'TELEFONO_CONTACTO', 'valor' => '3494008', 'descripcion' => 'Teléfono del Call Center']);
        Configuracion::create(['clave' => 'HORARIO_ATENCION', 'valor' => 'Lunes a Sábado: 07:00 - 19:00', 'descripcion' => 'Horario de atención']);
        Configuracion::create(['clave' => 'INTEROPERABILIDAD_SUIS', 'valor' => 'CONECTADO', 'descripcion' => 'Estado de sincronización con SUIS Bolivia']);

        // 7. Registro de Auditoría Inicial
        Auditoria::create([
            'id_usuario' => $userAdmin->id_usuario,
            'accion' => 'SISTEMA_INICIALIZADO',
            'tabla_afectada' => 'configuraciones',
            'registro_afectado' => 1,
            'detalle' => json_encode(['mensaje' => 'Se completó la siembra de datos de prueba para el Sprint MVP']),
            'fecha_hora' => now(),
            'ip_origen' => '127.0.0.1',
            'user_agent' => 'Seeder Script',
        ]);
    }
}
