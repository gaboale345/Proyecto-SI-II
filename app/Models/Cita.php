<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';
    protected $primaryKey = 'id_cita';

    protected $fillable = [
        'id_paciente',
        'id_medico',
        'id_agenda',
        'fecha_solicitud',
        'fecha_cita',
        'hora_cita',
        'estado',
        'motivo_cancelacion',
        'id_cita_original',
        'observaciones',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }

    public function agenda()
    {
        return $this->belongsTo(Agenda::class, 'id_agenda', 'id_agenda');
    }

    public function citaOriginal()
    {
        return $this->belongsTo(Cita::class, 'id_cita_original', 'id_cita');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'id_cita', 'id_cita');
    }
}
