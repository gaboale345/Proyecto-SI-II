<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';
    protected $primaryKey = 'id_pago';

    protected $fillable = [
        'id_cita',
        'id_paciente',
        'id_usuario_caja',
        'monto_total',
        'monto_pagado',
        'monto_pendiente',
        'metodo_pago',
        'estado_pago',
        'numero_comprobante',
        'observaciones'
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'id_cita', 'id_cita');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function cajero()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_caja', 'id_usuario');
    }
}
