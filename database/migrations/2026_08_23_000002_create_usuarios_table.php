<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->foreignId('id_rol')->constrained('roles', 'id_rol')->onDelete('cascade');
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->string('telefono', 20);
            $table->string('estado', 20)->default('ACTIVO'); // ACTIVO, INACTIVO, BLOQUEADO
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamp('ultimo_acceso')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
