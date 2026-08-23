<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id('id_cita');
            $table->foreignId('id_paciente')->constrained('pacientes', 'id_paciente')->onDelete('cascade');
            $table->foreignId('id_medico')->constrained('medicos', 'id_medico')->onDelete('cascade');
            $table->foreignId('id_agenda')->constrained('agendas', 'id_agenda')->onDelete('cascade');
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->date('fecha_cita');
            $table->time('hora_cita');
            $table->string('estado', 20)->default('SOLICITADA'); // SOLICITADA, CONFIRMADA, ATENDIDA, CANCELADA, REPROGRAMADA, NO_ASISTIO
            $table->string('motivo_cancelacion', 200)->nullable();
            $table->foreignId('id_cita_original')->nullable()->constrained('citas', 'id_cita')->onDelete('set null');
            $table->string('observaciones', 300)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
