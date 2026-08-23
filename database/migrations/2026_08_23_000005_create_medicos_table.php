<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicos', function (Blueprint $table) {
            $table->id('id_medico');
            $table->foreignId('id_usuario')->unique()->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->foreignId('id_especialidad')->constrained('especialidades', 'id_especialidad')->onDelete('cascade');
            $table->string('titulo', 100);
            $table->string('numero_colegiatura', 30)->unique();
            $table->string('estado', 20)->default('ACTIVO'); // ACTIVO, DE BAJA, EN VACACIONES
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicos');
    }
};
