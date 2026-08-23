<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Agenda;
use App\Models\Cita;
use App\Models\Role;
use App\Models\Especialidad;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CitasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_paciente_puede_iniciar_sesion_con_ci()
    {
        $response = $this->post('/login', [
            'login' => '12345678',
            'password' => 'paciente123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_paciente_puede_consultar_turnos_disponibles()
    {
        $user = Usuario::where('email', 'juan.perez@mail.com')->first();
        $especialidad = Especialidad::first();

        $response = $this->actingAs($user)->get('/paciente/citas/solicitar?especialidad_id=' . $especialidad->id_especialidad);

        $response->assertStatus(200);
        $response->assertSee($especialidad->nombre);
    }

    public function test_paciente_puede_reservar_cita_y_decrementar_cupos()
    {
        $user = Usuario::where('email', 'ana.garcia@mail.com')->first();

        // Buscar una agenda en una fecha donde la paciente no tenga cita previa
        $agenda = Agenda::where('estado', 'DISPONIBLE')
            ->where('disponibles', '>', 0)
            ->whereNotIn('fecha', function($q) use ($user) {
                $q->select('fecha_cita')->from('citas')->where('id_paciente', $user->paciente->id_paciente);
            })
            ->first();

        $this->assertNotNull($agenda);
        $cuposIniciales = $agenda->disponibles;

        $response = $this->actingAs($user)->post('/paciente/citas/reservar', [
            'id_agenda' => $agenda->id_agenda,
            'observaciones' => 'Prueba de test automatizado',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('citas', [
            'id_agenda' => $agenda->id_agenda,
            'id_paciente' => $user->paciente->id_paciente,
            'estado' => 'CONFIRMADA',
        ]);

        $agendaFresh = $agenda->fresh();
        $this->assertEquals($cuposIniciales - 1, $agendaFresh->disponibles);
    }

    public function test_admin_puede_acceder_a_dashboard_y_auditoria()
    {
        $admin = Usuario::where('email', 'admin@plan3000.gob.bo')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Panel General de Administración');

        $responseAudit = $this->actingAs($admin)->get('/admin/auditoria');
        $responseAudit->assertStatus(200);
        $responseAudit->assertSee('Registros de Auditoría');
    }
}
