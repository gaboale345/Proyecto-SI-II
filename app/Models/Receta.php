<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory;

    protected $table = 'recetas';
    protected $primaryKey = 'id_receta';

    protected $fillable = [
        'id_consulta',
        'id_paciente',
        'id_medico',
        'codigo_receta',
        'estado',
        'fecha_emision',
        'fecha_dispensacion',
        'indicaciones_generales',
        'observaciones_farmacia',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'fecha_dispensacion' => 'datetime',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'id_consulta', 'id_consulta');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }

    public function items()
    {
        return $this->hasMany(RecetaItem::class, 'id_receta', 'id_receta');
    }

    public function dispensaciones()
    {
        return $this->hasMany(Dispensacion::class, 'id_receta', 'id_receta');
    }
}
