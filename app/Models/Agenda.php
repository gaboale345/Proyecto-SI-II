<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agendas';
    protected $primaryKey = 'id_agenda';

    protected $fillable = [
        'id_medico',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'capacidad',
        'disponibles',
        'estado',
        'motivo_bloqueo',
    ];

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_agenda', 'id_agenda');
    }
}
