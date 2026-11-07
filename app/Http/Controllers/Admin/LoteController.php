<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class LoteController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim((string) $request->get('buscar', ''));

        $lotes = Lote::query()
            ->with([
                'producto.codigoBarraPrincipal',
            ])
            ->withCount([
                'escaneosAceptados',
            ])
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where(
                        'codigo_lote',
                        'like',
                        '%' . $buscar . '%'
                    )
                    ->orWhereHas(
                        'producto',
                        function ($productoQuery) use ($buscar) {
                            $productoQuery
                                ->where(
                                    'nombre',
                                    'like',
                                    '%' . $buscar . '%'
                                )
                                ->orWhere(
                                    'codigo_interno',
                                    'like',
                                    '%' . $buscar . '%'
                                );
                        }
                    );
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.lotes.index',
            compact('lotes', 'buscar')
        );
    }

    public function create()
    {
        $productos = Producto::query()
            ->with('codigoBarraPrincipal')
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        return view(
            'admin.lotes.create',
            compact('productos')
        );
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        try {
            $lote = DB::transaction(function () use ($datos) {

                /*
                 * Se usa temporalmente un código único
                 * para obtener el ID autoincremental.
                 */
                $lote = Lote::create([
                    'producto_id' => $datos['producto_id'],
                    'recepcion_detalle_id' => null,
                    'codigo_lote' => uniqid('TMP-LOTE-', true),
                    'fecha_fabricacion' =>
                        $datos['fecha_fabricacion'] ?? null,
                    'fecha_vencimiento' =>
                        $datos['fecha_vencimiento'] ?? null,

                    /*
                     * Cantidad que esperamos recibir.
                     */
                    'cantidad_inicial' =>
                        $datos['cantidad_inicial'],

                    /*
                     * Todavía no está confirmada físicamente.
                     * Se irá incrementando mediante los escaneos.
                     */
                    'cantidad_actual' => 0,
                ]);

                $lote->codigo_lote =
                    'LOT-' .
                    str_pad(
                        (string) $lote->id,
                        6,
                        '0',
                        STR_PAD_LEFT
                    );

                $lote->save();

                return $lote;
            });

            return redirect()
                ->route('admin.lotes.show', $lote)
                ->with(
                    'success',
                    'Lote creado correctamente. Falta realizar el escaneo físico de las unidades.'
                );

        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'No se pudo crear el lote.'
                );
        }
    }

    public function show(Lote $lote)
    {
        $lote->load([
            'producto.codigoBarraPrincipal',
            'escaneos' => function ($query) {
                $query
                    ->with([
                        'usuario',
                        'empleado',
                    ])
                    ->orderByDesc('fecha_escaneo')
                    ->orderByDesc('id');
            },
        ]);

        $cantidadEscaneada = $lote
            ->escaneos
            ->where('estado', 'aceptado')
            ->count();

        $cantidadEsperada =
            (float) $lote->cantidad_inicial;

        $cantidadPendiente = max(
            0,
            $cantidadEsperada - $cantidadEscaneada
        );

        $porcentaje = $cantidadEsperada > 0
            ? min(
                100,
                round(
                    ($cantidadEscaneada / $cantidadEsperada) * 100,
                    2
                )
            )
            : 0;

        return view(
            'admin.lotes.show',
            compact(
                'lote',
                'cantidadEscaneada',
                'cantidadEsperada',
                'cantidadPendiente',
                'porcentaje'
            )
        );
    }

    public function edit(Lote $lote)
    {
        /*
         * Una vez que ya existen escaneos,
         * no permitimos cambiar el producto.
         */
        $tieneEscaneos = $lote
            ->escaneosAceptados()
            ->exists();

        $productos = Producto::query()
            ->with('codigoBarraPrincipal')
            ->where(function ($query) use ($lote) {
                $query
                    ->where('estado', 'activo')
                    ->orWhere('id', $lote->producto_id);
            })
            ->orderBy('nombre')
            ->get();

        return view(
            'admin.lotes.edit',
            compact(
                'lote',
                'productos',
                'tieneEscaneos'
            )
        );
    }

    public function update(
        Request $request,
        Lote $lote
    ) {
        $datos = $this->validar(
            $request,
            $lote
        );

        $cantidadEscaneada = $lote
            ->escaneosAceptados()
            ->count();

        /*
         * Nunca permitir que el administrador reduzca
         * la cantidad esperada por debajo de lo que
         * físicamente ya fue escaneado.
         */
        if (
            (float) $datos['cantidad_inicial'] <
            $cantidadEscaneada
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'cantidad_inicial' =>
                        'La cantidad esperada no puede ser menor a las ' .
                        $cantidadEscaneada .
                        ' unidades que ya fueron escaneadas.',
                ]);
        }

        /*
         * Si ya se comenzó la recepción,
         * el producto no puede cambiarse.
         */
        if (
            $cantidadEscaneada > 0 &&
            (int) $datos['producto_id'] !==
            (int) $lote->producto_id
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'producto_id' =>
                        'No se puede cambiar el producto porque el lote ya tiene unidades escaneadas.',
                ]);
        }

        try {
            DB::transaction(function () use (
                $datos,
                $lote,
                $cantidadEscaneada
            ) {
                $lote->update([
                    'producto_id' =>
                        $datos['producto_id'],

                    'fecha_fabricacion' =>
                        $datos['fecha_fabricacion'] ?? null,

                    'fecha_vencimiento' =>
                        $datos['fecha_vencimiento'] ?? null,

                    'cantidad_inicial' =>
                        $datos['cantidad_inicial'],

                    /*
                     * cantidad_actual representa las
                     * unidades físicamente confirmadas.
                     */
                    'cantidad_actual' =>
                        $cantidadEscaneada,
                ]);
            });

            return redirect()
                ->route(
                    'admin.lotes.show',
                    $lote
                )
                ->with(
                    'success',
                    'Lote actualizado correctamente.'
                );

        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'No se pudo actualizar el lote.'
                );
        }
    }

    private function validar(
        Request $request,
        ?Lote $lote = null
    ): array {
        return $request->validate(
            [
                'producto_id' => [
                    'required',
                    'integer',
                    Rule::exists(
                        'productos',
                        'id'
                    ),
                ],

                'fecha_fabricacion' => [
                    'nullable',
                    'date',
                ],

                'fecha_vencimiento' => [
                    'nullable',
                    'date',
                    'after_or_equal:fecha_fabricacion',
                ],

                'cantidad_inicial' => [
                    'required',
                    'numeric',
                    'gt:0',
                ],
            ],
            [
                'producto_id.required' =>
                    'Debe seleccionar un producto.',

                'producto_id.exists' =>
                    'El producto seleccionado no existe.',

                'cantidad_inicial.required' =>
                    'Debe indicar la cantidad esperada.',

                'cantidad_inicial.numeric' =>
                    'La cantidad debe ser numérica.',

                'cantidad_inicial.gt' =>
                    'La cantidad debe ser mayor a cero.',

                'fecha_fabricacion.date' =>
                    'La fecha de fabricación no es válida.',

                'fecha_vencimiento.date' =>
                    'La fecha de vencimiento no es válida.',

                'fecha_vencimiento.after_or_equal' =>
                    'La fecha de vencimiento no puede ser anterior a la fecha de fabricación.',
            ]
        );
    }
}