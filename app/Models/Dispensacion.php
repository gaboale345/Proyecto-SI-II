<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispensacion extends Model
{
    use HasFactory;

    protected $table = 'dispensaciones';
    protected $primaryKey = 'id_dispensacion';

    protected $fillable = [
        'id_receta',
        'id_usuario_farmacia',
        'fecha_hora',
        'monto_total',
        'observaciones',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'monto_total' => 'decimal:2',
    ];

    public function receta()
    {
        return $this->belongsTo(Receta::class, 'id_receta', 'id_receta');
    }

    public function usuarioFarmacia()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_farmacia', 'id_usuario');
    }
}
