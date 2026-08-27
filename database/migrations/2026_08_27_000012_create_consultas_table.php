<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id('id_consulta');
            $table->foreignId('id_cita')->constrained('citas', 'id_cita')->onDelete('cascade');
            $table->foreignId('id_paciente')->constrained('pacientes', 'id_paciente')->onDelete('cascade');
            $table->foreignId('id_medico')->constrained('medicos', 'id_medico')->onDelete('cascade');
            $table->foreignId('id_especialidad')->constrained('especialidades', 'id_especialidad')->onDelete('cascade');
            $table->dateTime('fecha_hora');
            $table->text('motivo_consulta');
            $table->text('diagnostico_principal');
            $table->text('diagnostico_secundario')->nullable();
            $table->text('diagnostico_diferencial')->nullable();
            $table->text('plan_tratamiento')->nullable();
            $table->text('indicaciones')->nullable();
            $table->json('medicamentos_recetados')->nullable(); // Array of {nombre, dosis, frecuencia, duracion}
            $table->json('estudios_solicitados')->nullable(); // Array of {estudio, indicacion}
            $table->integer('incapacidad_dias')->default(0);
            $table->text('certificado_medico')->nullable();
            $table->date('proximo_control')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
