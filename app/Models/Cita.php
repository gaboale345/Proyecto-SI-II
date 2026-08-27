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
        'id_consultorio',
        'id_agenda',
        'fecha_solicitud',
        'fecha_cita',
        'hora_cita',
        'estado',
        'hora_llegada',
        'hora_atencion',
        'motivo_cancelacion',
        'id_cita_original',
        'observaciones',
    ];

    protected $casts = [
        'fecha_cita' => 'date',
        'hora_llegada' => 'datetime',
        'hora_atencion' => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }

    public function consultorio()
    {
        return $this->belongsTo(Consultorio::class, 'id_consultorio', 'id_consultorio');
    }

    public function agenda()
    {
        return $this->belongsTo(Agenda::class, 'id_agenda', 'id_agenda');
    }

    public function consulta()
    {
        return $this->hasOne(Consulta::class, 'id_cita', 'id_cita');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class, 'id_cita', 'id_cita');
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
