<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id('id_agenda');
            $table->foreignId('id_medico')->constrained('medicos', 'id_medico')->onDelete('cascade');
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->integer('capacidad')->default(1);
            $table->integer('disponibles')->default(1);
            $table->string('estado', 20)->default('DISPONIBLE'); // DISPONIBLE, BLOQUEADO, COMPLETO
            $table->string('motivo_bloqueo', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
