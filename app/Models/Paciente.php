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
        'sexo',
        'nacionalidad',
        'estado_civil',
        'ocupacion',
        'direccion',
        'ciudad',
        'telefono_emergencia',
        'whatsapp',
        'contacto_emergencia',
        'relacion_contacto'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_paciente', 'id_paciente');
    }

    public function expediente()
    {
        return $this->hasOne(ExpedienteMedico::class, 'id_paciente', 'id_paciente');
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'id_paciente', 'id_paciente');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_paciente', 'id_paciente');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'id_paciente', 'id_paciente');
    }

    public function getEdadAttribute()
    {
        if (!$this->fecha_nacimiento) return null;
        return \Carbon\Carbon::parse($this->fecha_nacimiento)->age;
    }
}
