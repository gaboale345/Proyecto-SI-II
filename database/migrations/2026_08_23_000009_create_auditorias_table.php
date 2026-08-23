<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id('id_auditoria');
            $table->foreignId('id_usuario')->nullable()->constrained('usuarios', 'id_usuario')->onDelete('set null');
            $table->string('accion', 50);
            $table->string('tabla_afectada', 50);
            $table->integer('registro_afectado')->nullable();
            $table->text('detalle')->nullable(); // JSON
            $table->timestamp('fecha_hora')->useCurrent();
            $table->string('ip_origen', 45)->default('127.0.0.1');
            $table->string('user_agent', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
