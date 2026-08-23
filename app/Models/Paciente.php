<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';
    protected $primaryKey = 'id_paciente';

    protected $fillable = [
        'id_usuario',
        'ci',
        'fecha_nacimiento',
        'genero',
        'direccion',
        'telefono_emergencia',
        'contacto_emergencia',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_paciente', 'id_paciente');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'id_paciente', 'id_paciente');
    }
}
