<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model implements AuthenticatableContract
{
    use HasFactory, Authenticatable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'id_rol',
        'nombre',
        'apellido',
        'email',
        'password',
        'telefono',
        'estado',
        'fecha_registro',
        'ultimo_acceso',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_rol', 'id_rol');
    }

    public function paciente()
    {
        return $this->hasOne(Paciente::class, 'id_usuario', 'id_usuario');
    }

    public function medico()
    {
        return $this->hasOne(Medico::class, 'id_usuario', 'id_usuario');
    }

    public function auditorias()
    {
        return $this->hasMany(Auditoria::class, 'id_usuario', 'id_usuario');
    }

    public function isPaciente(): bool
    {
        return optional($this->role)->nombre_rol === 'PACIENTE';
    }

    public function isMedico(): bool
    {
        return optional($this->role)->nombre_rol === 'MEDICO';
    }

    public function isCallCenter(): bool
    {
        return optional($this->role)->nombre_rol === 'CALL_CENTER';
    }

    public function isAdmin(): bool
    {
        return optional($this->role)->nombre_rol === 'ADMIN';
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }
}
