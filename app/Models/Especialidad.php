<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    use HasFactory;

    protected $table = 'especialidades';
    protected $primaryKey = 'id_especialidad';

    protected $fillable = [
        'nombre',
        'descripcion',
        'duracion_minutos',
        'precio_consulta',
        'estado',
    ];

    public function medicos()
    {
        return $this->hasMany(Medico::class, 'id_especialidad', 'id_especialidad');
    }

    public function consultorios()
    {
        return $this->hasMany(Consultorio::class, 'id_especialidad', 'id_especialidad');
    }
}
