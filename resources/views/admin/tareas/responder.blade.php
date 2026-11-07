<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis tareas — NANOS</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #101115;
            color: #eceef4;
            font-family: Arial, sans-serif;
        }

        .mt-page {
            max-width: 1050px;
            margin: 30px auto;
            padding: 0 18px;
            display: grid;
            gap: 18px;
        }

        .mt-card {
            padding: 22px;
            border: 1px solid #303440;
            border-radius: 14px;
            background: #191b22;
        }

        .mt-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
            border-top: 3px solid #e31b23;
        }

        h1 { margin: 8px 0; font-size: 25px; }
        h2 { margin: 0 0 14px; font-size: 17px; }

        .mt-help {
            color: #a3a8b7;
            font-size: 12px;
            line-height: 1.7;
        }

        .mt-button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-height: 42px;
            padding: 10px 15px;
            border: 1px solid #414657;
            border-radius: 8px;
            color: #fff;
            background: #282c37;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .mt-button.primary {
            background: #cc1b25;
            border-color: #e31b23;
        }

        .mt-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .mt-field {
            display: grid;
            gap: 8px;
            font-size: 13px;
        }

        .mt-field input,
        .mt-field select,
        .mt-field textarea {
            width: 100%;
            min-height: 44px;
            padding: 11px;
            border: 1px solid #414657;
            border-radius: 8px;
            color: #fff;
            background: #222630;
            font: inherit;
            color-scheme: dark;
        }

        .mt-field textarea {
            min-height: 100px;
            resize: vertical;
        }

        .mt-full { grid-column: 1 / -1; }

        .mt-message {
            padding: 15px;
            border: 1px solid #456153;
            border-radius: 10px;
            background: #1c3025;
            line-height: 1.7;
            font-size: 13px;
        }

        .mt-message.error {
            border-color: #864047;
            background: #351e24;
            color: #ffafb6;
        }

        .mt-text {
            white-space: pre-wrap;
            font-size: 13px;
            line-height: 1.8;
        }

        .mt-item {
            padding: 16px;
            margin: 14px 0;
            border: 1px solid #363b49;
            border-radius: 10px;
            background: #20242d;
        }

        .mt-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        @media (max-width: 650px) {
            .mt-grid { grid-template-columns: 1fr; }
            .mt-full { grid-column: auto; }
        }
    </style>
</head>
<body>
@php
    $formatearMiTarea = static function ($valor) {
        return $valor === null
            ? '—'
            : rtrim(rtrim(number_format((float) $valor, 3, ',', '.'), '0'), ',');
    };

    $exigeEvidenciaMiTarea = $tarea && in_array(
        $tarea->requiere_evidencia,
        [true, 1, '1', 't', 'true'],
        true
    );
@endphp

<main class="mt-page">
    <header class="mt-card mt-header">
        <div>
            <span class="mt-help">Minisúper NANOS</span>
            <h1>{{ $tarea ? $tarea->codigo : 'Mis tareas' }}</h1>
            <p class="mt-help">{{ auth()->user()->username }}</p>
        </div>

        <div class="mt-actions">
            @if ($tarea)
                <a href="{{ route('mis-tareas.index') }}" class="mt-button">
                    ← Mis tareas
                </a>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="mt-button">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </header>

    @if (session('success'))
        <div class="mt-message" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-message error" role="alert">
            <strong>Revisa los siguientes datos:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (!$tarea)
        @forelse ($tareas as $miTarea)
            <article class="mt-card">
                <p class="mt-help">
                    {{ $miTarea->codigo }} · {{ $miTarea->tipo }}
                </p>

                <h2>{{ $miTarea->titulo ?: 'Tarea asignada' }}</h2>

                <p class="mt-help">
                    Estado: {{ $miTarea->estado_nombre }}
                    · Prioridad: {{ ucfirst($miTarea->prioridad ?? 'normal') }}
                </p>

                <p class="mt-help">
                    Fecha límite:
                    {{ $miTarea->fecha_limite
                        ? \Illuminate\Support\Carbon::parse($miTarea->fecha_limite)->format('d/m/Y H:i')
                        : 'Sin fecha límite' }}
                </p>

                <a
                    href="{{ route('mis-tareas.responder', $miTarea->id) }}"
                    class="mt-button"
                >
                    Abrir tarea
                </a>
            </article>
        @empty
            <div class="mt-card">
                No tienes tareas asignadas.
            </div>
        @endforelse

        {{ $tareas->links() }}
    @else
        <section class="mt-card">
            <h2>{{ $tarea->titulo ?: $tarea->tipo }}</h2>

            <p class="mt-help">
                {{ $tarea->tipo }} · {{ $tarea->estado_nombre }}
            </p>

            <p class="mt-help">Instrucciones</p>
            @if (!empty($tarea->observaciones_cierre))
    <div class="mt-message" style="margin-top:18px;">
        <strong>Observación del administrador</strong>

        <div
            class="mt-text"
            style="margin-top:8px;"
        >{{ $tarea->observaciones_cierre }}</div>
    </div>
