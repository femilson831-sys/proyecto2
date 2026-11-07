<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TareaRevisionController extends Controller
{
    public function aprobar(Request $request, int $respuesta)
    {
        $datos = $request->validate([
            'confirmar_revision' => ['required', 'accepted'],
            'confirmar_diferencias' => ['nullable', 'accepted'],
            'observacion_revision' => ['nullable', 'string', 'max:3000'],
        ], [
            'confirmar_revision.required' => 'Confirma que revisaste las respuestas y evidencias.',
            'confirmar_revision.accepted' => 'Confirma que revisaste las respuestas y evidencias.',
            'confirmar_diferencias.accepted' => 'Confirma la aceptación de las observaciones.',
            'observacion_revision.max' => 'La observación permite hasta 3000 caracteres.',
        ]);

        DB::transaction(function () use ($request, $respuesta, $datos) {
            [$respuestaActual, $tarea] = $this->obtenerParaRevision(
                $respuesta
            );

            $asignaciones = DB::table('tarea_asignaciones')
                ->where('tarea_id', $tarea->id)
                ->get();

            if ($asignaciones->isEmpty()) {
                $this->error('La tarea no tiene responsables asignados.');
            }

            if ($asignaciones->contains(
                fn ($asignacion) => $asignacion->fecha_finalizacion === null
            )) {
                $this->error(
                    'Todavía falta la respuesta de uno o más responsables.'
                );
            }

            $usuariosAsignados = $asignaciones
                ->pluck('usuario_id')
                ->unique()
                ->values();

            $ultimasRespuestas = DB::table('tarea_historial')
                ->where('tarea_id', $tarea->id)
                ->where('accion', 'respuesta_empleado')
                ->whereIn('usuario_id', $usuariosAsignados)
                ->orderByDesc('id')
                ->get()
                ->unique('usuario_id')
                ->values();

            if ($ultimasRespuestas->count() !== $usuariosAsignados->count()) {
                $this->error(
                    'No se encontraron respuestas de todos los responsables.'
                );
            }

            $conObservaciones = false;

            foreach ($ultimasRespuestas as $reporte) {
                $contenido = $this->leerDatos($reporte->datos);

                if (($contenido['resultado'] ?? '') !== 'realizada') {
                    $this->error(
                        'Existe una respuesta parcial o no realizada. Solicita una corrección antes de completar la tarea.'
                    );
                }

                if (!empty($contenido['con_observaciones'])) {
                    $conObservaciones = true;
                }
            }

            $observacion = trim(
                (string) ($datos['observacion_revision'] ?? '')
            );

            if ($conObservaciones) {
                if (!$request->boolean('confirmar_diferencias')) {
                    $this->error(
                        'La tarea contiene observaciones o diferencias. Confirma su aceptación.'
                    );
                }

                if ($observacion === '') {
                    $this->error(
                        'Explica por qué apruebas el resultado con observaciones o diferencias.'
                    );
                }
            }

            $estadoCompletada = $this->estadoId('completada');

            DB::table('tareas')
                ->where('id', $tarea->id)
                ->update([
                    'estado_id' => $estadoCompletada,
                    'fecha_finalizacion' => now(),
                    'validado_por' => $request->user()->getAuthIdentifier(),
                    'fecha_validacion' => now(),
                    'observaciones_cierre' => $observacion ?: null,
                ]);

            DB::table('tarea_historial')->insert([
                'tarea_id' => $tarea->id,
                'usuario_id' => $request->user()->getAuthIdentifier(),
                'accion' => 'aprobada',
                'descripcion' => $observacion !== ''
                    ? $observacion
                    : 'El administrador revisó y aprobó el resultado.',
                'datos' => json_encode([
                    'respuesta_id' => $respuestaActual->id,
                    'respuestas_revisadas' => $ultimasRespuestas
                        ->pluck('id')
                        ->all(),
                    'con_observaciones' => $conObservaciones,
                ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'fecha' => now(),
            ]);

            $this->resolverAvisosDeRespuesta($tarea->id);
        });

        return redirect()
            ->route('admin.tareas.respuestas.show', $respuesta)
            ->with('success', 'La respuesta fue aprobada y la tarea quedó completada.');
    }

    public function corregir(Request $request, int $respuesta)
    {
        $datos = $request->validate([
            'motivo_correccion' => [
                'required',
                'string',
                'max:3000',
            ],
        ], [
            'motivo_correccion.required' => 'Explica qué debe corregir o volver a contar el empleado.',
            'motivo_correccion.max' => 'El motivo permite hasta 3000 caracteres.',
        ]);

        DB::transaction(function () use ($request, $respuesta, $datos) {
            [$respuestaActual, $tarea] = $this->obtenerParaRevision(
                $respuesta
            );

            $tipo = DB::table('tipos_tarea')
                ->where('id', $tarea->tipo_tarea_id)
                ->value('clave');

            $esInventario = $tipo === 'inventario';

            $estadoId = $this->estadoId(
                $esInventario ? 'reconteo solicitado' : 'asignada'
            );

            $motivo = trim($datos['motivo_correccion']);

            DB::table('tareas')
                ->where('id', $tarea->id)
                ->update([
                    'estado_id' => $estadoId,
                    'fecha_finalizacion' => null,
                    'validado_por' => null,
                    'fecha_validacion' => null,
                    'observaciones_cierre' => $motivo,
                ]);

            // Habilita un nuevo envío de los responsables.
            // Las respuestas anteriores permanecen en el historial.
            DB::table('tarea_asignaciones')
                ->where('tarea_id', $tarea->id)
                ->update([
                    'fecha_finalizacion' => null,
                ]);

            DB::table('tarea_historial')->insert([
                'tarea_id' => $tarea->id,
                'usuario_id' => $request->user()->getAuthIdentifier(),
                'accion' => 'correccion_solicitada',
                'descripcion' => $motivo,
                'datos' => json_encode([
                    'respuesta_id' => $respuestaActual->id,
                    'tipo' => $esInventario ? 'reconteo' : 'correccion',
                ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'fecha' => now(),
            ]);

            $this->resolverAvisosDeRespuesta($tarea->id);

            $this->notificarCorreccion(
                $tarea,
                $motivo,
                $esInventario
            );
        });

        return redirect()
            ->route('admin.tareas.respuestas.show', $respuesta)
            ->with(
                'success',
                'Se solicitó una corrección. Los responsables ya pueden enviar una nueva respuesta.'
            );
    }

    private function obtenerParaRevision(int $respuestaId): array
    {
        $respuesta = DB::table('tarea_historial')
            ->where('id', $respuestaId)
            ->where('accion', 'respuesta_empleado')
            ->first();

        abort_unless($respuesta, 404, 'La respuesta no existe.');

        $tarea = DB::table('tareas')
            ->where('id', $respuesta->tarea_id)
            ->lockForUpdate()
            ->first();

        abort_unless($tarea, 404, 'La tarea no existe.');

        $estado = DB::table('tarea_estados')
            ->where('id', $tarea->estado_id)
            ->value('nombre');

        if (mb_strtolower(trim((string) $estado)) !== 'pendiente de revisión') {
            $this->error(
                'La tarea no está pendiente de revisión o ya fue atendida.'
            );
        }

        $ultimaRespuestaId = DB::table('tarea_historial')
            ->where('tarea_id', $tarea->id)
            ->where('accion', 'respuesta_empleado')
            ->max('id');

        if ((int) $ultimaRespuestaId !== (int) $respuesta->id) {
            $this->error(
                'Hay una respuesta más reciente. Abre la última respuesta para revisar la tarea.'
            );
        }

        return [$respuesta, $tarea];
    }

    private function estadoId(string $nombre): int
    {
        $id = DB::table('tarea_estados')
            ->whereRaw('LOWER(TRIM(nombre)) = ?', [$nombre])
            ->value('id');

        if (!$id) {
            $this->error(
                "Falta el estado «{$nombre}» en la tabla tarea_estados."
            );
        }

        return (int) $id;
    }

    private function resolverAvisosDeRespuesta(int $tareaId): void
    {
        $tipos = DB::table('tipos_alerta')
            ->where('nombre', 'Respuesta de tarea')
            ->pluck('id');

        if ($tipos->isEmpty()) {
            return;
        }

        DB::table('alertas')
            ->where('tarea_id', $tareaId)
            ->whereIn('tipo_alerta_id', $tipos)
            ->where('estado', 'pendiente')
            ->update([
                'estado' => 'resuelta',
                'fecha_resolucion' => now(),
            ]);
    }

    private function notificarCorreccion(
        object $tarea,
        string $motivo,
        bool $esInventario
    ): void {
        DB::table('tipos_alerta')->insertOrIgnore([
            'nombre' => 'Corrección de tarea',
            'nivel' => 'media',
            'descripcion' => 'Solicitud de corrección o reconteo de una tarea.',
        ]);

        $tipoId = DB::table('tipos_alerta')
            ->where('nombre', 'Corrección de tarea')
            ->value('id');

        $alertaId = DB::table('alertas')->insertGetId([
            'tipo_alerta_id' => $tipoId,
            'tarea_id' => $tarea->id,
            'almacen_id' => $tarea->almacen_id,
            'titulo' => ($esInventario
                ? 'Reconteo solicitado: '
                : 'Corrección solicitada: ') . $tarea->codigo,
            'mensaje' => $motivo,
            'fecha_generacion' => now(),
            'estado' => 'pendiente',
        ], 'id');

        $usuarios = DB::table('tarea_asignaciones as ta')
            ->join('usuarios as u', 'u.id', '=', 'ta.usuario_id')
            ->where('ta.tarea_id', $tarea->id)
            ->where('u.estado', 'activo')
            ->distinct()
            ->pluck('u.id');

        if ($usuarios->isNotEmpty()) {
            DB::table('alerta_usuarios')->insert(
                $usuarios->map(fn ($id) => [
                    'alerta_id' => $alertaId,
                    'usuario_id' => $id,
                    'leida' => false,
                    'fecha_lectura' => null,
                ])->all()
            );
        }
    }

    private function leerDatos(mixed $valor): array
    {
        if (is_array($valor)) {
            return $valor;
        }

        $datos = is_string($valor)
            ? json_decode($valor, true)
            : [];

        return is_array($datos) ? $datos : [];
    }

    private function error(string $mensaje): never
    {
        throw ValidationException::withMessages([
            'revision' => $mensaje,
        ]);
    }
}