<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MedicamentoFarmacia;
use App\Models\Receta;
use App\Models\RecetaItem;
use App\Models\Dispensacion;
use App\Models\Auditoria;
use Carbon\Carbon;

class FarmaciaController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();

        $totalMedicamentos = MedicamentoFarmacia::count();
        $stockBajoCount = MedicamentoFarmacia::where('stock_actual', '<=', 10)->count();
        $recetasPendientes = Receta::where('estado', 'PENDIENTE')->count();
        $recetasDespachadasHoy = Receta::where('estado', 'DISPENSADA')
            ->whereDate('fecha_dispensacion', $today)
            ->count();

        $ultimasRecetas = Receta::with(['paciente.usuario', 'medico.usuario', 'medico.especialidad', 'items'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $productosBajoStock = MedicamentoFarmacia::where('stock_actual', '<=', 10)
            ->orderBy('stock_actual', 'asc')
            ->take(5)
            ->get();

        return view('farmacia.dashboard', compact(
            'totalMedicamentos', 'stockBajoCount', 'recetasPendientes',
            'recetasDespachadasHoy', 'ultimasRecetas', 'productosBajoStock'
        ));
    }

    public function inventarioIndex(Request $request)
    {
        $categoria = $request->get('categoria');
        $busqueda = $request->get('q');

        $query = MedicamentoFarmacia::query();

        if ($categoria && $categoria !== 'TODAS') {
            $query->where('categoria', $categoria);
        }

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre_comercial', 'LIKE', "%{$busqueda}%")
                  ->orWhere('nombre_generico', 'LIKE', "%{$busqueda}%")
                  ->orWhere('codigo_barras', 'LIKE', "%{$busqueda}%")
                  ->orWhere('lote', 'LIKE', "%{$busqueda}%");
            });
        }

        $medicamentos = $query->orderBy('nombre_comercial', 'asc')->get();

        $categorias = [
            'MEDICAMENTO' => 'Medicamentos / Comprimidos',
            'JARABE' => 'Jarabes / Suspensiones',
            'INYECTABLE' => 'Inyectables / Ampollas',
            'INSUMO_MEDICO' => 'Insumos Médicos (Alcohol, Algodón)',
            'MATERIAL_CURACION' => 'Material de Curación (Gasas, Vendas)',
            'OTRO' => 'Otros Productos'
        ];

        return view('farmacia.inventario', compact('medicamentos', 'categorias', 'categoria', 'busqueda'));
    }

    public function guardarProducto(Request $request)
    {
        $data = $request->validate([
            'codigo_barras' => 'nullable|string|max:50|unique:medicamentos_farmacia,codigo_barras',
            'nombre_comercial' => 'required|string|max:150',
            'nombre_generico' => 'nullable|string|max:150',
            'categoria' => 'required|string',
            'presentacion' => 'nullable|string|max:100',
            'concentracion' => 'nullable|string|max:100',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'precio_unitario' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'lote' => 'nullable|string|max:50',
            'ubicacion_estante' => 'nullable|string|max:50',
            'descripcion' => 'nullable|string',
        ]);

        $medicamento = MedicamentoFarmacia::create($data);

        Auditoria::create([
            'id_usuario' => Auth::user()->id_usuario,
            'accion' => 'CREAR_PRODUCTO_FARMACIA',
            'tabla_afectada' => 'medicamentos_farmacia',
            'registro_afectado' => $medicamento->id_medicamento,
            'detalle' => json_encode(['nombre' => $medicamento->nombre_comercial, 'stock' => $medicamento->stock_actual]),
            'fecha_hora' => now(),
            'ip_origen' => $request->ip(),
        ]);

        return redirect()->route('farmacia.inventario')->with('success', "Producto '{$medicamento->nombre_comercial}' agregado al inventario exitosamente.");
    }

    public function actualizarStock(Request $request, $id)
    {
        $medicamento = MedicamentoFarmacia::findOrFail($id);

        $request->validate([
            'stock_actual' => 'required|integer|min:0',
            'precio_unitario' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'lote' => 'nullable|string|max:50',
        ]);

        $medicamento->update([
            'stock_actual' => $request->stock_actual,
            'precio_unitario' => $request->precio_unitario,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'lote' => $request->lote ?? $medicamento->lote,
            'estado' => $request->stock_actual > 0 ? 'ACTIVO' : 'AGOTADO',
        ]);

        Auditoria::create([
            'id_usuario' => Auth::user()->id_usuario,
            'accion' => 'ACTUALIZAR_STOCK_FARMACIA',
            'tabla_afectada' => 'medicamentos_farmacia',
            'registro_afectado' => $medicamento->id_medicamento,
            'detalle' => json_encode(['nuevo_stock' => $request->stock_actual]),
            'fecha_hora' => now(),
            'ip_origen' => $request->ip(),
        ]);

        return redirect()->back()->with('success', "Stock del producto '{$medicamento->nombre_comercial}' actualizado a {$request->stock_actual} unidades.");
    }

    public function recetasIndex(Request $request)
    {
        $estado = $request->get('estado', 'PENDIENTE');
        $busqueda = $request->get('q');

        $query = Receta::with(['paciente.usuario', 'medico.usuario', 'medico.especialidad', 'items.medicamento', 'consulta']);

        if ($estado !== 'TODAS') {
            $query->where('estado', $estado);
        }

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('codigo_receta', 'LIKE', "%{$busqueda}%")
                  ->orWhereHas('paciente.usuario', function ($u) use ($busqueda) {
                      $u->where('nombre', 'LIKE', "%{$busqueda}%")
                        ->orWhere('apellido', 'LIKE', "%{$busqueda}%");
                  })
                  ->orWhereHas('paciente', function ($p) use ($busqueda) {
                      $p->where('ci', 'LIKE', "%{$busqueda}%");
                  });
            });
        }

        $recetas = $query->orderBy('created_at', 'desc')->get();

        return view('farmacia.recetas', compact('recetas', 'estado', 'busqueda'));
    }

    public function despacharRecetaForm($id_receta)
    {
        $receta = Receta::with([
            'paciente.usuario',
            'medico.usuario',
            'medico.especialidad',
            'items.medicamento',
            'consulta'
        ])->findOrFail($id_receta);

        $medicamentosDisponibles = MedicamentoFarmacia::where('estado', 'ACTIVO')
            ->where('stock_actual', '>', 0)
            ->orderBy('nombre_comercial', 'asc')
            ->get();

        return view('farmacia.despachar', compact('receta', 'medicamentosDisponibles'));
    }

    public function procesarDespacho(Request $request, $id_receta)
    {
        $receta = Receta::with('items')->findOrFail($id_receta);

        $montoTotal = 0;

        // Despachar ítems y descontar del inventario
        if ($request->has('items')) {
            foreach ($request->items as $itemId => $itemData) {
                $item = RecetaItem::find($itemId);
                if ($item) {
                    $cantDespachada = (int)($itemData['cantidad_despachada'] ?? 1);
                    $idMed = $itemData['id_medicamento'] ?? $item->id_medicamento;

                    if ($idMed) {
                        $med = MedicamentoFarmacia::find($idMed);
                        if ($med) {
                            // Descontar inventario
                            $nuevoStock = max(0, $med->stock_actual - $cantDespachada);
                            $med->update([
                                'stock_actual' => $nuevoStock,
                                'estado' => $nuevoStock == 0 ? 'AGOTADO' : 'ACTIVO'
                            ]);

                            $montoTotal += ($med->precio_unitario * $cantDespachada);
                            $item->id_medicamento = $idMed;
                        }
                    }

                    $item->update([
                        'cantidad_despachada' => $cantDespachada,
                        'estado_item' => $cantDespachada > 0 ? 'ENTREGADO' : 'NO_DISPONIBLE'
                    ]);
                }
            }
        }

        // Marcar receta como dispensada
        $receta->update([
            'estado' => 'DISPENSADA',
            'fecha_dispensacion' => now(),
            'observaciones_farmacia' => $request->observaciones_farmacia ?? 'Despachado en farmacia hospitalaria',
        ]);

        // Registrar dispensación
        Dispensacion::create([
            'id_receta' => $receta->id_receta,
            'id_usuario_farmacia' => Auth::user()->id_usuario,
            'fecha_hora' => now(),
            'monto_total' => $montoTotal,
            'observaciones' => $request->observaciones_farmacia,
        ]);

        Auditoria::create([
            'id_usuario' => Auth::user()->id_usuario,
            'accion' => 'DESPACHO_RECETA_FARMACIA',
            'tabla_afectada' => 'recetas',
            'registro_afectado' => $receta->id_receta,
            'detalle' => json_encode(['receta' => $receta->codigo_receta, 'monto_total' => $montoTotal]),
            'fecha_hora' => now(),
            'ip_origen' => $request->ip(),
        ]);

        return redirect()->route('farmacia.recetas')->with('success', "Receta Ref #{$receta->codigo_receta} despachada correctamente. El inventario fue actualizado en tiempo real.");
    }
}
