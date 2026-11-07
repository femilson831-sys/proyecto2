@extends('admin.layouts.app')

@section('title', 'Inventario')

@push('styles')
<style>
    .inv-page {
        --bg: #13141a;
        --panel: #1a1c23;
        --panel-2: #20232b;
        --border: #2b2e38;
        --border-soft: #23262e;
        --text: #ececf0;
        --muted: #9a9ca6;
        --muted-2: #6c6f7d;
        --red: #e31b23;
        --red-soft: rgba(227, 27, 35, .12);
        --green: #4ade80;
        --blue: #60a5fa;
        --yellow: #f5b942;
        --radius: 16px;
        --radius-sm: 10px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        color: var(--text);
        font-variant-numeric: tabular-nums;
    }

    .inv-page *,
    .inv-page *::before,
    .inv-page *::after { box-sizing: border-box; }

    /* ---------- Header ---------- */
    .inv-header {
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 22px;
        padding: 28px 26px;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background:
            radial-gradient(ellipse at 100% -30%, rgba(227, 27, 35, .22), transparent 55%),
            radial-gradient(ellipse at 0% 130%, rgba(96, 165, 250, .07), transparent 45%),
            var(--bg);
    }

    .inv-header::before {
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

    .inv-header > * { position: relative; z-index: 1; }

    .inv-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        padding: 5px 11px 5px 9px;
        border: 1px solid rgba(227, 27, 35, .28);
        border-radius: 999px;
        color: #ff9aa0;
        background: var(--red-soft);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.3px;
        text-transform: uppercase;
    }

    .inv-eyebrow::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--red);
        box-shadow: 0 0 10px var(--red);
    }

    .inv-header h1 {
        margin: 0 0 10px;
        color: #fff;
        font-size: 32px;
        font-weight: 800;
        letter-spacing: -.7px;
        line-height: 1.05;
    }

    .inv-header p {
        max-width: 640px;
        margin: 0;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.65;
    }

    .inv-header p strong { color: #f3f3f6; font-weight: 700; }

    .inv-header-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        flex-shrink: 0;
    }

    .inv-location {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        min-height: 42px;
        padding: 0 15px;
        border: 1px solid rgba(227, 27, 35, .3);
        border-radius: var(--radius-sm);
        color: #ffb3b7;
        background: rgba(227, 27, 35, .08);
        font-size: 11.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    .inv-location::before {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--red);
        animation: inv-pulse 2s infinite;
    }

    @keyframes inv-pulse {
        0%   { box-shadow: 0 0 0 0 rgba(227, 27, 35, .55); }
        70%  { box-shadow: 0 0 0 8px rgba(227, 27, 35, 0); }
        100% { box-shadow: 0 0 0 0 rgba(227, 27, 35, 0); }
    }

    /* ---------- Botones ---------- */
    .inv-link,
    .inv-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 17px;
        border: 1px solid var(--red);
        border-radius: var(--radius-sm);
        color: #fff;
        background: linear-gradient(180deg, #e6272f 0%, #c91820 100%);
        box-shadow: 0 1px 0 rgba(255,255,255,.14) inset, 0 8px 20px -9px rgba(227,27,35,.7);
        font-size: 12px;
        font-weight: 750;
        text-decoration: none;
        cursor: pointer;
        transition: transform .15s, filter .15s, box-shadow .15s, background .15s, border-color .15s;
    }

    .inv-link:hover,
    .inv-button:hover {
        color: #fff;
        filter: brightness(1.08);
        transform: translateY(-1px);
        box-shadow: 0 1px 0 rgba(255,255,255,.2) inset, 0 12px 24px -9px rgba(227,27,35,.8);
    }

    .inv-link:active,
    .inv-button:active { transform: translateY(0); }

    .inv-link:focus-visible,
    .inv-button:focus-visible {
        outline: 2px solid #ff9aa0;
        outline-offset: 2px;
    }

    .inv-link.secondary,
    .inv-button.secondary {
        border-color: var(--border);
        color: #dedee4;
        background: var(--panel-2);
        filter: none;
        box-shadow: 0 1px 0 rgba(255,255,255,.04) inset;
    }

    .inv-link.secondary:hover,
    .inv-button.secondary:hover {
        color: #fff;
        background: #2a2d37;
        border-color: #41454f;
        filter: none;
        box-shadow: 0 1px 0 rgba(255,255,255,.04) inset;
    }

    /* ---------- Alertas ---------- */
    .inv-alert {
        display: flex;
        gap: 12px;
        padding: 14px 16px;
        border: 1px solid;
        border-radius: var(--radius-sm);
        font-size: 12.5px;
        line-height: 1.55;
    }

    .inv-alert::before {
        content: "";
        flex-shrink: 0;
        width: 4px;
        border-radius: 4px;
        background: currentColor;
        opacity: .8;
    }

    .inv-alert.success {
        border-color: rgba(74, 222, 128, .3);
        color: #8ee5ad;
        background: rgba(74, 222, 128, .07);
    }

    .inv-alert.error {
        border-color: rgba(227, 27, 35, .35);
        color: #ff9297;
        background: rgba(227, 27, 35, .07);
    }

    .inv-alert strong { color: #fff; }
    .inv-alert ul { margin: 7px 0 0; padding-left: 20px; }

    /* ---------- Stats ---------- */
    .inv-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .inv-stat {
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
        transition: transform .18s, border-color .18s;
    }

    a.inv-stat:hover {
        transform: translateY(-2px);
        border-color: color-mix(in srgb, var(--accent) 45%, var(--border));
    }

    a.inv-stat:focus-visible {
        outline: 2px solid var(--accent);
        outline-offset: 2px;
    }

    .inv-stat.activo {
        border-color: color-mix(in srgb, var(--accent) 55%, var(--border));
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 12%, transparent);
    }

    .inv-stat.total   { --accent: #b4b7c1; }
    .inv-stat.agotado { --accent: #ff6b72; }
    .inv-stat.bajo    { --accent: var(--yellow); }
    .inv-stat.normal  { --accent: var(--green); }

    .inv-stat-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 16px;
    }

    .inv-stat-head > span:first-child {
        color: var(--muted);
        font-size: 11.5px;
        font-weight: 650;
    }

    .inv-stat-icon {
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        color: var(--accent);
        background: color-mix(in srgb, var(--accent) 14%, transparent);
        border: 1px solid color-mix(in srgb, var(--accent) 25%, transparent);
    }

    .inv-stat-valor {
        color: #f7f7f9;
        font-size: 30px;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -.8px;
    }

    .inv-stat-nota {
        display: block;
        margin-top: 8px;
        color: var(--muted-2);
        font-size: 11px;
    }

    /* ---------- Panel ---------- */
    .inv-panel {
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background: var(--bg);
    }

    .inv-panel-title {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 20px 22px;
        border-bottom: 1px solid var(--border-soft);
        background: linear-gradient(180deg, rgba(255,255,255,.015), transparent);
    }

    .inv-panel-title h2 {
        margin: 0 0 5px;
        color: #f4f4f6;
        font-size: 16px;
        font-weight: 760;
        letter-spacing: -.2px;
    }

    .inv-panel-title p {
        margin: 0;
        color: var(--muted);
        font-size: 12px;
    }

    .inv-panel-count {
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
    .inv-filters {
        display: grid;
        grid-template-columns: minmax(200px, 1.6fr) minmax(180px, 1fr) minmax(160px, 1fr) auto auto;
        align-items: end;
        gap: 12px;
        padding: 18px 22px;
        border-bottom: 1px solid var(--border-soft);
        background: var(--panel);
    }

    .inv-field label {
        display: block;
        margin-bottom: 7px;
        color: #b9bbc4;
        font-size: 10px;
        font-weight: 750;
        letter-spacing: .6px;
        text-transform: uppercase;
    }

    .inv-input-wrap { position: relative; }

    .inv-input-wrap svg {
        position: absolute;
        top: 50%;
        left: 12px;
        width: 14px;
        height: 14px;
        color: var(--muted-2);
        transform: translateY(-50%);
        pointer-events: none;
    }

    .inv-field input,
    .inv-field select {
        width: 100%;
        min-height: 42px;
        padding: 0 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        outline: none;
        color: var(--text);
        background: var(--panel-2);
        font-size: 12.5px;
        transition: border-color .15s, box-shadow .15s, background .15s;
    }

    .inv-input-wrap input { padding-left: 36px; }
    .inv-field input::placeholder { color: var(--muted-2); }

    .inv-field input:hover,
    .inv-field select:hover { border-color: #3d414c; }

    .inv-field input:focus,
    .inv-field select:focus {
        border-color: #c4252c;
        background: #24262e;
        box-shadow: 0 0 0 3px rgba(227, 27, 35, .14);
    }

    /* ---------- Tabla ---------- */
    .inv-table-wrap {
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--border) transparent;
    }

    .inv-table {
        width: 100%;
        min-width: 1040px;
        border-collapse: collapse;
    }

    .inv-table thead th {
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

    .inv-table thead th.num { text-align: right; }

    .inv-table tbody td {
        padding: 16px 18px;
        border-top: 1px solid var(--border-soft);
        color: #dedfe4;
        font-size: 12.5px;
        vertical-align: middle;
    }

    .inv-table tbody tr:first-child td { border-top: 0; }
    .inv-table tbody tr { transition: background .12s; }
    .inv-table tbody tr:hover td { background: rgba(255, 255, 255, .022); }

    .inv-table tbody tr:hover td:first-child {
        box-shadow: inset 3px 0 0 var(--red);
    }

    .inv-code-chip {
        display: inline-block;
        padding: 4px 9px;
        border: 1px solid var(--border-soft);
        border-radius: 7px;
        color: #b4b7c1;
        background: var(--panel);
        font-family: ui-monospace, "SF Mono", Menlo, monospace;
        font-size: 11.5px;
        font-weight: 600;
        white-space: nowrap;
    }

    .inv-product {
        display: block;
        color: #f2f2f4;
        font-weight: 750;
        font-size: 13px;
        line-height: 1.3;
    }

    .inv-description,
    .inv-subtext {
        display: block;
        margin-top: 4px;
        color: var(--muted);
        font-size: 11px;
        line-height: 1.5;
    }

    .inv-description {
        max-width: 320px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .inv-history-link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 8px;
        padding: 3px 9px 3px 7px;
        border: 1px solid rgba(227, 27, 35, .2);
        border-radius: 999px;
        color: #ff8f94;
        background: rgba(227, 27, 35, .06);
        font-size: 10.5px;
        font-weight: 700;
        text-decoration: none;
        transition: all .15s;
    }

    .inv-history-link:hover {
        color: #fff;
        background: rgba(227, 27, 35, .18);
        border-color: rgba(227, 27, 35, .4);
    }

    .inv-history-link:focus-visible {
        outline: 2px solid #ff8f94;
        outline-offset: 2px;
    }

    .inv-history-link svg { width: 11px; height: 11px; }

    .inv-fecha {
        display: block;
        color: #e4e5ea;
        font-weight: 600;
    }

    .inv-fecha small {
        display: block;
        margin-top: 3px;
        color: var(--muted-2);
        font-size: 10.5px;
        font-weight: 500;
    }

    .inv-quantity {
        text-align: right;
        white-space: nowrap;
        color: #f4f4f6;
        font-size: 14px;
        font-weight: 800;
    }

    .inv-quantity.dim { color: #c4c6ce; font-weight: 700; }

    .inv-unit {
        margin-left: 4px;
        color: var(--muted-2);
        font-size: 10.5px;
        font-weight: 600;
    }

    /* Barra de stock */
    .inv-stock { min-width: 140px; }

    .inv-meter {
        position: relative;
        height: 5px;
        margin-top: 9px;
        overflow: visible;
        border-radius: 999px;
        background: var(--panel-2);
    }

    .inv-meter-fill {
        height: 100%;
        border-radius: 999px;
        background: var(--c);
    }

    .inv-meter-min {
        position: absolute;
        top: -3px;
        bottom: -3px;
        width: 2px;
        left: 50%;
        border-radius: 2px;
        background: #6c6f7d;
    }

    /* Badges */
    .inv-badge {
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

    .inv-badge::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        box-shadow: 0 0 8px currentColor;
    }

    .inv-badge.agotado { --c: #ff6b72; }
    .inv-badge.bajo    { --c: var(--yellow); }
    .inv-badge.normal  { --c: var(--green); }

    .inv-empty {
        padding: 56px 20px !important;
        color: var(--muted) !important;
        text-align: center;
        font-size: 13px;
    }

    .inv-empty svg {
        display: block;
        margin: 0 auto 14px;
        width: 40px;
        height: 40px;
        color: var(--muted-2);
        opacity: .6;
    }

    .inv-pagination {
        padding: 14px 22px;
        border-top: 1px solid var(--border-soft);
        background: var(--panel);
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 1050px) {
        .inv-filters { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .inv-filters .inv-button { width: 100%; }
    }

    @media (max-width: 1000px) {
        .inv-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 820px) {
        .inv-header { flex-direction: column; }
        .inv-header-actions { justify-content: flex-start; }
    }

    @media (max-width: 620px) {
        .inv-header { padding: 22px 18px; }
        .inv-header h1 { font-size: 26px; }
        .inv-header-actions { width: 100%; }
        .inv-header-actions > * { flex: 1 1 100%; }
        .inv-filters { grid-template-columns: 1fr; padding: 16px; }
        .inv-stats { grid-template-columns: 1fr; }
        .inv-panel-title { padding: 16px; }

        .inv-table { min-width: 0; }
        .inv-table thead { display: none; }

        .inv-table tbody tr {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 14px;
            padding: 16px;
            border-top: 1px solid var(--border-soft);
        }

        .inv-table tbody tr:first-child { border-top: 0; }

        .inv-table tbody td {
            padding: 0;
            border: 0;
            background: transparent !important;
            box-shadow: none !important;
        }

        .inv-table tbody td[data-label]::before {
            content: attr(data-label);
            display: block;
            margin-bottom: 3px;
            color: var(--muted-2);
            font-size: 9.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .inv-table td.col-product,
        .inv-table td.col-estado { grid-column: 1 / -1; }

        .inv-table td.inv-quantity { text-align: left; }
        .inv-description { max-width: none; }

        .inv-table td.inv-empty {
            grid-column: 1 / -1;
            text-align: center;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .inv-page * { transition: none !important; animation: none !important; }
    }
</style>
@endpush

@section('content')
@php
    $formatearCantidad = static function ($cantidad) {
        return rtrim(rtrim(number_format((float) $cantidad, 3, ',', '.'), '0'), ',');
    };

    $hayFiltros = filled($buscar) || filled($almacenId) || (filled($estadoStock) && $estadoStock !== 'todos');

    // Enlaces de filtro rápido (conservan búsqueda y almacén)
    $urlEstado = static function (string $estado) use ($buscar, $almacenId) {
        return route('admin.inventario.index', array_filter([
            'buscar'       => $buscar,
            'almacen_id'   => $almacenId,
            'estado_stock' => $estado,
        ], static fn ($v) => filled($v)));
    };
@endphp

<div class="inv-page">

    {{-- ========== Header ========== --}}
    <header class="inv-header">
        <div>
            <span class="inv-eyebrow">Control de existencias</span>
            <h1>Inventario</h1>
            <p>
                @if ($almacenSeleccionado)
                    Entradas con lote y existencias del almacén
                    <strong>{{ $almacenSeleccionado->nombre }}</strong>.
                @else
                    Productos con entradas registradas por lote.
                    Las cantidades consolidan <strong>todos los almacenes</strong>.
                @endif
            </p>
        </div>

        <div class="inv-header-actions">
            <div class="inv-location">
                {{ $almacenSeleccionado
                    ? 'Almacén: ' . $almacenSeleccionado->nombre
                    : 'Todos los almacenes' }}
            </div>

            <a href="{{ route('admin.inventario.create') }}" class="inv-link">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Registrar entrada
            </a>

            <a href="{{ route('admin.inventario.salida.create') }}" class="inv-link secondary">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M5 12h14"/></svg>
                Registrar salida
            </a>
        </div>
    </header>

    {{-- ========== Alertas ========== --}}
    @if (session('success'))
        <div class="inv-alert success" role="status">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="inv-alert error" role="alert">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="inv-alert error" role="alert">
            <div>
                <strong>Revisa los siguientes datos:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ========== Resumen ========== --}}
    <section class="inv-stats" aria-label="Resumen de inventario">
        <a href="{{ $urlEstado('todos') }}" class="inv-stat total {{ $estadoStock === 'todos' ? 'activo' : '' }}">
            <div class="inv-stat-head">
                <span>Productos con lote</span>
                <span class="inv-stat-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 12l9 4 9-4"/><path d="M3 17l9 4 9-4"/></svg>
                </span>
            </div>
            <div class="inv-stat-valor">{{ number_format($totalProductos) }}</div>
            <span class="inv-stat-nota">SKU con entradas registradas</span>
        </a>

        <a href="{{ $urlEstado('agotado') }}" class="inv-stat agotado {{ $estadoStock === 'agotado' ? 'activo' : '' }}">
            <div class="inv-stat-head">
                <span>Sin existencias</span>
                <span class="inv-stat-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9 9l6 6M15 9l-6 6"/></svg>
                </span>
            </div>
            <div class="inv-stat-valor">{{ number_format($productosAgotados) }}</div>
            <span class="inv-stat-nota">Requieren reposición urgente</span>
        </a>

        <a href="{{ $urlEstado('bajo') }}" class="inv-stat bajo {{ $estadoStock === 'bajo' ? 'activo' : '' }}">
            <div class="inv-stat-head">
                <span>Stock bajo</span>
                <span class="inv-stat-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.86a2 2 0 0 1 3.4 0l7.4 12.8a2 2 0 0 1-1.7 3H4.6a2 2 0 0 1-1.7-3z"/></svg>
                </span>
            </div>
            <div class="inv-stat-valor">{{ number_format($productosBajos) }}</div>
            <span class="inv-stat-nota">En o bajo el mínimo definido</span>
        </a>

        <a href="{{ $urlEstado('normal') }}" class="inv-stat normal {{ $estadoStock === 'normal' ? 'activo' : '' }}">
            <div class="inv-stat-head">
                <span>Stock normal</span>
                <span class="inv-stat-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                </span>
            </div>
            <div class="inv-stat-valor">{{ number_format($productosNormales) }}</div>
            <span class="inv-stat-nota">Disponibilidad saludable</span>
        </a>
    </section>

    {{-- ========== Productos ========== --}}
    <section class="inv-panel">
        <div class="inv-panel-title">
            <div>
                <h2>Productos recibidos</h2>
                <p>Cada producto aparece una sola vez. La cantidad ingresada suma sus lotes.</p>
            </div>
            <span class="inv-panel-count">{{ $inventarios->total() }} registros</span>
        </div>

        <form action="{{ route('admin.inventario.index') }}" method="GET" class="inv-filters">
            <div class="inv-field">
                <label for="buscar">Buscar producto</label>
                <div class="inv-input-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    <input
                        type="search"
                        id="buscar"
                        name="buscar"
                        value="{{ $buscar }}"
                        maxlength="100"
                        placeholder="Nombre o código"
                    >
                </div>
            </div>

            <div class="inv-field">
                <label for="almacen_id">Almacén</label>
                <select name="almacen_id" id="almacen_id">
                    <option value="">Todos los almacenes</option>
                    @foreach ($almacenes as $almacen)
                        <option value="{{ $almacen->id }}" @selected((string) $almacenId === (string) $almacen->id)>
                            {{ $almacen->codigo }} — {{ $almacen->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="inv-field">
                <label for="estado_stock">Estado del stock</label>
                <select name="estado_stock" id="estado_stock">
                    <option value="todos"   @selected($estadoStock === 'todos')>Todos los estados</option>
                    <option value="normal"  @selected($estadoStock === 'normal')>Stock normal</option>
                    <option value="bajo"    @selected($estadoStock === 'bajo')>Stock bajo</option>
                    <option value="agotado" @selected($estadoStock === 'agotado')>Sin existencias</option>
                </select>
            </div>

            <button type="submit" class="inv-button">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5h18l-7 8v6l-4 2v-8z"/></svg>
                Filtrar
            </button>

            @if ($hayFiltros)
                <a href="{{ route('admin.inventario.index') }}" class="inv-button secondary">
                    Limpiar
                </a>
            @else
                <span></span>
            @endif
        </form>

        <div class="inv-table-wrap">
            <table class="inv-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Unidad</th>
                        <th class="num">Cant. ingresada</th>
                        <th class="num">Stock actual</th>
                        <th class="num">Stock mínimo</th>
                        <th>Última entrada</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($inventarios as $item)
                        @php
                            $stockActual       = (float) $item->stock_actual;
                            $stockMinimo       = (float) ($item->stock_minimo ?? 0);
                            $cantidadIngresada = (float) $item->cantidad_ingresada;

                            if ($stockActual <= 0) {
                                $estadoClase = 'agotado';
                                $estadoTexto = 'Sin existencias';
                            } elseif ($stockActual <= $stockMinimo) {
                                $estadoClase = 'bajo';
                                $estadoTexto = 'Stock bajo';
                            } else {
                                $estadoClase = 'normal';
                                $estadoTexto = 'Normal';
                            }

                            $colorEstado = match ($estadoClase) {
                                'agotado' => '#ff6b72',
                                'bajo'    => 'var(--yellow)',
                                default   => 'var(--green)',
                            };

                            // La marca del mínimo queda al 50% de la barra; llena al 100% = doble del mínimo
                            $pctStock = $stockMinimo > 0
                                ? max(0, min(100, ($stockActual / ($stockMinimo * 2)) * 100))
                                : ($stockActual > 0 ? 100 : 0);

                            $unidadNombre = $item->unidad_nombre ?: 'Sin unidad';
                            $unidad       = $item->unidad_abreviatura ?: $unidadNombre;

                            $fechaEntrada = $item->ultima_entrada
                                ? \Illuminate\Support\Carbon::parse($item->ultima_entrada)
                                : null;
                        @endphp

                        <tr>
                            <td class="col-code" data-label="Código">
                                <span class="inv-code-chip">{{ $item->codigo_interno }}</span>
                            </td>

                            <td class="col-product">
                                <span class="inv-product">{{ $item->nombre }}</span>
                                @if ($item->descripcion)
                                    <span class="inv-description">{{ $item->descripcion }}</span>
                                @endif
                                <a class="inv-history-link" href="{{ route('admin.inventario.historial', $item->id) }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 2"/><circle cx="12" cy="12" r="9"/></svg>
                                    Ver historial
                                </a>
                            </td>

                            <td class="col-unidad" data-label="Unidad">
                                {{ $unidadNombre }}
                                @if ($item->unidad_abreviatura)
                                    <span class="inv-unit">({{ $item->unidad_abreviatura }})</span>
                                @endif
                                @if ($item->permite_pesaje)
                                    <span class="inv-subtext">Permite pesaje</span>
                                @endif
                            </td>

                            <td class="inv-quantity dim col-qty" data-label="Cant. ingresada">
                                {{ $formatearCantidad($cantidadIngresada) }}<span class="inv-unit">{{ $unidad }}</span>
                            </td>

                            <td class="inv-quantity inv-stock col-stock" data-label="Stock actual">
                                {{ $formatearCantidad($stockActual) }}<span class="inv-unit">{{ $unidad }}</span>
                                <div class="inv-meter" title="Marca = stock mínimo">
                                    <div class="inv-meter-fill" style="--c: {{ $colorEstado }}; width: {{ $pctStock }}%"></div>
                                    @if ($stockMinimo > 0)
                                        <span class="inv-meter-min"></span>
                                    @endif
                                </div>
                            </td>

                            <td class="inv-quantity dim col-min" data-label="Stock mínimo">
                                {{ $formatearCantidad($stockMinimo) }}<span class="inv-unit">{{ $unidad }}</span>
                            </td>

                            <td class="col-fecha" data-label="Última entrada">
                                @if ($fechaEntrada)
                                    <span class="inv-fecha">
                                        {{ $fechaEntrada->format('d/m/Y') }}
                                        <small>{{ $fechaEntrada->format('H:i') }}</small>
                                    </span>
                                @else
                                    —
                                @endif
                            </td>

                            <td class="col-estado" data-label="Estado">
                                <span class="inv-badge {{ $estadoClase }}">{{ $estadoTexto }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="inv-empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                                No hay productos con entradas de lote para estos filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($inventarios->hasPages())
            <div class="inv-pagination">
                {{ $inventarios->links() }}
            </div>
        @endif
    </section>
</div>
@endsection