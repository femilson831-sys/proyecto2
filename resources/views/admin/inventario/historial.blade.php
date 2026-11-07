@extends('admin.layouts.app')

@section('title', 'Historial del producto')

@push('styles')
<style>
    .historial-page {
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

    .historial-page *,
    .historial-page *::before,
    .historial-page *::after { box-sizing: border-box; }

    /* ---------- Header ---------- */
    .historial-header {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
        padding: 28px 26px;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background:
            radial-gradient(ellipse at 100% -30%, rgba(227, 27, 35, .22), transparent 55%),
            radial-gradient(ellipse at 0% 130%, rgba(96, 165, 250, .07), transparent 45%),
            var(--bg);
    }

    .historial-header::before {
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

    .historial-header > * { position: relative; z-index: 1; }

    .historial-eyebrow {
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

    .historial-eyebrow::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--red);
        box-shadow: 0 0 10px var(--red);
    }

    .historial-header h1 {
        margin: 0 0 12px;
        color: #fff;
        font-size: 30px;
        font-weight: 800;
        letter-spacing: -.6px;
        line-height: 1.08;
    }

    .historial-producto {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin: 0;
        color: #c4c6ce;
        font-size: 13px;
    }

    .historial-producto .codigo {
        padding: 4px 10px;
        border: 1px solid var(--border);
        border-radius: 7px;
        color: #f0f0f3;
        background: var(--panel-2);
        font-family: ui-monospace, "SF Mono", Menlo, monospace;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .3px;
    }

    .historial-producto .sep { color: var(--muted-2); }

    .historial-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 16px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        color: #dedee4;
        background: var(--panel-2);
        box-shadow: 0 1px 0 rgba(255,255,255,.04) inset;
        font-size: 12px;
        font-weight: 750;
        text-decoration: none;
        transition: transform .15s, background .15s, border-color .15s, color .15s;
    }

    .historial-button svg { width: 14px; height: 14px; transition: transform .15s; }

    .historial-button:hover {
        color: #fff;
        background: #2a2d37;
        border-color: #41454f;
    }

    .historial-button:hover svg { transform: translateX(-3px); }

    .historial-button:focus-visible {
        outline: 2px solid #ff9aa0;
        outline-offset: 2px;
    }

    /* ---------- Resumen ---------- */
    .historial-resumen {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .historial-stat {
        --accent: #b4b7c1;
        position: relative;
        overflow: hidden;
        padding: 18px;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background:
            radial-gradient(ellipse at 100% 0%, color-mix(in srgb, var(--accent) 12%, transparent), transparent 60%),
            linear-gradient(180deg, var(--panel) 0%, var(--bg) 100%);
        transition: transform .18s, border-color .18s;
    }

    .historial-stat:hover {
        transform: translateY(-2px);
        border-color: color-mix(in srgb, var(--accent) 45%, var(--border));
    }

    .historial-stat.lotes     { --accent: #b4b7c1; }
    .historial-stat.ingresado { --accent: var(--green); }
    .historial-stat.existente { --accent: var(--blue); }
    .historial-stat.salidas   { --accent: var(--yellow); }

    .historial-stat-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 16px;
    }

    .historial-stat-head > span:first-child {
        color: var(--muted);
        font-size: 11.5px;
        font-weight: 650;
    }

    .historial-stat-icon {
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        color: var(--accent);
        background: color-mix(in srgb, var(--accent) 14%, transparent);
        border: 1px solid color-mix(in srgb, var(--accent) 25%, transparent);
    }

    .historial-stat-valor {
        display: flex;
        align-items: baseline;
        gap: 6px;
        color: #f7f7f9;
        font-size: 28px;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -.8px;
    }

    .historial-unidad {
        color: var(--muted-2);
        font-size: 11px;
        font-weight: 650;
        letter-spacing: 0;
    }

    .historial-stat-nota {
        display: block;
        margin-top: 8px;
        color: var(--muted-2);
        font-size: 11px;
    }

    /* ---------- Balance ---------- */
    .historial-balance {
        padding: 18px 20px;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background: var(--bg);
    }

    .historial-balance-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 12px;
    }

    .historial-balance-top h2 {
        margin: 0;
        color: #f4f4f6;
        font-size: 13.5px;
        font-weight: 750;
    }

    .historial-leyenda {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        color: var(--muted);
        font-size: 11px;
    }

    .historial-leyenda span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .historial-leyenda i {
        width: 8px;
        height: 8px;
        border-radius: 3px;
        background: var(--c);
    }

    .historial-barra {
        display: flex;
        height: 10px;
        overflow: hidden;
        border-radius: 999px;
        background: var(--panel-2);
        box-shadow: inset 0 0 0 1px var(--border-soft);
    }

    .historial-barra > div {
        height: 100%;
        background: var(--c);
        transition: width .5s ease;
    }

    .historial-barra > div + div { margin-left: 2px; }

    /* ---------- Card ---------- */
    .historial-card {
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background: var(--bg);
    }

    .historial-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 20px 22px;
        border-bottom: 1px solid var(--border-soft);
        background: linear-gradient(180deg, rgba(255,255,255,.015), transparent);
    }

    .historial-card-header h2 {
        margin: 0 0 5px;
        color: #f4f4f6;
        font-size: 16px;
        font-weight: 760;
        letter-spacing: -.2px;
    }

    .historial-card-header p {
        margin: 0;
        color: var(--muted);
        font-size: 12px;
    }

    .historial-panel-count {
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

    /* ---------- Tabla ---------- */
    .historial-tabla-contenedor {
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--border) transparent;
    }

    .historial-tabla {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
    }

    .historial-tabla thead th {
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

    .historial-tabla thead th.num { text-align: right; }

    .historial-tabla tbody td {
        padding: 16px 18px;
        border-top: 1px solid var(--border-soft);
        color: #dedfe4;
        font-size: 12.5px;
        vertical-align: middle;
    }

    .historial-tabla tbody tr:first-child td { border-top: 0; }

    .historial-tabla tbody tr { transition: background .12s; }

    .historial-tabla tbody tr:hover td { background: rgba(255, 255, 255, .022); }

    .historial-tabla tbody tr:hover td:first-child {
        box-shadow: inset 3px 0 0 var(--red);
    }

    .historial-principal {
        display: block;
        color: #f2f2f4;
        font-weight: 750;
        font-size: 13px;
        line-height: 1.3;
    }

    .historial-principal.mono {
        font-family: ui-monospace, "SF Mono", Menlo, monospace;
        font-size: 12px;
        letter-spacing: .2px;
    }

    .historial-secundario {
        display: block;
        margin-top: 4px;
        color: var(--muted);
        font-size: 11px;
        line-height: 1.5;
    }

    .historial-secundario.tag {
        display: inline-block;
        margin-top: 6px;
        padding: 2px 8px;
        border: 1px solid var(--border);
        border-radius: 6px;
        color: #b9bbc4;
        background: var(--panel-2);
        font-size: 10.5px;
        font-weight: 600;
    }

    .historial-fecha {
        display: block;
        color: #e4e5ea;
        font-weight: 600;
    }

    .historial-fecha small {
        display: block;
        margin-top: 3px;
        color: var(--muted-2);
        font-size: 10.5px;
        font-weight: 500;
    }

    .historial-cantidad {
        text-align: right;
        white-space: nowrap;
        color: #f4f4f6;
        font-size: 14px;
        font-weight: 800;
    }

    .historial-cantidad.dim {
        color: #c4c6ce;
        font-weight: 700;
    }

    .historial-cantidad .unidad {
        margin-left: 4px;
        color: var(--muted-2);
        font-size: 10.5px;
        font-weight: 600;
    }

    /* Disponible con barra */
    .historial-disp { min-width: 130px; }

    .historial-disp-valor {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    .historial-mini {
        height: 5px;
        margin-top: 8px;
        overflow: hidden;
        border-radius: 999px;
        background: var(--panel-2);
    }

    .historial-mini > div {
        height: 100%;
        border-radius: 999px;
        background: var(--c, var(--blue));
    }

    /* Badges */
    .historial-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px 4px 8px;
        border: 1px solid color-mix(in srgb, var(--c) 32%, transparent);
        border-radius: 999px;
        color: var(--c);
        background: color-mix(in srgb, var(--c) 10%, transparent);
        font-size: 10.5px;
        font-weight: 750;
        white-space: nowrap;
    }

    .historial-badge::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        box-shadow: 0 0 8px currentColor;
    }

    .historial-badge.vencido { --c: #ff6b72; }
    .historial-badge.pronto  { --c: var(--yellow); }
    .historial-badge.vigente { --c: var(--green); }
    .historial-badge.neutro  { --c: #9a9ca6; }
    .historial-badge.salida  { --c: #ff8f94; }
    .historial-badge.agotado {
        --c: #9a9ca6;
        padding: 2px 8px;
        font-size: 9.5px;
    }
    .historial-badge.agotado::before { display: none; }

    .historial-mono {
        display: inline-block;
        padding: 3px 8px;
        border: 1px solid var(--border-soft);
        border-radius: 6px;
        background: var(--panel);
        font-family: ui-monospace, "SF Mono", Menlo, monospace;
        font-size: 11.5px;
        color: #b4b7c1;
        white-space: nowrap;
    }

    .historial-motivo {
        max-width: 280px;
        color: #c4c6ce;
        line-height: 1.5;
    }

    .historial-vacio {
        padding: 56px 20px !important;
        color: var(--muted) !important;
        text-align: center;
        font-size: 13px;
    }

    .historial-vacio svg {
        display: block;
        margin: 0 auto 14px;
        width: 40px;
        height: 40px;
        color: var(--muted-2);
        opacity: .6;
    }

    .historial-paginacion {
        padding: 14px 22px;
        border-top: 1px solid var(--border-soft);
        background: var(--panel);
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 1000px) {
        .historial-resumen { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 620px) {
        .historial-header {
            flex-direction: column;
            align-items: stretch;
            padding: 22px 18px;
        }

        .historial-header h1 { font-size: 24px; }
        .historial-button { width: 100%; }
        .historial-resumen { grid-template-columns: 1fr; }
        .historial-card-header { padding: 16px; }

        .historial-tabla { min-width: 0; }
        .historial-tabla thead { display: none; }

        .historial-tabla tbody tr {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 14px;
            padding: 16px;
            border-top: 1px solid var(--border-soft);
        }

        .historial-tabla tbody tr:first-child { border-top: 0; }

        .historial-tabla tbody td {
            padding: 0;
            border: 0;
            background: transparent !important;
            box-shadow: none !important;
        }

        .historial-tabla tbody td[data-label]::before {
            content: attr(data-label);
            display: block;
            margin-bottom: 3px;
            color: var(--muted-2);
            font-size: 9.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .historial-tabla td.col-lote,
        .historial-tabla td.col-proveedor,
        .historial-tabla td.col-almacen,
        .historial-tabla td.col-motivo { grid-column: 1 / -1; }

        .historial-tabla td.historial-cantidad { text-align: left; }
        .historial-disp-valor { justify-content: flex-start; }
        .historial-motivo { max-width: none; }

        .historial-tabla td.historial-vacio {
            grid-column: 1 / -1;
            text-align: center;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .historial-page * { transition: none !important; }
    }
</style>
@endpush

@section('content')
@php
    $formatearCantidad = static function ($cantidad) {
        return rtrim(rtrim(number_format((float) $cantidad, 3, ',', '.'), '0'), ',');
    };

    $hoy = \Illuminate\Support\Carbon::today();

    $ingresadoTotal = max((float) $cantidadTotalIngresada, 0);
    $pctSalidas     = $ingresadoTotal > 0 ? min(100, ((float) $cantidadTotalSalidas / $ingresadoTotal) * 100) : 0;
    $pctExistente   = $ingresadoTotal > 0 ? min(100 - $pctSalidas, ((float) $stockActualLotes / $ingresadoTotal) * 100) : 0;
@endphp

<div class="historial-page">

    {{-- ========== Header ========== --}}
    <header class="historial-header">
        <div>
            <span class="historial-eyebrow">Inventario · Trazabilidad</span>
            <h1>Historial del producto</h1>
            <p class="historial-producto">
                <span class="codigo">{{ $producto->codigo_interno }}</span>
                <span class="sep">·</span>
                <span>{{ $producto->nombre }}</span>
            </p>
        </div>

        <a class="historial-button" href="{{ route('admin.inventario.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Volver al inventario
        </a>
    </header>

    {{-- ========== Resumen ========== --}}
    <section class="historial-resumen" aria-label="Resumen del producto">
        <article class="historial-stat lotes">
            <div class="historial-stat-head">
                <span>Lotes registrados</span>
                <span class="historial-stat-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 12l9 4 9-4"/><path d="M3 17l9 4 9-4"/></svg>
                </span>
            </div>
            <div class="historial-stat-valor">{{ number_format($lotes->count()) }}</div>
            <span class="historial-stat-nota">Recepciones con lote asociado</span>
        </article>

        <article class="historial-stat ingresado">
            <div class="historial-stat-head">
                <span>Total ingresado</span>
                <span class="historial-stat-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
                </span>
            </div>
            <div class="historial-stat-valor">
                {{ $formatearCantidad($cantidadTotalIngresada) }}
                <span class="historial-unidad">{{ $unidad }}</span>
            </div>
            <span class="historial-stat-nota">Acumulado de entradas</span>
        </article>

        <article class="historial-stat existente">
            <div class="historial-stat-head">
                <span>Existencia actual en lotes</span>
                <span class="historial-stat-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4 8 4 8-4z"/><path d="M4 12l8 4 8-4M4 17l8 4 8-4"/></svg>
                </span>
            </div>
            <div class="historial-stat-valor">
                {{ $formatearCantidad($stockActualLotes) }}
                <span class="historial-unidad">{{ $unidad }}</span>
            </div>
            <span class="historial-stat-nota">Disponible en lotes activos</span>
        </article>

        <article class="historial-stat salidas">
            <div class="historial-stat-head">
                <span>Total de salidas</span>
                <span class="historial-stat-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                </span>
            </div>
            <div class="historial-stat-valor">
                {{ $formatearCantidad($cantidadTotalSalidas) }}
                <span class="historial-unidad">{{ $unidad }}</span>
            </div>
            <span class="historial-stat-nota">Consumo acumulado</span>
        </article>
    </section>

    {{-- ========== Balance ========== --}}
    @if ($ingresadoTotal > 0)
        <section class="historial-balance" aria-label="Balance de movimientos">
            <div class="historial-balance-top">
                <h2>Balance de movimientos</h2>
                <div class="historial-leyenda">
                    <span style="--c: var(--yellow)"><i></i>Salidas {{ number_format($pctSalidas, 0) }}%</span>
                    <span style="--c: var(--blue)"><i></i>Existente {{ number_format($pctExistente, 0) }}%</span>
                </div>
            </div>
            <div class="historial-barra" role="img" aria-label="Salidas {{ number_format($pctSalidas, 0) }}% y existencia {{ number_format($pctExistente, 0) }}% del total ingresado">
                <div style="--c: var(--yellow); width: {{ $pctSalidas }}%"></div>
                <div style="--c: var(--blue); width: {{ $pctExistente }}%"></div>
            </div>
        </section>
    @endif

    {{-- ========== Lotes ========== --}}
    <section class="historial-card">
        <div class="historial-card-header">
            <div>
                <h2>Entradas y existencias por lote</h2>
                <p>Consulta cuánto ingresó y cuánto queda disponible en cada lote.</p>
            </div>
            <span class="historial-panel-count">{{ $lotes->count() }} lote(s)</span>
        </div>

        <div class="historial-tabla-contenedor">
            <table class="historial-tabla">
                <thead>
                    <tr>
                        <th>Lote / recepción</th>
                        <th>Fecha de entrada</th>
                        <th>Proveedor / almacén</th>
                        <th class="num">Ingresado</th>
                        <th class="num">Disponible</th>
                        <th class="num">Costo unitario</th>
                        <th>Vencimiento</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lotes as $lote)
                        @php
                            $recibida = (float) (
                                $lote->cantidad_inicial
                                ?: $lote->cantidad_aceptada
                                ?: $lote->cantidad
                                ?: 0
                            );
                            $proveedor = $lote->nombre_comercial
                                ?: ($lote->razon_social ?: 'Sin proveedor');

                            $actual    = (float) $lote->cantidad_actual;
                            $pctLote   = $recibida > 0 ? max(0, min(100, ($actual / $recibida) * 100)) : 0;
                            $colorLote = $pctLote > 50 ? 'var(--green)' : ($pctLote > 20 ? 'var(--yellow)' : '#ff6b72');

                            $fechaRecep = $lote->fecha_recepcion
                                ? \Illuminate\Support\Carbon::parse($lote->fecha_recepcion)
                                : null;

                            $estadoVenc = null;
                            $diasVenc   = null;
                            if ($lote->fecha_vencimiento) {
                                $venc     = \Illuminate\Support\Carbon::parse($lote->fecha_vencimiento)->startOfDay();
                                $diasVenc = (int) $hoy->diffInDays($venc, false);
                                $estadoVenc = $diasVenc < 0 ? 'vencido' : ($diasVenc <= 30 ? 'pronto' : 'vigente');
                            }
                        @endphp

                        <tr>
                            <td class="col-lote">
                                <span class="historial-principal mono">{{ $lote->codigo_lote }}</span>
                                <span class="historial-secundario">{{ $lote->numero_recepcion }}</span>
                                @if ($lote->numero_factura)
                                    <span class="historial-secundario tag">Factura: {{ $lote->numero_factura }}</span>
                                @endif
                            </td>

                            <td class="col-fecha" data-label="Fecha de entrada">
                                @if ($fechaRecep)
                                    <span class="historial-fecha">
                                        {{ $fechaRecep->format('d/m/Y') }}
                                        <small>{{ $fechaRecep->format('H:i') }}</small>
                                    </span>
                                @else
                                    —
                                @endif
                            </td>

                            <td class="col-proveedor">
                                <span class="historial-principal" style="font-size:12.5px">{{ $proveedor }}</span>
                                <span class="historial-secundario">{{ $lote->almacen ?: 'Almacén no indicado' }}</span>
                            </td>

                            <td class="historial-cantidad dim col-qty" data-label="Ingresado">
                                {{ $formatearCantidad($recibida) }}<span class="unidad">{{ $unidad }}</span>
                            </td>

                            <td class="historial-cantidad historial-disp col-stock" data-label="Disponible">
                                <div class="historial-disp-valor">
                                    @if ($actual <= 0)
                                        <span class="historial-badge agotado">Agotado</span>
                                    @endif
                                    <span>{{ $formatearCantidad($actual) }}<span class="unidad">{{ $unidad }}</span></span>
                                </div>
                                <div class="historial-mini" title="{{ number_format($pctLote, 0) }}% disponible">
                                    <div style="--c: {{ $colorLote }}; width: {{ $pctLote }}%"></div>
                                </div>
                            </td>

                            <td class="historial-cantidad dim col-costo" data-label="Costo unit.">
                                @if ($lote->costo_unitario !== null)
                                    {{ number_format((float) $lote->costo_unitario, 2, ',', '.') }}<span class="unidad">Bs</span>
                                @else
                                    —
                                @endif
                            </td>

                            <td class="col-venc" data-label="Vencimiento">
                                @if ($estadoVenc)
                                    <span class="historial-fecha">
                                        {{ $venc->format('d/m/Y') }}
                                    </span>
                                    <span class="historial-badge {{ $estadoVenc }}" style="margin-top:6px">
                                        @if ($estadoVenc === 'vencido')
                                            Vencido hace {{ abs($diasVenc) }} d
                                        @elseif ($estadoVenc === 'pronto')
                                            {{ $diasVenc === 0 ? 'Vence hoy' : 'Vence en '.$diasVenc.' d' }}
                                        @else
                                            Vigente
                                        @endif
                                    </span>
                                @else
                                    <span class="historial-badge neutro">Sin vencimiento</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="historial-vacio">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 12l9 4 9-4"/></svg>
                                Este producto no tiene lotes relacionados con recepciones.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- ========== Salidas ========== --}}
    <section class="historial-card">
        <div class="historial-card-header">
            <div>
                <h2>Historial de salidas</h2>
                <p>Movimientos de consumo asociados a este producto.</p>
            </div>
            <span class="historial-panel-count">{{ number_format($totalRegistrosSalidas) }} registro(s)</span>
        </div>

        <div class="historial-tabla-contenedor">
            <table class="historial-tabla">
                <thead>
                    <tr>
                        <th>Movimiento</th>
                        <th>Fecha</th>
                        <th>Tipo de salida</th>
                        <th>Lote</th>
                        <th>Almacén</th>
                        <th class="num">Cantidad</th>
                        <th>Motivo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($salidas as $salida)
                        @php
                            $fechaSalida = $salida->fecha_movimiento
                                ? \Illuminate\Support\Carbon::parse($salida->fecha_movimiento)
                                : null;
                        @endphp

                        <tr>
                            <td class="col-mov">
                                <span class="historial-principal mono">{{ $salida->numero_movimiento }}</span>
                            </td>

                            <td class="col-fecha" data-label="Fecha">
                                @if ($fechaSalida)
                                    <span class="historial-fecha">
                                        {{ $fechaSalida->format('d/m/Y') }}
                                        <small>{{ $fechaSalida->format('H:i') }}</small>
                                    </span>
                                @else
                                    —
                                @endif
                            </td>

                            <td class="col-tipo" data-label="Tipo">
                                <span class="historial-badge salida">{{ $salida->tipo_salida }}</span>
                            </td>

                            <td class="col-lote" data-label="Lote">
                                <span class="historial-mono">{{ $salida->codigo_lote ?: 'Sin lote' }}</span>
                            </td>

                            <td class="col-almacen" data-label="Almacén">
                                {{ $salida->almacen ?: '—' }}
                            </td>

                            <td class="historial-cantidad col-qty" data-label="Cantidad">
                                {{ $formatearCantidad($salida->cantidad) }}<span class="unidad">{{ $unidad }}</span>
                            </td>

                            <td class="col-motivo" data-label="Motivo">
                                <span class="historial-motivo">{{ $salida->motivo ?: 'Sin detalle' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="historial-vacio">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                                Todavía no hay salidas registradas para este producto.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($salidas->hasPages())
            <div class="historial-paginacion">
                {{ $salidas->links() }}
            </div>
        @endif
    </section>
</div>
@endsection