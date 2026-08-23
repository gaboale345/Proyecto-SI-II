<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\AdminController;
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
    });

    // Rutas Médico
    Route::middleware([CheckRole::class . ':MEDICO,ADMIN'])->group(function () {
        Route::get('/medico/agenda', [MedicoController::class, 'agendaPersonal'])->name('medico.agenda');
        Route::post('/medico/cita/{id}/estado', [MedicoController::class, 'cambiarEstadoCita'])->name('medico.cita.estado');
    });

    // Rutas Ventanilla / Call Center
    Route::middleware([CheckRole::class . ':CALL_CENTER,ADMIN'])->group(function () {
        Route::get('/ventanilla/dashboard', [DashboardController::class, 'ventanillaDashboard'])->name('ventanilla.dashboard');
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
        Route::get('/admin/auditoria', [AdminController::class, 'auditoria'])->name('admin.auditoria');
        Route::get('/admin/reportes', [AdminController::class, 'reportes'])->name('admin.reportes');
    });
});
