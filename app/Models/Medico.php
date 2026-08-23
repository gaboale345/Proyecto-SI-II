<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory;

    protected $table = 'medicos';
    protected $primaryKey = 'id_medico';

    protected $fillable = [
        'id_usuario',
        'id_especialidad',
        'titulo',
        'numero_colegiatura',
        'estado',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'id_especialidad', 'id_especialidad');
    }

    public function agendas()
    {
        return $this->hasMany(Agenda::class, 'id_medico', 'id_medico');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_medico', 'id_medico');
    }
}
