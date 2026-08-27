<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultaMedicinaGeneral extends Model
{
    protected $table = 'consultas_medicina_general';
    protected $primaryKey = 'id_med_gen';

    protected $fillable = [
        'id_consulta', 'presion_arterial', 'frecuencia_cardiaca', 'frecuencia_respiratoria',
        'saturacion_oxigeno', 'temperatura', 'peso', 'talla', 'imc',
        'exploracion_cabeza_cuello', 'exploracion_cardiopulmonar', 'exploracion_abdomen',
        'exploracion_neurologica', 'exploracion_piel_faneras', 'exploracion_musculoesqueletica'
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta', 'id_consulta');
    }
}
