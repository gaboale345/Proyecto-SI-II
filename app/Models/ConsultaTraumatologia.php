<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultaTraumatologia extends Model
{
    protected $table = 'consultas_traumatologia';
    protected $primaryKey = 'id_traumatologia';

    protected $fillable = [
        'id_consulta', 'zona_afectada', 'mecanismo_lesion', 'intensidad_dolor',
        'movilidad', 'fuerza_muscular', 'sensibilidad', 'deformidad',
        'estado_neurovascular', 'estudios_imagen', 'indicacion_inmovilizacion', 'indicacion_fisioterapia'
    ];

    protected $casts = [
        'estudios_imagen' => 'array'
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta', 'id_consulta');
    }
}
