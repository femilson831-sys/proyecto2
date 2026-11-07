@extends('admin.layouts.app')

@section('title', 'Detalle de tarea')

@push('styles')
<style>
    .td-page {
        --surface: #17181c;
        --surface-2: #1e2026;
        --surface-3: #262932;
        --border: #30333d;
        --border-soft: #383b45;
        --muted: #9699a3;
        --text: #ececf0;
        --accent: #e34b52;
        --accent-strong: #bd171f;
        color: var(--text);
        display: flex;
        flex-direction: column;
        gap: 16px;
        max-width: 1100px;
        margin: 0 auto;
    }

    .td-page * { box-sizing: border-box; }

    .td-card {
        border: 1px solid var(--border);
        border-radius: 14px;
        background: var(--surface);
    }

    /* ---------- Header ---------- */
    .td-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding: 20px 22px;
    }

    .td-eyebrow {
        color: #ff777d;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .td-header h1 { margin: 7px 0 4px; color: #fff; font-size: 24px; overflow-wrap: anywhere; }
    .td-header p { margin: 0; color: var(--muted); font-size: 12px; }

    .td-badges { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }

    .td-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 11px;
        border: 1px solid color-mix(in srgb, var(--c) 45%, transparent);
        border-radius: 999px;
        color: #fff;
        background: color-mix(in srgb, var(--c) 16%, var(--surface-2));
        font-size: 11px;
        font-weight: 800;
    }

    .td-badge::before {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--c);
    }

    .s-pending  { --c: #ffb347; }
    .s-progress { --c: #4ea8de; }
    .s-done     { --c: #7bd88f; }
    .s-cancel   { --c: #9699a3; }
    .s-late     { --c: #ff4d55; }
    .p-baja     { --c: #4ea8de; }
    .p-normal   { --c: #7bd88f; }
    .p-alta     { --c: #ffb347; }
    .p-urgente  { --c: #ff4d55; }

    /* ---------- Botones ---------- */
    .td-button {
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 14px;
        border: 1px solid #3a3d47;
        border-radius: 8px;
        color: #eee;
        background: #24262d;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
    }

    .td-button:hover { border-color: #555966; }
    .td-button.primary { border-color: #e31b23; color: #fff; background: var(--accent-strong); }
    .td-button.primary:hover { background: #d11b24; }
    .td-button.small { min-height: 32px; padding: 0 10px; font-size: 11px; }
    .td-button[disabled] { opacity: .5; cursor: not-allowed; }

    .td-spinner {
        display: none;
        width: 13px;
        height: 13px;
        border: 2px solid rgba(255, 255, 255, .35);
        border-top-color: #fff;
        border-radius: 50%;
        animation: td-spin .7s linear infinite;
    }

    .td-button.loading .td-spinner { display: inline-block; }
    @keyframes td-spin { to { transform: rotate(360deg); } }

    /* ---------- Alertas ---------- */
    .td-alert {
        padding: 12px 15px;
        border: 1px solid rgba(74, 222, 128, .3);
        border-radius: 9px;
        color: #9ae6b4;
        background: rgba(74, 222, 128, .08);
        font-size: 13px;
    }

    .td-alert.error {
        color: #ff9297;
        border-color: rgba(227, 27, 35, .35);
        background: rgba(227, 27, 35, .08);
    }

    .td-alert.warn {
        color: #ffcf8a;
        border-color: rgba(255, 179, 71, .35);
        background: rgba(255, 179, 71, .08);
    }

    /* ---------- Línea de tiempo ---------- */
    .td-timeline {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0;
        padding: 20px 22px;
    }

    .td-step { position: relative; padding-top: 24px; text-align: center; }

    .td-step::before {
        content: "";
        position: absolute;
        top: 6px;
        left: 50%;
        width: 14px;
        height: 14px;
        margin-left: -7px;
        border: 2px solid var(--border-soft);
        border-radius: 50%;
        background: var(--surface);
        z-index: 1;
    }

    .td-step::after {
        content: "";
        position: absolute;
        top: 12px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: var(--border-soft);
    }

    .td-step:last-child::after { display: none; }

    .td-step.done::before { border-color: #7bd88f; background: #7bd88f; }
    .td-step.done::after { background: #7bd88f; }
    .td-step.late::before { border-color: #ff4d55; background: #ff4d55; }

    .td-step b { display: block; color: #fff; font-size: 12px; }
    .td-step small { display: block; margin-top: 3px; color: var(--muted); font-size: 11px; }
    .td-step.late small { color: #ff9297; }

    /* ---------- Datos ---------- */
    .td-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        padding: 16px;
    }

    .td-dato {
        min-width: 0;
        padding: 13px;
        border: 1px solid #2d3038;
        border-radius: 9px;
        background: var(--surface-2);
    }

    .td-dato.full { grid-column: 1 / -1; }

    .td-dato > span {
        display: block;
        margin-bottom: 6px;
        color: var(--muted);
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .td-dato > strong { color: #fff; font-size: 13px; overflow-wrap: anywhere; }
    .td-dato > small { display: block; margin-top: 4px; color: var(--muted); font-size: 11px; }
    .td-dato > small.late { color: #ff9297; font-weight: 700; }
    .td-dato .desc { font-weight: 500; line-height: 1.7; white-space: pre-line; }

    /* ---------- Secciones ---------- */
    .td-section { padding: 18px; }

    .td-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .td-section h2 { margin: 0; color: #fff; font-size: 15px; }
    .td-count { color: var(--muted); font-size: 12px; font-weight: 600; }

    .td-inline { display: flex; align-items: end; gap: 10px; flex-wrap: wrap; }

    .td-field label {
        display: block;
        margin-bottom: 6px;
        color: #c8cad1;
        font-size: 11px;
        font-weight: 700;
    }

    .td-field select,
    .td-field input[type="text"] {
        width: 100%;
        min-width: 220px;
        min-height: 42px;
        padding: 0 12px;
        border: 1px solid var(--border-soft);
        border-radius: 8px;
        outline: none;
        color: #eee;
        background: var(--surface-2);
        font: inherit;
        font-size: 12px;
        color-scheme: dark;
    }

    .td-field select:focus,
    .td-field input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(227, 75, 82, .15); }

    /* ---------- Progreso ---------- */
    .td-progress {
        height: 6px;
        overflow: hidden;
        margin-bottom: 14px;
        border-radius: 999px;
        background: var(--surface-3);
    }

    .td-progress > i { display: block; height: 100%; background: #7bd88f; transition: width .3s; }

    /* ---------- Asignados ---------- */
    .td-people { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }

    .td-person {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border: 1px solid #2d3038;
        border-radius: 10px;
        background: var(--surface-2);
    }

    .td-avatar {
        flex: none;
        display: grid;
        place-items: center;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #3a3d49;
        color: #fff;
        font-size: 12px;
        font-weight: 800;
    }

    .td-person-info { flex: 1; min-width: 0; }
    .td-person-name { display: block; overflow: hidden; color: #fff; font-size: 13px; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
    .td-person-user { color: var(--muted); font-size: 11px; }
    .td-person-dates { margin-top: 6px; color: var(--muted); font-size: 11px; line-height: 1.6; }

    .td-empty {
        padding: 22px;
        border: 1px dashed var(--border-soft);
        border-radius: 10px;
        color: var(--muted);
        font-size: 12px;
        text-align: center;
    }

    /* ---------- Evidencias ---------- */
    .td-drop {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 4px;
        padding: 22px;
        border: 2px dashed var(--border-soft);
        border-radius: 12px;
        background: var(--surface-2);
        color: var(--muted);
        font-size: 12px;
        text-align: center;
        cursor: pointer;
        transition: border-color .15s, background .15s;
    }

    .td-drop:hover,
    .td-drop.over { border-color: var(--accent); background: rgba(227, 75, 82, .06); }
    .td-drop strong { color: #fff; font-size: 13px; }
    .td-drop input[type="file"] { position: absolute; width: 1px; height: 1px; opacity: 0; }
    .td-drop.has-file { border-style: solid; border-color: #7bd88f; }

    .td-upload-row { display: grid; grid-template-columns: 1fr auto; gap: 10px; align-items: end; margin-top: 12px; }

    .td-error { margin-top: 6px; color: #ff9297; font-size: 11px; }

    .td-evidencias {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 12px;
        margin-top: 18px;
    }

    .td-ev {
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #2d3038;
        border-radius: 10px;
        background: var(--surface-2);
    }

    .td-ev-thumb {
        display: grid;
        place-items: center;
        height: 130px;
        padding: 0;
        border: 0;
        background: #14151a;
        color: var(--muted);
        font-size: 28px;
        cursor: pointer;
    }

    .td-ev-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .td-ev-thumb.pdf { color: #ff777d; font-size: 13px; font-weight: 800; cursor: default; }

    .td-ev-body { display: flex; flex: 1; flex-direction: column; gap: 4px; padding: 11px; }
    .td-ev-desc { color: #e1e2e7; font-size: 12px; font-weight: 600; overflow-wrap: anywhere; }
    .td-ev-meta { color: var(--muted); font-size: 11px; }
    .td-ev .td-button { margin-top: auto; }

    dialog.td-lightbox {
        max-width: min(92vw, 1000px);
        padding: 0;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #0e0f12;
        color: #fff;
    }

    dialog.td-lightbox::backdrop { background: rgba(0, 0, 0, .75); }
    dialog.td-lightbox img { display: block; max-width: 100%; max-height: 80vh; margin: 0 auto; }
    dialog.td-lightbox form { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 10px 14px; font-size: 12px; }

    @media (max-width: 800px) {
        .td-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .td-people { grid-template-columns: 1fr; }
        .td-timeline { grid-template-columns: repeat(2, 1fr); row-gap: 18px; }
        .td-step:nth-child(2)::after { display: none; }
    }

    @media (max-width: 520px) {
        .td-grid { grid-template-columns: 1fr; }
        .td-dato.full { grid-column: auto; }
        .td-upload-row { grid-template-columns: 1fr; }
        .td-header .td-button,
        .td-inline .td-button,
        .td-upload-row .td-button { width: 100%; }
        .td-field select { min-width: 0; }
        .td-inline .td-field { flex: 1 1 100%; }
    }
</style>
@endpush

@section('content')
@php
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\Storage;

    $fmt = fn ($v, $alt = '—') => $v ? Carbon::parse($v)->format('d/m/Y H:i') : $alt;

    $clasificarEstado = function (?string $nombre): string {
        $n = mb_strtolower(\Illuminate\Support\Str::ascii((string) $nombre));
        return match (true) {
            str_contains($n, 'cancel') => 'cancel',
            str_contains($n, 'finaliz'), str_contains($n, 'complet'), str_contains($n, 'termin') => 'done',
            str_contains($n, 'proceso'), str_contains($n, 'curso'), str_contains($n, 'inici') => 'progress',
            default => 'pending',
        };
    };

    $estadoClase = $clasificarEstado($registro->estado_nombre);
    $prioridad   = $registro->prioridad ?? 'normal';
    $limite      = $registro->fecha_limite ? Carbon::parse($registro->fecha_limite) : null;
    $terminada   = $registro->fecha_finalizacion || in_array($estadoClase, ['done', 'cancel'], true);
    $vencida     = $limite && ! $terminada && $limite->isPast();

    $iniciales = function (string $texto): string {
        $partes = preg_split('/\s+/', trim($texto));
        $a = mb_substr($partes[0] ?? '', 0, 1);
        $b = count($partes) > 1 ? mb_substr(end($partes), 0, 1) : mb_substr($partes[0] ?? '', 1, 1);
        return mb_strtoupper($a . $b);
    };

    $totalAsignados   = $asignaciones->count();
    $finalizadasAsig  = $asignaciones->filter(fn ($a) => ! empty($a->fecha_finalizacion))->count();
    $porcentaje       = $totalAsignados > 0 ? round($finalizadasAsig / $totalAsignados * 100) : 0;
@endphp

<div class="td-page">

    {{-- Header --}}
    <header class="td-card td-header">
        <div>
            <span class="td-eyebrow">Detalle de tarea</span>
            <h1>{{ $registro->codigo }}</h1>
            <p>{{ $registro->tipo_nombre }}</p>

            <div class="td-badges">
                <span class="td-badge s-{{ $estadoClase }}" style="--c: var(--c)">{{ $registro->estado_nombre }}</span>
                <span class="td-badge p-{{ $prioridad }}">Prioridad {{ $prioridad }}</span>
                @if ($vencida)
                    <span class="td-badge s-late">Vencida</span>
                @endif
            </div>
        </div>

        <a href="{{ route('admin.tareas.index') }}" class="td-button">← Volver a tareas</a>
    </header>

    {{-- Mensajes --}}
    @if (session('success'))
        <div class="td-alert" role="status">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="td-alert error" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if ($vencida)
        <div class="td-alert warn" role="alert">
            Esta tarea venció {{ $limite->diffForHumans() }} y todavía no está finalizada.
        </div>
    @endif

    {{-- Línea de tiempo --}}
    <section class="td-card td-timeline" aria-label="Progreso de la tarea">
        <div class="td-step done">
            <b>Creada</b>
            <small>{{ $fmt($registro->fecha_creacion) }}</small>
        </div>
        <div class="td-step {{ $registro->fecha_inicio ? 'done' : '' }}">
            <b>Iniciada</b>
            <small>{{ $fmt($registro->fecha_inicio, 'Sin iniciar') }}</small>
        </div>
        <div class="td-step {{ $registro->fecha_finalizacion ? 'done' : '' }}">
            <b>Finalizada</b>
            <small>{{ $fmt($registro->fecha_finalizacion, 'Pendiente') }}</small>
        </div>
        <div class="td-step {{ $vencida ? 'late' : '' }}">
            <b>Fecha límite</b>
            <small>{{ $limite ? $limite->format('d/m/Y H:i') : 'Sin fecha límite' }}</small>
        </div>
    </section>

    {{-- Datos --}}
    <section class="td-card td-grid">
        <div class="td-dato">
            <span>Tipo</span>
            <strong>{{ $registro->tipo_nombre }}</strong>
        </div>

        <div class="td-dato">
            <span>Creada por</span>
            <strong>{{ $registro->creador ?: 'No registrado' }}</strong>
        </div>

        <div class="td-dato">
            <span>Almacén</span>
            <strong>{{ $registro->almacen ?: 'Sin almacén específico' }}</strong>
        </div>

        <div class="td-dato">
            <span>Fecha límite</span>
            <strong>{{ $limite ? $limite->format('d/m/Y H:i') : 'Sin fecha límite' }}</strong>
            @if ($limite && ! $terminada)
                <small class="{{ $vencida ? 'late' : '' }}">
                    {{ $vencida ? 'Venció' : 'Vence' }} {{ $limite->diffForHumans() }}
                </small>
            @endif
        </div>

        <div class="td-dato">
            <span>Inicio</span>
            <strong>{{ $fmt($registro->fecha_inicio, 'Sin iniciar') }}</strong>
        </div>

        <div class="td-dato">
            <span>Finalización</span>
            <strong>{{ $fmt($registro->fecha_finalizacion, 'Pendiente') }}</strong>
        </div>

        <div class="td-dato full">
            <span>Descripción</span>
            <strong class="desc">{{ $registro->descripcion ?: 'Sin descripción.' }}</strong>
        </div>
    </section>

    {{-- Actualizar estado --}}
    <section class="td-card td-section">
        <div class="td-section-head">
            <h2>Actualizar estado</h2>
        </div>

        <form action="{{ route('admin.tareas.estado', $registro->id) }}" method="POST" class="td-inline" id="estadoForm">
            @csrf
            @method('PATCH')

            <div class="td-field">
                <label for="estado_id">Nuevo estado</label>
                <select name="estado_id" id="estado_id" required data-actual="{{ $registro->estado_id }}">
                    @foreach ($estados as $estado)
                        @php $clase = $clasificarEstado($estado->nombre); @endphp
                        <option
                            value="{{ $estado->id }}"
                            data-cierre="{{ in_array($clase, ['done', 'cancel'], true) ? '1' : '0' }}"
                            @selected($registro->estado_id == $estado->id)
                        >
                            {{ $estado->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('estado_id')
                    <div class="td-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="td-button primary" id="estadoBtn" disabled>
                <span class="td-spinner"></span>
                <span>Guardar estado</span>
            </button>
        </form>
    </section>

    {{-- Personas asignadas --}}
    <section class="td-card td-section">
        <div class="td-section-head">
            <h2>Personas asignadas</h2>
            @if ($totalAsignados > 0)
                <span class="td-count">{{ $finalizadasAsig }} de {{ $totalAsignados }} finalizaron</span>
            @endif
        </div>

        @if ($totalAsignados > 0)
            <div class="td-progress" role="progressbar" aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">
                <i style="width: {{ $porcentaje }}%"></i>
            </div>

            <div class="td-people">
                @foreach ($asignaciones as $asignacion)
                    @php
                        $nombre  = trim(($asignacion->nombres ?? '') . ' ' . ($asignacion->apellidos ?? ''));
                        $titulo  = $nombre !== '' ? $nombre : $asignacion->username;
                        $estadoA = $asignacion->fecha_finalizacion ? ['done', 'Finalizada']
                                 : ($asignacion->fecha_aceptacion ? ['progress', 'En curso'] : ['pending', 'Sin iniciar']);
                    @endphp
                    <article class="td-person">
                        <span class="td-avatar">{{ $iniciales($titulo) }}</span>
                        <div class="td-person-info">
                            <span class="td-person-name">{{ $titulo }}</span>
                            <span class="td-person-user">{{ '@' . $asignacion->username }}</span>
                            <div class="td-person-dates">
                                Asignada: {{ $fmt($asignacion->fecha_asignacion) }}<br>
                                Inicio: {{ $fmt($asignacion->fecha_aceptacion, 'Sin iniciar') }}<br>
                                Fin: {{ $fmt($asignacion->fecha_finalizacion, 'Pendiente') }}
                            </div>
                        </div>
                        <span class="td-badge s-{{ $estadoA[0] }}">{{ $estadoA[1] }}</span>
                    </article>
                @endforeach
            </div>
        @else
            <div class="td-empty">Esta tarea todavía no tiene personas asignadas.</div>
        @endif
    </section>

    {{-- Evidencias --}}
    <section class="td-card td-section">
        <div class="td-section-head">
            <h2>Evidencias</h2>
            <span class="td-count">{{ $evidencias->count() }} {{ $evidencias->count() === 1 ? 'archivo' : 'archivos' }}</span>
        </div>

        <form
            action="{{ route('admin.tareas.evidencias.store', $registro->id) }}"
            method="POST"
            enctype="multipart/form-data"
            id="evidenciaForm"
            novalidate
        >
            @csrf

            <label class="td-drop" id="dropZone" for="archivo">
                <strong id="dropTitle">Arrastra un archivo o haz clic para elegir</strong>
                <span id="dropHint">JPG, PNG o PDF · máximo 5 MB</span>
                <input type="file" name="archivo" id="archivo" accept=".jpg,.jpeg,.png,.pdf" required>
            </label>
            <div class="td-error" id="archivoError" @if (! $errors->has('archivo')) hidden @endif>{{ $errors->first('archivo') }}</div>

            <div class="td-upload-row">
                <div class="td-field">
                    <label for="descripcion_evidencia">Descripción opcional</label>
                    <input
                        type="text"
                        name="descripcion"
                        id="descripcion_evidencia"
                        maxlength="255"
                        value="{{ old('descripcion') }}"
                        placeholder="Detalle de la evidencia"
                    >
                    @error('descripcion')
                        <div class="td-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="td-button primary" id="uploadBtn">
                    <span class="td-spinner"></span>
                    <span>Subir evidencia</span>
                </button>
            </div>
        </form>

        @if ($evidencias->count() > 0)
            <div class="td-evidencias">
                @foreach ($evidencias as $evidencia)
                    @php
                        $url   = Storage::disk('public')->url($evidencia->archivo);
                        $ext   = strtolower(pathinfo($evidencia->archivo, PATHINFO_EXTENSION));
                        $esImg = in_array($ext, ['jpg', 'jpeg', 'png'], true);
                    @endphp
                    <article class="td-ev">
                        @if ($esImg)
                            <button type="button" class="td-ev-thumb" data-lightbox="{{ $url }}" data-caption="{{ $evidencia->descripcion ?: 'Evidencia' }}" aria-label="Ver imagen ampliada">
                                <img src="{{ $url }}" alt="{{ $evidencia->descripcion ?: 'Evidencia' }}" loading="lazy">
                            </button>
                        @else
                            <div class="td-ev-thumb pdf">PDF</div>
                        @endif

                        <div class="td-ev-body">
                            <span class="td-ev-desc">{{ $evidencia->descripcion ?: 'Sin descripción' }}</span>
                            <span class="td-ev-meta">{{ $evidencia->username ?: 'No registrado' }} · {{ $fmt($evidencia->fecha) }}</span>
                            <a href="{{ $url }}" target="_blank" rel="noopener" class="td-button small">Abrir archivo</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="td-empty" style="margin-top: 16px">Todavía no hay evidencias adjuntas.</div>
        @endif
    </section>
</div>

<dialog class="td-lightbox" id="lightbox">
    <img src="" alt="" id="lightboxImg">
    <form method="dialog">
        <span id="lightboxCaption"></span>
        <button class="td-button small">Cerrar</button>
    </form>
</dialog>
@endsection

@push('scripts')
<script>
(function () {
    /* ---------- Estado: habilitar solo si cambia + confirmar cierre ---------- */
    const estadoForm = document.getElementById('estadoForm');
    const estadoSel  = document.getElementById('estado_id');
    const estadoBtn  = document.getElementById('estadoBtn');

    estadoSel.addEventListener('change', () => {
        estadoBtn.disabled = estadoSel.value === estadoSel.dataset.actual;
    });

    estadoForm.addEventListener('submit', e => {
        if (estadoBtn.classList.contains('loading')) { e.preventDefault(); return; }

        const opt = estadoSel.selectedOptions[0];
        if (opt && opt.dataset.cierre === '1') {
            if (!confirm(`¿Cambiar el estado a "${opt.textContent.trim()}"? Esta acción cierra la tarea.`)) {
                e.preventDefault();
                return;
            }
        }
        estadoBtn.classList.add('loading');
        estadoBtn.disabled = true;
    });

    /* ---------- Evidencia: validación, drag & drop, doble envío ---------- */
    const MAX_BYTES = 5 * 1024 * 1024;
    const OK_EXT    = ['jpg', 'jpeg', 'png', 'pdf'];

    const form      = document.getElementById('evidenciaForm');
    const input     = document.getElementById('archivo');
    const drop      = document.getElementById('dropZone');
    const dropTitle = document.getElementById('dropTitle');
    const dropHint  = document.getElementById('dropHint');
    const errBox    = document.getElementById('archivoError');
    const uploadBtn = document.getElementById('uploadBtn');

    const defaultTitle = dropTitle.textContent;
    const defaultHint  = dropHint.textContent;

    function showError(msg) {
        errBox.textContent = msg;
        errBox.hidden = !msg;
    }

    function formatSize(bytes) {
        return bytes < 1024 * 1024
            ? (bytes / 1024).toFixed(0) + ' KB'
            : (bytes / 1024 / 1024).toFixed(2) + ' MB';
    }

    function validate(file) {
        if (!file) return 'Selecciona un archivo.';
        const ext = file.name.split('.').pop().toLowerCase();
        if (!OK_EXT.includes(ext)) return 'Formato no permitido. Usa JPG, PNG o PDF.';
        if (file.size > MAX_BYTES) return `El archivo pesa ${formatSize(file.size)} y el máximo es 5 MB.`;
        return '';
    }

    function refresh() {
        const file = input.files[0];
        if (!file) {
            drop.classList.remove('has-file');
            dropTitle.textContent = defaultTitle;
            dropHint.textContent = defaultHint;
            return;
        }
        const msg = validate(file);
        showError(msg);
        if (msg) {
            input.value = '';
            drop.classList.remove('has-file');
            dropTitle.textContent = defaultTitle;
            dropHint.textContent = defaultHint;
            return;
        }
        drop.classList.add('has-file');
        dropTitle.textContent = file.name;
        dropHint.textContent = formatSize(file.size) + ' · listo para subir';
    }

    input.addEventListener('change', refresh);

    ['dragenter', 'dragover'].forEach(ev =>
        drop.addEventListener(ev, e => { e.preventDefault(); drop.classList.add('over'); }));
    ['dragleave', 'drop'].forEach(ev =>
        drop.addEventListener(ev, e => { e.preventDefault(); drop.classList.remove('over'); }));

    drop.addEventListener('drop', e => {
        if (e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            refresh();
        }
    });

    form.addEventListener('submit', e => {
        if (uploadBtn.classList.contains('loading')) { e.preventDefault(); return; }

        const msg = validate(input.files[0]);
        if (msg) {
            e.preventDefault();
            showError(msg);
            drop.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        uploadBtn.classList.add('loading');
        uploadBtn.disabled = true;
        uploadBtn.lastElementChild.textContent = 'Subiendo…';
    });

    /* ---------- Lightbox de imágenes ---------- */
    const dialog  = document.getElementById('lightbox');
    const dlgImg  = document.getElementById('lightboxImg');
    const dlgCap  = document.getElementById('lightboxCaption');

    document.querySelectorAll('[data-lightbox]').forEach(btn => {
        btn.addEventListener('click', () => {
            dlgImg.src = btn.dataset.lightbox;
            dlgCap.textContent = btn.dataset.caption;
            dialog.showModal();
        });
    });

    dialog.addEventListener('click', e => { if (e.target === dialog) dialog.close(); });
})();
</script>
@endpush