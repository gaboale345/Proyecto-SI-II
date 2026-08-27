<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultaGinecologia extends Model
{
    protected $table = 'consultas_ginecologia';
    protected $primaryKey = 'id_ginecologia';

    protected $fillable = [
        'id_consulta', 'fum', 'ciclo_menstrual', 'gestas', 'partos', 'cesareas', 'abortos',
        'metodo_anticonceptivo', 'resultado_papanicolaou', 'resultado_ecografia', 'exploracion_ginecológica'
    ];

    protected $casts = [
        'fum' => 'date'
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta', 'id_consulta');
    }
}
