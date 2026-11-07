@if (!empty($registro))
    @php
        $tareaRevision = \Illuminate\Support\Facades\DB::table('tareas as t')
            ->join('tarea_estados as te', 'te.id', '=', 't.estado_id')
            ->leftJoin('usuarios as u', 'u.id', '=', 't.validado_por')
            ->where('t.id', $registro->tarea_id)
            ->select([
                't.id',
                't.fecha_validacion',
                't.observaciones_cierre',
                'te.nombre as estado',
                'u.username as revisor',
            ])
            ->first();

        $ultimaRespuestaRevision = \Illuminate\Support\Facades\DB::table('tarea_historial')
            ->where('tarea_id', $registro->tarea_id)
            ->where('accion', 'respuesta_empleado')
            ->max('id');

        $esUltimaRespuestaRevision = (int) $ultimaRespuestaRevision
            === (int) $registro->id;

        $estadoRevision = mb_strtolower(
            trim((string) ($tareaRevision->estado ?? ''))
        );

        $puedeRevisarRespuesta = $tareaRevision
            && $estadoRevision === 'pendiente de revisión'
            && $esUltimaRespuestaRevision;
    @endphp

    <style>
        .tr-review-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .tr-review-box {
            padding: 20px;
            border: 1px solid #384353;
            border-radius: 12px;
            background: #1c222c;
        }

        .tr-review-box.approve {
            border-color: #3d745950;
            background: #1a2721;
        }

        .tr-review-box.correct {
            border-color: #b78b3e50;
            background: #29241b;
        }

        .tr-review-box h3 {
            margin: 0 0 10px;
            font-size: 15px;
        }

        .tr-review-box p {
            margin: 0 0 16px;
            color: #aab3c5;
            font-size: 12px;
            line-height: 1.8;
        }

        .tr-review-fields {
            display: grid;
            gap: 15px;
        }

        .tr-review-check {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            color: #cbd4e4;
            font-size: 12px;
            line-height: 1.7;
        }

        .tr-review-check input {
            margin-top: 4px;
            accent-color: #e31b23;
            flex-shrink: 0;
        }

        .tr-review-button {
            width: 100%;
            margin-top: 4px;
        }

        .tr-review-result {
            display: grid;
            gap: 10px;
            padding: 17px;
            border: 1px solid #3d495e;
            border-radius: 11px;
            background: #202735;
            color: #cdd8ec;
            font-size: 12px;
            line-height: 1.8;
        }

        .tr-review-note {
            white-space: pre-wrap;
        }

        @media (max-width: 760px) {
            .tr-review-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @if (session('success'))
        <div class="ta-success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="ta-error" role="alert">
            <strong>Revisa los siguientes datos:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="ta-card">
        <h2 class="ta-section-title">Revisión del administrador</h2>

        @if ($puedeRevisarRespuesta)
            <p class="ta-help" style="margin-bottom:20px;">
                Revisa las cantidades, las observaciones y las evidencias.
                Si hay varios responsables, todos deben haber respondido
                antes de aprobar la tarea.
            </p>

            <div class="tr-review-grid">
                <form
                    method="POST"
                    action="{{ route('admin.tareas.respuestas.aprobar', $registro->id) }}"
                    class="tr-review-box approve"
                >
                    @csrf

                    <h3>Aprobar resultado</h3>

                    <p>
                        Confirma la revisión y cierra la tarea como completada.
                        Si existen diferencias, debes explicar su aceptación.
                    </p>

                    <div class="tr-review-fields">
                        <label class="ta-field">
                            Observación de la revisión

                            <textarea
                                name="observacion_revision"
                                maxlength="3000"
                                placeholder="Explica la revisión realizada o la aceptación de diferencias."
                            >{{ old('observacion_revision') }}</textarea>

                            <span class="ta-help">
                                Obligatoria si las respuestas tienen observaciones o diferencias.
                            </span>
                        </label>

                        <label class="tr-review-check">
                            <input
                                type="checkbox"
                                name="confirmar_revision"
                                value="1"
                                required
                                @checked(old('confirmar_revision'))
                            >

                            <span>
                                He revisado las respuestas y las evidencias de los responsables.
                            </span>
                        </label>

                        <label class="tr-review-check">
                            <input
                                type="checkbox"
                                name="confirmar_diferencias"
                                value="1"
                                @checked(old('confirmar_diferencias'))
                            >

                            <span>
                                Acepto el resultado con las observaciones o diferencias informadas.
                            </span>
                        </label>

                        <button
                            type="submit"
                            class="ta-button primary tr-review-button"
                        >
                            Aprobar y completar tarea
                        </button>
                    </div>
                </form>

                <form
                    method="POST"
                    action="{{ route('admin.tareas.respuestas.corregir', $registro->id) }}"
                    class="tr-review-box correct"
                >
                    @csrf

                    <h3>Solicitar corrección o reconteo</h3>

                    <p>
                        La tarea volverá a los responsables para que revisen
                        lo indicado y envíen otra respuesta.
                    </p>

                    <div class="tr-review-fields">
                        <label class="ta-field">
                            ¿Qué deben corregir o verificar?

                            <textarea
                                name="motivo_correccion"
                                maxlength="3000"
                                required
                                placeholder="Ejemplo: vuelve a contar Fanta del lote 874 y adjunta una fotografía."
                            >{{ old('motivo_correccion') }}</textarea>
                        </label>

                        <p class="ta-help">
                            Las respuestas anteriores seguirán disponibles en el historial.
                        </p>

                        <button
                            type="submit"
                            class="ta-button tr-review-button"
                        >
                            Solicitar nueva respuesta
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="tr-review-result">
                <strong>
                    Estado actual:
                    {{ $tareaRevision->estado ?? 'No disponible' }}
                </strong>

                @if (!$esUltimaRespuestaRevision && $ultimaRespuestaRevision)
                    <span>
                        Existe una respuesta más reciente para esta tarea.
                    </span>

                    <a
                        href="{{ route('admin.tareas.respuestas.show', $ultimaRespuestaRevision) }}"
                        class="ta-button"
                    >
                        Ver última respuesta
                    </a>
                @endif

                @if (!empty($tareaRevision->revisor))
                    <span>
                        Revisado por: {{ $tareaRevision->revisor }}
                    </span>
                @endif

                @if (!empty($tareaRevision->fecha_validacion))
                    <span>
                        Fecha:
                        {{ \Illuminate\Support\Carbon::parse(
                            $tareaRevision->fecha_validacion
                        )->format('d/m/Y H:i') }}
                    </span>
                @endif

                @if (!empty($tareaRevision->observaciones_cierre))
                    <div>
                        <strong>Observación del administrador</strong>
                        <div class="tr-review-note">{{ $tareaRevision->observaciones_cierre }}</div>
                    </div>
                @endif
            </div>
        @endif
    </section>
@endif