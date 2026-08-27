<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\VentanillaController;
use App\Http\Controllers\ContingenciaController;
use App\Http\Middleware\CheckRole;

Route::get('/', function () {
    return redirect()->route('login');
});

// Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas Autenticadas
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rutas Paciente
    Route::middleware([CheckRole::class . ':PACIENTE,ADMIN,CALL_CENTER'])->group(function () {
        Route::get('/paciente/dashboard', [DashboardController::class, 'pacienteDashboard'])->name('paciente.dashboard');
        Route::get('/paciente/citas/solicitar', [CitaController::class, 'solicitarForm'])->name('paciente.citas.solicitar');
        Route::post('/paciente/citas/reservar', [CitaController::class, 'solicitarCita'])->name('paciente.citas.reservar');
        Route::get('/paciente/citas/historial', [CitaController::class, 'historial'])->name('paciente.citas.historial');
        Route::post('/paciente/citas/{id}/cancelar', [CitaController::class, 'cancelarCita'])->name('paciente.citas.cancelar');

        // Módulos extendidos de Paciente
        Route::get('/paciente/perfil', [PacienteController::class, 'perfil'])->name('paciente.perfil');
        Route::post('/paciente/perfil/update', [PacienteController::class, 'updatePerfil'])->name('paciente.perfil.update');
        Route::get('/paciente/historial-clinico', [PacienteController::class, 'historialClinico'])->name('paciente.historial_clinico');
        Route::get('/paciente/citas/{id}/reprogramar', [PacienteController::class, 'reprogramarCitaForm'])->name('paciente.citas.reprogramar_form');
        Route::post('/paciente/citas/{id}/reprogramar', [PacienteController::class, 'reprogramarCita'])->name('paciente.citas.reprogramar');
        Route::get('/paciente/documentos', [PacienteController::class, 'documentos'])->name('paciente.documentos');
        Route::get('/paciente/documentos/{id}/ver', [PacienteController::class, 'verDocumento'])->name('paciente.documento.ver');
    });

    // Rutas Médico
    Route::middleware([CheckRole::class . ':MEDICO,ADMIN'])->group(function () {
        Route::get('/medico/agenda', [MedicoController::class, 'agendaPersonal'])->name('medico.agenda');
        Route::post('/medico/cita/{id}/estado', [MedicoController::class, 'cambiarEstadoCita'])->name('medico.cita.estado');
        Route::get('/medico/cita/{id}/atender', [MedicoController::class, 'atenderConsultaForm'])->name('medico.cita.atender');
        Route::post('/medico/cita/{id}/guardar-consulta', [MedicoController::class, 'guardarConsulta'])->name('medico.cita.guardar_consulta');
    });

    // Rutas Ventanilla / Recepción / Call Center
    Route::middleware([CheckRole::class . ':CALL_CENTER,ADMIN'])->group(function () {
        Route::get('/ventanilla/dashboard', [DashboardController::class, 'ventanillaDashboard'])->name('ventanilla.dashboard');
        Route::get('/ventanilla/sala-espera', [VentanillaController::class, 'salaEspera'])->name('ventanilla.sala_espera');
        Route::post('/ventanilla/cita/{id}/llegada', [VentanillaController::class, 'cambiarEstadoLlegada'])->name('ventanilla.llegada');
        Route::get('/ventanilla/walkin', [VentanillaController::class, 'registroWalkin'])->name('ventanilla.walkin');
        Route::post('/ventanilla/walkin/store', [VentanillaController::class, 'guardarPacienteWalkin'])->name('ventanilla.walkin.store');
        Route::get('/ventanilla/caja', [VentanillaController::class, 'cajaIndex'])->name('ventanilla.caja');
        Route::post('/ventanilla/caja/pagar', [VentanillaController::class, 'procesarPago'])->name('ventanilla.caja.pagar');

        Route::get('/agendas', [AgendaController::class, 'index'])->name('agendas.index');
        Route::post('/agendas/store', [AgendaController::class, 'store'])->name('agendas.store');
        Route::post('/agendas/{id}/bloquear', [AgendaController::class, 'bloquear'])->name('agendas.bloquear');
        Route::get('/contingencia', [ContingenciaController::class, 'index'])->name('contingencia.index');
        Route::post('/contingencia/procesar', [ContingenciaController::class, 'procesarSuspension'])->name('contingencia.procesar');
    });

    // Rutas Administrador
    Route::middleware([CheckRole::class . ':ADMIN'])->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
        Route::get('/admin/usuarios', [AdminController::class, 'usuarios'])->name('admin.usuarios');
        Route::post('/admin/usuarios/crear', [AdminController::class, 'crearUsuario'])->name('admin.usuarios.crear');
        Route::post('/admin/usuarios/{id}/estado', [AdminController::class, 'cambiarEstadoUsuario'])->name('admin.usuarios.estado');
        Route::get('/admin/consultorios', [AdminController::class, 'consultoriosIndex'])->name('admin.consultorios');
        Route::post('/admin/consultorios/store', [AdminController::class, 'consultoriosStore'])->name('admin.consultorios.store');
        Route::post('/admin/horarios/generar', [AdminController::class, 'generarHorariosMasivos'])->name('admin.horarios.generar');
        Route::get('/admin/auditoria', [AdminController::class, 'auditoria'])->name('admin.auditoria');
        Route::get('/admin/reportes', [AdminController::class, 'reportes'])->name('admin.reportes');
        Route::get('/admin/reportes/exportar', [AdminController::class, 'exportarReporte'])->name('admin.reportes.exportar');
        Route::get('/admin/configuracion', [AdminController::class, 'configuracionIndex'])->name('admin.configuracion');
        Route::post('/admin/configuracion/update', [AdminController::class, 'configuracionUpdate'])->name('admin.configuracion.update');
    });
});
