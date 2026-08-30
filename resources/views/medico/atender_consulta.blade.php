@extends('layouts.app')

@section('title', 'Consulta Médica ECE - Hospital Plan 3000')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div class="card-title" style="margin: 0;">
            <i class="fa-solid fa-file-waveform text-primary"></i>
            <span>Expediente Clínico Electrónico (ECE) — Consulta de {{ $cita->medico->especialidad->nombre }}</span>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <a href="{{ route('medico.paciente.historial', $cita->id_paciente) }}" target="_blank" class="btn btn-sm btn-info" style="color: #fff;">
                <i class="fa-solid fa-clock-rotate-left"></i> Ver Historial Clínico Completo
            </a>
            <span class="status-badge status-EN_CONSULTA">En Consulta Activa</span>
        </div>
    </div>

    <!-- Resumen del Paciente -->
    <div style="background-color: var(--primary-light); padding: 1.25rem; border-radius: 10px; margin-bottom: 1.5rem; border-left: 4px solid var(--primary);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
            <div>
                <strong>Paciente:</strong> {{ $cita->paciente->usuario->nombre }} {{ $cita->paciente->usuario->apellido }}<br>
                <strong>CI:</strong> {{ $cita->paciente->ci }} | <strong>F. Nac:</strong> {{ $cita->paciente->fecha_nacimiento }}
            </div>
            <div>
                <strong>Sexo:</strong> {{ $cita->paciente->sexo ?? $cita->paciente->genero ?? 'N/A' }} | <strong>Tipo Sangre:</strong> <span style="color: red; font-weight: bold;">{{ $expediente->grupo_sanguineo ?? $expediente->tipo_sangre ?? 'ORH+' }}</span><br>
                <strong>Alergias:</strong> <span style="color: #b91c1c; font-weight: 600;">{{ $expediente->alergias ?? 'Ninguna conocida' }}</span>
            </div>
            <div>
                <strong>Antecedentes:</strong> {{ $expediente->antecedentes_patologicos ?? 'Ninguno reportado' }}<br>
                <strong>Medicamentos Previos:</strong> {{ $expediente->medicamentos_actuales ?? 'Ninguno' }}
            </div>
        </div>
    </div>

    <form action="{{ route('medico.cita.guardar_consulta', $cita->id_cita) }}" method="POST">
        @csrf

        <!-- 1. Evaluación Médica General -->
        <h4 style="color: var(--primary); margin-bottom: 1rem; border-bottom: 2px solid var(--primary-light); padding-bottom: 0.4rem;">
            <i class="fa-solid fa-clipboard-user"></i> 1. Evaluación General y Anamnesis
        </h4>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Motivo de Consulta *</label>
                <textarea name="motivo_consulta" class="form-control" rows="2" required placeholder="Describa el motivo de la consulta expresado por el paciente...">{{ $cita->observaciones }}</textarea>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Diagnóstico Principal (CIE-10) *</label>
                <input type="text" name="diagnostico_principal" class="form-control" required placeholder="Ej: Hipertensión Arterial Esencial (I10)">
            </div>

            <div class="form-group">
                <label class="form-label">Diagnóstico Secundario</label>
                <input type="text" name="diagnostico_secundario" class="form-control" placeholder="Diagnóstico secundario si aplica">
            </div>

            <div class="form-group">
                <label class="form-label">Plan de Tratamiento</label>
                <textarea name="plan_tratamiento" class="form-control" rows="2" placeholder="Plan farmacológico o no farmacológico"></textarea>
            </div>
        </div>

        <!-- 2. Formulario Especializado según Disciplina Médica -->
        <h4 style="color: var(--primary); margin: 1.5rem 0 1rem 0; border-bottom: 2px solid var(--primary-light); padding-bottom: 0.4rem;">
            <i class="fa-solid fa-stethoscope"></i> 2. Evaluación Especializada ({{ $cita->medico->especialidad->nombre }})
        </h4>

        @if(str_contains($especialidadNombre, 'cardio'))
            <!-- CARDIOLOGÍA -->
            <div style="background: #f8fafc; padding: 1.25rem; border-radius: 10px; border: 1px solid #cbd5e1; margin-bottom: 1.5rem;">
                <h5 style="color: #0284c7; margin-bottom: 0.75rem;"><i class="fa-solid fa-heart-pulse"></i> Signos Vitales & Exploración Cardiovascular</h5>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Presión Arterial (PA)</label>
                        <input type="text" name="presion_arterial" class="form-control" placeholder="Ej: 120/80 mmHg">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Frec. Cardíaca (FC bpm)</label>
                        <input type="number" name="frecuencia_cardiaca" class="form-control" placeholder="75">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sat. Oxígeno (% SpO2)</label>
                        <input type="number" step="0.1" name="saturacion_oxigeno" class="form-control" placeholder="98">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Peso (kg)</label>
                        <input type="number" step="0.1" id="peso" name="peso" class="form-control" placeholder="70.5" oninput="calcularIMC()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Talla (cm)</label>
                        <input type="number" step="1" id="talla" name="talla" class="form-control" placeholder="170" oninput="calcularIMC()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">IMC Calculado</label>
                        <input type="text" id="imc" name="imc" class="form-control" readonly style="background: #e2e8f0;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-top: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Ruidos Cardíacos</label>
                        <input type="text" name="ruidos_cardiacos" class="form-control" placeholder="R1, R2 normofonéticos">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ritmo</label>
                        <input type="text" name="ritmo" class="form-control" placeholder="Regular, Sinusal">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Soplos / Edemas</label>
                        <input type="text" name="soplos" class="form-control" placeholder="Ausencia de soplos">
                    </div>
                </div>
            </div>

        @elseif(str_contains($especialidadNombre, 'pedia'))
            <!-- PEDIATRÍA -->
            <div style="background: #fdf4ff; padding: 1.25rem; border-radius: 10px; border: 1px solid #f5d0fe; margin-bottom: 1.5rem;">
                <h5 style="color: #c026d3; margin-bottom: 0.75rem;"><i class="fa-solid fa-child"></i> Control de Crecimiento & Desarrollo Pediátrico</h5>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Nombre del Tutor / Padre</label>
                        <input type="text" name="responsable_nombre" class="form-control" placeholder="Nombre completo del responsable">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Parentesco</label>
                        <input type="text" name="responsable_relacion" class="form-control" placeholder="Madre, Padre, Tutor">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Peso (kg)</label>
                        <input type="number" step="0.01" name="peso" class="form-control" placeholder="12.50">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Talla (cm)</label>
                        <input type="number" step="0.5" name="talla" class="form-control" placeholder="85">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Perímetro Cefálico (cm)</label>
                        <input type="number" step="0.1" name="perimetro_cefalico" class="form-control" placeholder="46.5">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Percentil Crecimiento</label>
                        <input type="text" name="percentil_peso" class="form-control" placeholder="P50 / P75">
                    </div>
                </div>
            </div>

        @elseif(str_contains($especialidadNombre, 'gineco'))
            <!-- GINECOLOGÍA -->
            <div style="background: #fff1f2; padding: 1.25rem; border-radius: 10px; border: 1px solid #fecdd3; margin-bottom: 1.5rem;">
                <h5 style="color: #e11d48; margin-bottom: 0.75rem;"><i class="fa-solid fa-venus"></i> Historia Gineco-Obstétrica</h5>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Fecha Última Menstruación (FUM)</label>
                        <input type="date" name="fum" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ciclo Menstrual</label>
                        <input type="text" name="ciclo_menstrual" class="form-control" placeholder="28/5 regular">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gestas</label>
                        <input type="number" name="gestas" class="form-control" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Partos</label>
                        <input type="number" name="partos" class="form-control" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cesáreas</label>
                        <input type="number" name="cesareas" class="form-control" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Abortos</label>
                        <input type="number" name="abortos" class="form-control" value="0">
                    </div>
                </div>
            </div>

        @elseif(str_contains($especialidadNombre, 'trauma'))
            <!-- TRAUMATOLOGÍA -->
            <div style="background: #fff7ed; padding: 1.25rem; border-radius: 10px; border: 1px solid #ffedd5; margin-bottom: 1.5rem;">
                <h5 style="color: #c2410c; margin-bottom: 0.75rem;"><i class="fa-solid fa-bone"></i> Evaluación Musculoesquelética</h5>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Zona Afectada</label>
                        <input type="text" name="zona_afectada" class="form-control" placeholder="Rodilla derecha, Tobillo izq.">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mecanismo de Lesión</label>
                        <input type="text" name="mecanismo_lesion" class="form-control" placeholder="Caída de altura, Esguince deportivo">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Intensidad Dolor (1 - 10)</label>
                        <input type="number" min="1" max="10" name="intensidad_dolor" class="form-control" value="5">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fuerza / Movilidad</label>
                        <input type="text" name="movilidad" class="form-control" placeholder="Movilidad conservada / limitada">
                    </div>
                </div>
            </div>

        @else
            <!-- MEDICINA GENERAL -->
            <div style="background: #f8fafc; padding: 1.25rem; border-radius: 10px; border: 1px solid #cbd5e1; margin-bottom: 1.5rem;">
                <h5 style="color: #0f4c81; margin-bottom: 0.75rem;"><i class="fa-solid fa-notes-medical"></i> Exploración Física por Sistemas</h5>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Presión Arterial</label>
                        <input type="text" name="presion_arterial" class="form-control" placeholder="120/80">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Frecuencia Cardíaca</label>
                        <input type="number" name="frecuencia_cardiaca" class="form-control" placeholder="72">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Temperatura (°C)</label>
                        <input type="number" step="0.1" name="temperatura" class="form-control" placeholder="36.5">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Exploración Cardiopulmonar</label>
                        <input type="text" name="exploracion_cardiopulmonar" class="form-control" placeholder="Murmullo vesicular conservado">
                    </div>
                </div>
            </div>
        @endif

        <!-- 3. Prescripción de Medicamentos & Receta Digital -->
        <h4 style="color: var(--primary); margin: 1.5rem 0 1rem 0; border-bottom: 2px solid var(--primary-light); padding-bottom: 0.4rem; display: flex; justify-content: space-between; align-items: center;">
            <span><i class="fa-solid fa-prescription-bottle-medical"></i> 3. Receta Médica Digital (Enlace con Farmacia)</span>
            <span style="font-size: 0.8rem; color: #166534; font-weight: normal;"><i class="fa-solid fa-circle-check"></i> Stock de Farmacia Integrado</span>
        </h4>

        <datalist id="lista-medicamentos-farmacia">
            @foreach($medicamentosCatalogo as $medCat)
                <option value="{{ $medCat->nombre_comercial }} {{ $medCat->concentracion }} ({{ $medCat->presentacion }}) - Stock: {{ $medCat->stock_actual }}"></option>
            @endforeach
        </datalist>

        <div id="medicamentos-container">
            <div class="med-item" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                <input type="text" name="medicamentos_nombre[]" list="lista-medicamentos-farmacia" class="form-control" placeholder="Buscar medicamento o insumo en Farmacia...">
                <input type="text" name="medicamentos_dosis[]" class="form-control" placeholder="Dosis (Ej: 1 comp / 5ml)">
                <input type="text" name="medicamentos_frecuencia[]" class="form-control" placeholder="Frecuencia (Ej: C/8 hrs)">
                <input type="text" name="medicamentos_duracion[]" class="form-control" placeholder="Duración (Ej: 5 días)">
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-secondary" onclick="agregarMedicamento()" style="margin-bottom: 1.5rem;">
            <i class="fa-solid fa-plus"></i> Agregar Otro Medicamento / Insumo
        </button>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <div class="form-group">
                <label class="form-label">Indicaciones de Uso para el Paciente</label>
                <textarea name="indicaciones" class="form-control" rows="2" placeholder="Indicaciones dietéticas, reposo o cuidados..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Fecha para Próximo Control</label>
                <input type="date" name="proximo_control" class="form-control">
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; border-top: 1px solid #cbd5e1; padding-top: 1.25rem;">
            <a href="{{ route('medico.agenda') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Regresar a Agenda
            </a>
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fa-solid fa-floppy-disk"></i> Finalizar Consulta y Enviar Receta a Farmacia
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
function calcularIMC() {
    let peso = parseFloat(document.getElementById('peso').value);
    let tallaCm = parseFloat(document.getElementById('talla').value);

    if (peso > 0 && tallaCm > 0) {
        let tallaM = tallaCm / 100;
        let imc = (peso / (tallaM * tallaM)).toFixed(2);
        document.getElementById('imc').value = imc;
    }
}

function agregarMedicamento() {
    let container = document.getElementById('medicamentos-container');
    let div = document.createElement('div');
    div.className = 'med-item';
    div.style = 'display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;';
    div.innerHTML = `
        <input type="text" name="medicamentos_nombre[]" list="lista-medicamentos-farmacia" class="form-control" placeholder="Buscar medicamento o insumo...">
        <input type="text" name="medicamentos_dosis[]" class="form-control" placeholder="Dosis">
        <input type="text" name="medicamentos_frecuencia[]" class="form-control" placeholder="Frecuencia">
        <input type="text" name="medicamentos_duracion[]" class="form-control" placeholder="Duración">
    `;
    container.appendChild(div);
}
</script>
@endsection
