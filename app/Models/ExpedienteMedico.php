<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpedienteMedico extends Model
{
    use HasFactory;

    protected $table = 'expedientes_medicos';
    protected $primaryKey = 'id_expediente';

    protected $fillable = [
        'id_paciente',
        'tipo_sangre',
        'alergias',
        'alergias_medicamentosas',
        'enfermedades_cronicas',
        'antecedentes_personales',
        'antecedentes_familiares',
        'cirugias_previas',
        'hospitalizaciones',
        'medicamentos_actuales',
        'habitos',
        'observaciones_medicas'
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }
}
