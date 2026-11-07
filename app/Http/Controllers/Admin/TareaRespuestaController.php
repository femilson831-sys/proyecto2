<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class TareaRespuestaController extends Controller
{
    public function misTareas(Request $request)
    {
        $usuarioId = $request->user()->getAuthIdentifier();

        $tareas = DB::table('tareas as t')
            ->join('tipos_tarea as tt', 'tt.id', '=', 't.tipo_tarea_id')
            ->join('tarea_estados as te', 'te.id', '=', 't.estado_id')
            ->whereExists(function (Builder $query) use ($usuarioId) {
                $query->selectRaw('1')
                    ->from('tarea_asignaciones as ta')
                    ->whereColumn('ta.tarea_id', 't.id')
                    ->where('ta.usuario_id', $usuarioId);
            })
            ->select([
                't.id',
                't.codigo',
                't.titulo',
                't.prioridad',
                't.fecha_limite',
                'tt.nombre as tipo',
                'te.nombre as estado_nombre',
            ])
            ->orderByDesc('t.id')
            ->paginate(15);

        return view('tareas.responder', [
            'tareas' => $tareas,
            'tarea' => null,
            'detalles' => collect(),
            'puedeResponder' => false,
        ]);
    }

    public function formulario(Request $request, int $tarea)
    {
        $usuarioId = $request->user()->getAuthIdentifier();
        $registro = $this->tareaAsignada($tarea, $usuarioId);

        $yaRespondio = DB::table('tarea_asignaciones')
            ->where('tarea_id', $tarea)
            ->where('usuario_id', $usuarioId)
            ->whereNotNull('fecha_finalizacion')
            ->exists();

        $estado = mb_strtolower(trim($registro->estado_nombre));

        $cerrada = in_array($estado, [
            'completada',
            'cancelada',
        ], true);

        $puedeResponder = !$cerrada
            && (!$yaRespondio || $estado === 'reconteo solicitado');

        return view('tareas.responder', [
            'tareas' => null,
            'tarea' => $registro,
            'detalles' => $this->detalles($tarea),
            'puedeResponder' => $puedeResponder,
        ]);
    }

    public function guardar(Request $request, int $tarea)
    {
        $usuarioId = $request->user()->getAuthIdentifier();

        $this->tareaAsignada($tarea, $usuarioId);

        $datos = $request->validate([
            'token_respuesta' => ['required', 'uuid'],
            'resultado' => ['required', 'in:realizada,parcial,no_realizada'],
            'mensaje' => ['required', 'string', 'max:3000'],
            'observaciones' => ['nullable', 'string', 'max:3000'],

            'detalles' => ['nullable', 'array'],
            'detalles.*.cantidad' => [
                'nullable',
                'numeric',
                'decimal:0,3',
                'min:0',
                'max:999999999.999',
            ],

            'evidencias' => ['nullable', 'array', 'max:5'],
            'evidencias.*' => [
                'file',
                'mimetypes:image/jpeg,image/png,image/webp,application/pdf',
                'max:5120',
            ],
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'resultado.in' => 'Selecciona un resultado válido.',
            'detalles.*.cantidad.numeric' => 'Las cantidades deben ser numéricas.',
            'detalles.*.cantidad.decimal' => 'Las cantidades permiten hasta tres decimales.',
            'detalles.*.cantidad.min' => 'Las cantidades no pueden ser negativas.',
            'evidencias.max' => 'Puedes adjuntar hasta cinco archivos.',
            'evidencias.*.max' => 'Cada archivo puede pesar hasta 5 MB.',
            'evidencias.*.mimetypes' => 'Adjunta fotografías JPG, PNG, WEBP o documentos PDF.',
        ], [
            'mensaje' => 'respuesta',
            'resultado' => 'resultado',
        ]);

        $archivosGuardados = [];

        try {
            DB::transaction(function () use (
                $request,
                $tarea,
                $usuarioId,
                $datos,
                &$archivosGuardados
            ) {
                // Evita procesar simultáneamente dos envíos de la misma tarea.
                DB::table('tareas')
                    ->where('id', $tarea)
                    ->lockForUpdate()
                    ->first();

                $registro = $this->tareaAsignada($tarea, $usuarioId);

                $duplicada = DB::table('tarea_historial')
                    ->where('tarea_id', $tarea)
                    ->where('usuario_id', $usuarioId)
                    ->where('accion', 'respuesta_empleado')
                    ->where('datos->token', $datos['token_respuesta'])
                    ->exists();

                if ($duplicada) {
                    return;
                }

                $estado = mb_strtolower(trim($registro->estado_nombre));

                if (in_array($estado, ['completada', 'cancelada'], true)) {
                    $this->error(
                        'resultado',
                        'Esta tarea está cerrada y no admite respuestas.'
                    );
                }

                $yaRespondio = DB::table('tarea_asignaciones')
                    ->where('tarea_id', $tarea)
                    ->where('usuario_id', $usuarioId)
                    ->whereNotNull('fecha_finalizacion')
                    ->exists();

                if ($yaRespondio && $estado !== 'reconteo solicitado') {
                    $this->error(
                        'resultado',
                        'Ya enviaste una respuesta. Espera la revisión del administrador.'
                    );
                }

                $noRealizada = $datos['resultado'] === 'no_realizada';
                $esRevision = $registro->tipo_clave === 'revision';
                $esInventario = $registro->tipo_clave === 'inventario';

                $observaciones = trim($datos['observaciones'] ?? '');
                $conObservaciones = $datos['resultado'] !== 'realizada'
                    || $observaciones !== '';

                $detalles = $this->detalles($tarea);
                $detalleRespuesta = [];
                $hayDiferencias = false;

                foreach ($detalles as $detalle) {
                    $cantidadIngresada = $datos['detalles'][$detalle->id]['cantidad']
                        ?? null;

                    $necesitaCantidad = !$esRevision
                        && $detalle->producto_id !== null;

                    if (
                        !$noRealizada
                        && $necesitaCantidad
                        && $datos['resultado'] === 'realizada'
                        && $cantidadIngresada === null
                    ) {
                        $this->error(
                            "detalles.{$detalle->id}.cantidad",
                            'Registra la cantidad de cada producto.'
                        );
                    }

                    $realizada = null;
                    $diferencia = null;

                    $referencia = $esInventario
                        ? $detalle->cantidad_sistema
                        : $detalle->cantidad_solicitada;

                    if (
                        !$noRealizada
                        && $necesitaCantidad
                        && $cantidadIngresada !== null
                    ) {
                        $mil = (int) round((float) $cantidadIngresada * 1000);

                        if (!$detalle->pesable && $mil % 1000 !== 0) {
                            $this->error(
                                "detalles.{$detalle->id}.cantidad",
                                "{$detalle->producto} debe registrarse en cantidades enteras."
                            );
                        }

                        $realizada = number_format($mil / 1000, 3, '.', '');

                        if ($referencia !== null) {
                            $referenciaMil = (int) round(
                                (float) $referencia * 1000
                            );

                            $diferenciaMil = $mil - $referenciaMil;

                            $diferencia = number_format(
                                $diferenciaMil / 1000,
                                3,
                                '.',
                                ''
                            );

                            if ($diferenciaMil !== 0) {
                                $hayDiferencias = true;
                            }
                        }
                    }

                    $resultadoDetalle = 'reportado';

                    if ($noRealizada) {
                        $resultadoDetalle = 'no_realizada';
                    } elseif ($necesitaCantidad && $realizada === null) {
                        $resultadoDetalle = 'sin_reportar';
                        $conObservaciones = true;
                    } elseif ($necesitaCantidad && $referencia === null) {
                        $resultadoDetalle = 'sin_referencia';
                        $conObservaciones = true;
                    } elseif ($diferencia !== null) {
                        $resultadoDetalle = (float) $diferencia === 0.0
                            ? 'coincide_referencia'
                            : 'con_diferencia';
                    }

                    DB::table('tarea_detalles')
                        ->where('id', $detalle->id)
                        ->where('tarea_id', $tarea)
                        ->update([
                            'cantidad_realizada' => $realizada,
                            'diferencia' => $diferencia,
                            'resultado' => $resultadoDetalle,
                            'observacion_resultado' => $observaciones ?: null,
                            'fecha_realizacion' => now(),
                        ]);

                    // Conserva una copia del resultado en cada respuesta.
                    $detalleRespuesta[] = [
                        'producto' => $detalle->producto
                            ?: 'Revisión de ubicación',
                        'lote' => $detalle->codigo_lote,
                        'unidad' => $detalle->unidad,
                        'referencia' => $referencia,
                        'fecha_referencia' => $detalle->fecha_referencia,
                        'realizada' => $realizada,
                        'diferencia' => $diferencia,
                        'resultado' => $resultadoDetalle,
                    ];
                }

                $conObservaciones = $conObservaciones || $hayDiferencias;

                if (
                    ($hayDiferencias || $datos['resultado'] !== 'realizada')
                    && $observaciones === ''
                ) {
                    $this->error(
                        'observaciones',
                        'Explica las diferencias o por qué la tarea no se realizó completamente.'
                    );
                }

                $archivos = $request->file('evidencias', []);

                $exigeEvidencia = in_array(
                    $registro->requiere_evidencia,
                    [true, 1, '1', 't', 'true'],
                    true
                );

                if ($exigeEvidencia && !$noRealizada && count($archivos) === 0) {
                    $this->error(
                        'evidencias',
                        'Esta tarea requiere al menos una fotografía o documento.'
                    );
                }

                $evidencias = [];

                foreach ($archivos as $archivo) {
                    $ruta = $archivo->store("tareas/{$tarea}", 'public');

                    if (!$ruta) {
                        throw new \RuntimeException(
                            'No se pudo guardar uno de los archivos.'
                        );
                    }

                    $archivosGuardados[] = $ruta;

                    $evidenciaId = DB::table('tarea_evidencias')->insertGetId([
                        'tarea_id' => $tarea,
                        'usuario_id' => $usuarioId,
                        'archivo' => $ruta,
                        'descripcion' => 'Evidencia de respuesta del empleado.',
                        'fecha' => now(),
                    ], 'id');

                    $evidencias[] = [
                        'id' => $evidenciaId,
                        'archivo' => $ruta,
                    ];
                }

                $estadoRevisionId = DB::table('tarea_estados')
                    ->whereRaw(
                        'LOWER(TRIM(nombre)) = ?',
                        ['pendiente de revisión']
                    )
                    ->value('id');

                if (!$estadoRevisionId) {
                    $this->error(
                        'resultado',
                        'Falta el estado Pendiente de revisión en tarea_estados.'
                    );
                }

                $alertaId = $this->notificarRespuesta(
                    $registro,
                    $request->user()->username ?? 'Empleado',
                    $conObservaciones
                );

                DB::table('tarea_historial')->insert([
                    'tarea_id' => $tarea,
                    'usuario_id' => $usuarioId,
                    'accion' => 'respuesta_empleado',
                    'descripcion' => trim($datos['mensaje']),
                    'datos' => json_encode([
                        'token' => $datos['token_respuesta'],
                        'alerta_id' => $alertaId,
                        'resultado' => $datos['resultado'],
                        'con_observaciones' => $conObservaciones,
                        'observaciones' => $observaciones,
                        'detalles' => $detalleRespuesta,
                        'evidencias' => $evidencias,
                    ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                    'fecha' => now(),
                ]);

                DB::table('tarea_asignaciones')
                    ->where('tarea_id', $tarea)
                    ->where('usuario_id', $usuarioId)
                    ->update([
                        'fecha_finalizacion' => now(),
                    ]);

                DB::table('tareas')
                    ->where('id', $tarea)
                    ->update([
                        'estado_id' => $estadoRevisionId,
                    ]);
            });
        } catch (Throwable $error) {
            if ($archivosGuardados !== []) {
                Storage::disk('public')->delete($archivosGuardados);
            }

            throw $error;
        }

        return redirect()
            ->route('mis-tareas.responder', $tarea)
            ->with(
                'success',
                'Tu respuesta fue enviada al administrador.'
            );
    }

    public function index(Request $request)
    {
        $filtros = $request->validate([
            'vista' => ['nullable', 'in:respuestas,notificaciones'],
            'tarea_id' => ['nullable', 'integer', 'exists:tareas,id'],
        ]);

        $vista = $filtros['vista'] ?? 'respuestas';
        $tareaId = $filtros['tarea_id'] ?? null;

        $query = $this->consultaRespuestas(
            $request->user()->getAuthIdentifier()
        );

        if ($tareaId) {
            $query->where('h.tarea_id', $tareaId);
        }

        if ($vista === 'notificaciones') {
            $query->whereNotNull('n.alerta_id');
        }

        return view('admin.tareas.respuestas', [
            'respuestas' => $query
                ->orderByDesc('h.id')
                ->paginate(15)
                ->withQueryString(),
            'registro' => null,
            'datosRespuesta' => [],
            'vista' => $vista,
        ]);
    }

    public function show(Request $request, int $respuesta)
    {
        $registro = $this->consultaRespuestas(
            $request->user()->getAuthIdentifier()
        )
            ->where('h.id', $respuesta)
            ->first();

        abort_unless($registro, 404, 'La respuesta no existe.');

        return view('admin.tareas.respuestas', [
            'respuestas' => null,
            'registro' => $registro,
            'datosRespuesta' => json_decode(
                $registro->datos ?? '{}',
                true
            ) ?: [],
            'vista' => 'respuestas',
        ]);
    }

    public function abrir(Request $request, int $respuesta)
    {
        $registro = DB::table('tarea_historial')
            ->where('id', $respuesta)
            ->where('accion', 'respuesta_empleado')
            ->first();

        abort_unless($registro, 404, 'La respuesta no existe.');

        $datos = json_decode($registro->datos ?? '{}', true) ?: [];
        $alertaId = $datos['alerta_id'] ?? null;

        if ($alertaId) {
            DB::table('alerta_usuarios')
                ->where('alerta_id', $alertaId)
                ->where(
                    'usuario_id',
                    $request->user()->getAuthIdentifier()
                )
                ->where(function (Builder $query) {
                    $query->where('leida', false)
                        ->orWhereNull('leida');
                })
                ->update([
                    'leida' => true,
                    'fecha_lectura' => now(),
                ]);
        }

        return redirect()->route(
            'admin.tareas.respuestas.show',
            $respuesta
        );
    }

    private function tareaAsignada(int $tareaId, mixed $usuarioId): object
    {
        $tarea = DB::table('tareas as t')
            ->join('tipos_tarea as tt', 'tt.id', '=', 't.tipo_tarea_id')
            ->join('tarea_estados as te', 'te.id', '=', 't.estado_id')
            ->where('t.id', $tareaId)
            ->whereExists(function (Builder $query) use ($usuarioId) {
                $query->selectRaw('1')
                    ->from('tarea_asignaciones as ta')
                    ->whereColumn('ta.tarea_id', 't.id')
                    ->where('ta.usuario_id', $usuarioId);
            })
            ->select([
                't.*',
                'tt.nombre as tipo',
                'tt.clave as tipo_clave',
                'te.nombre as estado_nombre',
            ])
            ->first();

        abort_unless(
            $tarea,
            404,
            'La tarea no existe o no está asignada a tu cuenta.'
        );

        return $tarea;
    }

    private function detalles(int $tarea): Collection
    {
        return DB::table('tarea_detalles as td')
            ->leftJoin('productos as p', 'p.id', '=', 'td.producto_id')
            ->leftJoin('lotes as l', 'l.id', '=', 'td.lote_id')
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
            ->where('td.tarea_id', $tarea)
            ->select([
                'td.*',
                'p.nombre as producto',
                'l.codigo_lote',
                'um.abreviatura as unidad',
                'tp.permite_pesaje',
            ])
            ->orderBy('td.id')
            ->get()
            ->map(function ($detalle) {
                $detalle->pesable = in_array(
                    $detalle->permite_pesaje,
                    [true, 1, '1', 't', 'true'],
                    true
                );

                return $detalle;
            });
    }

    private function consultaRespuestas(mixed $usuarioId): Builder
    {
        $nuevas = DB::table('alerta_usuarios as au')
            ->join('alertas as a', 'a.id', '=', 'au.alerta_id')
            ->where('au.usuario_id', $usuarioId)
            ->where('a.estado', 'pendiente')
            ->where(function (Builder $query) {
                $query->where('au.leida', false)
                    ->orWhereNull('au.leida');
            })
            ->select('au.alerta_id')
            ->distinct();

        return DB::table('tarea_historial as h')
            ->join('tareas as t', 't.id', '=', 'h.tarea_id')
            ->join('tipos_tarea as tt', 'tt.id', '=', 't.tipo_tarea_id')
            ->leftJoin('usuarios as u', 'u.id', '=', 'h.usuario_id')
            ->leftJoinSub($nuevas, 'n', function ($join) {
                $join->on(
                    DB::raw('CAST(n.alerta_id AS TEXT)'),
                    '=',
                    DB::raw("(h.datos->>'alerta_id')")
                );
            })
            ->where('h.accion', 'respuesta_empleado')
            ->select([
                'h.id',
                'h.tarea_id',
                'h.fecha',
                'h.descripcion',
                'h.datos',
                't.codigo',
                't.titulo',
                'tt.nombre as tipo',
            ])
            ->selectRaw("COALESCE(u.username, 'Usuario eliminado') AS empleado")
            ->selectRaw(
                'CASE WHEN n.alerta_id IS NULL THEN 0 ELSE 1 END AS nueva'
            );
    }

    private function notificarRespuesta(
        object $tarea,
        string $empleado,
        bool $conObservaciones
    ): int {
        DB::table('tipos_alerta')->insertOrIgnore([
            'nombre' => 'Respuesta de tarea',
            'nivel' => 'media',
            'descripcion' => 'Respuesta enviada por un empleado a una tarea.',
        ]);

        $tipoId = DB::table('tipos_alerta')
            ->where('nombre', 'Respuesta de tarea')
            ->value('id');

        $alertaId = DB::table('alertas')->insertGetId([
            'tipo_alerta_id' => $tipoId,
            'tarea_id' => $tarea->id,
            'almacen_id' => $tarea->almacen_id,
            'titulo' => 'Respuesta recibida: ' . $tarea->codigo,
            'mensaje' => $empleado . ' respondió a la tarea'
                . ($conObservaciones
                    ? ' con observaciones o diferencias.'
                    : '.'),
            'fecha_generacion' => now(),
            'estado' => 'pendiente',
        ], 'id');

        $administradores = DB::table('usuarios as u')
            ->join('roles as r', 'r.id', '=', 'u.rol_id')
            ->where('u.estado', 'activo')
            ->whereRaw(
                'LOWER(TRIM(r.nombre)) = ?',
                ['administrador']
            )
            ->distinct()
            ->pluck('u.id');

        if ($administradores->isNotEmpty()) {
            DB::table('alerta_usuarios')->insert(
                $administradores->map(fn ($id) => [
                    'alerta_id' => $alertaId,
                    'usuario_id' => $id,
                    'leida' => false,
                    'fecha_lectura' => null,
                ])->all()
            );
        }

        return (int) $alertaId;
    }

    private function error(string $campo, string $mensaje): never
    {
        throw ValidationException::withMessages([
            $campo => $mensaje,
        ]);
    }
}