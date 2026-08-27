<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Cardiología
        Schema::create('consultas_cardiologia', function (Blueprint $table) {
            $table->id('id_cardio');
            $table->foreignId('id_consulta')->constrained('consultas', 'id_consulta')->onDelete('cascade');
            $table->string('presion_arterial', 20)->nullable();
            $table->integer('frecuencia_cardiaca')->nullable();
            $table->integer('frecuencia_respiratoria')->nullable();
            $table->decimal('saturacion_oxigeno', 5, 2)->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('talla', 5, 2)->nullable();
            $table->decimal('imc', 5, 2)->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->json('sintomas')->nullable(); // dolor toracico, palpitaciones, falta de aire, etc.
            $table->string('ruidos_cardiacos', 100)->nullable();
            $table->string('ritmo', 50)->nullable();
            $table->string('soplos', 100)->nullable();
            $table->string('pulsos', 100)->nullable();
            $table->string('edemas', 100)->nullable();
            $table->json('estudios_solicitados')->nullable(); // ECG, Eco, Holter, MAPA, etc.
            $table->timestamps();
        });

        // 2. Pediatría
        Schema::create('consultas_pediatria', function (Blueprint $table) {
            $table->id('id_pediatria');
            $table->foreignId('id_consulta')->constrained('consultas', 'id_consulta')->onDelete('cascade');
            $table->string('responsable_nombre', 100)->nullable();
            $table->string('responsable_relacion', 50)->nullable();
            $table->string('responsable_contacto', 50)->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('talla', 5, 2)->nullable();
            $table->decimal('perimetro_cefalico', 5, 2)->nullable();
            $table->string('percentil_peso', 20)->nullable();
            $table->string('percentil_talla', 20)->nullable();
            $table->text('antecedentes_perinatales')->nullable();
            $table->json('vacunas_aplicadas')->nullable(); // array de {vacuna, dosis, fecha, lote}
            $table->text('desarrollo_observaciones')->nullable();
            $table->timestamps();
        });

        // 3. Medicina General
        Schema::create('consultas_medicina_general', function (Blueprint $table) {
            $table->id('id_med_gen');
            $table->foreignId('id_consulta')->constrained('consultas', 'id_consulta')->onDelete('cascade');
            $table->string('presion_arterial', 20)->nullable();
            $table->integer('frecuencia_cardiaca')->nullable();
            $table->integer('frecuencia_respiratoria')->nullable();
            $table->decimal('saturacion_oxigeno', 5, 2)->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('talla', 5, 2)->nullable();
            $table->decimal('imc', 5, 2)->nullable();
            $table->text('exploracion_cabeza_cuello')->nullable();
            $table->text('exploracion_cardiopulmonar')->nullable();
            $table->text('exploracion_abdomen')->nullable();
            $table->text('exploracion_neurologica')->nullable();
            $table->text('exploracion_piel_faneras')->nullable();
            $table->text('exploracion_musculoesqueletica')->nullable();
            $table->timestamps();
        });

        // 4. Ginecología
        Schema::create('consultas_ginecologia', function (Blueprint $table) {
            $table->id('id_ginecologia');
            $table->foreignId('id_consulta')->constrained('consultas', 'id_consulta')->onDelete('cascade');
            $table->date('fum')->nullable(); // Fecha de Ultima Menstruacion
            $table->string('ciclo_menstrual', 50)->nullable();
            $table->integer('gestas')->default(0);
            $table->integer('partos')->default(0);
            $table->integer('cesareas')->default(0);
            $table->integer('abortos')->default(0);
            $table->string('metodo_anticonceptivo', 100)->nullable();
            $table->text('resultado_papanicolaou')->nullable();
            $table->text('resultado_ecografia')->nullable();
            $table->text('exploracion_ginecológica')->nullable();
            $table->timestamps();
        });

        // 5. Traumatología
        Schema::create('consultas_traumatologia', function (Blueprint $table) {
            $table->id('id_traumatologia');
            $table->foreignId('id_consulta')->constrained('consultas', 'id_consulta')->onDelete('cascade');
            $table->string('zona_afectada', 100)->nullable();
            $table->text('mecanismo_lesion')->nullable();
            $table->integer('intensidad_dolor')->default(1); // Escala 1 a 10
            $table->string('movilidad', 100)->nullable();
            $table->string('fuerza_muscular', 100)->nullable();
            $table->string('sensibilidad', 100)->nullable();
            $table->string('deformidad', 100)->nullable();
            $table->string('estado_neurovascular', 100)->nullable();
            $table->json('estudios_imagen')->nullable(); // Radiografia, Tomografia, Resonancia, etc.
            $table->text('indicacion_inmovilizacion')->nullable();
            $table->text('indicacion_fisioterapia')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas_traumatologia');
        Schema::dropIfExists('consultas_ginecologia');
        Schema::dropIfExists('consultas_medicina_general');
        Schema::dropIfExists('consultas_pediatria');
        Schema::dropIfExists('consultas_cardiologia');
    }
};