@endif
        </section>

        @if (!$puedeResponder)
            <div class="mt-message">
                Esta tarea está cerrada o ya enviaste tu respuesta.
                Espera la revisión del administrador.
            </div>
        @else
            <form
                method="POST"
                action="{{ route('mis-tareas.guardar', $tarea->id) }}"
                enctype="multipart/form-data"
                class="mt-card"
            >
                @csrf

                <input
                    type="hidden"
                    name="token_respuesta"
                    value="{{ old('token_respuesta', (string) \Illuminate\Support\Str::uuid()) }}"
                >

                <h2>Responder tarea</h2>

                <label class="mt-field">
                    Resultado de tu trabajo

                    <select name="resultado" id="resultado" required>
                        <option value="realizada" @selected(old('resultado', 'realizada') === 'realizada')>
                            Realizada
                        </option>

                        <option value="parcial" @selected(old('resultado') === 'parcial')>
                            Realizada parcialmente
                        </option>

                        <option value="no_realizada" @selected(old('resultado') === 'no_realizada')>
                            No se pudo realizar
                        </option>
                    </select>
                </label>

                @foreach ($detalles as $detalle)
                    <article class="mt-item">
                        <strong>
                            {{ $detalle->producto ?: 'Revisión de ubicación' }}
                        </strong>

                        <p class="mt-help">
                            Lote: {{ $detalle->codigo_lote ?: 'No corresponde' }}
                        </p>

                        @if ($tarea->tipo_clave !== 'revision' && $detalle->producto_id)
                            @php
                                $referenciaMiTarea = $tarea->tipo_clave === 'inventario'
                                    ? $detalle->cantidad_sistema
                                    : $detalle->cantidad_solicitada;
                            @endphp

                            <p class="mt-help">
                                {{ $tarea->tipo_clave === 'inventario'
                                    ? 'Referencia del sistema guardada en la tarea:'
                                    : 'Cantidad solicitada:' }}

                                {{ $formatearMiTarea($referenciaMiTarea) }}
                                {{ $detalle->unidad }}
                            </p>

                            <label class="mt-field">
                                {{ $tarea->tipo_clave === 'inventario'
                                    ? 'Cantidad contada físicamente'
                                    : 'Cantidad realmente realizada' }}

                                <input
                                    type="number"
                                    name="detalles[{{ $detalle->id }}][cantidad]"
                                    class="cantidad-respuesta"
                                    value="{{ old('detalles.' . $detalle->id . '.cantidad') }}"
                                    min="0"
                                    max="999999999.999"
                                    step="{{ $detalle->pesable ? '0.001' : '1' }}"
                                >
                            </label>
                        @endif
                    </article>
                @endforeach

                <div class="mt-grid">
                    <label class="mt-field mt-full">
                        Tu respuesta

                        <textarea
                            name="mensaje"
                            maxlength="3000"
                            required
                            placeholder="Explica qué realizaste."
                        >{{ old('mensaje') }}</textarea>
                    </label>

                    <label class="mt-field mt-full">
                        Observaciones

                        <textarea
                            name="observaciones"
                            maxlength="3000"
                            placeholder="Explica diferencias, productos faltantes o dificultades."
                        >{{ old('observaciones') }}</textarea>

                        <span class="mt-help">
                            Obligatorias cuando existen diferencias o no se completó la tarea.
                        </span>
                    </label>

                    <label class="mt-field mt-full">
                        Fotografías o documentos

                        <input
                            type="file"
                            name="evidencias[]"
                            id="evidencias"
                            accept="image/jpeg,image/png,image/webp,application/pdf"
                            multiple
                        >

                        <span class="mt-help">
                            Hasta cinco archivos de 5 MB cada uno.
                            {{ $exigeEvidenciaMiTarea
                                ? 'Esta tarea solicita evidencia si se realizó la actividad.'
                                : 'La evidencia es opcional.' }}
                        </span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="mt-button primary"
                    style="margin-top:20px;"
                >
                    Enviar respuesta al administrador
                </button>
            </form>
        @endif
    @endif
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const resultado = document.getElementById('resultado');

    if (!resultado) {
        return;
    }

    const exigeEvidencia = {{ $exigeEvidenciaMiTarea ? 'true' : 'false' }};
    const evidencias = document.getElementById('evidencias');

    function actualizarRespuesta() {
        const noRealizada = resultado.value === 'no_realizada';
        const realizada = resultado.value === 'realizada';

        document.querySelectorAll('.cantidad-respuesta').forEach(input => {
            input.disabled = noRealizada;
            input.required = realizada;
        });

        evidencias.required = exigeEvidencia && !noRealizada;
    }

    resultado.addEventListener('change', actualizarRespuesta);
    actualizarRespuesta();
});
</script>
</body>
</html>