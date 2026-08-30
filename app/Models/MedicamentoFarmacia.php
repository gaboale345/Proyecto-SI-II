<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicamentoFarmacia extends Model
{
    use HasFactory;

    protected $table = 'medicamentos_farmacia';
    protected $primaryKey = 'id_medicamento';

    protected $fillable = [
        'codigo_barras',
        'nombre_comercial',
        'nombre_generico',
        'categoria',
        'presentacion',
        'concentracion',
        'stock_actual',
        'stock_minimo',
        'precio_unitario',
        'fecha_vencimiento',
        'lote',
        'ubicacion_estante',
        'estado',
        'descripcion',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'precio_unitario' => 'decimal:2',
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
    ];

    public function itemsReceta()
    {
        return $this->hasMany(RecetaItem::class, 'id_medicamento', 'id_medicamento');
    }

    public function isBajoStock(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }

    public function isAgotado(): bool
    {
        return $this->stock_actual <= 0;
    }
}
