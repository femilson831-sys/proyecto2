<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InventarioController extends Controller
{
    /**
     * Muestra productos que tuvieron entradas registradas con lote.
     */
    public function index(Request $request)
    {
        $datos = $request->validate([
            'almacen_id' => ['nullable', 'integer', 'exists:almacenes,id'],
            'buscar' => ['nullable', 'string', 'max:100'],
            'estado_stock' => ['nullable', 'in:todos,bajo,agotado,normal'],
        ]);

        $almacenId = $datos['almacen_id'] ?? null;
        $buscar = trim($datos['buscar'] ?? '');
        $estadoStock = $datos['estado_stock'] ?? 'todos';

        $almacenes = DB::table('almacenes')
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get(['id', 'codigo', 'nombre']);

        $almacenSeleccionado = $almacenId
            ? DB::table('almacenes')->where('id', $almacenId)->first()
            : null;

        $stockPorProducto = DB::table('inventarios')
            ->select('producto_id')
            ->selectRaw('SUM(COALESCE(cantidad, 0)) AS stock_actual')
            ->when($almacenId, function ($query) use ($almacenId) {
                $query->where('almacen_id', $almacenId);
            })
            ->groupBy('producto_id');

        /*
         * Agrupa los lotes por producto.
         * Si se seleccionó un almacén, solo considera sus recepciones.
         */
        $entradasPorProducto = DB::table('lotes as l')
            ->join(
                'recepcion_detalles as rd',
                'rd.id',
                '=',
                'l.recepcion_detalle_id'
            )
            ->join('recepciones as r', 'r.id', '=', 'rd.recepcion_id')
            ->whereNotNull('l.recepcion_detalle_id')
            ->whereColumn('rd.producto_id', 'l.producto_id')
            ->when($almacenId, function ($query) use ($almacenId) {
                $query->where('r.almacen_id', $almacenId);
            })
            ->select('l.producto_id')
            ->selectRaw(
                'SUM(
                    COALESCE(
                        NULLIF(l.cantidad_inicial, 0),
                        NULLIF(rd.cantidad_aceptada, 0),
                        rd.cantidad,
                        0
                    )
                ) AS cantidad_ingresada'
            )
            ->selectRaw('MAX(r.fecha_recepcion) AS ultima_entrada')
            ->groupBy('l.producto_id');

        /*
         * El INNER JOIN excluye los productos que no tienen lotes
         * relacionados con una recepción.
         */
        $productosBase = DB::table('productos as p')
            ->joinSub($entradasPorProducto, 'entradas', function ($join) {
                $join->on('entradas.producto_id', '=', 'p.id');
            })
            ->leftJoinSub($stockPorProducto, 'stock', function ($join) {
                $join->on('stock.producto_id', '=', 'p.id');
            })
            ->where('p.estado', 'activo');

        $totalProductos = (clone $productosBase)->count('p.id');

        $productosAgotados = (clone $productosBase)
            ->whereRaw('COALESCE(stock.stock_actual, 0) <= 0')
            ->count('p.id');

        $productosBajos = (clone $productosBase)
            ->whereRaw('COALESCE(stock.stock_actual, 0) > 0')
            ->whereRaw(
                'COALESCE(stock.stock_actual, 0) <= COALESCE(p.stock_minimo, 0)'
            )
            ->count('p.id');

        $productosNormales = (clone $productosBase)
            ->whereRaw(
                'COALESCE(stock.stock_actual, 0) > COALESCE(p.stock_minimo, 0)'
            )
            ->count('p.id');

        $query = DB::table('productos as p')
            ->joinSub($entradasPorProducto, 'entradas', function ($join) {
                $join->on('entradas.producto_id', '=', 'p.id');
            })
            ->leftJoinSub($stockPorProducto, 'stock', function ($join) {
                $join->on('stock.producto_id', '=', 'p.id');
            })
            ->leftJoin(
                'unidades_medida as um',
                'um.id',
                '=',
                'p.unidad_medida_id'
            )
            ->leftJoin(
                'tipos_producto as tp',
                'tp.id',
                '=',
                'p.tipo_producto_id'
            )
            ->where('p.estado', 'activo')
            ->select([
                'p.id',
                'p.codigo_interno',
                'p.nombre',
                'p.descripcion',
                'p.stock_minimo',
                'um.nombre as unidad_nombre',
                'um.abreviatura as unidad_abreviatura',
                'tp.permite_pesaje',
                'entradas.cantidad_ingresada',
                'entradas.ultima_entrada',
            ])
            ->selectRaw('COALESCE(stock.stock_actual, 0) AS stock_actual');

        if ($buscar !== '') {
            $query->where(function ($subquery) use ($buscar) {
                $subquery
                    ->where('p.nombre', 'like', "%{$buscar}%")
                    ->orWhere('p.codigo_interno', 'like', "%{$buscar}%");
            });
        }

        if ($estadoStock === 'agotado') {
            $query->whereRaw('COALESCE(stock.stock_actual, 0) <= 0');
        } elseif ($estadoStock === 'bajo') {
            $query
                ->whereRaw('COALESCE(stock.stock_actual, 0) > 0')
                ->whereRaw(
                    'COALESCE(stock.stock_actual, 0) <= COALESCE(p.stock_minimo, 0)'
                );
        } elseif ($estadoStock === 'normal') {
            $query->whereRaw(
                'COALESCE(stock.stock_actual, 0) > COALESCE(p.stock_minimo, 0)'
            );
        }

        $inventarios = $query
            ->orderByDesc('entradas.ultima_entrada')
            ->orderBy('p.nombre')
            ->paginate(15)
            ->withQueryString();

        return view('admin.inventario.index', compact(
            'inventarios',
            'almacenes',
            'almacenSeleccionado',
            'almacenId',
            'buscar',
            'estadoStock',
            'totalProductos',
            'productosAgotados',
            'productosBajos',
            'productosNormales'
        ));
    }

    /**
     * Muestra las entradas por lote y las salidas confirmadas de un producto.
     */
    public function historialProducto(int $productoId)
    {
        $producto = DB::table('productos as p')
            ->leftJoin('unidades_medida as um', 'um.id', '=', 'p.unidad_medida_id')
            ->where('p.id', $productoId)
            ->first([
                'p.id',
                'p.codigo_interno',
                'p.nombre',
                'p.descripcion',
                'um.nombre as unidad_nombre',
                'um.abreviatura as unidad_abreviatura',
            ]);

        abort_if(!$producto, 404, 'No se encontró el producto.');

        $lotes = DB::table('lotes as l')
            ->join('recepcion_detalles as rd', 'rd.id', '=', 'l.recepcion_detalle_id')
            ->join('recepciones as r', 'r.id', '=', 'rd.recepcion_id')
            ->leftJoin('almacenes as a', 'a.id', '=', 'r.almacen_id')
            ->leftJoin('proveedores as pr', 'pr.id', '=', 'r.proveedor_id')
            ->where('l.producto_id', $productoId)
            ->whereNotNull('l.recepcion_detalle_id')
            ->whereColumn('rd.producto_id', 'l.producto_id')
            ->orderByDesc('r.fecha_recepcion')
            ->orderByDesc('l.id')
            ->get([
                'l.id',
                'l.codigo_lote',
                'l.fecha_fabricacion',
                'l.fecha_vencimiento',
                'l.cantidad_inicial',
                'l.cantidad_actual',
                'rd.cantidad',
                'rd.cantidad_aceptada',
                'rd.costo_unitario',
                'r.numero_recepcion',
                'r.numero_factura',
                'r.fecha_recepcion',
                'a.nombre as almacen',
                'pr.razon_social',
                'pr.nombre_comercial',
            ]);

        $cantidadTotalIngresada = $lotes->sum(function ($lote) {
            return (float) (
                $lote->cantidad_inicial
                ?: $lote->cantidad_aceptada
                ?: $lote->cantidad
                ?: 0
            );
        });

        $stockActualLotes = $lotes->sum(function ($lote) {
            return (float) ($lote->cantidad_actual ?? 0);
        });

        $consultaSalidas = DB::table('movimiento_detalles as md')
            ->join('movimientos_inventario as mi', 'mi.id', '=', 'md.movimiento_id')
            ->join('tipos_movimiento as tm', 'tm.id', '=', 'mi.tipo_movimiento_id')
            ->leftJoin('lotes as l', 'l.id', '=', 'md.lote_id')
            ->leftJoin('almacenes as a', 'a.id', '=', 'mi.almacen_origen_id')
            ->where('md.producto_id', $productoId)
            ->where('tm.naturaleza', 'salida')
            ->where('mi.estado', 'confirmado')
            ->select([
                'md.id as detalle_id',
                'md.cantidad',
                'md.costo_unitario',
                'l.codigo_lote',
                'mi.numero_movimiento',
                'mi.fecha_movimiento',
                'mi.motivo',
                'tm.nombre as tipo_salida',
                'a.nombre as almacen',
            ]);

        $cantidadTotalSalidas = (float) (clone $consultaSalidas)
            ->sum('md.cantidad');

        $totalRegistrosSalidas = (clone $consultaSalidas)
            ->count('md.id');

        $salidas = $consultaSalidas
            ->orderByDesc('mi.fecha_movimiento')
            ->orderByDesc('mi.id')
            ->paginate(10, ['*'], 'pagina_salidas')
            ->withQueryString();

        $unidad = $producto->unidad_abreviatura
            ?: ($producto->unidad_nombre ?: 'unidades');

        return view('admin.inventario.historial', compact(
            'producto',
            'lotes',
            'cantidadTotalIngresada',
            'stockActualLotes',
            'cantidadTotalSalidas',
            'totalRegistrosSalidas',
            'salidas',
            'unidad'
        ));
    }

    /**
     * Muestra el formulario de entrada.
     */
    public function create()
    {
        $productos = DB::table('productos as p')
            ->leftJoin(
                'unidades_medida as um',
                'um.id',
                '=',
                'p.unidad_medida_id'
            )
            ->where('p.estado', 'activo')
            ->orderBy('p.nombre')
            ->get([
                'p.id',
                'p.codigo_interno',
                'p.nombre',
                'um.nombre as unidad_nombre',
                'um.abreviatura as unidad_medida',
            ]);

        $almacenes = DB::table('almacenes')
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get(['id', 'codigo', 'nombre']);

        return view('admin.inventario.create', compact(
            'productos',
            'almacenes'
        ));
    }

    /**
     * Mantiene compatibilidad con rutas que todavía apuntan a store().
     */
    public function store(Request $request)
    {
        return $this->storeEntrada($request);
    }

    /**
     * Registra recepción, detalle, lote, movimiento y stock.
     */
    public function storeEntrada(Request $request)
    {
        $datos = $request->validate([
            'almacen_id' => ['required', 'integer', 'exists:almacenes,id'],
            'fecha_recepcion' => ['required', 'date'],
            'numero_factura' => ['nullable', 'string', 'max:80'],
            'producto_id' => ['required', 'integer', 'exists:productos,id'],
            'proveedor_id' => ['required', 'integer', 'exists:proveedores,id'],
            'cantidad' => [
                'required',
                'numeric',
                'gt:0',
                'max:999999999.999',
            ],
            'costo_unitario' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],
            'codigo_lote' => ['required', 'string', 'max:100'],
            'fecha_fabricacion' => ['nullable', 'date'],
            'fecha_vencimiento' => [
                'nullable',
                'date',
                'after_or_equal:fecha_fabricacion',
            ],
            'observaciones' => ['nullable', 'string', 'max:5000'],
        ], [
            'almacen_id.required' => 'Selecciona el almacén de destino.',
            'fecha_recepcion.required' => 'Indica la fecha de recepción.',
            'producto_id.required' => 'Selecciona un producto.',
            'proveedor_id.required' =>
                'El producto debe tener un proveedor relacionado.',
            'cantidad.required' => 'Ingresa la cantidad recibida.',
            'cantidad.gt' => 'La cantidad debe ser mayor que cero.',
            'costo_unitario.required' => 'Ingresa el costo unitario.',
            'codigo_lote.required' => 'Ingresa el código del lote.',
            'fecha_vencimiento.after_or_equal' =>
                'La fecha de vencimiento debe ser igual o posterior a la fecha de fabricación.',
        ]);

        $almacenActivo = DB::table('almacenes')
            ->where('id', $datos['almacen_id'])
            ->where('estado', 'activo')
            ->exists();

        if (!$almacenActivo) {
            throw ValidationException::withMessages([
                'almacen_id' => 'Selecciona un almacén activo.',
            ]);
        }

        $producto = DB::table('productos as p')
            ->leftJoin(
                'tipos_producto as tp',
                'tp.id',
                '=',
                'p.tipo_producto_id'
            )
            ->where('p.id', $datos['producto_id'])
            ->where('p.estado', 'activo')
            ->first([
                'p.id',
                'p.nombre',
                'tp.permite_pesaje',
            ]);

        if (!$producto) {
            throw ValidationException::withMessages([
                'producto_id' => 'Selecciona un producto activo.',
            ]);
        }

        $proveedorRelacionado = DB::table('proveedor_producto')
            ->where('producto_id', $datos['producto_id'])
            ->where('proveedor_id', $datos['proveedor_id'])
            ->exists();

        if (!$proveedorRelacionado) {
            throw ValidationException::withMessages([
                'proveedor_id' =>
                    'El proveedor seleccionado no está relacionado con este producto.',
            ]);
        }

        $cantidad = round((float) $datos['cantidad'], 3);
        $esPesable = (bool) ($producto->permite_pesaje ?? false);

        if (!$esPesable && abs($cantidad - round($cantidad)) > 0.00001) {
            throw ValidationException::withMessages([
                'cantidad' => 'Este producto se controla por unidades enteras.',
            ]);
        }

        $tipoEntrada = DB::table('tipos_movimiento')
            ->where('nombre', 'Entrada')
            ->first();

        if (!$tipoEntrada) {
            throw ValidationException::withMessages([
                'producto_id' =>
                    'No existe el tipo de movimiento “Entrada” en tipos_movimiento.',
            ]);
        }

        $usuarioId = $request->user()?->getAuthIdentifier();

        DB::transaction(function () use (
            $datos,
            $tipoEntrada,
            $cantidad,
            $usuarioId
        ) {
            $fechaRecepcion = Carbon::parse($datos['fecha_recepcion'])
                ->format('Y-m-d H:i:s');

            $numeroRecepcion = 'REC-'
                . now()->format('YmdHis')
                . '-'
                . Str::upper(Str::random(4));

            $recepcionId = DB::table('recepciones')->insertGetId([
                'numero_recepcion' => $numeroRecepcion,
                'proveedor_id' => $datos['proveedor_id'],
                'almacen_id' => $datos['almacen_id'],
                'usuario_id' => $usuarioId,
                'fecha_recepcion' => $fechaRecepcion,
                'numero_factura' => $datos['numero_factura'] ?? null,
                'estado' => 'recibida',
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            $detalleId = DB::table('recepcion_detalles')->insertGetId([
                'recepcion_id' => $recepcionId,
                'producto_id' => $datos['producto_id'],
                'cantidad' => $cantidad,
                'costo_unitario' => $datos['costo_unitario'],
                'cantidad_aceptada' => $cantidad,
                'cantidad_rechazada' => 0,
            ]);

            $loteId = DB::table('lotes')->insertGetId([
                'producto_id' => $datos['producto_id'],
                'recepcion_detalle_id' => $detalleId,
                'codigo_lote' => trim($datos['codigo_lote']),
                'fecha_fabricacion' => $datos['fecha_fabricacion'] ?? null,
                'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
                'cantidad_inicial' => $cantidad,
                'cantidad_actual' => $cantidad,
            ]);

            $movimientoId = DB::table('movimientos_inventario')->insertGetId([
                'numero_movimiento' => 'ENT-'
                    . now()->format('YmdHis')
                    . '-'
                    . Str::upper(Str::random(4)),
                'tipo_movimiento_id' => $tipoEntrada->id,
                'usuario_id' => $usuarioId,
                'almacen_destino_id' => $datos['almacen_id'],
                'fecha_movimiento' => $fechaRecepcion,
                'motivo' => 'Entrada por recepción ' . $numeroRecepcion,
                'estado' => 'confirmado',
            ]);

            DB::table('movimiento_detalles')->insert([
                'movimiento_id' => $movimientoId,
                'producto_id' => $datos['producto_id'],
                'lote_id' => $loteId,
                'cantidad' => $cantidad,
                'costo_unitario' => $datos['costo_unitario'],
            ]);

            $inventario = DB::table('inventarios')
                ->where('almacen_id', $datos['almacen_id'])
                ->where('producto_id', $datos['producto_id'])
                ->lockForUpdate()
                ->first();

            if ($inventario) {
                DB::table('inventarios')
                    ->where('id', $inventario->id)
                    ->increment('cantidad', $cantidad);
            } else {
                DB::table('inventarios')->insert([
                    'almacen_id' => $datos['almacen_id'],
                    'producto_id' => $datos['producto_id'],
                    'cantidad' => $cantidad,
                ]);
            }
        });

        return redirect()
            ->route('admin.inventario.index', [
                'almacen_id' => $datos['almacen_id'],
            ])
            ->with(
                'success',
                'La entrada, el lote y el stock se registraron correctamente.'
            );
    }

    /**
     * Devuelve proveedor, unidad y costo para el formulario.
     */
    public function datosProducto(int $productoId)
    {
        $producto = DB::table('productos as p')
            ->leftJoin(
                'unidades_medida as um',
                'um.id',
                '=',
                'p.unidad_medida_id'
            )
            ->leftJoin(
                'tipos_producto as tp',
                'tp.id',
                '=',
                'p.tipo_producto_id'
            )
            ->where('p.id', $productoId)
            ->where('p.estado', 'activo')
            ->first([
                'p.id',
                'p.codigo_interno',
                'p.nombre',
                'um.nombre as unidad_nombre',
                'um.abreviatura as unidad_abreviatura',
                'tp.permite_pesaje',
            ]);

        if (!$producto) {
            return response()->json([
                'message' => 'No se encontró un producto activo.',
            ], 404);
        }

        $proveedor = DB::table('proveedor_producto as pp')
            ->join('proveedores as pr', 'pr.id', '=', 'pp.proveedor_id')
            ->where('pp.producto_id', $productoId)
            ->orderByDesc('pp.es_principal')
            ->orderBy('pp.proveedor_id')
            ->first([
                'pp.proveedor_id',
                'pp.precio_compra as costo_proveedor',
                'pr.razon_social',
                'pr.nombre_comercial',
            ]);

        $costoHistorial = DB::table('productos_precios')
            ->where('producto_id', $productoId)
            ->where('estado', 1)
            ->orderByDesc('fecha_inicio')
            ->value('precio_compra');

        $costo = $proveedor && $proveedor->costo_proveedor !== null
            ? (float) $proveedor->costo_proveedor
            : ($costoHistorial !== null ? (float) $costoHistorial : null);

        return response()->json([
            'id' => $producto->id,
            'codigo_interno' => $producto->codigo_interno,
            'nombre' => $producto->nombre,
            'proveedor' => $proveedor
                ? ($proveedor->nombre_comercial ?: $proveedor->razon_social)
                : null,
            'proveedor_id' => $proveedor->proveedor_id ?? null,
            'unidad' => $producto->unidad_abreviatura
                ?: $producto->unidad_nombre,
            'unidad_nombre' => $producto->unidad_nombre,
            'unidad_abreviatura' => $producto->unidad_abreviatura,
            'costo_unitario' => $costo,
            'permite_pesaje' => (bool) $producto->permite_pesaje,
        ]);
    }

    /**
     * Muestra el formulario de salida.
     */
    public function createSalida()
    {
        $almacenes = DB::table('almacenes')
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get(['id', 'codigo', 'nombre']);

        $existencias = DB::table('inventarios as i')
            ->join('productos as p', 'p.id', '=', 'i.producto_id')
            ->join('almacenes as a', 'a.id', '=', 'i.almacen_id')
            ->leftJoin(
                'unidades_medida as um',
                'um.id',
                '=',
                'p.unidad_medida_id'
            )
            ->leftJoin(
                'tipos_producto as tp',
                'tp.id',
                '=',
                'p.tipo_producto_id'
            )
            ->where('i.cantidad', '>', 0)
            ->where('p.estado', 'activo')
            ->where('a.estado', 'activo')
            ->orderBy('p.nombre')
            ->get([
                'i.id as inventario_id',
                'i.almacen_id',
                'i.producto_id',
                'i.cantidad as stock_actual',
                'p.codigo_interno',
                'p.nombre as producto',
                'a.nombre as almacen',
                'um.nombre as unidad_nombre',
                'um.abreviatura as unidad_medida',
                'tp.permite_pesaje',
            ]);

        $lotes = DB::table('lotes as l')
            ->join(
                'recepcion_detalles as rd',
                'rd.id',
                '=',
                'l.recepcion_detalle_id'
            )
            ->join('recepciones as r', 'r.id', '=', 'rd.recepcion_id')
            ->whereNotNull('l.recepcion_detalle_id')
            ->where('l.cantidad_actual', '>', 0)
            ->whereColumn('rd.producto_id', 'l.producto_id')
            ->orderByRaw('l.fecha_vencimiento IS NULL')
            ->orderBy('l.fecha_vencimiento')
            ->orderBy('l.codigo_lote')
            ->get([
                'l.id',
                'l.producto_id',
                'r.almacen_id',
                'l.codigo_lote',
                'l.fecha_vencimiento',
                'l.cantidad_actual',
            ]);

        $tiposSalida = DB::table('tipos_movimiento')
            ->where('naturaleza', 'salida')
            ->where('nombre', '<>', 'Venta')
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return view('admin.inventario.salida', compact(
            'almacenes',
            'existencias',
            'lotes',
            'tiposSalida'
        ));
    }

    /**
     * Registra una salida y descuenta existencias de sus lotes.
     */
    public function storeSalida(Request $request)
    {
        $datos = $request->validate([
            'almacen_id' => ['required', 'integer', 'exists:almacenes,id'],
            'tipo_movimiento_id' => [
                'required',
                'integer',
                'exists:tipos_movimiento,id',
            ],
            'motivo' => ['required', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1', 'max:5'],
            'items.*.inventario_id' => [
                'required',
                'integer',
                'exists:inventarios,id',
            ],
            'items.*.lote_id' => [
                'required',
                'integer',
                'distinct',
                'exists:lotes,id',
            ],
            'items.*.cantidad' => [
                'required',
                'numeric',
                'gt:0',
                'max:999999999.999',
            ],
        ]);

        $tipoSalida = DB::table('tipos_movimiento')
            ->where('id', $datos['tipo_movimiento_id'])
            ->where('naturaleza', 'salida')
            ->where('nombre', '<>', 'Venta')
            ->first();

        if (!$tipoSalida) {
            throw ValidationException::withMessages([
                'tipo_movimiento_id' => 'Selecciona un tipo de salida válido.',
            ]);
        }

        $almacenActivo = DB::table('almacenes')
            ->where('id', $datos['almacen_id'])
            ->where('estado', 'activo')
            ->exists();

        if (!$almacenActivo) {
            throw ValidationException::withMessages([
                'almacen_id' => 'Selecciona un almacén activo.',
            ]);
        }

        $usuarioId = $request->user()?->getAuthIdentifier();

        DB::transaction(function () use (
            $datos,
            $tipoSalida,
            $usuarioId
        ) {
            $lineas = [];
            $cantidadesPorInventario = [];

            foreach ($datos['items'] as $indice => $item) {
                $inventario = DB::table('inventarios')
                    ->where('id', $item['inventario_id'])
                    ->lockForUpdate()
                    ->first();

                if (
                    !$inventario
                    || (int) $inventario->almacen_id
                        !== (int) $datos['almacen_id']
                ) {
                    throw ValidationException::withMessages([
                        "items.$indice.inventario_id" =>
                            'El producto no pertenece al almacén seleccionado.',
                    ]);
                }

                $producto = DB::table('productos as p')
                    ->leftJoin(
                        'tipos_producto as tp',
                        'tp.id',
                        '=',
                        'p.tipo_producto_id'
                    )
                    ->where('p.id', $inventario->producto_id)
                    ->where('p.estado', 'activo')
                    ->first([
                        'p.id',
                        'tp.permite_pesaje',
                    ]);

                if (!$producto) {
                    throw ValidationException::withMessages([
                        "items.$indice.inventario_id" =>
                            'El producto seleccionado no está activo.',
                    ]);
                }

                $lote = DB::table('lotes')
                    ->where('id', $item['lote_id'])
                    ->lockForUpdate()
                    ->first();

                if (
                    !$lote
                    || (int) $lote->producto_id
                        !== (int) $inventario->producto_id
                ) {
                    throw ValidationException::withMessages([
                        "items.$indice.lote_id" =>
                            'El lote no corresponde al producto seleccionado.',
                    ]);
                }

                $loteEnAlmacen = DB::table('recepcion_detalles as rd')
                    ->join('recepciones as r', 'r.id', '=', 'rd.recepcion_id')
                    ->where('rd.id', $lote->recepcion_detalle_id)
                    ->where('rd.producto_id', $inventario->producto_id)
                    ->where('r.almacen_id', $datos['almacen_id'])
                    ->exists();

                if (!$loteEnAlmacen) {
                    throw ValidationException::withMessages([
                        "items.$indice.lote_id" =>
                            'El lote no pertenece al almacén seleccionado.',
                    ]);
                }

                $cantidad = round((float) $item['cantidad'], 3);
                $esPesable = (bool) ($producto->permite_pesaje ?? false);

                if (!$esPesable && abs($cantidad - round($cantidad)) > 0.00001) {
                    throw ValidationException::withMessages([
                        "items.$indice.cantidad" =>
                            'Este producto debe salir en unidades enteras.',
                    ]);
                }

                if ($cantidad > (float) $lote->cantidad_actual) {
                    throw ValidationException::withMessages([
                        "items.$indice.cantidad" =>
                            'La cantidad supera lo disponible en el lote.',
                    ]);
                }

                $lineas[] = [
                    'inventario_id' => $inventario->id,
                    'producto_id' => $inventario->producto_id,
                    'lote_id' => $lote->id,
                    'cantidad' => $cantidad,
                ];

                $cantidadesPorInventario[$inventario->id] =
                    ($cantidadesPorInventario[$inventario->id] ?? 0)
                    + $cantidad;
            }

            foreach ($cantidadesPorInventario as $inventarioId => $total) {
                $inventario = DB::table('inventarios')
                    ->where('id', $inventarioId)
                    ->lockForUpdate()
                    ->first();

                if (!$inventario || $total > (float) $inventario->cantidad) {
                    throw ValidationException::withMessages([
                        'items' =>
                            'La cantidad solicitada supera el stock del almacén.',
                    ]);
                }
            }

            $movimientoId = DB::table('movimientos_inventario')->insertGetId([
                'numero_movimiento' => 'SAL-'
                    . now()->format('YmdHis')
                    . '-'
                    . Str::upper(Str::random(4)),
                'tipo_movimiento_id' => $tipoSalida->id,
                'usuario_id' => $usuarioId,
                'almacen_origen_id' => $datos['almacen_id'],
                'fecha_movimiento' => now(),
                'motivo' => trim($datos['motivo']),
                'estado' => 'confirmado',
            ]);

            foreach ($lineas as $linea) {
                DB::table('movimiento_detalles')->insert([
                    'movimiento_id' => $movimientoId,
                    'producto_id' => $linea['producto_id'],
                    'lote_id' => $linea['lote_id'],
                    'cantidad' => $linea['cantidad'],
                    'costo_unitario' => null,
                ]);

                DB::table('lotes')
                    ->where('id', $linea['lote_id'])
                    ->decrement('cantidad_actual', $linea['cantidad']);
            }

            foreach ($cantidadesPorInventario as $inventarioId => $total) {
                DB::table('inventarios')
                    ->where('id', $inventarioId)
                    ->decrement('cantidad', $total);
            }
        });

        return redirect()
            ->route('admin.inventario.index', [
                'almacen_id' => $datos['almacen_id'],
            ])
            ->with(
                'success',
                'La salida se registró y se actualizaron el inventario y los lotes.'
            );
    }
}