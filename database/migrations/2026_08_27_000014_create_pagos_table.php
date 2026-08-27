<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('id_pago');
            $table->foreignId('id_cita')->constrained('citas', 'id_cita')->onDelete('cascade');
            $table->foreignId('id_paciente')->constrained('pacientes', 'id_paciente')->onDelete('cascade');
            $table->foreignId('id_usuario_caja')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->decimal('monto_total', 10, 2);
            $table->decimal('monto_pagado', 10, 2);
            $table->decimal('monto_pendiente', 10, 2)->default(0.00);
            $table->string('metodo_pago', 30)->default('EFECTIVO'); // EFECTIVO, TARJETA, TRANSFERENCIA, QR
            $table->string('estado_pago', 20)->default('PAGADO'); // PAGADO, PARCIAL, PENDIENTE, DEVOLUCION
            $table->string('numero_comprobante', 50)->unique();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
