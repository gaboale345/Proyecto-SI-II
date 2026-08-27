<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('especialidades', function (Blueprint $table) {
            $table->decimal('precio_consulta', 8, 2)->default(50.00)->after('duracion_turno');
        });

        Schema::create('consultorios', function (Blueprint $table) {
            $table->id('id_consultorio');
            $table->string('nombre_numero', 50); // Ej: Consultorio 101, Bloque B
            $table->foreignId('id_especialidad')->nullable()->constrained('especialidades', 'id_especialidad')->onDelete('set null');
            $table->foreignId('id_medico')->nullable()->constrained('medicos', 'id_medico')->onDelete('set null');
            $table->string('estado', 20)->default('DISPONIBLE'); // DISPONIBLE, OCUPADO, MANTENIMIENTO
            $table->text('equipamiento')->nullable();
            $table->timestamps();
        });

        Schema::table('citas', function (Blueprint $table) {
            $table->foreignId('id_consultorio')->nullable()->after('id_medico')->constrained('consultorios', 'id_consultorio')->onDelete('set null');
            $table->timestamp('hora_llegada')->nullable()->after('estado');
            $table->timestamp('hora_atencion')->nullable()->after('hora_llegada');
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropForeign(['id_consultorio']);
            $table->dropColumn(['id_consultorio', 'hora_llegada', 'hora_atencion']);
        });

        Schema::dropIfExists('consultorios');

        Schema::table('especialidades', function (Blueprint $table) {
            $table->dropColumn('precio_consulta');
        });
    }
};
