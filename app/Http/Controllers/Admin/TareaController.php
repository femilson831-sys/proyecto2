<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TareaController extends Controller
{
    private const TIPOS = [
        'reposicion',
        'traslado',
        'devolucion',
        'inventario',
        'revision',
        'retiro',
        'preparacion',
    ];

    public function index(Request $request)
    {
        $filtros = $request->validate([
            'buscar' => [
                'nullable',
                'string',
                'max:100',
            ],
            'estado_id' => [
                'nullable',
                'integer',
                'exists:tarea_estados,id',
            ],
        ]);

        $buscar = trim($filtros['buscar'] ?? '');
        $estadoId = $filtros['estado_id'] ?? null;

        $tareas = $this->consultaTareas()
            ->when(
                $buscar !== '',
                function (Builder $query) use ($buscar) {
                    $texto = '%' . Str::lower($buscar) . '%';

                    $query->where(function (Builder $subquery) use ($texto) {
                        $subquery
                            ->whereRaw(
                                'LOWER(t.codigo) LIKE ?',
                                [$texto]
                            )
                            ->orWhereRaw(
                                "LOWER(COALESCE(t.titulo, '')) LIKE ?",
                                [$texto]
                            );
                    });
                }
            )
            ->when(
                $estadoId,
                function (Builder $query) use ($estadoId) {
                    $query->where('t.estado_id', $estadoId);
                }
            )
            ->orderByDesc('t.id')
            ->paginate(15)
            ->withQueryString();

        $estados = DB::table('tarea_estados')
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

        return view('admin.tareas.index', [
            'tareas' => $tareas,
            'estados' => $estados,
            'buscar' => $buscar,
            'estadoId' => $estadoId,
            'registro' => null,
            'detalles' => collect(),
            'asignados' => collect(),
        ]);
    }

    public function create()
    {
        $tipos = DB::table('tipos_tarea')
            ->whereIn('clave', self::TIPOS)
            ->orderBy('nombre')
            ->get([
                'id',
                'clave',
                'nombre',
            ]);

        $usuarios = $this->usuariosAsignables()->get();

        $almacenes = DB::table('almacenes')
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get([
                'id',
                'codigo',
                'nombre',
            ]);

        $ocupacion = DB::table('inventario_ubicaciones')
            ->select('ubicacion_id')
            ->selectRaw(
                'SUM(COALESCE(porcentaje_ocupacion, 0)) AS ocupado'
            )
            ->groupBy('ubicacion_id');

        $ubicaciones = DB::query()
            ->fromSub(
                $this->ubicacionesConAlmacen(),
                'u'
            )
            ->join(
                'almacenes as a',
                'a.id',
                '=',
                'u.almacen_id'
            )
            ->leftJoinSub(
                $ocupacion,
                'ocupacion',
                function ($join) {
                    $join->on(
                        'ocupacion.ubicacion_id',
                        '=',
                        'u.id'
                    );
                }
            )
            ->where('u.estado', 'activa')
            ->where('a.estado', 'activo')
            ->select([
                'u.id',
                'u.almacen_id',
                'u.codigo',
                'u.nombre',
            ])
            ->selectRaw(
                'COALESCE(ocupacion.ocupado, 0) AS ocupado'
            )
            ->orderBy('u.codigo')
            ->get();

        $proveedores = DB::table('proveedores')
            ->orderBy('razon_social')
            ->get([
                'id',
                'razon_social',
                'nombre_comercial',
            ]);

        $existencias = $this->existencias();

        return view('admin.tareas.create', compact(
            'tipos',
            'usuarios',
            'almacenes',
            'ubicaciones',
            'proveedores',
            'existencias'
        ));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'tipo_tarea_id' => [
                'required',
                'integer',
                Rule::exists('tipos_tarea', 'id')
                    ->whereIn('clave', self::TIPOS),
            ],

            'titulo' => [
                'required',
                'string',
                'max:180',
            ],

            'prioridad' => [
                'required',
                Rule::in([
                    'baja',
                    'normal',
                    'alta',
                    'urgente',
                ]),
            ],

            'usuario_id' => [
                'required',
                'integer',
                Rule::exists('usuarios', 'id')
                    ->where('estado', 'activo'),
            ],

            'fecha_limite' => [
                'nullable',
                'date',
                'after:now',
            ],

            'descripcion' => [
                'required',
                'string',
                'max:5000',
            ],

            'requiere_evidencia' => [
                'nullable',
                'boolean',
            ],

            'almacen_origen_id' => [
                'required',
                'integer',
                Rule::exists('almacenes', 'id')
                    ->where('estado', 'activo'),
            ],

            'ubicacion_origen_id' => [
                'required',
                'integer',
                Rule::exists('ubicaciones', 'id')
                    ->where('estado', 'activa'),
            ],

            'almacen_destino_id' => [
                'nullable',
                'integer',
                Rule::exists('almacenes', 'id')
                    ->where('estado', 'activo'),
            ],

            'ubicacion_destino_id' => [
                'nullable',
                'integer',
                Rule::exists('ubicaciones', 'id')
                    ->where('estado', 'activa'),
            ],

            'destino_tipo' => [
                'nullable',
                Rule::in([
                    'interno',
                    'proveedor',
                    'cuarentena',
                    'baja',
                ]),
            ],

            'proveedor_id' => [
                'nullable',
                'integer',
                'exists:proveedores,id',
            ],

            'motivo' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'alcance_revision' => [
                'nullable',
                Rule::in([
                    'productos',
                    'ubicacion',
                ]),
            ],

            'controles' => [
                'nullable',
                'array',
            ],

            'controles.*' => [
                Rule::in([
                    'empaque',
                    'vencimiento',
                    'limpieza',
                    'etiquetado',
                    'conservacion',
                ]),
            ],

            'items' => [
                'nullable',
                'array',
                'max:100',
            ],

            'items.*.producto_id' => [
                'required',
                'integer',
                'exists:productos,id',
            ],

            'items.*.lote_id' => [
                'required',
                'integer',
                'exists:lotes,id',
            ],

            'items.*.cantidad_solicitada' => [
                'nullable',
                'numeric',
                'decimal:0,3',
                'gt:0',
                'max:999999999.999',
            ],

            'items.*.porcentaje_ocupacion_destino' => [
                'nullable',
                'numeric',
                'decimal:0,2',
                'gt:0',
                'max:100',
            ],
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'exists' => 'La opción seleccionada en :attribute no es válida.',
            'in' => 'La opción seleccionada en :attribute no es válida.',
            'integer' => 'El campo :attribute debe ser un identificador válido.',
            'numeric' => 'El campo :attribute debe ser numérico.',
            'fecha_limite.after' => 'La fecha límite debe ser posterior a la fecha actual.',
            'items.max' => 'Puedes agregar hasta 100 productos por tarea.',
            'items.*.cantidad_solicitada.gt' => 'La cantidad solicitada debe ser mayor que cero.',
            'items.*.cantidad_solicitada.decimal' => 'La cantidad permite hasta tres decimales.',
            'items.*.porcentaje_ocupacion_destino.decimal' => 'El porcentaje permite hasta dos decimales.',
            'items.*.porcentaje_ocupacion_destino.max' => 'El porcentaje no puede superar el 100%.',
        ], [
            'tipo_tarea_id' => 'tipo de tarea',
            'titulo' => 'título',
            'prioridad' => 'prioridad',
            'usuario_id' => 'empleado responsable',
            'fecha_limite' => 'fecha límite',
            'descripcion' => 'instrucciones',
            'almacen_origen_id' => 'almacén de origen',
            'ubicacion_origen_id' => 'ubicación de origen',
            'almacen_destino_id' => 'almacén de destino',
            'ubicacion_destino_id' => 'ubicación de destino',
            'proveedor_id' => 'proveedor',
            'items.*.producto_id' => 'producto',
            'items.*.lote_id' => 'lote',
            'items.*.cantidad_solicitada' => 'cantidad solicitada',
            'items.*.porcentaje_ocupacion_destino' => 'porcentaje de ocupación',
        ]);

        $tipo = DB::table('tipos_tarea')
            ->where('id', $datos['tipo_tarea_id'])
            ->first();

        abort_unless($tipo, 404, 'El tipo de tarea no existe.');

        $usuarioDisponible = $this->usuariosAsignables()
            ->where('u.id', $datos['usuario_id'])
            ->exists();

        if (!$usuarioDisponible) {
            $this->error(
                'usuario_id',
                'Selecciona un usuario activo cuyo empleado esté disponible.'
            );
        }

        $clave = $tipo->clave;

        $esMovimiento = in_array($clave, [
            'reposicion',
            'traslado',
            'devolucion',
            'retiro',
            'preparacion',
        ], true);

        $revisionUbicacion = $clave === 'revision'
            && ($datos['alcance_revision'] ?? 'productos') === 'ubicacion';

        $destinoTipo = null;

        if ($clave === 'devolucion') {
            $destinoTipo = $datos['destino_tipo'] ?? '';

            if (!in_array($destinoTipo, ['interno', 'proveedor'], true)) {
                $this->error(
                    'destino_tipo',
                    'Selecciona una devolución interna o al proveedor.'
                );
            }
        }

        if ($clave === 'retiro') {
            $destinoTipo = $datos['destino_tipo'] ?? '';

            if (!in_array($destinoTipo, ['cuarentena', 'baja'], true)) {
                $this->error(
                    'destino_tipo',
                    'Selecciona si el retiro será a cuarentena o para solicitar una baja.'
                );
            }
        }

        $requiereDestino = in_array($clave, [
            'reposicion',
            'traslado',
            'preparacion',
        ], true)
            || ($clave === 'devolucion' && $destinoTipo === 'interno')
            || ($clave === 'retiro' && $destinoTipo === 'cuarentena');

        if (
            in_array($clave, ['devolucion', 'retiro'], true)
            && trim($datos['motivo'] ?? '') === ''
        ) {
            $this->error(
                'motivo',
                'Explica el motivo de esta operación.'
            );
        }

        if (
            $clave === 'devolucion'
            && $destinoTipo === 'proveedor'
            && empty($datos['proveedor_id'])
        ) {
            $this->error(
                'proveedor_id',
                'Selecciona el proveedor al que se devolverán los productos.'
            );
        }

        if ($clave === 'revision' && empty($datos['controles'])) {
            $this->error(
                'controles',
                'Selecciona al menos un aspecto que se debe revisar.'
            );
        }

        $origen = $this->ubicacionDelAlmacen(
            (int) $datos['ubicacion_origen_id'],
            (int) $datos['almacen_origen_id']
        );

        if (!$origen) {
            $this->error(
                'ubicacion_origen_id',
                'La ubicación de origen no pertenece al almacén seleccionado o no está activa.'
            );
        }

        $destino = null;

        if ($requiereDestino) {
            if (
                empty($datos['almacen_destino_id'])
                || empty($datos['ubicacion_destino_id'])
            ) {
                $this->error(
                    'ubicacion_destino_id',
                    'Selecciona el almacén y la ubicación de destino.'
                );
            }

            $destino = $this->ubicacionDelAlmacen(
                (int) $datos['ubicacion_destino_id'],
                (int) $datos['almacen_destino_id']
            );

            if (!$destino) {
                $this->error(
                    'ubicacion_destino_id',
                    'La ubicación de destino no pertenece al almacén seleccionado o no está activa.'
                );
            }

            if ((int) $origen->id === (int) $destino->id) {
                $this->error(
                    'ubicacion_destino_id',
                    'La ubicación de destino debe ser diferente al origen.'
                );
            }
        }

        $items = $datos['items'] ?? [];

        if (!$revisionUbicacion && count($items) === 0) {
            $this->error(
                'items',
                'Agrega al menos un producto y lote a la tarea.'
            );
        }

        if ($revisionUbicacion) {
            $items = [];
        }

        $tareaId = DB::transaction(function () use (
            $request,
            $datos,
            $tipo,
            $clave,
            $esMovimiento,
            $revisionUbicacion,
            $requiereDestino,
            $destinoTipo,
            $origen,
            $destino,
            $items
        ) {
            $disponibles = $this->existencias((int) $origen->id)
                ->keyBy(function ($fila) {
                    return $fila->producto_id . ':' . $fila->lote_id;
                });

            $lineas = [];
            $repetidos = [];
            $porcentajeTotal = 0.0;

            $adicionales = [
                'destino_tipo' => $destinoTipo,
                'motivo' => trim($datos['motivo'] ?? ''),
                'alcance_revision' => $clave === 'revision'
                    ? ($datos['alcance_revision'] ?? 'productos')
                    : null,
                'controles' => $clave === 'revision'
                    ? array_values(array_unique($datos['controles'] ?? []))
                    : [],
            ];

            $adicionalesJson = json_encode(
                $adicionales,
                JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
            );

            foreach ($items as $indice => $item) {
                $llave = $item['producto_id'] . ':' . $item['lote_id'];

                if (isset($repetidos[$llave])) {
                    $this->error(
                        "items.{$indice}.lote_id",
                        'Ese producto y lote ya están agregados a la tarea.'
                    );
                }

                $repetidos[$llave] = true;
                $stock = $disponibles->get($llave);

                if (!$stock) {
                    $this->error(
                        "items.{$indice}.lote_id",
                        'El producto y lote no están registrados en la ubicación de origen.'
                    );
                }

                $cantidad = null;

                if ($esMovimiento) {
                    if (
                        !isset($item['cantidad_solicitada'])
                        || $item['cantidad_solicitada'] === ''
                    ) {
                        $this->error(
                            "items.{$indice}.cantidad_solicitada",
                            'Ingresa la cantidad solicitada.'
                        );
                    }

                    $cantidad = (float) $item['cantidad_solicitada'];

                    $cantidadMil = (int) round(
                        $cantidad * 1000
                    );

                    $disponibleMil = (int) round(
                        (float) $stock->cantidad * 1000
                    );

                    if (
                        $cantidadMil <= 0
                        || $cantidadMil > $disponibleMil
                    ) {
                        $this->error(
                            "items.{$indice}.cantidad_solicitada",
                            "La cantidad de {$stock->producto} supera lo disponible en esa ubicación."
                        );
                    }

                    if (
                        !$stock->pesable
                        && $cantidadMil % 1000 !== 0
                    ) {
                        $this->error(
                            "items.{$indice}.cantidad_solicitada",
                            "{$stock->producto} se controla en cantidades enteras."
                        );
                    }
                }

                $porcentaje = $requiereDestino
                    ? ($item['porcentaje_ocupacion_destino'] ?? null)
                    : null;

                $porcentajeTotal += (float) ($porcentaje ?? 0);

                $lineas[] = [
                    'producto_id' => $stock->producto_id,
                    'lote_id' => $stock->lote_id,
                    'ubicacion_id' => $origen->id,
                    'origen_id' => $esMovimiento ? $origen->id : null,
                    'destino_id' => $destino?->id,
                    'cantidad_solicitada' => $cantidad,
                    'cantidad_sistema' => $stock->cantidad,
                    'cantidad_realizada' => null,
                    'diferencia' => null,
                    'resultado' => 'pendiente',
                    'porcentaje_ocupacion_destino' => $porcentaje,
                    'fecha_referencia' => now(),
                    'datos_adicionales' => $adicionalesJson,
                ];
            }

            if ($requiereDestino && $porcentajeTotal > 0) {
                $ocupado = (float) DB::table('inventario_ubicaciones')
                    ->where('ubicacion_id', $destino->id)
                    ->sum('porcentaje_ocupacion');

                $libre = max(0, 100 - $ocupado);

                if ($porcentajeTotal > $libre + 0.00001) {
                    $this->error(
                        'ubicacion_destino_id',
                        'Los porcentajes indicados superan el espacio libre actual de la ubicación de destino.'
                    );
                }
            }

            if ($revisionUbicacion) {
                $lineas[] = [
                    'producto_id' => null,
                    'lote_id' => null,
                    'ubicacion_id' => $origen->id,
                    'origen_id' => null,
                    'destino_id' => null,
                    'cantidad_solicitada' => null,
                    'cantidad_sistema' => null,
                    'cantidad_realizada' => null,
                    'diferencia' => null,
                    'resultado' => 'pendiente',
                    'porcentaje_ocupacion_destino' => null,
                    'fecha_referencia' => now(),
                    'datos_adicionales' => $adicionalesJson,
                ];
            }

            $estadoId = DB::table('tarea_estados')
                ->whereRaw(
                    'LOWER(TRIM(nombre)) = ?',
                    ['asignada']
                )
                ->value('id');

            if (!$estadoId) {
                $this->error(
                    'tipo_tarea_id',
                    'Falta el estado Asignada en la tabla tarea_estados.'
                );
            }

            do {
                $codigo = 'TAR-'
                    . now()->format('Ymd')
                    . '-'
                    . Str::upper(Str::random(10));
            } while (
                DB::table('tareas')
                    ->where('codigo', $codigo)
                    ->exists()
            );

            $usuarioActualId = $request->user()->getAuthIdentifier();

            $id = DB::table('tareas')->insertGetId([
                'codigo' => $codigo,
                'titulo' => trim($datos['titulo']),
                'tipo_tarea_id' => $tipo->id,
                'estado_id' => $estadoId,
                'creado_por' => $usuarioActualId,

                'almacen_id' => $datos['almacen_origen_id'],
                'almacen_origen_id' => $datos['almacen_origen_id'],

                'almacen_destino_id' => $requiereDestino
                    ? $datos['almacen_destino_id']
                    : null,

                'proveedor_id' => (
                    $clave === 'devolucion'
                    && $destinoTipo === 'proveedor'
                )
                    ? $datos['proveedor_id']
                    : null,

                'fecha_creacion' => now(),
                'fecha_limite' => $datos['fecha_limite'] ?? null,
                'prioridad' => $datos['prioridad'],
                'descripcion' => trim($datos['descripcion']),

                'requiere_evidencia' => $request->boolean(
                    'requiere_evidencia'
                ),
            ], 'id');

            $lineas = array_map(
                function (array $linea) use ($id) {
                    $linea['tarea_id'] = $id;

                    return $linea;
                },
                $lineas
            );

            DB::table('tarea_detalles')->insert($lineas);

            DB::table('tarea_asignaciones')->insert([
                'tarea_id' => $id,
                'usuario_id' => $datos['usuario_id'],
                'fecha_asignacion' => now(),
            ]);

            DB::table('tarea_historial')->insert([
                'tarea_id' => $id,
                'usuario_id' => $usuarioActualId,
                'accion' => 'creada',
                'descripcion' => 'Tarea creada y asignada a un responsable.',
                'datos' => json_encode([
                    'responsable_id' => (int) $datos['usuario_id'],
                    'tipo' => $clave,
                    'detalles' => count($lineas),
                ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'fecha' => now(),
            ]);

            return $id;
        });

        return redirect()
            ->route('admin.tareas.show', $tareaId)
            ->with(
                'success',
                'La tarea se creó y se asignó correctamente.'
            );
    }

    public function show(int $tarea)
    {
        $registro = $this->consultaTareas()
            ->where('t.id', $tarea)
            ->first();

        abort_unless(
            $registro,
            404,
            'La tarea no existe.'
        );

        $detalles = DB::table('tarea_detalles as td')
            ->leftJoin(
                'productos as p',
                'p.id',
                '=',
                'td.producto_id'
            )
            ->leftJoin(
                'lotes as l',
                'l.id',
                '=',
                'td.lote_id'
            )
            ->leftJoin(
                'unidades_medida as um',
                'um.id',
                '=',
                'p.unidad_medida_id'
            )
            ->leftJoin(
                'ubicaciones as u',
                'u.id',
                '=',
                'td.ubicacion_id'
            )
            ->leftJoin(
                'ubicaciones as o',
                'o.id',
                '=',
                'td.origen_id'
            )
            ->leftJoin(
                'ubicaciones as d',
                'd.id',
                '=',
                'td.destino_id'
            )
            ->where('td.tarea_id', $tarea)
            ->select([
                'td.*',
                'p.nombre as producto',
                'p.codigo_interno',
                'l.codigo_lote',
                'um.abreviatura as unidad',
                'u.codigo as ubicacion',
                'o.codigo as origen',
                'd.codigo as destino',
            ])
            ->orderBy('td.id')
            ->get();

        $asignados = DB::table('tarea_asignaciones as ta')
            ->join(
                'usuarios as u',
                'u.id',
                '=',
                'ta.usuario_id'
            )
            ->leftJoin(
                'empleados as e',
                'e.id',
                '=',
                'u.empleado_id'
            )
            ->where('ta.tarea_id', $tarea)
            ->selectRaw("
                COALESCE(
                    NULLIF(
                        TRIM(CONCAT(e.nombres, ' ', e.apellidos)),
                        ''
                    ),
                    u.username
                ) AS nombre
            ")
            ->get();

        return view('admin.tareas.index', [
            'registro' => $registro,
            'detalles' => $detalles,
            'asignados' => $asignados,
            'tareas' => null,
            'estados' => collect(),
            'buscar' => '',
            'estadoId' => null,
        ]);
    }

    private function consultaTareas(): Builder
    {
        return DB::table('tareas as t')
            ->join(
                'tipos_tarea as tt',
                'tt.id',
                '=',
                't.tipo_tarea_id'
            )
            ->join(
                'tarea_estados as te',
                'te.id',
                '=',
                't.estado_id'
            )
            ->leftJoin(
                'almacenes as ao',
                'ao.id',
                '=',
                't.almacen_origen_id'
            )
            ->leftJoin(
                'almacenes as ad',
                'ad.id',
                '=',
                't.almacen_destino_id'
            )
            ->leftJoin(
                'proveedores as pr',
                'pr.id',
                '=',
                't.proveedor_id'
            )
            ->select([
                't.*',
                'tt.nombre as tipo',
                'tt.clave as tipo_clave',
                'te.nombre as estado',
                'ao.nombre as almacen_origen',
                'ad.nombre as almacen_destino',
                'pr.razon_social as proveedor',
            ]);
    }

    private function usuariosAsignables(): Builder
    {
        return DB::table('usuarios as u')
            ->leftJoin(
                'empleados as e',
                'e.id',
                '=',
                'u.empleado_id'
            )
            ->where('u.estado', 'activo')
            ->where(function (Builder $query) {
                $query
                    ->whereNull('u.empleado_id')
                    ->orWhere(function (Builder $empleado) {
                        $empleado
                            ->whereNull('e.deleted_at')
                            ->where('e.estado', 'activo');
                    });
            })
            ->select([
                'u.id',
                'u.username',
            ])
            ->selectRaw("
                COALESCE(
                    NULLIF(
                        TRIM(CONCAT(e.nombres, ' ', e.apellidos)),
                        ''
                    ),
                    u.username
                ) AS nombre
            ")
            ->orderBy('nombre');
    }

    /**
     * Devuelve las ubicaciones con su almacén.
     *
     * Admite dos estructuras:
     *
     * 1. ubicaciones.almacen_id
     *
     * 2. ubicaciones -> niveles_estanteria -> estanterias
     *    -> pasillos -> zonas -> almacenes
     */
    private function ubicacionesConAlmacen(): Builder
    {
        if (Schema::hasColumn('ubicaciones', 'almacen_id')) {
            return DB::table('ubicaciones as ub')
                ->select([
                    'ub.id',
                    'ub.codigo',
                    'ub.nombre',
                    'ub.estado',
                    'ub.almacen_id',
                ]);
        }

        return DB::table('ubicaciones as ub')
            ->leftJoin(
                'niveles_estanteria as ne',
                'ne.id',
                '=',
                'ub.nivel_id'
            )
            ->leftJoin(
                'estanterias as es',
                'es.id',
                '=',
                'ne.estanteria_id'
            )
            ->leftJoin(
                'pasillos as pa',
                'pa.id',
                '=',
                'es.pasillo_id'
            )
            ->leftJoin(
                'zonas as zo',
                'zo.id',
                '=',
                'pa.zona_id'
            )
            ->select([
                'ub.id',
                'ub.codigo',
                'ub.nombre',
                'ub.estado',
                'zo.almacen_id as almacen_id',
            ]);
    }

    private function ubicacionDelAlmacen(
        int $ubicacionId,
        int $almacenId
    ): ?object {
        return DB::query()
            ->fromSub(
                $this->ubicacionesConAlmacen(),
                'u'
            )
            ->where('u.id', $ubicacionId)
            ->where('u.almacen_id', $almacenId)
            ->where('u.estado', 'activa')
            ->first();
    }

    private function existencias(
        ?int $ubicacionId = null
    ): Collection {
        return DB::table('inventario_ubicaciones as iu')
            ->join(
                'inventarios as i',
                'i.id',
                '=',
                'iu.inventario_id'
            )
            ->joinSub(
                $this->ubicacionesConAlmacen(),
                'u',
                function ($join) {
                    $join->on(
                        'u.id',
                        '=',
                        'iu.ubicacion_id'
                    );
                }
            )
            ->join(
                'productos as p',
                'p.id',
                '=',
                'i.producto_id'
            )
            ->join(
                'lotes as l',
                'l.id',
                '=',
                'iu.lote_id'
            )
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
            ->where('u.estado', 'activa')
            ->where('p.estado', 'activo')
            ->whereColumn(
                'i.almacen_id',
                'u.almacen_id'
            )
            ->whereColumn(
                'l.producto_id',
                'p.id'
            )
            ->when(
                $ubicacionId !== null,
                function (Builder $query) use ($ubicacionId) {
                    $query->where(
                        'u.id',
                        $ubicacionId
                    );
                }
            )
            ->select([
                'u.id as ubicacion_id',
                'i.almacen_id',
                'p.id as producto_id',
                'p.nombre as producto',
                'p.codigo_interno',
                'l.id as lote_id',
                'l.codigo_lote',
                'l.fecha_vencimiento',
                'um.abreviatura as unidad',
                'tp.permite_pesaje',
            ])
            ->selectRaw(
                'SUM(COALESCE(iu.cantidad, 0)) AS cantidad'
            )
            ->groupBy([
                'u.id',
                'i.almacen_id',
                'p.id',
                'p.nombre',
                'p.codigo_interno',
                'l.id',
                'l.codigo_lote',
                'l.fecha_vencimiento',
                'um.abreviatura',
                'tp.permite_pesaje',
            ])
            ->orderBy('p.nombre')
            ->orderBy('l.codigo_lote')
            ->get()
            ->map(function ($fila) {
                $fila->pesable = in_array(
                    $fila->permite_pesaje,
                    [
                        true,
                        1,
                        '1',
                        't',
                        'true',
                    ],
                    true
                );

                unset($fila->permite_pesaje);

                return $fila;
            });
    }

    private function error(
        string $campo,
        string $mensaje
    ): never {
        throw ValidationException::withMessages([
            $campo => $mensaje,
        ]);
    }
}