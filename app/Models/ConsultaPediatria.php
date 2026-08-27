<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultaPediatria extends Model
{
    protected $table = 'consultas_pediatria';
    protected $primaryKey = 'id_pediatria';

    protected $fillable = [
        'id_consulta', 'responsable_nombre', 'responsable_relacion', 'responsable_contacto',
        'peso', 'talla', 'perimetro_cefalico', 'percentil_peso', 'percentil_talla',
        'antecedentes_perinatales', 'vacunas_aplicadas', 'desarrollo_observaciones'
    ];

    protected $casts = [
        'vacunas_aplicadas' => 'array'
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta', 'id_consulta');
    }
}
