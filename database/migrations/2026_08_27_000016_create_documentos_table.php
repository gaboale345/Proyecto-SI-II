<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id('id_documento');
            $table->foreignId('id_paciente')->constrained('pacientes', 'id_paciente')->onDelete('cascade');
            $table->foreignId('id_consulta')->nullable()->constrained('consultas', 'id_consulta')->onDelete('cascade');
            $table->foreignId('id_medico')->nullable()->constrained('medicos', 'id_medico')->onDelete('set null');
            $table->string('tipo_documento', 50); // RECETA, CERTIFICADO, INFORME, ESTUDIO
            $table->string('titulo', 150);
            $table->text('contenido_html');
            $table->string('codigo_verificacion', 50)->unique();
            $table->boolean('autorizado_descarga')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
