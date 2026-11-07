<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AlertaInventarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class AlertaController extends Controller
{
    private const CLAVE_SINCRONIZACION =
        'nanos:alertas:sonido:sincronizacion';

    /*
    |--------------------------------------------------------------------------
    | Pantalla principal
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request,
        AlertaInventarioService $servicio
    ) {
        $datos = $request->validate([
            'buscar' => [
                'nullable',
                'string',
                'max:100',
            ],

            'tipo_alerta_id' => [
                'nullable',
                'integer',
                'exists:tipos_alerta,id',
            ],

            'almacen_id' => [
                'nullable',
                'integer',
                'exists:almacenes,id',
            ],

            'nivel' => [
                'nullable',
                Rule::in([
                    'todos',
                    'critica',
                    'alta',
                    'media',
                    'baja',
                ]),
            ],

            'estado' => [
                'nullable',
                Rule::in([
                    'pendiente',
                    'resuelta',
                    'todos',
                ]),
            ],

            'lectura' => [
                'nullable',
                Rule::in([
                    'todos',
                    'leida',
                    'no_leida',
                ]),
            ],
        ], [
            'in' =>
                'Selecciona una opción válida para :attribute.',

            'exists' =>
                'El registro seleccionado no existe.',

            'max' =>
                'La búsqueda admite hasta 100 caracteres.',

            'integer' =>
                'Selecciona una opción válida.',

            'string' =>
                'La búsqueda debe contener texto.',
        ]);

        $servicio->sincronizar();

        Cache::put(
            self::CLAVE_SINCRONIZACION,
            true,
            60
        );

        $usuarioId = $request->user()->getAuthIdentifier();

        $filtros = [
            'buscar' => trim($datos['buscar'] ?? ''),

            'tipo_alerta_id' => isset($datos['tipo_alerta_id'])
                ? (int) $datos['tipo_alerta_id']
                : null,

            'almacen_id' => isset($datos['almacen_id'])
                ? (int) $datos['almacen_id']
                : null,

            'nivel' => $datos['nivel'] ?? 'todos',

            'estado' => $datos['estado'] ?? 'pendiente',

            'lectura' => $datos['lectura'] ?? 'todos',
        ];

        /*
         * Una fila de lectura por alerta en la consulta,
         * aunque existan registros antiguos duplicados.
         */
        $lecturas = DB::table('alerta_usuarios')
            ->where('usuario_id', $usuarioId)
            ->select('alerta_id')
            ->selectRaw(
                'MAX(COALESCE(leida, 0)) AS leida'
            )
            ->groupBy('alerta_id');

        $base = DB::table('alertas as a')
            ->join(
                'tipos_alerta as ta',
                'ta.id',
                '=',
                'a.tipo_alerta_id'
            )
            ->leftJoinSub(
                $lecturas,
                'lectura',
                function ($join) {
                    $join->on(
                        'lectura.alerta_id',
                        '=',
                        'a.id'
                    );
                }
            );

        /*
         * Los indicadores muestran el resumen general,
         * antes de aplicar los filtros de la lista.
         */
        $resumen = [
            'pendientes' => (clone $base)
                ->where('a.estado', 'pendiente')
                ->count(),

            'sin_leer' => (clone $base)
                ->where('a.estado', 'pendiente')
                ->whereRaw(
                    'COALESCE(lectura.leida, 0) = 0'
                )
                ->count(),

            'criticas' => (clone $base)
                ->where('a.estado', 'pendiente')
                ->where('ta.nivel', 'critica')
                ->count(),

            'resueltas' => (clone $base)
                ->where('a.estado', 'resuelta')
                ->count(),
        ];

        $consulta = (clone $base)
            ->leftJoin(
                'productos as p',
                'p.id',
                '=',
                'a.producto_id'
            )
            ->leftJoin(
                'almacenes as al',
                'al.id',
                '=',
                'a.almacen_id'
            )
            ->leftJoin(
                'lotes as l',
                'l.id',
                '=',
                'a.lote_id'
            )
            ->leftJoin(
                'tareas as t',
                't.id',
                '=',
                'a.tarea_id'
            )
            ->select([
                'a.*',
                'ta.nombre as tipo_nombre',
                'ta.nivel',
                'p.nombre as producto',
                'p.codigo_interno',
                'al.nombre as almacen',
                'l.codigo_lote',
                't.codigo as codigo_tarea',
            ])
            ->selectRaw(
                'COALESCE(lectura.leida, 0) AS leida'
            );

        if ($filtros['buscar'] !== '') {
            $buscar = '%' . $filtros['buscar'] . '%';

            $consulta->where(function ($query) use ($buscar) {
                $query
                    ->where('a.titulo', 'ilike', $buscar)
                    ->orWhere('a.mensaje', 'ilike', $buscar)
                    ->orWhere('p.nombre', 'ilike', $buscar)
                    ->orWhere('p.codigo_interno', 'ilike', $buscar)
                    ->orWhere('l.codigo_lote', 'ilike', $buscar)
                    ->orWhere('t.codigo', 'ilike', $buscar);
            });
        }

        if ($filtros['tipo_alerta_id'] !== null) {
            $consulta->where(
                'a.tipo_alerta_id',
                $filtros['tipo_alerta_id']
            );
        }

        if ($filtros['almacen_id'] !== null) {
            $consulta->where(
                'a.almacen_id',
                $filtros['almacen_id']
            );
        }

        if ($filtros['nivel'] !== 'todos') {
            $consulta->where(
                'ta.nivel',
                $filtros['nivel']
            );
        }

        if ($filtros['estado'] !== 'todos') {
            $consulta->where(
                'a.estado',
                $filtros['estado']
            );
        }

        if ($filtros['lectura'] === 'leida') {
            $consulta->whereRaw(
                'COALESCE(lectura.leida, 0) = 1'
            );
        } elseif ($filtros['lectura'] === 'no_leida') {
            $consulta->whereRaw(
                'COALESCE(lectura.leida, 0) = 0'
            );
        }

        $alertas = $consulta
            ->orderByRaw("
                CASE ta.nivel
                    WHEN 'critica' THEN 1
                    WHEN 'alta' THEN 2
                    WHEN 'media' THEN 3
                    ELSE 4
                END
            ")
            ->orderByDesc('a.fecha_generacion')
            ->orderByDesc('a.id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.alertas.index', [
            'alertas' => $alertas,
            'resumen' => $resumen,
            'filtros' => $filtros,

            'tipos' => DB::table('tipos_alerta')
                ->orderBy('nombre')
                ->get([
                    'id',
                    'nombre',
                ]),

            'almacenes' => DB::table('almacenes')
                ->orderBy('nombre')
                ->get([
                    'id',
                    'codigo',
                    'nombre',
                ]),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar manualmente
    |--------------------------------------------------------------------------
    */

    public function actualizar(
        AlertaInventarioService $servicio
    ) {
        $servicio->sincronizar();

        Cache::put(
            self::CLAVE_SINCRONIZACION,
            true,
            60
        );

        return redirect()
            ->route('admin.alertas.index')
            ->with(
                'success',
                'Alertas actualizadas correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Consultar alertas para el sonido
    |--------------------------------------------------------------------------
    */

    public function sonido(
        Request $request,
        AlertaInventarioService $servicio
    ) {
        /*
         * Las consultas del navegador comparten esta referencia.
         * Se revisan las condiciones como máximo una vez por minuto
         * mediante este endpoint.
         */
        if (Cache::add(
            self::CLAVE_SINCRONIZACION,
            true,
            60
        )) {
            try {
                $servicio->sincronizar();
            } catch (Throwable $error) {
                Cache::forget(
                    self::CLAVE_SINCRONIZACION
                );

                throw $error;
            }
        }

        $usuarioId = $request->user()->getAuthIdentifier();

        $alertas = DB::table('alertas as a')
            ->where('a.estado', 'pendiente')
            ->whereNotExists(function ($query) use ($usuarioId) {
                $query
                    ->selectRaw('1')
                    ->from('alerta_usuarios as au')
                    ->whereColumn(
                        'au.alerta_id',
                        'a.id'
                    )
                    ->where(
                        'au.usuario_id',
                        $usuarioId
                    )
                    ->where('au.leida', 1);
            })
            ->orderByDesc('a.fecha_generacion')
            ->orderByDesc('a.id')
            ->get([
                'a.id',
                'a.fecha_generacion',
            ]);

        /*
         * Una alerta reabierta cambia su fecha de generación.
         * Así puede reconocerse nuevamente como una notificación.
         */
        $identificadores = $alertas
            ->map(function ($alerta): string {
                return sha1(
                    $alerta->id
                    . '|'
                    . ($alerta->fecha_generacion ?? '')
                );
            })
            ->values()
            ->all();

        return response()
            ->json([
                'total' => $alertas->count(),
                'identificadores' => $identificadores,
            ])
            ->header(
                'Cache-Control',
                'no-store, private'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Marcar una alerta como leída para el usuario actual
    |--------------------------------------------------------------------------
    */

    public function marcarLeida(
        Request $request,
        int $alerta
    ) {
        $usuarioId = $request->user()->getAuthIdentifier();

        DB::transaction(function () use (
            $alerta,
            $usuarioId
        ): void {
            $registro = DB::table('alertas')
                ->where('id', $alerta)
                ->lockForUpdate()
                ->first(['id']);

            abort_unless(
                $registro,
                404,
                'La alerta no existe.'
            );

            DB::table('alerta_usuarios')->updateOrInsert(
                [
                    'alerta_id' => $alerta,
                    'usuario_id' => $usuarioId,
                ],
                [
                    'leida' => 1,
                    'fecha_lectura' => now(),
                ]
            );
        }, 3);

        return back()->with(
            'success',
            'Alerta marcada como leída.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Marcar todas las pendientes como leídas
    |--------------------------------------------------------------------------
    */

    public function marcarTodasLeidas(Request $request)
    {
        $usuarioId = $request->user()->getAuthIdentifier();

        DB::transaction(function () use ($usuarioId): void {
            $ids = DB::table('alertas')
                ->where('estado', 'pendiente')
                ->orderBy('id')
                ->lockForUpdate()
                ->pluck('id')
                ->all();

            if ($ids === []) {
                return;
            }

            $existentes = DB::table('alerta_usuarios')
                ->where('usuario_id', $usuarioId)
                ->whereIn('alerta_id', $ids)
                ->pluck('alerta_id')
                ->all();

            $fecha = now();

            DB::table('alerta_usuarios')
                ->where('usuario_id', $usuarioId)
                ->whereIn('alerta_id', $ids)
                ->update([
                    'leida' => 1,
                    'fecha_lectura' => $fecha,
                ]);

            $nuevosIds = array_diff(
                $ids,
                $existentes
            );

            foreach (
                array_chunk($nuevosIds, 500)
                as $grupo
            ) {
                $filas = [];

                foreach ($grupo as $id) {
                    $filas[] = [
                        'alerta_id' => $id,
                        'usuario_id' => $usuarioId,
                        'leida' => 1,
                        'fecha_lectura' => $fecha,
                    ];
                }

                DB::table('alerta_usuarios')
                    ->insert($filas);
            }
        }, 3);

        return back()->with(
            'success',
            'Todas las alertas pendientes fueron marcadas como leídas para tu usuario.'
        );
    }
}