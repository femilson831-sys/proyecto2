<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AlmacenUbicacionController extends Controller
{
    public function index(Request $request, int $almacenId)
    {
        $almacen = DB::table('almacenes')
            ->where('id', $almacenId)
            ->first();

        abort_unless($almacen, 404, 'El almacén no existe.');

        $ubicaciones = DB::table('ubicaciones')
            ->where('almacen_id', $almacenId)
            ->where('estado', 'activa')
            ->orderBy('codigo')
            ->get();

        foreach ($ubicaciones as $ubicacion) {
            $ubicacion->ocupado = (float) DB::table('inventario_ubicaciones')
                ->where('ubicacion_id', $ubicacion->id)
                ->sum('porcentaje_ocupacion');

            $ubicacion->asignaciones = DB::table('inventario_ubicaciones as iu')
                ->join('inventarios as i', 'i.id', '=', 'iu.inventario_id')
                ->join('productos as p', 'p.id', '=', 'i.producto_id')
                ->leftJoin('lotes as l', 'l.id', '=', 'iu.lote_id')
                ->leftJoin('unidades_medida as um', 'um.id', '=', 'p.unidad_medida_id')
                ->where('iu.ubicacion_id', $ubicacion->id)
                ->select([
                    'iu.id as asignacion_id',
                    'iu.inventario_id',
                    'iu.lote_id',
                    'iu.cantidad',
                    'iu.porcentaje_ocupacion',
                    'p.codigo_interno',
                    'p.nombre as producto',
                    'um.abreviatura as unidad',
                    'l.codigo_lote',
                    'l.fecha_vencimiento',
                ])
                ->orderBy('p.nombre')
                ->get();

            $ubicacion->asignaciones_count = $ubicacion->asignaciones->count();
        }

        $ubicacionSeleccionada = null;
        $ubicacionId = $request->query('ubicacion_id');

        if ($ubicacionId) {
            $ubicacionSeleccionada = $ubicaciones->firstWhere(
                'id',
                (int) $ubicacionId
            );
        }

        $lotesDisponibles = DB::table('lotes as l')
            ->join('recepcion_detalles as rd', 'rd.id', '=', 'l.recepcion_detalle_id')
            ->join('recepciones as r', 'r.id', '=', 'rd.recepcion_id')
            ->join('inventarios as i', function ($join) {
                $join->on('i.producto_id', '=', 'l.producto_id')
                    ->on('i.almacen_id', '=', 'r.almacen_id');
            })
            ->join('productos as p', 'p.id', '=', 'l.producto_id')
            ->leftJoin('unidades_medida as um', 'um.id', '=', 'p.unidad_medida_id')
            ->where('r.almacen_id', $almacenId)
            ->whereColumn('rd.producto_id', 'l.producto_id')
            ->whereRaw(
                'l.cantidad_actual > COALESCE(
                    (
                        SELECT SUM(iu2.cantidad)
                        FROM inventario_ubicaciones iu2
                        WHERE iu2.lote_id = l.id
                    ),
                    0
                )'
            )
            ->select([
                'l.id as lote_id',
                'l.codigo_lote',
                'l.producto_id',
                'l.cantidad_actual',
                'l.fecha_vencimiento',
                'i.id as inventario_id',
                'p.nombre as producto',
                'p.codigo_interno',
                'um.abreviatura as unidad',
            ])
            ->selectRaw(
                'GREATEST(
                    l.cantidad_actual - COALESCE(
                        (
                            SELECT SUM(iu2.cantidad)
                            FROM inventario_ubicaciones iu2
                            WHERE iu2.lote_id = l.id
                        ),
                        0
                    ),
                    0
                ) AS cantidad_disponible'
            )
            ->orderBy('p.nombre')
            ->orderBy('l.fecha_vencimiento')
            ->get();

        return view(
            'admin.almacenes.ubicaciones.index',
            compact(
                'almacen',
                'ubicaciones',
                'ubicacionSeleccionada',
                'lotesDisponibles'
            )
        );
    }

    public function store(Request $request, int $almacenId)
    {
        $almacen = DB::table('almacenes')
            ->where('id', $almacenId)
            ->first();

        abort_unless($almacen, 404, 'El almacén no existe.');

        $datos = $request->validate([
            'codigo' => ['required', 'string', 'max:40'],
            'nombre' => ['nullable', 'string', 'max:120'],
        ]);

        $codigoVisible = strtoupper(trim($datos['codigo']));
        $codigoCompleto = strtoupper($almacen->codigo . '-' . $codigoVisible);

        if (DB::table('ubicaciones')->where('codigo', $codigoCompleto)->exists()) {
            throw ValidationException::withMessages([
                'codigo' => 'Esa ubicación ya está registrada en este almacén.',
            ]);
        }

        $ubicacionId = DB::table('ubicaciones')->insertGetId([
            'almacen_id' => $almacenId,
            'nivel_id' => null,
            'codigo' => $codigoCompleto,
            'nombre' => trim($datos['nombre'] ?? '')
                ?: 'Ubicación ' . $codigoVisible,
            'tipo' => 'almacenamiento',
            'capacidad_m3' => null,
            'estado' => 'activa',
        ]);

        return redirect()
            ->route('admin.almacenes.ubicaciones.index', [
                'almacenId' => $almacenId,
                'ubicacion_id' => $ubicacionId,
            ])
            ->with('success', "Ubicación {$codigoVisible} creada.");
    }

    public function asignarLote(
        Request $request,
        int $almacenId,
        int $ubicacionId
    ) {
        $datos = $request->validate([
            'inventario_id' => [
                'required',
                'integer',
                'exists:inventarios,id',
            ],
            'lote_id' => [
                'required',
                'integer',
                'exists:lotes,id',
            ],
            'cantidad_modo' => [
                'required',
                'in:todo,personalizada',
            ],
            'cantidad' => [
                'nullable',
                'numeric',
                'gt:0',
                'max:999999999.999',
            ],
            'porcentaje_ocupacion' => [
                'required',
                'numeric',
                'gt:0',
                'max:100',
            ],
        ]);

        if (
            $datos['cantidad_modo'] === 'personalizada'
            && empty($datos['cantidad'])
        ) {
            throw ValidationException::withMessages([
                'cantidad' => 'Escribe la cantidad que irá en esta ubicación.',
            ]);
        }

        DB::transaction(function () use ($datos, $almacenId, $ubicacionId) {
            $ubicacion = DB::table('ubicaciones')
                ->where('id', $ubicacionId)
                ->where('almacen_id', $almacenId)
                ->where('estado', 'activa')
                ->lockForUpdate()
                ->first();

            abort_unless(
                $ubicacion,
                404,
                'La ubicación no pertenece a este almacén.'
            );

            $inventario = DB::table('inventarios')
                ->where('id', $datos['inventario_id'])
                ->where('almacen_id', $almacenId)
                ->lockForUpdate()
                ->first();

            if (!$inventario) {
                throw ValidationException::withMessages([
                    'inventario_id' => 'El producto no pertenece a este almacén.',
                ]);
            }

            $lote = DB::table('lotes as l')
                ->join('recepcion_detalles as rd', 'rd.id', '=', 'l.recepcion_detalle_id')
                ->join('recepciones as r', 'r.id', '=', 'rd.recepcion_id')
                ->where('l.id', $datos['lote_id'])
                ->where('l.producto_id', $inventario->producto_id)
                ->whereColumn('rd.producto_id', 'l.producto_id')
                ->where('r.almacen_id', $almacenId)
                ->select(['l.id', 'l.cantidad_actual'])
                ->lockForUpdate()
                ->first();

            if (!$lote) {
                throw ValidationException::withMessages([
                    'lote_id' => 'El lote no corresponde al producto o almacén seleccionado.',
                ]);
            }

            $porcentajeActual = (float) DB::table('inventario_ubicaciones')
                ->where('ubicacion_id', $ubicacionId)
                ->sum('porcentaje_ocupacion');

            $porcentajeNuevo = (float) $datos['porcentaje_ocupacion'];

            if (($porcentajeActual + $porcentajeNuevo) > 100) {
                throw ValidationException::withMessages([
                    'porcentaje_ocupacion' =>
                        'La ubicación ya está ocupada al '
                        . number_format($porcentajeActual, 2)
                        . '%. No se puede superar el 100%.',
                ]);
            }

            $cantidadYaUbicada = (float) DB::table('inventario_ubicaciones')
                ->where('lote_id', $lote->id)
                ->sum('cantidad');

            $cantidadPendiente = max(
                0,
                (float) $lote->cantidad_actual - $cantidadYaUbicada
            );

            $cantidadARegistrar = $datos['cantidad_modo'] === 'todo'
                ? $cantidadPendiente
                : (float) $datos['cantidad'];

            if ($cantidadARegistrar <= 0) {
                throw ValidationException::withMessages([
                    'cantidad' => 'Este lote no tiene unidades pendientes de ubicar.',
                ]);
            }

            if ($cantidadARegistrar > $cantidadPendiente) {
                throw ValidationException::withMessages([
                    'cantidad' =>
                        'La cantidad supera lo pendiente por ubicar: '
                        . number_format($cantidadPendiente, 3, ',', '.')
                        . '.',
                ]);
            }

            DB::table('inventario_ubicaciones')->insert([
                'inventario_id' => $inventario->id,
                'ubicacion_id' => $ubicacionId,
                'lote_id' => $lote->id,
                'cantidad' => $cantidadARegistrar,
                'porcentaje_ocupacion' => $porcentajeNuevo,
            ]);
        });

        $loteResumen = DB::table('lotes as l')
            ->join('productos as p', 'p.id', '=', 'l.producto_id')
            ->join('recepcion_detalles as rd', 'rd.id', '=', 'l.recepcion_detalle_id')
            ->join('recepciones as r', 'r.id', '=', 'rd.recepcion_id')
            ->join('almacenes as a', 'a.id', '=', 'r.almacen_id')
            ->where('l.id', $datos['lote_id'])
            ->select([
                'l.id',
                'l.codigo_lote',
                'l.producto_id',
                'l.cantidad_actual',
                'p.nombre as producto',
                'a.nombre as almacen',
            ])
            ->first();

        if (!$loteResumen) {
            return redirect()
                ->route('admin.almacenes.ubicaciones.index', [
                    'almacenId' => $almacenId,
                    'ubicacion_id' => $ubicacionId,
                ])
                ->with('warning', 'La asignación se guardó, pero no se pudo consultar el resumen del lote.');
        }

        $cantidadUbicada = (float) DB::table('inventario_ubicaciones as iu')
            ->join('ubicaciones as u', 'u.id', '=', 'iu.ubicacion_id')
            ->where('iu.lote_id', $datos['lote_id'])
            ->where('u.almacen_id', $almacenId)
            ->sum('iu.cantidad');

        $cantidadPendiente = max(
            0,
            round((float) $loteResumen->cantidad_actual - $cantidadUbicada, 3)
        );

        $tipoAlerta = DB::table('tipos_alerta')
            ->where('nombre', 'Unidades sin ubicación')
            ->first(['id']);

        if (!$tipoAlerta) {
            $tipoAlertaId = DB::table('tipos_alerta')->insertGetId([
                'nombre' => 'Unidades sin ubicación',
                'nivel' => 'media',
                'descripcion' => 'Lotes con existencias que aún no fueron asignadas a una ubicación.',
            ]);
        } else {
            $tipoAlertaId = (int) $tipoAlerta->id;
        }

        $tituloAlerta = 'Lote '
            . $loteResumen->codigo_lote
            . ' con unidades sin ubicación';

        $alertaPendiente = DB::table('alertas')
            ->where('tipo_alerta_id', $tipoAlertaId)
            ->where('producto_id', $loteResumen->producto_id)
            ->where('almacen_id', $almacenId)
            ->where('titulo', $tituloAlerta)
            ->where('estado', 'pendiente')
            ->first(['id']);

        if ($cantidadPendiente > 0) {
            $cantidadFormateada = rtrim(
                rtrim(number_format($cantidadPendiente, 3, ',', '.'), '0'),
                ','
            );

            $mensajeAlerta = $loteResumen->producto
                . ' — lote '
                . $loteResumen->codigo_lote
                . ' del almacén '
                . $loteResumen->almacen
                . '. Quedan '
                . $cantidadFormateada
                . ' unidades sin asignar a una ubicación.';

            $datosAlerta = [
                'ubicacion_id' => $ubicacionId,
                'titulo' => $tituloAlerta,
                'mensaje' => $mensajeAlerta,
                'valor_actual' => $cantidadPendiente,
                'valor_referencia' => (float) $loteResumen->cantidad_actual,
                'fecha_generacion' => now(),
                'fecha_resolucion' => null,
                'estado' => 'pendiente',
            ];

            if ($alertaPendiente) {
                DB::table('alertas')
                    ->where('id', $alertaPendiente->id)
                    ->update($datosAlerta);
            } else {
                DB::table('alertas')->insert($datosAlerta + [
                    'tipo_alerta_id' => $tipoAlertaId,
                    'producto_id' => $loteResumen->producto_id,
                    'almacen_id' => $almacenId,
                    'tarea_id' => null,
                ]);
            }

            return redirect()
                ->route('admin.almacenes.ubicaciones.index', [
                    'almacenId' => $almacenId,
                    'ubicacion_id' => $ubicacionId,
                ])
                ->with(
                    'warning',
                    'Asignación guardada. Quedan '
                    . $cantidadFormateada
                    . ' unidades del lote '
                    . $loteResumen->codigo_lote
                    . ' sin ubicación.'
                );
        }

        DB::table('alertas')
            ->where('tipo_alerta_id', $tipoAlertaId)
            ->where('producto_id', $loteResumen->producto_id)
            ->where('almacen_id', $almacenId)
            ->where('titulo', $tituloAlerta)
            ->where('estado', 'pendiente')
            ->update([
                'estado' => 'resuelta',
                'fecha_resolucion' => now(),
            ]);

        return redirect()
            ->route('admin.almacenes.ubicaciones.index', [
                'almacenId' => $almacenId,
                'ubicacion_id' => $ubicacionId,
            ])
            ->with('success', 'Asignación guardada. Todo el lote tiene una ubicación asignada.');
    }

    /**
     * Traslada un producto/lote de una ubicación a otra
     * dentro del mismo almacén.
     */
    public function trasladar(Request $request, int $almacenId)
    {
        $datos = $request->validate([
            'asignacion_id' => [
                'required',
                'integer',
                'exists:inventario_ubicaciones,id',
            ],
            'ubicacion_destino_id' => [
                'required',
                'integer',
                'exists:ubicaciones,id',
            ],
            'cantidad' => [
                'required',
                'numeric',
                'gt:0',
                'max:999999999.999',
            ],
            'porcentaje_destino' => [
                'nullable',
                'numeric',
                'gt:0',
                'max:100',
            ],
            'motivo' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $asignacionOrigenInicial = DB::table('inventario_ubicaciones')
            ->where('id', $datos['asignacion_id'])
            ->first(['ubicacion_id']);

        if (!$asignacionOrigenInicial) {
            throw ValidationException::withMessages([
                'asignacion_id' => 'La asignación de origen no existe.',
            ]);
        }

        $ubicacionOrigenId = (int) $asignacionOrigenInicial->ubicacion_id;
        $ubicacionDestinoId = (int) $datos['ubicacion_destino_id'];
        $cantidadTrasladar = (float) $datos['cantidad'];

        if ($ubicacionOrigenId === $ubicacionDestinoId) {
            throw ValidationException::withMessages([
                'ubicacion_destino_id' => 'Selecciona una ubicación distinta a la de origen.',
            ]);
        }

        DB::transaction(function () use (
            $datos,
            $almacenId,
            $ubicacionOrigenId,
            $ubicacionDestinoId,
            $cantidadTrasladar
        ) {
            $ubicaciones = DB::table('ubicaciones')
                ->where('almacen_id', $almacenId)
                ->whereIn('id', [$ubicacionOrigenId, $ubicacionDestinoId])
                ->where('estado', 'activa')
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if (
                !$ubicaciones->has($ubicacionOrigenId)
                || !$ubicaciones->has($ubicacionDestinoId)
            ) {
                throw ValidationException::withMessages([
                    'ubicacion_destino_id' =>
                        'El origen y el destino deben ser ubicaciones activas del mismo almacén.',
                ]);
            }

            $origen = $ubicaciones->get($ubicacionOrigenId);
            $destino = $ubicaciones->get($ubicacionDestinoId);

            $asignacionOrigen = DB::table('inventario_ubicaciones')
                ->where('id', $datos['asignacion_id'])
                ->where('ubicacion_id', $ubicacionOrigenId)
                ->lockForUpdate()
                ->first();

            if (!$asignacionOrigen) {
                throw ValidationException::withMessages([
                    'asignacion_id' => 'La asignación de origen ya no existe.',
                ]);
            }

            $inventario = DB::table('inventarios')
                ->where('id', $asignacionOrigen->inventario_id)
                ->where('almacen_id', $almacenId)
                ->lockForUpdate()
                ->first();

            if (!$inventario) {
                throw ValidationException::withMessages([
                    'asignacion_id' => 'No se encontró el inventario de esta asignación.',
                ]);
            }

            $cantidadOrigen = (float) $asignacionOrigen->cantidad;

            if ($cantidadTrasladar > $cantidadOrigen) {
                throw ValidationException::withMessages([
                    'cantidad' =>
                        'La cantidad supera lo disponible en la ubicación de origen: '
                        . number_format($cantidadOrigen, 3, ',', '.')
                        . '.',
                ]);
            }

            $cantidadOrigenRestante = round(
                $cantidadOrigen - $cantidadTrasladar,
                3
            );

            $porcentajeOrigen = (float) (
                $asignacionOrigen->porcentaje_ocupacion ?? 0
            );

            if ($cantidadOrigenRestante <= 0.0001) {
                $porcentajeTrasladadoCalculado = $porcentajeOrigen;
                $porcentajeOrigenRestante = 0;
                $cantidadOrigenRestante = 0;
            } else {
                $proporcionTrasladada = $cantidadTrasladar / $cantidadOrigen;

                $porcentajeTrasladadoCalculado = round(
                    $porcentajeOrigen * $proporcionTrasladada,
                    3
                );

                $porcentajeOrigenRestante = max(
                    0,
                    round(
                        $porcentajeOrigen - $porcentajeTrasladadoCalculado,
                        3
                    )
                );
            }

            $porcentajeDestino = isset($datos['porcentaje_destino'])
                ? (float) $datos['porcentaje_destino']
                : $porcentajeTrasladadoCalculado;

            $ocupacionDestinoActual = (float) DB::table('inventario_ubicaciones')
                ->where('ubicacion_id', $ubicacionDestinoId)
                ->sum('porcentaje_ocupacion');

            if ($ocupacionDestinoActual + $porcentajeDestino > 100.001) {
                throw ValidationException::withMessages([
                    'porcentaje_destino' =>
                        'La ubicación de destino no tiene espacio suficiente. '
                        . 'Está ocupada al '
                        . number_format($ocupacionDestinoActual, 2, ',', '.')
                        . '%.',
                ]);
            }

            $consultaDestino = DB::table('inventario_ubicaciones')
                ->where('inventario_id', $asignacionOrigen->inventario_id)
                ->where('ubicacion_id', $ubicacionDestinoId);

            if ($asignacionOrigen->lote_id === null) {
                $consultaDestino->whereNull('lote_id');
            } else {
                $consultaDestino->where('lote_id', $asignacionOrigen->lote_id);
            }

            $asignacionDestino = $consultaDestino
                ->lockForUpdate()
                ->first();

            if ($cantidadOrigenRestante <= 0.0001) {
                DB::table('inventario_ubicaciones')
                    ->where('id', $asignacionOrigen->id)
                    ->delete();
            } else {
                DB::table('inventario_ubicaciones')
                    ->where('id', $asignacionOrigen->id)
                    ->update([
                        'cantidad' => $cantidadOrigenRestante,
                        'porcentaje_ocupacion' => $porcentajeOrigenRestante,
                    ]);
            }

            if ($asignacionDestino) {
                DB::table('inventario_ubicaciones')
                    ->where('id', $asignacionDestino->id)
                    ->update([
                        'cantidad' => round(
                            (float) $asignacionDestino->cantidad
                            + $cantidadTrasladar,
                            3
                        ),
                        'porcentaje_ocupacion' => round(
                            (float) ($asignacionDestino->porcentaje_ocupacion ?? 0)
                            + $porcentajeDestino,
                            3
                        ),
                    ]);
            } else {
                DB::table('inventario_ubicaciones')->insert([
                    'inventario_id' => $asignacionOrigen->inventario_id,
                    'ubicacion_id' => $ubicacionDestinoId,
                    'lote_id' => $asignacionOrigen->lote_id,
                    'cantidad' => $cantidadTrasladar,
                    'porcentaje_ocupacion' => $porcentajeDestino,
                ]);
            }

            $tipoTraslado = DB::table('tipos_movimiento')
                ->whereRaw('LOWER(nombre) = ?', ['traslado'])
                ->first(['id']);

            if (!$tipoTraslado) {
                throw ValidationException::withMessages([
                    'tipo_movimiento' =>
                        'No existe el tipo “Traslado” en la tabla tipos_movimiento.',
                ]);
            }

            $producto = DB::table('productos')
                ->where('id', $inventario->producto_id)
                ->first(['nombre']);

            $lote = $asignacionOrigen->lote_id
                ? DB::table('lotes')
                    ->where('id', $asignacionOrigen->lote_id)
                    ->first(['codigo_lote'])
                : null;

            $costoUnitario = null;

            if ($asignacionOrigen->lote_id) {
                $costoUnitario = DB::table('lotes as l')
                    ->leftJoin(
                        'recepcion_detalles as rd',
                        'rd.id',
                        '=',
                        'l.recepcion_detalle_id'
                    )
                    ->where('l.id', $asignacionOrigen->lote_id)
                    ->value('rd.costo_unitario');
            }

            do {
                $numeroMovimiento = 'TR-'
                    . now()->format('YmdHis')
                    . '-'
                    . Str::upper(Str::random(6));
            } while (
                DB::table('movimientos_inventario')
                    ->where('numero_movimiento', $numeroMovimiento)
                    ->exists()
            );

            $movimientoId = DB::table('movimientos_inventario')
                ->insertGetId([
                    'numero_movimiento' => $numeroMovimiento,
                    'tipo_movimiento_id' => $tipoTraslado->id,
                    'usuario_id' => auth()->id(),
                    'empleado_id' => auth()->user()?->empleado?->id,
                    'almacen_origen_id' => $almacenId,
                    'almacen_destino_id' => $almacenId,
                    'ubicacion_origen_id' => $ubicacionOrigenId,
                    'ubicacion_destino_id' => $ubicacionDestinoId,
                    'fecha_movimiento' => now(),
                    'motivo' => trim($datos['motivo'] ?? '')
                        ?: 'Traslado interno'
                            . ($lote ? ' — lote ' . $lote->codigo_lote : ''),
                    'estado' => 'confirmado',
                ]);

            DB::table('movimiento_detalles')->insert([
                'movimiento_id' => $movimientoId,
                'producto_id' => $inventario->producto_id,
                'lote_id' => $asignacionOrigen->lote_id,
                'cantidad' => $cantidadTrasladar,
                'costo_unitario' => $costoUnitario,
            ]);
        });

        return redirect()
            ->route('admin.almacenes.ubicaciones.index', [
                'almacenId' => $almacenId,
                'ubicacion_id' => $ubicacionDestinoId,
            ])
            ->with(
                'success',
                'Traslado registrado correctamente. El stock total del almacén no cambió.'
            );
    }

    public function quitarAsignacion(
        int $almacenId,
        int $ubicacionId,
        int $asignacionId
    ) {
        $ubicacionExiste = DB::table('ubicaciones')
            ->where('id', $ubicacionId)
            ->where('almacen_id', $almacenId)
            ->exists();

        abort_unless(
            $ubicacionExiste,
            404,
            'La ubicación no pertenece a este almacén.'
        );

        DB::table('inventario_ubicaciones')
            ->where('id', $asignacionId)
            ->where('ubicacion_id', $ubicacionId)
            ->delete();

        return redirect()
            ->route('admin.almacenes.ubicaciones.index', [
                'almacenId' => $almacenId,
                'ubicacion_id' => $ubicacionId,
            ])
            ->with(
                'success',
                'Se quitó el lote de la ubicación. El stock del almacén se conserva.'
            );
    }

    public function destroy(int $almacenId, int $ubicacionId)
    {
        $ubicacion = DB::table('ubicaciones')
            ->where('id', $ubicacionId)
            ->where('almacen_id', $almacenId)
            ->first();

        abort_unless(
            $ubicacion,
            404,
            'La ubicación no existe en este almacén.'
        );

        $tieneProductos = DB::table('inventario_ubicaciones')
            ->where('ubicacion_id', $ubicacionId)
            ->exists();

        if ($tieneProductos) {
            return redirect()
                ->route('admin.almacenes.ubicaciones.index', [
                    'almacenId' => $almacenId,
                    'ubicacion_id' => $ubicacionId,
                ])
                ->with(
                    'error',
                    'Quita primero los lotes de esta ubicación para poder eliminarla.'
                );
        }

        DB::table('ubicaciones')
            ->where('id', $ubicacionId)
            ->where('almacen_id', $almacenId)
            ->delete();

        return redirect()
            ->route('admin.almacenes.ubicaciones.index', [
                'almacenId' => $almacenId,
            ])
            ->with('success', 'Ubicación eliminada.');
    }
}