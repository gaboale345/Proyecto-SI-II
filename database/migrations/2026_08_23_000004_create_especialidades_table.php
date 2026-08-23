<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('especialidades', function (Blueprint $table) {
            $table->id('id_especialidad');
            $table->string('nombre', 50)->unique();
            $table->string('descripcion', 200)->nullable();
            $table->integer('duracion_turno')->default(20); // minutos
            $table->string('estado', 20)->default('ACTIVO');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('especialidades');
    }
};
