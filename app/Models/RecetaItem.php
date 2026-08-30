<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecetaItem extends Model
{
    use HasFactory;

    protected $table = 'receta_items';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'id_receta',
        'id_medicamento',
        'nombre_medicamento',
        'dosis',
        'frecuencia',
        'duracion',
        'cantidad_solicitada',
        'cantidad_despachada',
        'estado_item',
    ];

    public function receta()
    {
        return $this->belongsTo(Receta::class, 'id_receta', 'id_receta');
    }

    public function medicamento()
    {
        return $this->belongsTo(MedicamentoFarmacia::class, 'id_medicamento', 'id_medicamento');
    }
}
