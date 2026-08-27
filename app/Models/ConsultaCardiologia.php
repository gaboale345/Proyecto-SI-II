<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultaCardiologia extends Model
{
    protected $table = 'consultas_cardiologia';
    protected $primaryKey = 'id_cardio';

    protected $fillable = [
        'id_consulta', 'presion_arterial', 'frecuencia_cardiaca', 'frecuencia_respiratoria',
        'saturacion_oxigeno', 'peso', 'talla', 'imc', 'temperatura', 'sintomas',
        'ruidos_cardiacos', 'ritmo', 'soplos', 'pulsos', 'edemas', 'estudios_solicitados'
    ];

    protected $casts = [
        'sintomas' => 'array',
        'estudios_solicitados' => 'array'
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta', 'id_consulta');
    }
}
