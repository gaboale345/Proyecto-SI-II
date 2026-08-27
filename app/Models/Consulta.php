<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory;

    protected $table = 'consultas';
    protected $primaryKey = 'id_consulta';

    protected $fillable = [
        'id_cita',
        'id_paciente',
        'id_medico',
        'id_especialidad',
        'fecha_hora',
        'motivo_consulta',
        'diagnostico_principal',
        'diagnostico_secundario',
        'diagnostico_diferencial',
        'plan_tratamiento',
        'indicaciones',
        'medicamentos_recetados',
        'estudios_solicitados',
        'incapacidad_dias',
        'certificado_medico',
        'proximo_control'
    ];

    protected $casts = [
        'medicamentos_recetados' => 'array',
        'estudios_solicitados' => 'array',
        'fecha_hora' => 'datetime',
        'proximo_control' => 'date'
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'id_cita', 'id_cita');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'id_especialidad', 'id_especialidad');
    }

    public function cardiologia()
    {
        return $this->hasOne(ConsultaCardiologia::class, 'id_consulta', 'id_consulta');
    }

    public function pediatria()
    {
        return $this->hasOne(ConsultaPediatria::class, 'id_consulta', 'id_consulta');
    }

    public function medicinaGeneral()
    {
        return $this->hasOne(ConsultaMedicinaGeneral::class, 'id_consulta', 'id_consulta');
    }

    public function ginecologia()
    {
        return $this->hasOne(ConsultaGinecologia::class, 'id_consulta', 'id_consulta');
    }

    public function traumatologia()
    {
        return $this->hasOne(ConsultaTraumatologia::class, 'id_consulta', 'id_consulta');
    }
}
