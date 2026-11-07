@extends('admin.layouts.app')

@section('title', 'Respuestas de tareas')

@push('styles')
    @include('admin.tareas._styles')
@endpush

@section('content')
@php
    $formatearRespuesta = static function ($valor) {
        return $valor === null
            ? '—'
            : rtrim(rtrim(number_format((float) $valor, 3, ',', '.'), '0'), ',');
    };

    $resultadosRespuesta = [
        'realizada' => 'Realizada',
        'parcial' => 'Realizada parcialmente',
        'no_realizada' => 'No se pudo realizar',
    ];
@endphp

<div class="ta-page">
    <header class="ta-header">
        <div>
            <span class="ta-eyebrow">Tareas del personal</span>

            <h1>
                {{ $registro
                    ? 'Respuesta de ' . $registro->codigo
                    : ($vista === 'notificaciones'
                        ? 'Notificaciones de tareas'
                        : 'Respuestas del personal') }}
            </h1>

            <p>Consulta lo informado por los empleados y sus observaciones.</p>
        </div>

        <a href="{{ route('admin.tareas.index') }}" class="ta-button">
            ← Volver a tareas
        </a>
    </header>

    @include('admin.tareas._menu_respuestas')

    @if ($registro)
        <section class="ta-card">
            <h2 class="ta-section-title">Información de la respuesta</h2>

            <div class="ta-grid">
                <div class="ta-field">
                    Empleado
                    <strong>{{ $registro->empleado }}</strong>
                </div>

                <div class="ta-field">
                    Fecha de respuesta
                    <strong>
                        {{ \Illuminate\Support\Carbon::parse($registro->fecha)->format('d/m/Y H:i') }}
                    </strong>
                </div>

                <div class="ta-field">
                    Tarea
                    <strong>{{ $registro->titulo ?: $registro->codigo }}</strong>
                </div>

                <div class="ta-field">
                    Tipo
                    <strong>{{ $registro->tipo }}</strong>
                </div>

                <div class="ta-field">
                    Resultado informado
                    <strong>
                        {{ $resultadosRespuesta[$datosRespuesta['resultado'] ?? '']
                            ?? 'No especificado' }}
                    </strong>
                </div>

                <div class="ta-field">
                    Observaciones
                    <strong>
                        {{ !empty($datosRespuesta['con_observaciones'])
                            ? 'Con observaciones o diferencias'
                            : 'Sin observaciones informadas' }}
                    </strong>
                </div>

                <div class="ta-full">
                    <p class="ta-help">Respuesta del empleado</p>
                    <div class="ta-description">{{ $registro->descripcion }}</div>
                </div>

                @if (!empty($datosRespuesta['observaciones']))
                    <div class="ta-full ta-error">
                        <strong>Observaciones informadas</strong>
                        <div class="ta-description">{{ $datosRespuesta['observaciones'] }}</div>
                    </div>
                @endif
            </div>
        </section>

        <section class="ta-card">
            <h2 class="ta-section-title">Cantidades informadas</h2>

            <div class="ta-table-wrap">
                <table class="ta-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Lote</th>
                            <th>Referencia</th>
                            <th>Informado</th>
                            <th>Diferencia</th>
                            <th>Comparación</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse (($datosRespuesta['detalles'] ?? []) as $detalleRespuesta)
                            <tr>
                                <td>{{ $detalleRespuesta['producto'] ?? '—' }}</td>
                                <td>{{ $detalleRespuesta['lote'] ?? '—' }}</td>

                                <td>
                                    {{ $formatearRespuesta($detalleRespuesta['referencia'] ?? null) }}
                                    {{ $detalleRespuesta['unidad'] ?? '' }}

                                    @if (!empty($detalleRespuesta['fecha_referencia']))
                                        <small>
                                            Referencia:
                                            {{ \Illuminate\Support\Carbon::parse($detalleRespuesta['fecha_referencia'])->format('d/m/Y H:i') }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    {{ $formatearRespuesta($detalleRespuesta['realizada'] ?? null) }}
                                    {{ $detalleRespuesta['unidad'] ?? '' }}
                                </td>

                                <td>
                                    {{ $formatearRespuesta($detalleRespuesta['diferencia'] ?? null) }}
                                </td>

                                <td>
                                    {{ ucfirst(str_replace(
                                        '_',
                                        ' ',
                                        $detalleRespuesta['resultado'] ?? 'reportado'
                                    )) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">No hay cantidades registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="ta-help" style="margin-top:15px;">
                La comparación utiliza la referencia guardada en la tarea.
                El resultado debe revisarse considerando los movimientos posteriores
                a esa referencia.
            </p>
        </section>

        <section class="ta-card">
            <h2 class="ta-section-title">Evidencias</h2>

            <div class="ta-actions" style="justify-content:flex-start;">
                @forelse (($datosRespuesta['evidencias'] ?? []) as $evidenciaRespuesta)
                    <a
                        href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($evidenciaRespuesta['archivo']) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="ta-button"
                    >
                        Abrir evidencia {{ $loop->iteration }}
                    </a>
                @empty
                    <p class="ta-help">El empleado no adjuntó archivos.</p>
                @endforelse
            </div>
        </section>

        @if ((int) $registro->nueva === 1)
            <form
                method="POST"
                action="{{ route('admin.tareas.respuestas.abrir', $registro->id) }}"
            >
                @csrf
                <button type="submit" class="ta-button">
                    Marcar respuesta como leída
                </button>
            </form>
        @endif

       @include('admin.tareas._revision_respuesta')
    @else
        <section class="ta-card">
            <div class="ta-table-wrap">
                <table class="ta-table">
                    <thead>
                        <tr>
                            <th>Tarea</th>
                            <th>Empleado</th>
                            <th>Fecha</th>
                            <th>Resultado</th>
                            <th>Observaciones</th>
                            <th>Lectura</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($respuestas as $respuesta)
                            @php
                                $contenidoRespuesta = json_decode(
                                    $respuesta->datos ?? '{}',
                                    true
                                ) ?: [];
                            @endphp

                            <tr>
                                <td>
                                    <strong>{{ $respuesta->codigo }}</strong>
                                    <small>{{ $respuesta->titulo }}</small>
                                </td>

                                <td>{{ $respuesta->empleado }}</td>

                                <td>
                                    {{ \Illuminate\Support\Carbon::parse($respuesta->fecha)->format('d/m/Y H:i') }}
                                </td>

                                <td>
                                    {{ $resultadosRespuesta[$contenidoRespuesta['resultado'] ?? '']
                                        ?? 'No especificado' }}
                                </td>

                                <td>
                                    {{ !empty($contenidoRespuesta['con_observaciones'])
                                        ? 'Con observaciones'
                                        : 'Sin observaciones' }}
                                </td>

                                <td>
                                    <span class="ta-badge">
                                        {{ (int) $respuesta->nueva === 1
                                            ? 'Nueva respuesta'
                                            : 'Sin aviso pendiente' }}
                                    </span>
                                </td>

                                <td>
                                    <form
                                        method="POST"
                                        action="{{ route('admin.tareas.respuestas.abrir', $respuesta->id) }}"
                                    >
                                        @csrf

                                        <button type="submit" class="ta-button">
                                            Ver respuesta
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    {{ $vista === 'notificaciones'
                                        ? 'No tienes respuestas nuevas sin leer.'
                                        : 'Todavía no hay respuestas para esta consulta.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:18px;">
                {{ $respuestas->links() }}
            </div>
        </section>
    @endif
</div>
@endsection