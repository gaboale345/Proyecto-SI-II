<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('sexo', 20)->default('MASCULINO')->after('genero');
            $table->string('nacionalidad', 50)->default('Boliviana')->after('sexo');
            $table->string('estado_civil', 30)->default('SOLTERO/A')->after('nacionalidad');
            $table->string('ocupacion', 100)->nullable()->after('estado_civil');
            $table->string('ciudad', 80)->default('Santa Cruz de la Sierra')->after('direccion');
            $table->string('whatsapp', 20)->nullable()->after('telefono_emergencia');
            $table->string('relacion_contacto', 50)->nullable()->after('contacto_emergencia');
        });

        Schema::create('expedientes_medicos', function (Blueprint $table) {
            $table->id('id_expediente');
            $table->foreignId('id_paciente')->unique()->constrained('pacientes', 'id_paciente')->onDelete('cascade');
            $table->string('tipo_sangre', 10)->default('ORH+');
            $table->text('alergias')->nullable();
            $table->text('alergias_medicamentosas')->nullable();
            $table->text('enfermedades_cronicas')->nullable();
            $table->text('antecedentes_personales')->nullable();
            $table->text('antecedentes_familiares')->nullable();
            $table->text('cirugias_previas')->nullable();
            $table->text('hospitalizaciones')->nullable();
            $table->text('medicamentos_actuales')->nullable();
            $table->text('habitos')->nullable();
            $table->text('observaciones_medicas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expedientes_medicos');
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn([
                'sexo', 'nacionalidad', 'estado_civil', 'ocupacion',
                'ciudad', 'whatsapp', 'relacion_contacto'
            ]);
        });
    }
};
