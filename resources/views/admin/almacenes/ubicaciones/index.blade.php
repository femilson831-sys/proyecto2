@extends('admin.layouts.app')

@section('title', 'Ubicaciones del almacén')

@push('styles')
<style>
    .ubicaciones-page {
        --bg: #13141a;
        --panel: #1a1c23;
        --panel-2: #20232b;
        --border: #2b2e38;
        --text: #ececf0;
        --muted: #9a9ca6;
        --red: #e31b23;
        --red-light: #ff9aa0;
        --green: #4ade80;
        --amber: #f5b942;

        display: flex;
        flex-direction: column;
        gap: 18px;
        color: var(--text);
        font-variant-numeric: tabular-nums;
    }

    .ubicaciones-page *,
    .ubicaciones-page *::before,
    .ubicaciones-page *::after {
        box-sizing: border-box;
    }

    .ubicaciones-header,
    .ubicaciones-card,
    .ubicaciones-stat {
        border: 1px solid var(--border);
        border-radius: 16px;
        background: var(--panel);
    }

    .ubicaciones-header {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
        padding: 25px;
        background:
            radial-gradient(ellipse at 100% 0%, rgba(227, 27, 35, .17), transparent 55%),
            var(--bg);
    }

    .ubicaciones-header::before {
        content: "";
        position: absolute;
        inset: 0 0 auto;
        height: 2px;
        background: linear-gradient(90deg, var(--red), transparent);
    }

    .ubicaciones-eyebrow {
        color: var(--red-light);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.1px;
        text-transform: uppercase;
    }

    .ubicaciones-header h1 {
        margin: 8px 0;
        color: #fff;
        font-size: clamp(22px, 3vw, 30px);
    }

    .ubicaciones-header p,
    .ubicaciones-card-header p {
        margin: 0;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.6;
    }

    .ubicaciones-button {
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 14px;
        border: 1px solid #383b45;
        border-radius: 9px;
        color: #eee;
        background: #24262e;
        font: inherit;
        font-size: 12px;
        font-weight: 750;
        text-decoration: none;
        cursor: pointer;
        transition: .18s ease;
    }

    .ubicaciones-button:hover {
        color: #fff;
        border-color: #585d6a;
        background: #2b2e37;
        transform: translateY(-1px);
    }

    .ubicaciones-button.primary {
        border-color: #c9161e;
        color: #fff;
        background: linear-gradient(135deg, #e31b23, #ad1219);
    }

    .ubicaciones-button.primary:hover {
        background: linear-gradient(135deg, #f02a32, #c4161e);
    }

    .ubicaciones-button.danger {
        color: #ff9aa0;
        border-color: rgba(227, 27, 35, .35);
        background: rgba(227, 27, 35, .08);
    }

    .ubicaciones-button:focus-visible,
    .ubicaciones-page input:focus-visible,
    .ubicaciones-page select:focus-visible {
        outline: 2px solid #ff777d;
        outline-offset: 2px;
    }

    .ubicaciones-alert {
        padding: 13px 15px;
        border: 1px solid;
        border-radius: 10px;
        font-size: 12px;
        line-height: 1.55;
    }

    .ubicaciones-alert.success {
        color: #9beab8;
        border-color: rgba(74, 222, 128, .3);
        background: rgba(74, 222, 128, .08);
    }

    .ubicaciones-alert.error {
        color: #ff9aa0;
        border-color: rgba(227, 27, 35, .35);
        background: rgba(227, 27, 35, .08);
    }

    .ubicaciones-alert ul {
        margin: 7px 0 0;
        padding-left: 19px;
    }

    .ubicaciones-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .ubicaciones-stat {
        padding: 16px;
        background: linear-gradient(145deg, #1c1e25, #17181e);
    }

    .ubicaciones-stat span {
        display: block;
        color: var(--muted);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
    }

    .ubicaciones-stat strong {
        display: block;
        margin: 7px 0 3px;
        color: #fff;
        font-size: 25px;
    }

    .ubicaciones-stat small {
        color: var(--muted);
        font-size: 10px;
    }

    .ubicaciones-card {
        overflow: hidden;
    }

    .ubicaciones-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding: 17px 19px;
        border-bottom: 1px solid var(--border);
    }

    .ubicaciones-card-header h2 {
        margin: 0 0 5px;
        color: #fff;
        font-size: 15px;
    }

    .ubicaciones-conteo {
        padding: 6px 9px;
        border: 1px solid #383b45;
        border-radius: 999px;
        color: #c9cad0;
        background: #20222a;
        font-size: 10px;
        font-weight: 700;
    }

    .selector-ubicacion,
    .ubicaciones-form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: end;
        gap: 12px;
        padding: 17px 19px;
    }

    .ubicaciones-form {
        grid-template-columns:
            minmax(130px, .65fr)
            minmax(180px, 1fr)
            minmax(130px, .65fr)
            auto;
    }

    .ubicaciones-field {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .ubicaciones-label,
    .ubicaciones-field label {
        color: #d8d9de;
        font-size: 11px;
        font-weight: 750;
    }

    .ubicaciones-page input:not([type="radio"]),
    .ubicaciones-page select {
        width: 100%;
        min-height: 42px;
        padding: 0 11px;
        border: 1px solid #383b45;
        border-radius: 8px;
        color: #eee;
        background: #202229;
        font: inherit;
        font-size: 12px;
    }

    .ubicaciones-page input::placeholder {
        color: #737681;
    }

    .ubicaciones-page select:disabled,
    .ubicaciones-page input:disabled {
        opacity: .55;
        cursor: not-allowed;
    }

    .ub-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
        padding: 0 19px 19px;
    }

    .ub-tile {
        display: block;
        min-width: 0;
        padding: 13px;
        border: 1px solid #30333d;
        border-radius: 11px;
        color: inherit;
        background: #1d1f26;
        text-decoration: none;
        transition: .18s ease;
    }

    .ub-tile:hover,
    .ub-tile.activa {
        border-color: rgba(227, 27, 35, .6);
        background: linear-gradient(145deg, rgba(227, 27, 35, .12), #1d1f26 70%);
        transform: translateY(-1px);
    }

    .ub-tile-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .ub-tile-codigo {
        color: #fff;
        font-size: 16px;
        font-weight: 850;
    }

    .ub-tile-lotes {
        color: var(--muted);
        font-size: 9px;
    }

    .ub-tile-nombre {
        display: block;
        overflow: hidden;
        margin: 8px 0;
        color: #bfc1c8;
        font-size: 10px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ub-mini,
    .ocupacion-barra {
        height: 6px;
        overflow: hidden;
        border-radius: 99px;
        background: #30323b;
    }

    .ub-mini span,
    .ocupacion-barra span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #e31b23, #f5b942);
    }

    .ub-tile-pct {
        display: block;
        margin-top: 6px;
        color: var(--muted);
        font-size: 9px;
    }

    .ubicacion-detail {
        padding: 17px;
    }

    .ubicacion-summary {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(180px, .55fr) auto;
        align-items: center;
        gap: 16px;
        padding: 16px;
        border: 1px solid #30333d;
        border-radius: 12px;
        background: #1e2027;
    }

    .ubicacion-identidad {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .ubicacion-codigo {
        display: grid;
        place-items: center;
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        border: 1px solid rgba(227, 27, 35, .35);
        border-radius: 12px;
        color: #ff9aa0;
        background: rgba(227, 27, 35, .1);
        font-size: 14px;
        font-weight: 850;
    }

    .ubicacion-identidad h3 {
        margin: 0 0 4px;
        color: #fff;
        font-size: 14px;
    }

    .ubicacion-identidad p {
        margin: 0;
        color: var(--muted);
        font-size: 10px;
    }

    .ubicacion-ocupacion-top {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 7px;
        font-size: 10px;
    }

    .ubicacion-ocupacion-top strong {
        color: #fff;
    }

    .ubicacion-ocupacion-top span {
        color: var(--muted);
    }

    .ubicacion-contenido {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(300px, .9fr);
        gap: 14px;
        margin-top: 15px;
    }

    .ubicacion-asignaciones,
    .asignar-lote,
    .trasladar-form {
        min-width: 0;
        padding: 16px;
        border: 1px solid #30333d;
        border-radius: 12px;
        background: #1d1f26;
    }

    .ubicacion-asignaciones h4,
    .asignar-lote h4,
    .trasladar-form h4 {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin: 0 0 12px;
        color: #fff;
        font-size: 12px;
    }

    .ubicacion-asignaciones h4 span {
        display: grid;
        place-items: center;
        min-width: 23px;
        height: 23px;
        padding: 0 6px;
        border-radius: 99px;
        color: #ff9aa0;
        background: rgba(227, 27, 35, .13);
    }

    .asignacion {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 0;
        border-top: 1px solid #2b2e37;
    }

    .asignacion:first-of-type {
        border-top: 0;
    }

    .asignacion-datos {
        min-width: 0;
    }

    .asignacion-datos strong {
        display: flex;
        align-items: center;
        gap: 7px;
        flex-wrap: wrap;
        color: #eee;
        font-size: 11px;
    }

    .asignacion-lote {
        color: #ff9aa0;
        font-size: 9px;
        font-weight: 700;
    }

    .asignacion-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 7px 12px;
        margin-top: 6px;
        color: var(--muted);
        font-size: 9px;
    }

    .asignacion-acciones {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .ubicaciones-button.sm {
        min-height: 32px;
        padding: 0 9px;
        font-size: 10px;
    }

    .asignacion-empty,
    .ubicaciones-vacio {
        padding: 22px 15px;
        color: var(--muted);
        text-align: center;
        font-size: 11px;
        line-height: 1.6;
    }

    .asignar-lote,
    .trasladar-form {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .asignar-lote > p {
        margin: -7px 0 3px;
        color: var(--muted);
        font-size: 10px;
        line-height: 1.5;
    }

    .form-fields {
        display: grid;
        gap: 11px;
    }

    .paso {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 6px;
    }

    .paso-num {
        display: grid;
        place-items: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        color: #ff9aa0;
        background: rgba(227, 27, 35, .14);
        font-size: 9px;
        font-weight: 800;
    }

    .modo-cantidad {
        display: grid;
        gap: 8px;
        margin: 1px 0 8px;
    }

    .modo-cantidad label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #c8c9d0;
        font-size: 10px;
        cursor: pointer;
    }

    .modo-cantidad input {
        accent-color: var(--red);
    }

    .ubicaciones-hint {
        color: var(--muted);
        font-size: 9px;
        line-height: 1.5;
    }

    .ub-pct-chips {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 6px;
    }

    .ub-chip-pct {
        min-height: 35px;
        border: 1px solid #383b45;
        border-radius: 7px;
        color: #dfe0e5;
        background: #24262e;
        font-size: 10px;
        font-weight: 700;
        cursor: pointer;
    }

    .ub-chip-pct small {
        display: block;
        color: var(--muted);
        font-size: 8px;
        font-weight: 500;
    }

    .ub-chip-pct:hover,
    .ub-chip-pct.activo {
        border-color: var(--red);
        color: #fff;
        background: rgba(227, 27, 35, .13);
    }

    .ub-aviso {
        padding: 9px;
        border: 1px solid rgba(245, 185, 66, .3);
        border-radius: 8px;
        color: #f6d58d;
        background: rgba(245, 185, 66, .08);
        font-size: 10px;
        line-height: 1.5;
    }

    [hidden] {
        display: none !important;
    }

    .asignar-lote > .ubicaciones-button,
    .trasladar-form > .ubicaciones-button {
        width: 100%;
    }

    .ubicaciones-vacio {
        border: 1px dashed #383b45;
        border-radius: 12px;
        background: #191b21;
    }

    /* ===== Formulario de traslado ===== */
    .trasladar-form {
        position: relative;
        overflow: hidden;
        gap: 14px;
        background:
            radial-gradient(ellipse at 100% 0%, rgba(227, 27, 35, .10), transparent 50%),
            #1d1f26;
    }

    .trasladar-form::before {
        content: "";
        position: absolute;
        inset: 0 0 auto;
        height: 2px;
        background: linear-gradient(90deg, var(--red), transparent);
    }

    .traslado-head {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .traslado-icono {
        display: grid;
        place-items: center;
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        border: 1px solid rgba(227, 27, 35, .35);
        border-radius: 11px;
        color: var(--red-light);
        background: rgba(227, 27, 35, .1);
        font-size: 18px;
        font-weight: 800;
    }

    .traslado-head h4 {
        margin: 0 0 4px;
        font-size: 13px;
    }

    .traslado-head p {
        margin: 0;
        color: var(--muted);
        font-size: 10px;
        line-height: 1.55;
    }

    .traslado-ruta {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
        align-items: center;
        gap: 10px;
    }

    .traslado-punto {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 3px;
        padding: 11px 12px;
        border: 1px solid #30333d;
        border-radius: 11px;
        background: #22242c;
        transition: .18s ease;
    }

    .traslado-punto.destino {
        border-style: dashed;
    }

    .traslado-punto.destino.lista {
        border-style: solid;
        border-color: rgba(74, 222, 128, .4);
        background: rgba(74, 222, 128, .06);
    }

    .traslado-punto-label {
        color: var(--muted);
        font-size: 8px;
        font-weight: 800;
        letter-spacing: .9px;
        text-transform: uppercase;
    }

    .traslado-punto-codigo {
        overflow: hidden;
        color: #fff;
        font-size: 17px;
        font-weight: 850;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .traslado-punto small {
        overflow: hidden;
        color: var(--muted);
        font-size: 9px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .traslado-flecha {
        display: grid;
        place-items: center;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        color: #fff;
        background: linear-gradient(135deg, #e31b23, #ad1219);
        box-shadow: 0 4px 12px rgba(227, 27, 35, .3);
        font-size: 14px;
        font-weight: 800;
    }

    .traslado-seccion {
        display: flex;
        flex-direction: column;
        gap: 7px;
        padding: 12px;
        border: 1px solid #2b2e37;
        border-radius: 11px;
        background: #1a1c23;
    }

    .traslado-seccion .paso {
        margin-bottom: 0;
    }

    .traslado-chips {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 6px;
    }

    .traslado-chip {
        min-height: 32px;
        border: 1px solid #383b45;
        border-radius: 7px;
        color: #dfe0e5;
        background: #24262e;
        font: inherit;
        font-size: 10px;
        font-weight: 700;
        cursor: pointer;
        transition: .15s ease;
    }

    .traslado-chip:hover:not(:disabled),
    .traslado-chip.activo {
        border-color: var(--red);
        color: #fff;
        background: rgba(227, 27, 35, .13);
    }

    .traslado-chip:disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    .traslado-motivos {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .traslado-motivos .traslado-chip {
        min-height: 28px;
        padding: 0 10px;
        border-radius: 999px;
        font-weight: 600;
    }

    .traslado-resumen {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px;
        border: 1px solid rgba(74, 222, 128, .3);
        border-radius: 11px;
        color: #b5f0cb;
        background: rgba(74, 222, 128, .07);
        font-size: 10px;
        line-height: 1.6;
    }

    .traslado-resumen-icono {
        display: grid;
        place-items: center;
        width: 22px;
        height: 22px;
        flex: 0 0 22px;
        border-radius: 50%;
        color: #0f2a19;
        background: var(--green);
        font-size: 12px;
        font-weight: 900;
    }

    .traslado-submit {
        min-height: 44px;
        width: 100%;
        font-size: 12px;
    }

    @media (max-width: 900px) {
        .ubicacion-contenido {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 680px) {
        .ubicaciones-stats {
            grid-template-columns: 1fr;
        }

        .ubicaciones-header {
            align-items: stretch;
            padding: 20px;
        }

        .ubicaciones-header .ubicaciones-button,
        .selector-ubicacion .ubicaciones-button {
            width: 100%;
        }

        .selector-ubicacion,
        .ubicaciones-form {
            grid-template-columns: 1fr;
            padding: 15px;
        }

        .ub-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            padding: 0 15px 15px;
        }

        .ubicacion-summary {
            grid-template-columns: 1fr;
        }

        .ubicacion-summary > form,
        .ubicacion-summary > form .ubicaciones-button {
            width: 100%;
        }

        .traslado-ruta {
            grid-template-columns: 1fr;
        }

        .traslado-flecha {
            justify-self: center;
            transform: rotate(90deg);
        }
    }

    @media (max-width: 420px) {
        .ub-grid {
            grid-template-columns: 1fr;
        }

        .ub-pct-chips {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .traslado-chips {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .asignacion {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
@php
    $totalAsignaciones = $ubicaciones->sum(
        fn ($ubicacion) => (int) $ubicacion->asignaciones_count
    );

    $sumaOcupacion = $ubicaciones->sum(
        fn ($ubicacion) => (float) $ubicacion->ocupado
    );

    $hoyUbic = \Illuminate\Support\Carbon::today();

    $codigoVisible = $ubicacionSeleccionada
        ? str_replace($almacen->codigo . '-', '', $ubicacionSeleccionada->codigo)
        : '';

    $ocupado = $ubicacionSeleccionada
        ? min(100, max(0, (float) $ubicacionSeleccionada->ocupado))
        : 0;

    $espacioDisponible = max(0, 100 - $ocupado);
@endphp

<div class="ubicaciones-page">

    <header class="ubicaciones-header">
        <div>
            <span class="ubicaciones-eyebrow">Distribución del inventario</span>

            <h1>Ubicaciones · Almacén {{ $almacen->codigo }}</h1>

            <p>
                Consulta los lotes por ubicación, registra mercancía pendiente
                y trasládala entre ubicaciones del mismo almacén.
            </p>
        </div>

        <a href="{{ route('admin.almacenes.index') }}" class="ubicaciones-button">
            ← Volver a almacenes
        </a>
    </header>

    @if (session('success'))
        <div class="ubicaciones-alert success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="ubicaciones-alert error" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="ubicaciones-alert error" role="alert">
            <strong>Revisa los datos ingresados:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="ubicaciones-stats" aria-label="Resumen de ubicaciones">
        <article class="ubicaciones-stat">
            <span>Ubicaciones registradas</span>
            <strong>{{ number_format($ubicaciones->count(), 0, ',', '.') }}</strong>
            <small>Dentro de este almacén</small>
        </article>

        <article class="ubicaciones-stat">
            <span>Productos y lotes ubicados</span>
            <strong>{{ number_format($totalAsignaciones, 0, ',', '.') }}</strong>
            <small>Asignaciones activas</small>
        </article>

        <article class="ubicaciones-stat">
            <span>Ocupación registrada</span>
            <strong>{{ number_format($sumaOcupacion, 2, ',', '.') }}%</strong>
            <small>Suma referencial de las ubicaciones</small>
        </article>
    </section>

    <section class="ubicaciones-card">
        <div class="ubicaciones-card-header">
            <div>
                <h2>Seleccionar ubicación</h2>
                <p>Elige una ubicación para consultar, guardar o trasladar sus productos.</p>
            </div>

            <span class="ubicaciones-conteo">
                {{ $ubicaciones->count() }} ubicación(es)
            </span>
        </div>

        <form
            method="GET"
            action="{{ route('admin.almacenes.ubicaciones.index', ['almacenId' => $almacen->id]) }}"
            class="selector-ubicacion"
        >
            <div class="ubicaciones-field">
                <label for="ubicacion_id">Ubicación del almacén</label>

                <select id="ubicacion_id" name="ubicacion_id">
                    <option value="">Selecciona una ubicación</option>

                    @foreach ($ubicaciones as $opcion)
                        @php
                            $codigoOpcion = str_replace(
                                $almacen->codigo . '-',
                                '',
                                $opcion->codigo
                            );
                        @endphp

                        <option
                            value="{{ $opcion->id }}"
                            @selected($ubicacionSeleccionada && $ubicacionSeleccionada->id == $opcion->id)
                        >
                            {{ $codigoOpcion }} — {{ $opcion->nombre ?: 'Sin descripción' }}
                            · {{ $opcion->asignaciones_count }} lote(s)
                        </option>
                    @endforeach
                </select>
            </div>

            <button class="ubicaciones-button primary" type="submit">
                Ver ubicación
            </button>
        </form>

        @if ($ubicaciones->isNotEmpty())
            <div class="ub-grid" aria-label="Acceso rápido a ubicaciones">
                @foreach ($ubicaciones as $tile)
                    @php
                        $codigoTile = str_replace(
                            $almacen->codigo . '-',
                            '',
                            $tile->codigo
                        );

                        $pctTile = min(100, max(0, (float) $tile->ocupado));
                    @endphp

                    <a
                        href="{{ route('admin.almacenes.ubicaciones.index', [
                            'almacenId' => $almacen->id,
                            'ubicacion_id' => $tile->id,
                        ]) }}"
                        class="ub-tile {{ $ubicacionSeleccionada && $ubicacionSeleccionada->id == $tile->id ? 'activa' : '' }}"
                    >
                        <div class="ub-tile-top">
                            <span class="ub-tile-codigo">{{ $codigoTile }}</span>
                            <span class="ub-tile-lotes">{{ $tile->asignaciones_count }} lote(s)</span>
                        </div>

                        <span class="ub-tile-nombre">
                            {{ $tile->nombre ?: 'Sin descripción' }}
                        </span>

                        <div class="ub-mini">
                            <span style="width: {{ $pctTile }}%"></span>
                        </div>

                        <span class="ub-tile-pct">
                            {{ number_format($pctTile, 0, ',', '.') }}% ocupado
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <section class="ubicaciones-card">
        <div class="ubicaciones-card-header">
            <div>
                <h2>Crear ubicación</h2>
                <p>Registra un código como AB1, B3 o B8. La capacidad en m es opcional.</p>
            </div>
        </div>

        <form
            action="{{ route('admin.almacenes.ubicaciones.store', ['almacenId' => $almacen->id]) }}"
            method="POST"
            class="ubicaciones-form"
        >
            @csrf

            <div class="ubicaciones-field">
                <label for="codigo">Código *</label>
                <input
                    id="codigo"
                    name="codigo"
                    value="{{ old('codigo') }}"
                    maxlength="40"
                    placeholder="AB1"
                    required
                >
            </div>

            <div class="ubicaciones-field">
                <label for="nombre">Descripción opcional</label>
                <input
                    id="nombre"
                    name="nombre"
                    value="{{ old('nombre') }}"
                    maxlength="120"
                    placeholder="Estante de bebidas"
                >
            </div>

            <div class="ubicaciones-field">
                <label for="capacidad_m3">Capacidad en m (opcional)</label>
                <input
                    id="capacidad_m3"
                    name="capacidad_m3"
                    type="number"
                    value="{{ old('capacidad_m3') }}"
                    min="0.001"
                    step="0.001"
                    placeholder="Ejemplo: 12.5"
                >
            </div>

            <button type="submit" class="ubicaciones-button primary">
                ＋ Crear ubicación
            </button>
        </form>
    </section>

    @if ($ubicacionSeleccionada)
        <section class="ubicaciones-card">
            <div class="ubicaciones-card-header">
                <div>
                    <h2>Contenido de {{ $codigoVisible }}</h2>
                    <p>
                        Revisa los lotes guardados, asigna unidades pendientes
                        y mueve existencias a otra ubicación.
                    </p>
                </div>
            </div>

            <div class="ubicacion-detail">
                <div class="ubicacion-summary">
                    <div class="ubicacion-identidad">
                        <span class="ubicacion-codigo">{{ $codigoVisible }}</span>

                        <div>
                            <h3>{{ $ubicacionSeleccionada->nombre ?: 'Sin descripción' }}</h3>
                            <p>Almacén {{ $almacen->codigo }}</p>
                        </div>
                    </div>

                    <div class="ubicacion-ocupacion">
                        <div class="ubicacion-ocupacion-top">
                            <strong>{{ number_format($ocupado, 2, ',', '.') }}% ocupado</strong>
                            <span>{{ number_format($espacioDisponible, 2, ',', '.') }}% libre</span>
                        </div>

                        <div
                            class="ocupacion-barra"
                            role="progressbar"
                            aria-valuenow="{{ $ocupado }}"
                            aria-valuemin="0"
                            aria-valuemax="100"
                        >
                            <span style="width: {{ $ocupado }}%"></span>
                        </div>
                    </div>

                    @if ((int) $ubicacionSeleccionada->asignaciones_count === 0)
                        <form
                            action="{{ route('admin.almacenes.ubicaciones.destroy', [
                                'almacenId' => $almacen->id,
                                'ubicacionId' => $ubicacionSeleccionada->id,
                            ]) }}"
                            method="POST"
                            onsubmit="return confirm('¿Eliminar esta ubicación vacía?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button class="ubicaciones-button danger" type="submit">
                                Eliminar ubicación
                            </button>
                        </form>
                    @endif
                </div>

                <div class="ubicacion-contenido">
                    <section class="ubicacion-asignaciones">
                        <h4>
                            Productos y lotes guardados aquí
                            <span>{{ $ubicacionSeleccionada->asignaciones_count }}</span>
                        </h4>

                        @forelse ($ubicacionSeleccionada->asignaciones as $asignacion)
                            @php
                                $diasVenc = $asignacion->fecha_vencimiento
                                    ? $hoyUbic->diffInDays(
                                        \Illuminate\Support\Carbon::parse($asignacion->fecha_vencimiento)->startOfDay(),
                                        false
                                    )
                                    : null;
                            @endphp

                            <article class="asignacion">
                                <div class="asignacion-datos">
                                    <strong>
                                        {{ $asignacion->producto }}

                                        <span class="asignacion-lote">
                                            Lote {{ $asignacion->codigo_lote ?: 'Sin código' }}
                                        </span>
                                    </strong>

                                    <div class="asignacion-meta">
                                        <span>
                                            {{ number_format((float) $asignacion->cantidad, 3, ',', '.') }}
                                            {{ $asignacion->unidad }}
                                        </span>

                                        <span>
                                            Ocupa
                                            {{ number_format((float) $asignacion->porcentaje_ocupacion, 2, ',', '.') }}%
                                        </span>

                                        @if ($asignacion->fecha_vencimiento)
                                            <span>
                                                {{ $diasVenc < 0 ? 'Vencido' : ($diasVenc <= 30 ? 'Vence pronto' : 'Vence') }}:
                                                {{ \Illuminate\Support\Carbon::parse($asignacion->fecha_vencimiento)->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="asignacion-acciones">
                                    <form
                                        action="{{ route('admin.almacenes.ubicaciones.lotes.destroy', [
                                            'almacenId' => $almacen->id,
                                            'ubicacionId' => $ubicacionSeleccionada->id,
                                            'asignacionId' => $asignacion->asignacion_id,
                                        ]) }}"
                                        method="POST"
                                        onsubmit="return confirm('¿Retirar este lote de la ubicación?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="ubicaciones-button danger sm">
                                            Retirar
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <div class="asignacion-empty">
                                Esta ubicación todavía no tiene lotes guardados.
                            </div>
                        @endforelse
                    </section>

                    <form
                        action="{{ route('admin.almacenes.ubicaciones.lotes.store', [
                            'almacenId' => $almacen->id,
                            'ubicacionId' => $ubicacionSeleccionada->id,
                        ]) }}"
                        method="POST"
                        class="asignar-lote js-asignar-lote"
                        data-espacio="{{ $espacioDisponible }}"
                    >
                        @csrf

                        <h4>Asignar lote a {{ $codigoVisible }}</h4>
                        <p>
                            Selecciona primero el producto y luego uno de sus lotes
                            con unidades pendientes por ubicar.
                        </p>

                        <div class="ubicaciones-alert error js-error" role="alert" hidden>
                            <span class="js-error-texto"></span>
                        </div>

                        <div class="form-fields">
                            <div class="ubicaciones-field">
                                <div class="paso">
                                    <span class="paso-num">1</span>
                                    <label class="ubicaciones-label" for="producto-select">Producto *</label>
                                </div>

                                <select class="js-producto-select" id="producto-select" required>
                                    <option value="">Selecciona un producto</option>

                                    @foreach ($lotesDisponibles->unique('producto_id') as $producto)
                                        <option value="{{ $producto->producto_id }}">
                                            {{ $producto->codigo_interno }} — {{ $producto->producto }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="ubicaciones-field">
                                <div class="paso">
                                    <span class="paso-num">2</span>
                                    <label class="ubicaciones-label" for="lote-select">Lote *</label>
                                </div>

                                <select
                                    name="lote_id"
                                    id="lote-select"
                                    class="js-lote-select"
                                    required
                                    disabled
                                >
                                    <option value="">Primero selecciona un producto</option>

                                    @foreach ($lotesDisponibles as $lote)
                                        <option
                                            value="{{ $lote->lote_id }}"
                                            data-producto="{{ $lote->producto_id }}"
                                            data-inventario="{{ $lote->inventario_id }}"
                                            data-disponible="{{ $lote->cantidad_disponible }}"
                                            data-unidad="{{ $lote->unidad }}"
                                        >
                                            Lote {{ $lote->codigo_lote }}
                                            · Pendiente:
                                            {{ number_format((float) $lote->cantidad_disponible, 3, ',', '.') }}
                                            {{ $lote->unidad }}

                                            @if ($lote->fecha_vencimiento)
                                                · Vence
                                                {{ \Illuminate\Support\Carbon::parse($lote->fecha_vencimiento)->format('d/m/Y') }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" name="inventario_id" class="js-inventario-id">

                            <div class="ubicaciones-field">
                                <div class="paso">
                                    <span class="paso-num">3</span>
                                    <span class="ubicaciones-label">Cantidad *</span>
                                </div>

                                <div class="modo-cantidad">
                                    <label>
                                        <input type="radio" name="cantidad_modo" value="todo" checked>
                                        Todo lo pendiente por ubicar
                                    </label>

                                    <label>
                                        <input type="radio" name="cantidad_modo" value="personalizada">
                                        Escribir una cantidad
                                    </label>
                                </div>

                                <input
                                    type="number"
                                    name="cantidad"
                                    class="js-cantidad"
                                    min="0.001"
                                    step="0.001"
                                    placeholder="Ejemplo: 50"
                                    inputmode="decimal"
                                    disabled
                                >

                                <small class="ubicaciones-hint js-disponible">
                                    Selecciona primero el producto y el lote.
                                </small>
                            </div>

                            <div class="ubicaciones-field">
                                <div class="paso">
                                    <span class="paso-num">4</span>
                                    <span class="ubicaciones-label">Espacio ocupado *</span>
                                </div>

                                <div class="ub-pct-chips" role="group" aria-label="Porcentaje de espacio ocupado">
                                    <button type="button" class="ub-chip-pct" data-pct="25">¼<small>25%</small></button>
                                    <button type="button" class="ub-chip-pct" data-pct="50">½<small>50%</small></button>
                                    <button type="button" class="ub-chip-pct" data-pct="75">¾<small>75%</small></button>
                                    <button type="button" class="ub-chip-pct" data-pct="100">Todo<small>100%</small></button>
                                    <button type="button" class="ub-chip-pct" data-pct="personalizado">Otro</button>
                                </div>

                                <input type="hidden" name="porcentaje_ocupacion" class="js-porcentaje-final">

                                <input
                                    type="number"
                                    class="js-porcentaje-personalizado"
                                    min="0.01"
                                    max="100"
                                    step="0.01"
                                    placeholder="Porcentaje personalizado"
                                    inputmode="decimal"
                                    aria-label="Porcentaje personalizado"
                                    hidden
                                >

                                <div class="ub-aviso js-pct-aviso" role="status" hidden></div>
                            </div>
                        </div>

                        <button type="submit" class="ubicaciones-button primary">
                            Guardar en {{ $codigoVisible }}
                        </button>
                    </form>

                    <form
                        action="{{ route('admin.almacenes.ubicaciones.trasladar', [
                            'almacenId' => $almacen->id,
                        ]) }}"
                        method="POST"
                        class="trasladar-form js-trasladar-form"
                        data-origen="{{ $codigoVisible }}"
                    >
                        @csrf

                        <div class="traslado-head">
                            <span class="traslado-icono" aria-hidden="true">⇄</span>

                            <div>
                                <h4>Trasladar a otra ubicación</h4>
                                <p>
                                    Mueve todo o parte de un lote desde {{ $codigoVisible }}
                                    a otra ubicación del almacén. El stock total no cambia.
                                </p>
                            </div>
                        </div>

                        @if ($ubicacionSeleccionada->asignaciones->isEmpty())
                            <div class="ub-aviso" role="status">
                                Esta ubicación no tiene lotes para trasladar todavía.
                            </div>
                        @endif

                        <div class="traslado-ruta" aria-label="Ruta del traslado">
                            <div class="traslado-punto origen">
                                <span class="traslado-punto-label">Origen</span>
                                <strong class="traslado-punto-codigo">{{ $codigoVisible }}</strong>
                                <small>{{ $ubicacionSeleccionada->nombre ?: 'Sin descripción' }}</small>
                            </div>

                            <span class="traslado-flecha" aria-hidden="true">→</span>

                            <div class="traslado-punto destino js-destino-card">
                                <span class="traslado-punto-label">Destino</span>
                                <strong class="traslado-punto-codigo js-destino-codigo">—</strong>
                                <small class="js-destino-libre">Selecciona un destino</small>
                            </div>
                        </div>

                        <div class="traslado-seccion">
                            <div class="paso">
                                <span class="paso-num">1</span>
                                <label class="ubicaciones-label" for="traslado-asignacion">Producto y lote de origen *</label>
                            </div>

                            <select name="asignacion_id" id="traslado-asignacion" required>
                                <option value="">Selecciona producto y lote</option>

                                @foreach ($ubicacionSeleccionada->asignaciones as $asignacion)
                                    <option
                                        value="{{ $asignacion->asignacion_id }}"
                                        data-cantidad="{{ $asignacion->cantidad }}"
                                        data-unidad="{{ $asignacion->unidad }}"
                                        data-producto="{{ $asignacion->producto }}"
                                    >
                                        {{ $asignacion->producto }}
                                        · lote {{ $asignacion->codigo_lote ?: 's/código' }}
                                        · disponibles
                                        {{ number_format((float) $asignacion->cantidad, 3, ',', '.') }}
                                        {{ $asignacion->unidad }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="traslado-seccion">
                            <div class="paso">
                                <span class="paso-num">2</span>
                                <label class="ubicaciones-label" for="ubicacion-destino">Ubicación de destino *</label>
                            </div>

                            <select name="ubicacion_destino_id" id="ubicacion-destino" required>
                                <option value="">Selecciona destino</option>

                                @foreach ($ubicaciones as $destino)
                                    @if ($destino->id != $ubicacionSeleccionada->id)
                                        @php
                                            $codigoDestino = str_replace($almacen->codigo . '-', '', $destino->codigo);
                                            $libreDestino = max(0, 100 - (float) $destino->ocupado);
                                        @endphp

                                        <option
                                            value="{{ $destino->id }}"
                                            data-codigo="{{ $codigoDestino }}"
                                            data-libre="{{ $libreDestino }}"
                                        >
                                            {{ $codigoDestino }}
                                            — {{ $destino->nombre ?: 'Sin descripción' }}
                                            ({{ number_format($libreDestino, 2, ',', '.') }}% libre)
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="traslado-seccion">
                            <div class="paso">
                                <span class="paso-num">3</span>
                                <label class="ubicaciones-label" for="cantidad-traslado">Cantidad que se moverá *</label>
                            </div>

                            <div class="traslado-chips" role="group" aria-label="Cantidad rápida">
                                <button type="button" class="traslado-chip js-chip-cant" data-frac="0.25" disabled>¼</button>
                                <button type="button" class="traslado-chip js-chip-cant" data-frac="0.5" disabled>½</button>
                                <button type="button" class="traslado-chip js-chip-cant" data-frac="0.75" disabled>¾</button>
                                <button type="button" class="traslado-chip js-chip-cant" data-frac="1" disabled>Todo</button>
                            </div>

                            <input
                                type="number"
                                name="cantidad"
                                id="cantidad-traslado"
                                min="0.001"
                                step="0.001"
                                required
                                inputmode="decimal"
                                placeholder="Selecciona primero el lote"
                            >

                            <small class="ubicaciones-hint js-ayuda-traslado">
                                Elige el lote para conocer las unidades disponibles.
                            </small>
                        </div>

                        <div class="traslado-seccion">
                            <div class="paso">
                                <span class="paso-num">4</span>
                                <label class="ubicaciones-label" for="porcentaje-destino">Espacio que ocupará en destino (%)</label>
                            </div>

                            <input
                                type="number"
                                name="porcentaje_destino"
                                id="porcentaje-destino"
                                min="0.01"
                                max="100"
                                step="0.01"
                                inputmode="decimal"
                                placeholder="Opcional; proporcional a la cantidad"
                            >

                            <div class="ub-aviso js-aviso-destino" role="status" hidden></div>
                        </div>

                        <div class="traslado-seccion">
                            <div class="paso">
                                <span class="paso-num">5</span>
                                <label class="ubicaciones-label" for="motivo-traslado">Motivo (opcional)</label>
                            </div>

                            <div class="traslado-motivos" role="group" aria-label="Motivos frecuentes">
                                <button type="button" class="traslado-chip js-chip-motivo">Reorganización</button>
                                <button type="button" class="traslado-chip js-chip-motivo">Vencimiento próximo</button>
                                <button type="button" class="traslado-chip js-chip-motivo">Falta de espacio</button>
                                <button type="button" class="traslado-chip js-chip-motivo">Corrección de ubicación</button>
                            </div>

                            <input
                                type="text"
                                name="motivo"
                                id="motivo-traslado"
                                maxlength="255"
                                placeholder="Ejemplo: reorganización del almacén"
                            >
                        </div>

                        <div class="traslado-resumen js-resumen" role="status" hidden>
                            <span class="traslado-resumen-icono" aria-hidden="true">✓</span>
                            <span class="js-resumen-texto"></span>
                        </div>

                        <button type="submit" class="ubicaciones-button primary traslado-submit">
                            ⇄ Trasladar lote
                        </button>
                    </form>
                </div>
            </div>
        </section>
    @else
        <div class="ubicaciones-vacio">
            Selecciona una ubicación de la lista para ver sus productos,
            registrar lotes o iniciar un traslado.
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectorUbicacion = document.getElementById('ubicacion_id');

    if (selectorUbicacion) {
        selectorUbicacion.addEventListener('change', function () {
            this.form.submit();
        });
    }

    document.querySelectorAll('.js-asignar-lote').forEach(function (form) {
        const producto = form.querySelector('.js-producto-select');
        const lote = form.querySelector('.js-lote-select');
        const inventario = form.querySelector('.js-inventario-id');
        const cantidad = form.querySelector('.js-cantidad');
        const modos = form.querySelectorAll('input[name="cantidad_modo"]');
        const disponible = form.querySelector('.js-disponible');
        const chips = form.querySelectorAll('.ub-chip-pct');
        const porcentajeFinal = form.querySelector('.js-porcentaje-final');
        const porcentajePersonalizado = form.querySelector('.js-porcentaje-personalizado');
        const avisoPorcentaje = form.querySelector('.js-pct-aviso');
        const alerta = form.querySelector('.js-error');
        const alertaTexto = form.querySelector('.js-error-texto');
        const espacioLibre = Number(form.dataset.espacio || 0);

        const opcionesLote = Array.from(
            lote.querySelectorAll('option[data-producto]')
        );

        function mostrarError(mensaje, campo) {
            alertaTexto.textContent = mensaje;
            alerta.hidden = false;
            alerta.scrollIntoView({ behavior: 'smooth', block: 'center' });

            if (campo) {
                campo.focus({ preventScroll: true });
            }
        }

        function ocultarError() {
            alerta.hidden = true;
        }

        function actualizarAvisoPorcentaje() {
            const porcentaje = Number(porcentajeFinal.value || 0);
            const excedeEspacio = porcentaje > espacioLibre + 0.0001;

            avisoPorcentaje.hidden = !excedeEspacio;
            avisoPorcentaje.textContent = excedeEspacio
                ? `La ubicación solo tiene ${espacioLibre.toLocaleString('es-BO')}% libre.`
                : '';
        }

        function actualizarCantidad() {
            const opcion = lote.options[lote.selectedIndex];
            const hayLoteSeleccionado = opcion && opcion.dataset.disponible;

            inventario.value = hayLoteSeleccionado
                ? opcion.dataset.inventario
                : '';

            const modoSeleccionado = form.querySelector(
                'input[name="cantidad_modo"]:checked'
            )?.value;

            if (hayLoteSeleccionado) {
                cantidad.max = opcion.dataset.disponible;

                disponible.textContent =
                    `Pendiente por ubicar: ${opcion.dataset.disponible} ` +
                    `${opcion.dataset.unidad || ''}`;
            } else {
                cantidad.removeAttribute('max');
                disponible.textContent =
                    'Selecciona primero el producto y el lote.';
            }

            cantidad.disabled =
                !hayLoteSeleccionado || modoSeleccionado !== 'personalizada';

            if (modoSeleccionado === 'todo') {
                cantidad.value = '';
            }
        }

        function actualizarLotes() {
            lote.value = '';
            inventario.value = '';

            opcionesLote.forEach(function (opcion) {
                const coincide = opcion.dataset.producto === producto.value;
                opcion.hidden = !coincide;
                opcion.disabled = !coincide;
            });

            lote.disabled = !producto.value;
            lote.options[0].text = producto.value
                ? 'Selecciona un lote'
                : 'Primero selecciona un producto';

            actualizarCantidad();
        }

        producto.addEventListener('change', function () {
            ocultarError();
            actualizarLotes();
        });

        lote.addEventListener('change', function () {
            ocultarError();
            actualizarCantidad();
        });

        modos.forEach(function (radio) {
            radio.addEventListener('change', actualizarCantidad);
        });

        chips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                ocultarError();

                chips.forEach(function (otroChip) {
                    otroChip.classList.remove('activo');
                });

                chip.classList.add('activo');

                if (chip.dataset.pct === 'personalizado') {
                    porcentajePersonalizado.hidden = false;
                    porcentajeFinal.value = porcentajePersonalizado.value;
                    porcentajePersonalizado.focus();
                } else {
                    porcentajePersonalizado.hidden = true;
                    porcentajePersonalizado.value = '';
                    porcentajeFinal.value = chip.dataset.pct;
                }

                actualizarAvisoPorcentaje();
            });
        });

        porcentajePersonalizado.addEventListener('input', function () {
            porcentajeFinal.value = this.value;
            actualizarAvisoPorcentaje();
        });

        form.addEventListener('submit', function (event) {
            const modoSeleccionado = form.querySelector(
                'input[name="cantidad_modo"]:checked'
            )?.value;

            if (!producto.value || !lote.value || !inventario.value) {
                event.preventDefault();

                mostrarError(
                    'Selecciona el producto y uno de sus lotes.',
                    producto.value ? lote : producto
                );

                return;
            }

            if (
                modoSeleccionado === 'personalizada' &&
                (
                    !cantidad.value ||
                    Number(cantidad.value) > Number(cantidad.max)
                )
            ) {
                event.preventDefault();

                mostrarError(
                    'Indica una cantidad válida que no supere lo pendiente por ubicar.',
                    cantidad
                );

                return;
            }

            if (
                !porcentajeFinal.value ||
                Number(porcentajeFinal.value) <= 0 ||
                Number(porcentajeFinal.value) > 100
            ) {
                event.preventDefault();

                mostrarError(
                    'Selecciona o escribe un porcentaje de espacio válido.',
                    porcentajePersonalizado
                );
            }
        });

        actualizarLotes();
    });

    /* ===== Traslado entre ubicaciones ===== */
    const trasladoForm = document.querySelector('.js-trasladar-form');

    if (trasladoForm) {
        const selOrigen = trasladoForm.querySelector('#traslado-asignacion');
        const selDestino = trasladoForm.querySelector('#ubicacion-destino');
        const inpCantidad = trasladoForm.querySelector('#cantidad-traslado');
        const inpPct = trasladoForm.querySelector('#porcentaje-destino');
        const inpMotivo = trasladoForm.querySelector('#motivo-traslado');
        const ayuda = trasladoForm.querySelector('.js-ayuda-traslado');
        const chipsCant = trasladoForm.querySelectorAll('.js-chip-cant');
        const chipsMotivo = trasladoForm.querySelectorAll('.js-chip-motivo');
        const destinoCard = trasladoForm.querySelector('.js-destino-card');
        const destinoCodigo = trasladoForm.querySelector('.js-destino-codigo');
        const destinoLibre = trasladoForm.querySelector('.js-destino-libre');
        const avisoDestino = trasladoForm.querySelector('.js-aviso-destino');
        const resumen = trasladoForm.querySelector('.js-resumen');
        const resumenTexto = trasladoForm.querySelector('.js-resumen-texto');
        const codigoOrigen = trasladoForm.dataset.origen || '';

        const fmt = function (n) {
            return Number(n).toLocaleString('es-BO', { maximumFractionDigits: 3 });
        };

        function datosOrigen() {
            const op = selOrigen.options[selOrigen.selectedIndex];

            return {
                max: Number(op?.dataset.cantidad || 0),
                unidad: op?.dataset.unidad || '',
                producto: op?.dataset.producto || '',
            };
        }

        function datosDestino() {
            const op = selDestino.options[selDestino.selectedIndex];

            if (!op || !op.value) {
                return null;
            }

            return {
                codigo: op.dataset.codigo || '',
                libre: Number(op.dataset.libre || 0),
            };
        }

        function actualizarDestino() {
            const destino = datosDestino();

            destinoCard.classList.toggle('lista', !!destino);
            destinoCodigo.textContent = destino ? destino.codigo : '—';
            destinoLibre.textContent = destino
                ? `${destino.libre.toLocaleString('es-BO', { maximumFractionDigits: 2 })}% libre`
                : 'Selecciona un destino';
        }

        function actualizarAvisoDestino() {
            const destino = datosDestino();
            const pct = Number(inpPct.value || 0);
            const excede = destino && pct > destino.libre + 0.0001;

            avisoDestino.hidden = !excede;
            avisoDestino.textContent = excede
                ? `La ubicación ${destino.codigo} solo tiene ${destino.libre.toLocaleString('es-BO', { maximumFractionDigits: 2 })}% libre.`
                : '';
        }

        function actualizarResumen() {
            const origen = datosOrigen();
            const destino = datosDestino();
            const cant = Number(inpCantidad.value || 0);

            const valido =
                origen.max > 0 &&
                destino &&
                cant > 0 &&
                cant <= origen.max + 0.0001;

            resumen.hidden = !valido;

            if (valido) {
                const restante = Math.max(0, origen.max - cant);

                resumenTexto.textContent =
                    `Se moverán ${fmt(cant)} ${origen.unidad} de ${origen.producto} ` +
                    `desde ${codigoOrigen} hacia ${destino.codigo}. ` +
                    `Quedarán ${fmt(restante)} ${origen.unidad} en ${codigoOrigen}.`;
            }
        }

        function marcarChipCantidad() {
            const origen = datosOrigen();
            const cant = Number(inpCantidad.value || 0);

            chipsCant.forEach(function (chip) {
                const esperado = Math.round(origen.max * Number(chip.dataset.frac) * 1000) / 1000;

                chip.classList.toggle(
                    'activo',
                    origen.max > 0 && cant > 0 && Math.abs(cant - esperado) < 0.0005
                );
            });
        }

        selOrigen.addEventListener('change', function () {
            const origen = datosOrigen();

            inpCantidad.setCustomValidity('');
            inpCantidad.max = origen.max || '';
            inpCantidad.value = '';
            inpCantidad.placeholder = origen.max
                ? `Máximo ${fmt(origen.max)}`
                : 'Selecciona primero el lote';

            ayuda.textContent = origen.max
                ? `Máximo disponible: ${fmt(origen.max)} ${origen.unidad}. Puedes trasladar una parte o todo el lote.`
                : 'Elige el lote para conocer las unidades disponibles.';

            chipsCant.forEach(function (chip) {
                chip.disabled = !origen.max;
                chip.classList.remove('activo');
            });

            actualizarResumen();
        });

        selDestino.addEventListener('change', function () {
            actualizarDestino();
            actualizarAvisoDestino();
            actualizarResumen();
        });

        chipsCant.forEach(function (chip) {
            chip.addEventListener('click', function () {
                const origen = datosOrigen();

                if (!origen.max) {
                    return;
                }

                inpCantidad.setCustomValidity('');
                inpCantidad.value = Math.round(origen.max * Number(chip.dataset.frac) * 1000) / 1000;

                marcarChipCantidad();
                actualizarResumen();
            });
        });

        inpCantidad.addEventListener('input', function () {
            this.setCustomValidity('');
            marcarChipCantidad();
            actualizarResumen();
        });

        inpPct.addEventListener('input', actualizarAvisoDestino);

        chipsMotivo.forEach(function (chip) {
            chip.addEventListener('click', function () {
                inpMotivo.value = chip.textContent.trim();

                chipsMotivo.forEach(function (otro) {
                    otro.classList.toggle('activo', otro === chip);
                });
            });
        });

        inpMotivo.addEventListener('input', function () {
            chipsMotivo.forEach(function (chip) {
                chip.classList.toggle('activo', chip.textContent.trim() === inpMotivo.value.trim());
            });
        });

        trasladoForm.addEventListener('submit', function (event) {
            const maximo = Number(inpCantidad.max || 0);

            if (maximo && Number(inpCantidad.value) > maximo) {
                event.preventDefault();
                inpCantidad.setCustomValidity(
                    `No puedes trasladar más de ${maximo}.`
                );
                inpCantidad.reportValidity();
            } else {
                inpCantidad.setCustomValidity('');
            }
        });
    }
});
</script>
@endsection