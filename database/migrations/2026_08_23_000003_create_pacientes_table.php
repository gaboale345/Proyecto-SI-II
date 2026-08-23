<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id('id_paciente');
            $table->foreignId('id_usuario')->unique()->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->string('ci', 15)->unique();
            $table->date('fecha_nacimiento');
            $table->string('genero', 10); // MASCULINO, FEMENINO, OTRO
            $table->string('direccion', 150)->nullable();
            $table->string('telefono_emergencia', 20)->nullable();
            $table->string('contacto_emergencia', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
