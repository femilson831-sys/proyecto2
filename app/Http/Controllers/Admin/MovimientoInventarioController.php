<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimientoInventarioController extends Controller
{
    /**
     * Muestra el historial de movimientos con filtros.
     */
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'tipo_movimiento_id' => [
                'nullable',
                'integer',
                'exists:tipos_movimiento,id',
            ],
            'almacen_id' => ['nullable', 'integer', 'exists:almacenes,id'],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);

        $buscar = trim($filtros['buscar'] ?? '');

        $tiposMovimiento = DB::table('tipos_movimiento')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'naturaleza']);

        $almacenes = DB::table('almacenes')
            ->orderBy('nombre')
            ->get(['id', 'codigo', 'nombre']);

        $resumenDetalles = DB::table('movimiento_detalles')
            ->select('movimiento_id')
            ->selectRaw('COUNT(id) AS cantidad_lineas')
            ->groupBy('movimiento_id');

        $base = DB::table('movimientos_inventario as mi')
            ->join('tipos_movimiento as tm', 'tm.id', '=', 'mi.tipo_movimiento_id')
            ->leftJoinSub($resumenDetalles, 'resumen', function ($join) {
                $join->on('resumen.movimiento_id', '=', 'mi.id');
            });

        if (!empty($filtros['tipo_movimiento_id'])) {
            $base->where('mi.tipo_movimiento_id', $filtros['tipo_movimiento_id']);
        }

        if (!empty($filtros['almacen_id'])) {
            $almacenId = (int) $filtros['almacen_id'];

            $base->where(function ($query) use ($almacenId) {
                $query->where('mi.almacen_origen_id', $almacenId)
                    ->orWhere('mi.almacen_destino_id', $almacenId);
            });
        }

        if (!empty($filtros['desde'])) {
            $base->whereDate('mi.fecha_movimiento', '>=', $filtros['desde']);
        }

        if (!empty($filtros['hasta'])) {
            $base->whereDate('mi.fecha_movimiento', '<=', $filtros['hasta']);
        }

        if ($buscar !== '') {
            $base->where(function ($query) use ($buscar) {
                $query->where('mi.numero_movimiento', 'like', "%{$buscar}%")
                    ->orWhere('mi.motivo', 'like', "%{$buscar}%")
                    ->orWhere('tm.nombre', 'like', "%{$buscar}%")
                    ->orWhereExists(function ($subquery) use ($buscar) {
                        $subquery->selectRaw('1')
                            ->from('movimiento_detalles as md')
                            ->join('productos as p', 'p.id', '=', 'md.producto_id')
                            ->leftJoin('lotes as l', 'l.id', '=', 'md.lote_id')
                            ->whereColumn('md.movimiento_id', 'mi.id')
                            ->where(function ($productoQuery) use ($buscar) {
                                $productoQuery
                                    ->where('p.nombre', 'like', "%{$buscar}%")
                                    ->orWhere('p.codigo_interno', 'like', "%{$buscar}%")
                                    ->orWhere('l.codigo_lote', 'like', "%{$buscar}%");
                            });
                    });
            });
        }

        $totalMovimientos = (clone $base)->count('mi.id');

        $totalEntradas = (clone $base)
            ->where('tm.naturaleza', 'entrada')
            ->count('mi.id');

        $totalSalidas = (clone $base)
            ->where('tm.naturaleza', 'salida')
            ->count('mi.id');

        $totalInternos = (clone $base)
            ->where('tm.naturaleza', 'interno')
            ->count('mi.id');

        $movimientos = (clone $base)
            ->leftJoin('almacenes as origen', 'origen.id', '=', 'mi.almacen_origen_id')
            ->leftJoin('almacenes as destino', 'destino.id', '=', 'mi.almacen_destino_id')
            ->leftJoin('usuarios as us', 'us.id', '=', 'mi.usuario_id')
            ->select([
                'mi.id',
                'mi.numero_movimiento',
                'mi.fecha_movimiento',
                'mi.motivo',
                'mi.estado',
                'tm.nombre as tipo_nombre',
                'tm.naturaleza',
                'origen.nombre as almacen_origen',
                'destino.nombre as almacen_destino',
                'us.username as usuario',
            ])
            ->selectRaw('COALESCE(resumen.cantidad_lineas, 0) AS cantidad_lineas')
            ->orderByDesc('mi.fecha_movimiento')
            ->orderByDesc('mi.id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.movimientos.index', compact(
            'movimientos',
            'tiposMovimiento',
            'almacenes',
            'filtros',
            'buscar',
            'totalMovimientos',
            'totalEntradas',
            'totalSalidas',
            'totalInternos'
        ));
    }

    /**
     * Muestra el detalle de un movimiento.
     */
    public function show(int $movimiento)
    {
        $registro = DB::table('movimientos_inventario as mi')
            ->join('tipos_movimiento as tm', 'tm.id', '=', 'mi.tipo_movimiento_id')
            ->leftJoin('almacenes as origen', 'origen.id', '=', 'mi.almacen_origen_id')
            ->leftJoin('almacenes as destino', 'destino.id', '=', 'mi.almacen_destino_id')
            ->leftJoin('ubicaciones as ubicacion_origen', 'ubicacion_origen.id', '=', 'mi.ubicacion_origen_id')
            ->leftJoin('ubicaciones as ubicacion_destino', 'ubicacion_destino.id', '=', 'mi.ubicacion_destino_id')
            ->leftJoin('usuarios as us', 'us.id', '=', 'mi.usuario_id')
            ->where('mi.id', $movimiento)
            ->select([
                'mi.id',
                'mi.numero_movimiento',
                'mi.fecha_movimiento',
                'mi.motivo',
                'mi.estado',
                'tm.nombre as tipo_nombre',
                'tm.naturaleza',
                'origen.nombre as almacen_origen',
                'destino.nombre as almacen_destino',
                'ubicacion_origen.codigo as ubicacion_origen',
                'ubicacion_destino.codigo as ubicacion_destino',
                'us.username as usuario',
            ])
            ->first();

        abort_unless($registro, 404);

        $detalles = DB::table('movimiento_detalles as md')
            ->join('productos as p', 'p.id', '=', 'md.producto_id')
            ->leftJoin('lotes as l', 'l.id', '=', 'md.lote_id')
            ->leftJoin('unidades_medida as um', 'um.id', '=', 'p.unidad_medida_id')
            ->where('md.movimiento_id', $movimiento)
            ->select([
                'md.id',
                'md.cantidad',
                'md.costo_unitario',
                'p.codigo_interno',
                'p.nombre as producto',
                'um.abreviatura as unidad',
                'l.codigo_lote',
                'l.fecha_vencimiento',
            ])
            ->orderBy('p.nombre')
            ->orderBy('l.codigo_lote')
            ->get();

        return view('admin.movimientos.show', compact(
            'registro',
            'detalles'
        ));
    }
}