@extends('admin.layouts.app')

@section('title', 'Almacenes')

@push('styles')
<style>
    .almacenes-page {
        --bg: #13141a;
        --panel: #1a1c23;
        --panel-2: #20232b;
        --border: #2b2e38;
        --border-soft: #23262e;
        --text: #ececf0;
        --muted: #9a9ca6;
        --muted-2: #6c6f7d;
        --red: #e31b23;
        --red-light: #ff9aa0;
        --red-soft: rgba(227, 27, 35, .12);
        --green: #4ade80;
        --blue: #60a5fa;
        --radius: 16px;
        --radius-sm: 10px;

        display: flex;
        flex-direction: column;
        gap: 20px;
        color: var(--text);
        font-variant-numeric: tabular-nums;
    }

    .almacenes-page *,
    .almacenes-page *::before,
    .almacenes-page *::after { box-sizing: border-box; }

    /* ---------- Header ---------- */
    .almacenes-header {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 28px 26px;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background:
            radial-gradient(ellipse at 100% -30%, rgba(227, 27, 35, .22), transparent 55%),
            radial-gradient(ellipse at 0% 130%, rgba(96, 165, 250, .07), transparent 45%),
            var(--bg);
    }

    .almacenes-header::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,.02) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.02) 1px, transparent 1px);
        background-size: 30px 30px;
        mask-image: radial-gradient(ellipse at 75% 10%, #000 0%, transparent 70%);
        -webkit-mask-image: radial-gradient(ellipse at 75% 10%, #000 0%, transparent 70%);
        pointer-events: none;
    }

    .almacenes-header > * { position: relative; z-index: 1; }

    .almacenes-header-text { min-width: 0; }

    .almacenes-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        padding: 5px 11px 5px 9px;
        border: 1px solid rgba(227, 27, 35, .28);
        border-radius: 999px;
        color: var(--red-light);
        background: var(--red-soft);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.3px;
        text-transform: uppercase;
    }

    .almacenes-eyebrow::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--red);
        box-shadow: 0 0 10px var(--red);
    }

    .almacenes-header h1 {
        margin: 0 0 10px;
        color: #fff;
        font-size: clamp(26px, 3vw, 32px);
        font-weight: 800;
        letter-spacing: -.7px;
        line-height: 1.05;
    }

    .almacenes-header p {
        max-width: 620px;
        margin: 0;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.65;
    }

    /* ---------- Botones ---------- */
    .almacenes-button {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 17px;
        border: 1px solid var(--red);
        border-radius: var(--radius-sm);
        color: #fff;
        background: linear-gradient(180deg, #e6272f 0%, #c91820 100%);
        box-shadow: 0 1px 0 rgba(255,255,255,.14) inset, 0 8px 20px -9px rgba(227,27,35,.7);
        font-family: inherit;
        font-size: 12px;
        font-weight: 750;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition: transform .15s, filter .15s, box-shadow .15s, background .15s, border-color .15s;
    }

    .almacenes-button svg { width: 13px; height: 13px; flex-shrink: 0; }

    .almacenes-button:hover {
        color: #fff;
        transform: translateY(-1px);
        filter: brightness(1.08);
        box-shadow: 0 1px 0 rgba(255,255,255,.2) inset, 0 12px 24px -9px rgba(227,27,35,.8);
    }

    .almacenes-button:active { transform: translateY(0); }

    .almacenes-button.secundario {
        border-color: var(--border);
        color: #dedee4;
        background: var(--panel-2);
        box-shadow: 0 1px 0 rgba(255,255,255,.04) inset;
        filter: none;
    }

    .almacenes-button.secundario:hover {
        color: #fff;
        border-color: #41454f;
        background: #2a2d37;
        box-shadow: 0 1px 0 rgba(255,255,255,.04) inset;
        filter: none;
    }

    .almacenes-button:focus-visible,
    .almacenes-input:focus-visible,
    .almacenes-select:focus-visible,
    .almacen-acciones a:focus-visible,
    .almacen-acciones button:focus-visible,
    a.almacenes-stat:focus-visible {
        outline: 2px solid var(--red-light);
        outline-offset: 2px;
    }

    /* ---------- Alertas ---------- */
    .almacenes-alerta {
        display: flex;
        gap: 12px;
        padding: 14px 16px;
        border: 1px solid rgba(74, 222, 128, .3);
        border-radius: var(--radius-sm);
        color: #8ee5ad;
        background: rgba(74, 222, 128, .07);
        font-size: 12.5px;
        line-height: 1.55;
    }

    .almacenes-alerta::before {
        content: "";
        flex-shrink: 0;
        width: 4px;
        border-radius: 4px;
        background: currentColor;
        opacity: .8;
    }

    .almacenes-alerta.error {
        border-color: rgba(227, 27, 35, .35);
        color: #ff9297;
        background: rgba(227, 27, 35, .07);
    }

    .almacenes-alerta strong { color: #fff; }
    .almacenes-alerta ul { margin: 7px 0 0; padding-left: 20px; }

    /* ---------- Stats ---------- */
    .almacenes-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .almacenes-stat {
        --accent: #b4b7c1;
        position: relative;
        display: block;
        overflow: hidden;
        padding: 18px;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        color: inherit;
        text-decoration: none;
        background:
            radial-gradient(ellipse at 100% 0%, color-mix(in srgb, var(--accent) 13%, transparent), transparent 60%),
            linear-gradient(180deg, var(--panel) 0%, var(--bg) 100%);
        transition: transform .18s, border-color .18s, box-shadow .18s;
    }

    a.almacenes-stat:hover {
        transform: translateY(-2px);
        border-color: color-mix(in srgb, var(--accent) 45%, var(--border));
    }

    .almacenes-stat.activo-filtro {
        border-color: color-mix(in srgb, var(--accent) 55%, var(--border));
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 12%, transparent);
    }

    .almacenes-stat.total     { --accent: #b4b7c1; }
    .almacenes-stat.activos   { --accent: var(--green); }
    .almacenes-stat.inactivos { --accent: #8a8c97; }

    .almacenes-stat-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 16px;
    }

    .almacenes-stat-head > span:first-child {
        color: var(--muted);
        font-size: 11.5px;
        font-weight: 650;
    }

    .almacenes-stat-icon {
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        color: var(--accent);
        background: color-mix(in srgb, var(--accent) 14%, transparent);
        border: 1px solid color-mix(in srgb, var(--accent) 25%, transparent);
    }

    .almacenes-stat-valor {
        color: #f7f7f9;
        font-size: 30px;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -.8px;
    }

    .almacenes-stat-nota {
        display: block;
        margin-top: 8px;
        color: var(--muted-2);
        font-size: 11px;
    }

    /* ---------- Panel ---------- */
    .almacenes-panel {
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background: var(--bg);
    }

    .almacenes-panel-titulo {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 20px 22px;
        border-bottom: 1px solid var(--border-soft);
        background: linear-gradient(180deg, rgba(255,255,255,.015), transparent);
    }

    .almacenes-panel-titulo h2 {
        margin: 0 0 5px;
        color: #f4f4f6;
        font-size: 16px;
        font-weight: 760;
        letter-spacing: -.2px;
    }

    .almacenes-panel-titulo p {
        margin: 0;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.55;
    }

    .almacenes-conteo {
        flex-shrink: 0;
        padding: 6px 12px;
        border: 1px solid var(--border);
        border-radius: 999px;
        color: #c4c6ce;
        background: var(--panel-2);
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    /* ---------- Filtros ---------- */
    .almacenes-filtros {
        display: grid;
        grid-template-columns: minmax(200px, 1fr) 200px auto auto;
        align-items: center;
        gap: 12px;
        padding: 18px 22px;
        border-bottom: 1px solid var(--border-soft);
        background: var(--panel);
    }

    .almacenes-busqueda { position: relative; }

    .almacenes-busqueda svg {
        position: absolute;
        top: 50%;
        left: 12px;
        width: 14px;
        height: 14px;
        color: var(--muted-2);
        transform: translateY(-50%);
        pointer-events: none;
    }

    .almacenes-input,
    .almacenes-select {
        width: 100%;
        min-height: 42px;
        padding: 0 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        outline: none;
        color: var(--text);
        background: var(--panel-2);
        color-scheme: dark;
        font-family: inherit;
        font-size: 12.5px;
        transition: border-color .15s, box-shadow .15s, background .15s;
    }

    .almacenes-busqueda .almacenes-input { padding-left: 36px; }

    .almacenes-select {
        appearance: none;
        -webkit-appearance: none;
        padding-right: 36px;
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%239a9ca6' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
    }

    .almacenes-select option { color: var(--text); background: var(--panel-2); }
    .almacenes-input::placeholder { color: var(--muted-2); }

    .almacenes-input:hover,
    .almacenes-select:hover { border-color: #3d414c; }

    .almacenes-input:focus,
    .almacenes-select:focus {
        border-color: #c4252c;
        background-color: #24262e;
        box-shadow: 0 0 0 3px rgba(227, 27, 35, .14);
    }

    /* ---------- Tabla ---------- */
    .almacenes-tabla-contenedor {
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--border) transparent;
    }

    .almacenes-tabla {
        width: 100%;
        min-width: 960px;
        border-collapse: collapse;
    }

    .almacenes-tabla th {
        padding: 13px 18px;
        color: #8a8c97;
        background: #16171c;
        border-bottom: 1px solid var(--border);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .8px;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .almacenes-tabla td {
        padding: 16px 18px;
        border-top: 1px solid var(--border-soft);
        color: #dedfe4;
        font-size: 12.5px;
        vertical-align: middle;
    }

    .almacenes-tabla tbody tr:first-child td { border-top: 0; }
    .almacenes-tabla tbody tr { transition: background .12s; }
    .almacenes-tabla tbody tr:hover td { background: rgba(255, 255, 255, .022); }

    .almacenes-tabla tbody tr:hover td:first-child {
        box-shadow: inset 3px 0 0 var(--red);
    }

    .almacen-identidad {
        min-width: 190px;
        display: flex;
        align-items: center;
        gap: 13px;
    }

    .almacen-icono {
        flex-shrink: 0;
        display: grid;
        place-items: center;
        width: 40px;
        height: 40px;
        border: 1px solid rgba(227, 27, 35, .25);
        border-radius: 11px;
        color: var(--red-light);
        background: var(--red-soft);
    }

    .almacen-icono svg { width: 18px; height: 18px; }

    .almacen-codigo {
        display: inline-block;
        padding: 3px 8px;
        border: 1px solid var(--border-soft);
        border-radius: 6px;
        color: #b4b7c1;
        background: var(--panel);
        font-family: ui-monospace, "SF Mono", Menlo, monospace;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .3px;
    }

    .almacen-nombre {
        display: block;
        margin-top: 5px;
        color: #f2f2f4;
        font-size: 13.5px;
        font-weight: 760;
        line-height: 1.25;
    }

    .almacen-subtexto {
        display: block;
        max-width: 260px;
        overflow: hidden;
        color: var(--muted);
        font-size: 12px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .almacen-subtexto.vacio { color: var(--muted-2); font-style: italic; }

    .almacen-capacidad { font-weight: 700; color: #f0f0f2; white-space: nowrap; }

    .almacen-capacidad small {
        margin-left: 3px;
        color: var(--muted-2);
        font-size: 10.5px;
        font-weight: 600;
    }

    .almacen-stock {
        display: inline-flex;
        align-items: center;
        min-width: 34px;
        justify-content: center;
        padding: 4px 11px;
        border: 1px solid var(--border);
        border-radius: 999px;
        color: var(--muted-2);
        background: var(--panel-2);
        font-size: 12.5px;
        font-weight: 800;
    }

    .almacen-stock.con-stock {
        border-color: rgba(96, 165, 250, .3);
        color: var(--blue);
        background: rgba(96, 165, 250, .09);
    }

    .almacen-estado {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 11px 5px 9px;
        border: 1px solid color-mix(in srgb, var(--c) 30%, transparent);
        border-radius: 999px;
        color: var(--c);
        background: color-mix(in srgb, var(--c) 10%, transparent);
        font-size: 10.5px;
        font-weight: 750;
        white-space: nowrap;
    }

    .almacen-estado::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        box-shadow: 0 0 8px currentColor;
    }

    .almacen-estado.activo   { --c: var(--green); }
    .almacen-estado.inactivo { --c: #9a9ca6; }
    .almacen-estado.inactivo::before { box-shadow: none; }

    /* ---------- Acciones ---------- */
    .almacen-acciones {
        display: flex;
        align-items: center;
        gap: 7px;
        white-space: nowrap;
    }

    .almacen-acciones form { display: inline-flex; margin: 0; }

    .almacen-acciones a,
    .almacen-acciones button {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0 12px;
        border: 1px solid var(--border);
        border-radius: 9px;
        color: #dedfe3;
        background: var(--panel-2);
        font-family: inherit;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: background .15s, border-color .15s, color .15s;
    }

    .almacen-acciones svg { width: 12px; height: 12px; flex-shrink: 0; }

    .almacen-acciones a:hover,
    .almacen-acciones button:hover {
        border-color: #41454f;
        color: #fff;
        background: #2a2d37;
    }

    .almacen-acciones a.inventario {
        border-color: rgba(227, 27, 35, .3);
        color: #ff9aa0;
        background: rgba(227, 27, 35, .08);
    }

    .almacen-acciones a.inventario:hover {
        border-color: rgba(227, 27, 35, .55);
        color: #fff;
        background: rgba(227, 27, 35, .22);
    }

    .almacen-acciones button.desactivar { color: #ff9aa0; }

    .almacen-acciones button.desactivar:hover {
        border-color: rgba(227, 27, 35, .45);
        background: rgba(227, 27, 35, .12);
    }

    .almacen-acciones button.activar { color: #8ee5ad; }

    .almacen-acciones button.activar:hover {
        border-color: rgba(74, 222, 128, .4);
        background: rgba(74, 222, 128, .1);
    }

    /* ---------- Vacío y paginación ---------- */
    .almacenes-vacio {
        padding: 56px 20px !important;
        color: var(--muted) !important;
        text-align: center;
        font-size: 13px;
    }

    .almacenes-vacio svg {
        display: block;
        margin: 0 auto 14px;
        width: 40px;
        height: 40px;
        color: var(--muted-2);
        opacity: .6;
    }

    .almacenes-vacio strong {
        display: block;
        margin-bottom: 5px;
        color: #f0f0f2;
        font-size: 14px;
    }

    .almacenes-paginacion {
        padding: 14px 22px;
        border-top: 1px solid var(--border-soft);
        background: var(--panel);
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 1000px) {
        .almacenes-filtros { grid-template-columns: minmax(0, 1fr) 200px; }
        .almacenes-filtros .almacenes-button { width: 100%; }
    }

    @media (max-width: 800px) {
        .almacenes-header { align-items: flex-start; flex-direction: column; }
        .almacenes-header .almacenes-button { width: 100%; }
        .almacenes-stats { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    @media (max-width: 620px) {
        .almacenes-header { padding: 22px 18px; }
        .almacenes-stats { grid-template-columns: 1fr; }
        .almacenes-panel-titulo { padding: 16px; }
        .almacenes-filtros { grid-template-columns: 1fr; padding: 16px; }

        .almacenes-tabla { min-width: 0; }
        .almacenes-tabla thead { display: none; }

        .almacenes-tabla tbody tr {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 14px;
            padding: 16px;
            border-top: 1px solid var(--border-soft);
        }

        .almacenes-tabla tbody tr:first-child { border-top: 0; }

        .almacenes-tabla tbody td {
            padding: 0;
            border: 0;
            background: transparent !important;
            box-shadow: none !important;
        }

        .almacenes-tabla tbody td[data-label]::before {
            content: attr(data-label);
            display: block;
            margin-bottom: 4px;
            color: var(--muted-2);
            font-size: 9.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .almacenes-tabla td.col-almacen,
        .almacenes-tabla td.col-direccion,
        .almacenes-tabla td.col-acciones { grid-column: 1 / -1; }

        .almacen-subtexto { max-width: none; white-space: normal; }
        .almacen-acciones { flex-wrap: wrap; }
        .almacen-acciones a.inventario { flex: 1 1 100%; }
        .almacen-acciones a,
        .almacen-acciones form,
        .almacen-acciones button { flex: 1; }

        .almacenes-tabla td.almacenes-vacio {
            grid-column: 1 / -1;
            text-align: center;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .almacenes-page * { transition: none !important; animation: none !important; }
    }
</style>
@endpush

@section('content')
@php
    $buscarActual = $buscar ?? request('buscar', '');
    $estadoActual = $estado ?? request('estado', 'todos');

    $hayFiltros = filled($buscarActual) || (filled($estadoActual) && $estadoActual !== 'todos');

    // Enlaces de filtro rápido (conservan la búsqueda)
    $urlEstado = static function (string $estadoFiltro) use ($buscarActual) {
        return route('admin.almacenes.index', array_filter([
            'buscar' => $buscarActual,
            'estado' => $estadoFiltro,
        ], static fn ($v) => filled($v)));
    };

    $totalRegistros = isset($almacenes) && method_exists($almacenes, 'total')
        ? $almacenes->total()
        : (isset($almacenes) ? count($almacenes) : 0);
@endphp

<div class="almacenes-page">

    {{-- ========== Header ========== --}}
    <header class="almacenes-header">
        <div class="almacenes-header-text">
            <span class="almacenes-eyebrow">Control de inventario</span>
            <h1>Almacenes</h1>
            <p>
                Consulta las existencias de cada almacén y organiza los lotes por pasillo,
                por ejemplo: almacén A1, pasillo B3.
            </p>
        </div>

        <a href="{{ route('admin.almacenes.create') }}" class="almacenes-button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Registrar almacén
        </a>
    </header>

    {{-- ========== Alertas ========== --}}
    @if (session('success'))
        <div class="almacenes-alerta" role="status">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="almacenes-alerta error" role="alert">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="almacenes-alerta error" role="alert">
            <div>
                <strong>Revisa los datos ingresados:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ========== Resumen ========== --}}
    <section class="almacenes-stats" aria-label="Resumen de almacenes">
        <a href="{{ $urlEstado('todos') }}"
           class="almacenes-stat total {{ $estadoActual === 'todos' ? 'activo-filtro' : '' }}">
            <div class="almacenes-stat-head">
                <span>Total de almacenes</span>
                <span class="almacenes-stat-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-6 9 6v11a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/><path d="M9 21V12h6v9"/></svg>
                </span>
            </div>
            <div class="almacenes-stat-valor">{{ number_format((int) ($totalAlmacenes ?? 0), 0, ',', '.') }}</div>
            <span class="almacenes-stat-nota">Registrados en el sistema</span>
        </a>

        <a href="{{ $urlEstado('activo') }}"
           class="almacenes-stat activos {{ $estadoActual === 'activo' ? 'activo-filtro' : '' }}">
            <div class="almacenes-stat-head">
                <span>Almacenes activos</span>
                <span class="almacenes-stat-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                </span>
            </div>
            <div class="almacenes-stat-valor">{{ number_format((int) ($almacenesActivos ?? 0), 0, ',', '.') }}</div>
            <span class="almacenes-stat-nota">Disponibles para operar</span>
        </a>

        <a href="{{ $urlEstado('inactivo') }}"
           class="almacenes-stat inactivos {{ $estadoActual === 'inactivo' ? 'activo-filtro' : '' }}">
            <div class="almacenes-stat-head">
                <span>Almacenes inactivos</span>
                <span class="almacenes-stat-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9 9l6 6M15 9l-6 6"/></svg>
                </span>
            </div>
            <div class="almacenes-stat-valor">{{ number_format((int) ($almacenesInactivos ?? 0), 0, ',', '.') }}</div>
            <span class="almacenes-stat-nota">Fuera de operación</span>
        </a>
    </section>

    {{-- ========== Listado ========== --}}
    <section class="almacenes-panel">
        <div class="almacenes-panel-titulo">
            <div>
                <h2>Listado de almacenes</h2>
                <p>
                    Abre “Pasillos e inventario” para consultar productos, lotes y espacio ocupado.
                </p>
            </div>
            <span class="almacenes-conteo">{{ number_format($totalRegistros) }} registros</span>
        </div>

        <form
            method="GET"
            action="{{ route('admin.almacenes.index') }}"
            class="almacenes-filtros"
        >
            <div class="almacenes-busqueda">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                <input
                    type="search"
                    name="buscar"
                    class="almacenes-input"
                    value="{{ $buscarActual }}"
                    maxlength="100"
                    placeholder="Buscar por código, nombre o dirección"
                    aria-label="Buscar almacenes"
                >
            </div>

            <select name="estado" class="almacenes-select" aria-label="Filtrar por estado">
                <option value="todos" @selected($estadoActual === 'todos')>Todos los estados</option>
                <option value="activo" @selected($estadoActual === 'activo')>Activos</option>
                <option value="inactivo" @selected($estadoActual === 'inactivo')>Inactivos</option>
            </select>

            <button type="submit" class="almacenes-button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                Buscar
            </button>

            @if ($hayFiltros)
                <a href="{{ route('admin.almacenes.index') }}" class="almacenes-button secundario">
                    Limpiar
                </a>
            @else
                <span></span>
            @endif
        </form>

        <div class="almacenes-tabla-contenedor">
            <table class="almacenes-tabla">
                <thead>
                    <tr>
                        <th>Almacén</th>
                        <th>Dirección</th>
                        <th>Capacidad</th>
                        <th>Productos con stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($almacenes as $almacen)
                        @php
                            $almacenActivo = strtolower((string) $almacen->estado) === 'activo';
                            $productosConStock = (int) ($almacen->productos_con_stock ?? 0);
                        @endphp

                        <tr>
                            <td class="col-almacen">
                                <div class="almacen-identidad">
                                    <span class="almacen-icono" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-6 9 6v11a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/><path d="M9 21V12h6v9"/></svg>
                                    </span>
                                    <div>
                                        <span class="almacen-codigo">{{ $almacen->codigo }}</span>
                                        <span class="almacen-nombre">{{ $almacen->nombre }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="col-direccion" data-label="Dirección">
                                @if ($almacen->direccion)
                                    <span class="almacen-subtexto" title="{{ $almacen->direccion }}">{{ $almacen->direccion }}</span>
                                @else
                                    <span class="almacen-subtexto vacio">Sin dirección registrada</span>
                                @endif
                            </td>

                            <td class="col-capacidad" data-label="Capacidad">
                                @if ($almacen->capacidad_m3 !== null)
                                    <span class="almacen-capacidad">
                                        {{ number_format((float) $almacen->capacidad_m3, 3, ',', '.') }}<small>m</small>
                                    </span>
                                @else
                                    <span class="almacen-subtexto vacio">No especificada</span>
                                @endif
                            </td>

                            <td class="col-stock" data-label="Productos con stock">
                                <span class="almacen-stock {{ $productosConStock > 0 ? 'con-stock' : '' }}">
                                    {{ number_format($productosConStock, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="col-estado" data-label="Estado">
                                <span class="almacen-estado {{ $almacenActivo ? 'activo' : 'inactivo' }}">
                                    {{ $almacenActivo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>

                            <td class="col-acciones">
                                <div class="almacen-acciones">
                                    <a
                                        class="inventario"
                                        href="{{ route('admin.almacenes.ubicaciones.index', [
                                            'almacenId' => $almacen->id,
                                        ]) }}"
                                        aria-label="Ver pasillos e inventario del almacén {{ $almacen->codigo }}"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 12l9 4 9-4"/><path d="M3 17l9 4 9-4"/></svg>
                                        Pasillos e inventario
                                    </a>

                                    <a href="{{ route('admin.almacenes.edit', $almacen->id) }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                        Editar
                                    </a>

                                    <form
                                        action="{{ route('admin.almacenes.estado', $almacen->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('¿Deseas cambiar el estado de este almacén?')"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="{{ $almacenActivo ? 'desactivar' : 'activar' }}"
                                        >
                                            @if ($almacenActivo)
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9 9l6 6M15 9l-6 6"/></svg>
                                            @else
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                                            @endif
                                            {{ $almacenActivo ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="almacenes-vacio">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-6 9 6v11a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/><path d="M9 21V12h6v9"/></svg>
                                <strong>No hay almacenes para mostrar</strong>
                                Prueba con otros filtros o registra un nuevo almacén.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (isset($almacenes) && method_exists($almacenes, 'hasPages') && $almacenes->hasPages())
            <div class="almacenes-paginacion">
                {{ $almacenes->withQueryString()->links() }}
            </div>
        @endif
    </section>
</div>
@endsection