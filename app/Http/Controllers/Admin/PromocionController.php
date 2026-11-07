<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PromocionController extends Controller
{
    private const MAX_CENTAVOS = 999999999999;

    /*
    |--------------------------------------------------------------------------
    | LISTADO
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $conteo = DB::table('promocion_productos')
            ->select('promocion_id')
            ->selectRaw('COUNT(*) AS productos_total')
            ->groupBy('promocion_id');

        $promociones = DB::table('promociones as pr')
            ->leftJoinSub($conteo, 'conteo', function ($join) {
                $join->on(
                    'conteo.promocion_id',
                    '=',
                    'pr.id'
                );
            })
            ->select('pr.*')
            ->selectRaw(
                'COALESCE(conteo.productos_total, 0) AS productos_total'
            )
            ->orderByDesc('pr.id')
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.promociones.index',
            compact('promociones')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO DE CREACIÓN
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.promociones.form', [
            'promocion' => null,
            'productos' => $this->productosDisponibles(),
            'productosSeleccionados' => [],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTRAR
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        DB::transaction(function () use ($datos) {
            $preparado = $this->preparar($datos);

            $promocionId = DB::table('promociones')
                ->insertGetId([
                    'codigo' => $datos['codigo']
                        ?? $this->generarCodigo(),

                    'nombre' => $datos['nombre'],

                    'descripcion' => $datos['descripcion'] ?? null,

                    'tipo' => $datos['tipo'],

                    'modo_calculo' => $preparado['modo'],

                    'valor_descuento' =>
                        $preparado['valor_descuento'],

                    'precio_promocional' =>
                        $preparado['precio_final'],

                    'total_regular_referencia' =>
                        $preparado['total_normal'],

                    'limite_aplicaciones' =>
                        $preparado['limite'],

                    'aplicaciones_usadas' => 0,

                    'fecha_inicio' => $datos['fecha_inicio'],

                    'fecha_fin' => $datos['fecha_fin'] ?? null,

                    'estado' => $datos['estado'],

                    'created_at' => now(),

                    'updated_at' => now(),
                ]);

            $this->guardarProductos(
                $promocionId,
                $preparado['items']
            );
        });

        return redirect()
            ->route('admin.promociones.index')
            ->with(
                'success',
                'Promoción registrada correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO DE EDICIÓN
    |--------------------------------------------------------------------------
    */

    public function edit(int $promocion)
    {
        $registro = DB::table('promociones')
            ->where('id', $promocion)
            ->first();

        abort_unless(
            $registro,
            404,
            'La promoción no existe.'
        );

        $productosSeleccionados = DB::table('promocion_productos')
            ->where('promocion_id', $promocion)
            ->pluck(
                'cantidad_requerida',
                'producto_id'
            )
            ->all();

        return view('admin.promociones.form', [
            'promocion' => $registro,
            'productos' => $this->productosDisponibles(),
            'productosSeleccionados' => $productosSeleccionados,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, int $promocion)
    {
        $datos = $this->validar(
            $request,
            $promocion
        );

        DB::transaction(function () use ($datos, $promocion) {
            $registro = DB::table('promociones')
                ->where('id', $promocion)
                ->lockForUpdate()
                ->first();

            abort_unless(
                $registro,
                404,
                'La promoción no existe.'
            );

            $preparado = $this->preparar($datos);

            $usadas = (int) $registro->aplicaciones_usadas;

            if (
                $preparado['limite'] !== null
                && $preparado['limite'] < $usadas
            ) {
                throw ValidationException::withMessages([
                    'limite_aplicaciones' =>
                        "El cupo no puede ser menor que las {$usadas} aplicaciones ya utilizadas.",
                ]);
            }

            DB::table('promociones')
                ->where('id', $promocion)
                ->update([
                    'codigo' => $datos['codigo']
                        ?? $registro->codigo,

                    'nombre' => $datos['nombre'],

                    'descripcion' => $datos['descripcion'] ?? null,

                    'tipo' => $datos['tipo'],

                    'modo_calculo' => $preparado['modo'],

                    'valor_descuento' =>
                        $preparado['valor_descuento'],

                    'precio_promocional' =>
                        $preparado['precio_final'],

                    'total_regular_referencia' =>
                        $preparado['total_normal'],

                    'limite_aplicaciones' =>
                        $preparado['limite'],

                    'fecha_inicio' => $datos['fecha_inicio'],

                    'fecha_fin' => $datos['fecha_fin'] ?? null,

                    'estado' => $datos['estado'],

                    'updated_at' => now(),
                ]);

            DB::table('promocion_productos')
                ->where('promocion_id', $promocion)
                ->delete();

            $this->guardarProductos(
                $promocion,
                $preparado['items']
            );
        });

        return redirect()
            ->route('admin.promociones.index')
            ->with(
                'success',
                'Promoción actualizada correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIVAR O DESACTIVAR
    |--------------------------------------------------------------------------
    */

    public function cambiarEstado(int $promocion)
    {
        DB::transaction(function () use ($promocion) {
            $registro = DB::table('promociones')
                ->where('id', $promocion)
                ->lockForUpdate()
                ->first();

            abort_unless(
                $registro,
                404,
                'La promoción no existe.'
            );

            DB::table('promociones')
                ->where('id', $promocion)
                ->update([
                    'estado' => $registro->estado === 'activa'
                        ? 'inactiva'
                        : 'activa',

                    'updated_at' => now(),
                ]);
        });

        return back()->with(
            'success',
            'Estado de la promoción actualizado correctamente.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    public function destroy(int $promocion)
    {
        DB::transaction(function () use ($promocion) {
            $registro = DB::table('promociones')
                ->where('id', $promocion)
                ->lockForUpdate()
                ->first();

            abort_unless(
                $registro,
                404,
                'La promoción no existe.'
            );

            if ((int) $registro->aplicaciones_usadas > 0) {
                throw ValidationException::withMessages([
                    'promocion' =>
                        'La promoción tiene aplicaciones registradas. Desactívala para conservar su historial.',
                ]);
            }

            DB::table('promocion_productos')
                ->where('promocion_id', $promocion)
                ->delete();

            DB::table('promociones')
                ->where('id', $promocion)
                ->delete();
        });

        return redirect()
            ->route('admin.promociones.index')
            ->with(
                'success',
                'Promoción eliminada correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDACIÓN
    |--------------------------------------------------------------------------
    */

    private function validar(
        Request $request,
        ?int $promocionId = null
    ): array {
        $camposOpcionales = [
            'codigo',
            'descripcion',
            'fecha_fin',
            'valor_descuento',
            'precio_promocional',
            'limite_aplicaciones',
        ];

        foreach ($camposOpcionales as $campo) {
            $valor = $request->input($campo);

            if (is_string($valor)) {
                $valor = trim($valor);

                $request->merge([
                    $campo => $valor === '' ? null : $valor,
                ]);
            }
        }

        /*
         * El tipo individual determina su modo de cálculo.
         * En los combos el usuario puede escogerlo.
         */
        $tipo = $request->input('tipo');

        $modoIndividual = match ($tipo) {
            'descuento_porcentaje' => 'porcentaje',
            'descuento_monto' => 'monto',
            'precio_fijo' => 'precio_fijo',
            default => null,
        };

        if ($modoIndividual !== null) {
            $request->merge([
                'modo_calculo' => $modoIndividual,
            ]);
        }

        /*
         * Ignorar campos que no corresponden a la configuración
         * seleccionada.
         */
        $modo = $request->input('modo_calculo');

        if ($modo === 'precio_fijo') {
            $request->merge([
                'valor_descuento' => null,
            ]);
        } elseif (in_array($modo, ['porcentaje', 'monto'], true)) {
            $request->merge([
                'precio_promocional' => null,
            ]);
        }

        if ($request->input('limite_tipo') === 'stock') {
            $request->merge([
                'limite_aplicaciones' => null,
            ]);
        }

        $datos = $request->validate([
            'codigo' => [
                'nullable',
                'string',
                'max:40',
                Rule::unique('promociones', 'codigo')
                    ->ignore($promocionId),
            ],

            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'tipo' => [
                'required',
                Rule::in([
                    'combo',
                    'descuento_porcentaje',
                    'descuento_monto',
                    'precio_fijo',
                ]),
            ],

            'modo_calculo' => [
                'required',
                Rule::in([
                    'porcentaje',
                    'monto',
                    'precio_fijo',
                ]),
            ],

            'valor_descuento' => [
                'nullable',
                'required_if:modo_calculo,porcentaje,monto',
                'numeric',
                'decimal:0,2',
                'min:0.01',
                'max:9999999999.99',
            ],

            'precio_promocional' => [
                'nullable',
                'required_if:modo_calculo,precio_fijo',
                'numeric',
                'decimal:0,2',
                'min:0.01',
                'max:9999999999.99',
            ],

            'fecha_inicio' => [
                'required',
                'date',
            ],

            'fecha_fin' => [
                'nullable',
                'date',
                'after_or_equal:fecha_inicio',
            ],

            'estado' => [
                'required',
                Rule::in([
                    'activa',
                    'inactiva',
                ]),
            ],

            'limite_tipo' => [
                'required',
                Rule::in([
                    'stock',
                    'cantidad',
                ]),
            ],

            'limite_aplicaciones' => [
                'nullable',
                'required_if:limite_tipo,cantidad',
                'integer',
                'min:1',
                'max:2147483647',
            ],

            'productos' => [
                'required',
                'array',
                'min:1',
                'max:100',
            ],

            'productos.*' => [
                'required',
                'integer',
                'distinct',
                'exists:productos,id',
            ],

            'cantidades' => [
                'required',
                'array',
            ],

            'cantidades.*' => [
                'required',
                'numeric',
                'decimal:0,3',
                'min:0.001',
                'max:999999999.999',
            ],
        ], [
            'required' =>
                'El campo :attribute es obligatorio.',

            'required_if' =>
                'Completa el campo :attribute.',

            'string' =>
                'El campo :attribute debe contener texto.',

            'numeric' =>
                'El campo :attribute debe ser un número.',

            'integer' =>
                'El campo :attribute debe ser un número entero.',

            'decimal' =>
                'Revisa la cantidad de decimales en :attribute.',

            'min' =>
                'El campo :attribute debe ser como mínimo :min.',

            'max' =>
                'El campo :attribute supera el máximo permitido.',

            'date' =>
                'El campo :attribute debe contener una fecha válida.',

            'in' =>
                'Selecciona una opción válida para :attribute.',

            'array' =>
                'El campo :attribute debe contener una lista válida.',

            'exists' =>
                'El registro seleccionado en :attribute no existe.',

            'distinct' =>
                'No puedes agregar el mismo producto dos veces.',

            'codigo.unique' =>
                'El código ya pertenece a otra promoción.',

            'productos.required' =>
                'Agrega al menos un producto.',

            'fecha_fin.after_or_equal' =>
                'La fecha final debe ser posterior o igual a la fecha inicial.',
        ], [
            'codigo' => 'código',
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
            'tipo' => 'tipo de promoción',
            'modo_calculo' => 'forma de cálculo',
            'valor_descuento' => 'descuento',
            'precio_promocional' => 'precio final',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha final',
            'estado' => 'estado',
            'limite_tipo' => 'disponibilidad',
            'limite_aplicaciones' => 'cupo de aplicaciones',
            'productos' => 'productos',
            'productos.*' => 'producto',
            'cantidades' => 'cantidades',
            'cantidades.*' => 'cantidad por producto',
        ]);

        $datos['nombre'] = trim($datos['nombre']);

        if ($datos['nombre'] === '') {
            throw ValidationException::withMessages([
                'nombre' =>
                    'Escribe un nombre para la promoción.',
            ]);
        }

        return $datos;
    }

    /*
    |--------------------------------------------------------------------------
    | CALCULAR Y PREPARAR DATOS
    |--------------------------------------------------------------------------
    */

    private function preparar(array $datos): array
    {
        $ids = array_map(
            'intval',
            $datos['productos']
        );

        if (
            $datos['tipo'] === 'combo'
            && count($ids) < 2
        ) {
            throw ValidationException::withMessages([
                'productos' =>
                    'Un combo debe contener al menos dos productos distintos.',
            ]);
        }

        if (
            $datos['tipo'] !== 'combo'
            && count($ids) !== 1
        ) {
            throw ValidationException::withMessages([
                'productos' =>
                    'Para una promoción individual selecciona solamente un producto.',
            ]);
        }

        $modo = match ($datos['tipo']) {
            'descuento_porcentaje' => 'porcentaje',
            'descuento_monto' => 'monto',
            'precio_fijo' => 'precio_fijo',
            default => $datos['modo_calculo'],
        };

        $catalogo = $this->productosDisponibles()
            ->keyBy('id');

        $items = [];
        $totalCentavos = 0;

        foreach ($ids as $productoId) {
            $producto = $catalogo->get($productoId);

            if (
                !$producto
                || $producto->estado !== 'activo'
            ) {
                throw ValidationException::withMessages([
                    'productos' =>
                        'Uno de los productos seleccionados no está activo.',
                ]);
            }

            if ($producto->precio_venta === null) {
                throw ValidationException::withMessages([
                    'productos' =>
                        "{$producto->nombre} no tiene un precio de venta vigente.",
                ]);
            }

            if (
                !array_key_exists(
                    $productoId,
                    $datos['cantidades']
                )
            ) {
                throw ValidationException::withMessages([
                    "cantidades.{$productoId}" =>
                        "Indica la cantidad de {$producto->nombre}.",
                ]);
            }

            /*
             * Cantidad expresada en milésimas.
             * Ejemplo: 1.400 kg = 1400.
             */
            $cantidadMil = (int) round(
                (float) $datos['cantidades'][$productoId] * 1000
            );

            if ($cantidadMil <= 0) {
                throw ValidationException::withMessages([
                    "cantidades.{$productoId}" =>
                        "La cantidad de {$producto->nombre} debe ser mayor que cero.",
                ]);
            }

            $esPesable = (int) $producto->es_pesable === 1;

            if (
                !$esPesable
                && $cantidadMil % 1000 !== 0
            ) {
                throw ValidationException::withMessages([
                    "cantidades.{$productoId}" =>
                        "{$producto->nombre} se maneja por unidades o paquetes. Ingresa una cantidad entera.",
                ]);
            }

            /*
             * Precio expresado en centavos.
             * Ejemplo: Bs 8.50 = 850.
             */
            $precioCentavos = (int) round(
                (float) $producto->precio_venta * 100
            );

            if ($precioCentavos <= 0) {
                throw ValidationException::withMessages([
                    'productos' =>
                        "{$producto->nombre} debe tener un precio de venta mayor que cero.",
                ]);
            }

            /*
             * Comprobar el máximo antes de multiplicar.
             */
            $maxProducto = self::MAX_CENTAVOS * 1000 + 499;

            if (
                $cantidadMil > intdiv(
                    $maxProducto,
                    $precioCentavos
                )
            ) {
                throw ValidationException::withMessages([
                    'productos' =>
                        "El importe de {$producto->nombre} supera el máximo permitido.",
                ]);
            }

            /*
             * Redondeo del subtotal a centavos.
             */
            $subtotalCentavos = intdiv(
                $cantidadMil * $precioCentavos + 500,
                1000
            );

            if (
                $subtotalCentavos >
                self::MAX_CENTAVOS - $totalCentavos
            ) {
                throw ValidationException::withMessages([
                    'productos' =>
                        'El total de la promoción supera el máximo permitido.',
                ]);
            }

            $totalCentavos += $subtotalCentavos;

            $items[] = [
                'producto_id' => $productoId,

                'cantidad_requerida' =>
                    $this->decimalEscalado(
                        $cantidadMil,
                        3
                    ),
            ];
        }

        if ($totalCentavos <= 0) {
            throw ValidationException::withMessages([
                'productos' =>
                    'El total normal debe ser mayor que cero.',
            ]);
        }

        $valorDescuento = '0.00';

        /*
         * DESCUENTO POR PORCENTAJE
         */
        if ($modo === 'porcentaje') {
            $porcentaje = (int) round(
                (float) ($datos['valor_descuento'] ?? 0) * 100
            );

            if (
                $porcentaje < 1
                || $porcentaje > 10000
            ) {
                throw ValidationException::withMessages([
                    'valor_descuento' =>
                        'El porcentaje debe estar entre 0,01 y 100.',
                ]);
            }

            $descuentoCentavos = intdiv(
                $totalCentavos * $porcentaje + 5000,
                10000
            );

            $finalCentavos = $totalCentavos - $descuentoCentavos;

            $valorDescuento = $this->decimalEscalado(
                $porcentaje,
                2
            );
        }

        /*
         * DESCUENTO POR MONTO
         */
        elseif ($modo === 'monto') {
            $descuentoCentavos = (int) round(
                (float) ($datos['valor_descuento'] ?? 0) * 100
            );

            if (
                $descuentoCentavos <= 0
                || $descuentoCentavos > $totalCentavos
            ) {
                throw ValidationException::withMessages([
                    'valor_descuento' =>
                        'El descuento debe ser mayor que cero y no superar el total normal.',
                ]);
            }

            $finalCentavos = $totalCentavos - $descuentoCentavos;

            $valorDescuento = $this->decimalEscalado(
                $descuentoCentavos,
                2
            );
        }

        /*
         * PRECIO FINAL FIJO
         */
        else {
            $finalCentavos = (int) round(
                (float) ($datos['precio_promocional'] ?? 0) * 100
            );

            if (
                $finalCentavos <= 0
                || $finalCentavos > $totalCentavos
            ) {
                throw ValidationException::withMessages([
                    'precio_promocional' =>
                        'El precio final debe ser mayor que cero y no superar el total normal.',
                ]);
            }
        }

        return [
            'modo' => $modo,

            'valor_descuento' => $valorDescuento,

            'total_normal' => $this->decimalEscalado(
                $totalCentavos,
                2
            ),

            'precio_final' => $this->decimalEscalado(
                $finalCentavos,
                2
            ),

            'limite' => $datos['limite_tipo'] === 'cantidad'
                ? (int) $datos['limite_aplicaciones']
                : null,

            'items' => $items,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCTOS, PRECIO VIGENTE Y STOCK REGISTRADO
    |--------------------------------------------------------------------------
    */

    private function productosDisponibles()
    {
        $ahora = now();

        /*
         * Seleccionar el precio vigente más reciente
         * de cada producto.
         */
        $precios = DB::table('productos_precios')
            ->select([
                'producto_id',
                'precio_venta',
            ])
            ->selectRaw(
                'ROW_NUMBER() OVER (
                    PARTITION BY producto_id
                    ORDER BY fecha_inicio DESC, id DESC
                ) AS posicion'
            )
            ->where('estado', 1)
            ->where('fecha_inicio', '<=', $ahora)
            ->where(function ($query) use ($ahora) {
                $query
                    ->whereNull('fecha_fin')
                    ->orWhere('fecha_fin', '>', $ahora);
            });

        /*
         * Stock registrado en todos los almacenes.
         */
        $stock = DB::table('inventarios')
            ->select('producto_id')
            ->selectRaw(
                'SUM(COALESCE(cantidad, 0)) AS stock_actual'
            )
            ->groupBy('producto_id');

        return DB::table('productos as p')
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
            ->leftJoinSub(
                $precios,
                'precio',
                function ($join) {
                    $join
                        ->on(
                            'precio.producto_id',
                            '=',
                            'p.id'
                        )
                        ->where(
                            'precio.posicion',
                            '=',
                            1
                        );
                }
            )
            ->leftJoinSub(
                $stock,
                'stock',
                function ($join) {
                    $join->on(
                        'stock.producto_id',
                        '=',
                        'p.id'
                    );
                }
            )
            ->select([
                'p.id',
                'p.codigo_interno',
                'p.nombre',
                'p.estado',
                'um.abreviatura as unidad',
                'precio.precio_venta',
            ])
            ->selectRaw(
                'CASE
                    WHEN COALESCE(tp.permite_pesaje, 0) = 1
                    THEN 1
                    ELSE 0
                END AS es_pesable'
            )
            ->selectRaw(
                'COALESCE(stock.stock_actual, 0) AS stock_actual'
            )
            ->orderBy('p.nombre')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | GUARDAR PRODUCTOS DE LA PROMOCIÓN
    |--------------------------------------------------------------------------
    */

    private function guardarProductos(
        int $promocionId,
        array $items
    ): void {
        $filas = [];

        foreach ($items as $item) {
            $filas[] = [
                'promocion_id' => $promocionId,

                'producto_id' =>
                    $item['producto_id'],

                'cantidad_requerida' =>
                    $item['cantidad_requerida'],
            ];
        }

        if ($filas !== []) {
            DB::table('promocion_productos')
                ->insert($filas);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CONVERTIR ENTEROS ESCALADOS A DECIMALES
    |--------------------------------------------------------------------------
    */

    private function decimalEscalado(
        int $valor,
        int $decimales
    ): string {
        $factor = 10 ** $decimales;

        $entero = intdiv(
            $valor,
            $factor
        );

        $fraccion = str_pad(
            (string) ($valor % $factor),
            $decimales,
            '0',
            STR_PAD_LEFT
        );

        return $entero . '.' . $fraccion;
    }

    /*
    |--------------------------------------------------------------------------
    | GENERAR CÓDIGO
    |--------------------------------------------------------------------------
    */

    private function generarCodigo(): string
    {
        do {
            $codigo = 'PROMO-' . Str::upper(
                Str::random(10)
            );
        } while (
            DB::table('promociones')
                ->where('codigo', $codigo)
                ->exists()
        );

        return $codigo;
    }
}