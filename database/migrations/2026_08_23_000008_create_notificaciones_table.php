<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id('id_notificacion');
            $table->foreignId('id_cita')->constrained('citas', 'id_cita')->onDelete('cascade');
            $table->foreignId('id_paciente')->constrained('pacientes', 'id_paciente')->onDelete('cascade');
            $table->string('tipo', 30); // CONFIRMACION, RECORDATORIO, SUSPENSION, REPROGRAMACION, ALERTA
            $table->string('canal', 20); // SMS, CORREO, WHATSAPP, PUSH
            $table->text('mensaje');
            $table->timestamp('fecha_envio')->useCurrent();
            $table->string('estado', 20)->default('ENVIADO'); // PENDIENTE, ENVIADO, FALLIDO, VISTO
            $table->string('error', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
