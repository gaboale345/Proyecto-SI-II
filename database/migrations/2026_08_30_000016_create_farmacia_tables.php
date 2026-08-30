<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Catálogo / Inventario de Farmacia
        Schema::create('medicamentos_farmacia', function (Blueprint $table) {
            $table->id('id_medicamento');
            $table->string('codigo_barras', 50)->nullable()->unique();
            $table->string('nombre_comercial', 150);
            $table->string('nombre_generico', 150)->nullable();
            $table->string('categoria', 50)->default('MEDICAMENTO'); // MEDICAMENTO, JARABE, INYECTABLE, INSUMO_MEDICO, MATERIAL_CURACION, OTRO
            $table->string('presentacion', 100)->nullable(); // Tabletas, Frasco 120ml, Ampolla, Caja x 20, etc.
            $table->string('concentracion', 100)->nullable(); // 500mg, 100mg/5ml, 70%, etc.
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(10);
            $table->decimal('precio_unitario', 10, 2)->default(0.00);
            $table->date('fecha_vencimiento')->nullable();
            $table->string('lote', 50)->nullable();
            $table->string('ubicacion_estante', 50)->nullable();
            $table->string('estado', 20)->default('ACTIVO'); // ACTIVO, AGOTADO, VENCIDO, INACTIVO
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        // 2. Recetas Médicas emitidas en consultas
        Schema::create('recetas', function (Blueprint $table) {
            $table->id('id_receta');
            $table->unsignedBigInteger('id_consulta');
            $table->unsignedBigInteger('id_paciente');
            $table->unsignedBigInteger('id_medico');
            $table->string('codigo_receta', 50)->unique();
            $table->string('estado', 20)->default('PENDIENTE'); // PENDIENTE, DISPENSADA, PARCIAL, CANCELADA
            $table->dateTime('fecha_emision')->useCurrent();
            $table->dateTime('fecha_dispensacion')->nullable();
            $table->text('indicaciones_generales')->nullable();
            $table->text('observaciones_farmacia')->nullable();
            $table->timestamps();

            $table->foreign('id_consulta')->references('id_consulta')->on('consultas')->onDelete('cascade');
            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');
            $table->foreign('id_medico')->references('id_medico')->on('medicos')->onDelete('cascade');
        });

        // 3. Ítems detallados de la Receta
        Schema::create('receta_items', function (Blueprint $table) {
            $table->id('id_item');
            $table->unsignedBigInteger('id_receta');
            $table->unsignedBigInteger('id_medicamento')->nullable();
            $table->string('nombre_medicamento', 150);
            $table->string('dosis', 100)->nullable();
            $table->string('frecuencia', 100)->nullable();
            $table->string('duracion', 100)->nullable();
            $table->integer('cantidad_solicitada')->default(1);
            $table->integer('cantidad_despachada')->default(0);
            $table->string('estado_item', 20)->default('PENDIENTE'); // PENDIENTE, ENTREGADO, NO_DISPONIBLE
            $table->timestamps();

            $table->foreign('id_receta')->references('id_receta')->on('recetas')->onDelete('cascade');
            $table->foreign('id_medicamento')->references('id_medicamento')->on('medicamentos_farmacia')->nullOnDelete();
        });

        // 4. Dispensaciones / Entregas en Farmacia
        Schema::create('dispensaciones', function (Blueprint $table) {
            $table->id('id_dispensacion');
            $table->unsignedBigInteger('id_receta');
            $table->unsignedBigInteger('id_usuario_farmacia');
            $table->dateTime('fecha_hora')->useCurrent();
            $table->decimal('monto_total', 10, 2)->default(0.00);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('id_receta')->references('id_receta')->on('recetas')->onDelete('cascade');
            $table->foreign('id_usuario_farmacia')->references('id_usuario')->on('usuarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispensaciones');
        Schema::dropIfExists('receta_items');
        Schema::dropIfExists('recetas');
        Schema::dropIfExists('medicamentos_farmacia');
    }
};
