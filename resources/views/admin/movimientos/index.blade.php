@extends('admin.layouts.app')

@section('title', 'Movimientos de inventario')

@push('styles')
<style>
    .mov-page {
        --bg: #13141a;
        --surface: #1a1c23;
        --surface-2: #202229;
        --border: #2b2e38;
        --text: #ececf0;
        --muted: #92949c;
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

    .mov-page *,
    .mov-page *::before,
    .mov-page *::after {
        box-sizing: border-box;
    }

    .mov-header,
    .mov-card,
    .mov-stat {
        border: 1px solid var(--border);
        border-radius: 16px;
        background: var(--surface);
    }

    /* ===== Cabecera ===== */
    .mov-header {
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

    .mov-header::before {
        content: "";
        position: absolute;
        inset: 0 0 auto;
        height: 2px;
        background: linear-gradient(90deg, var(--red), transparent);
    }

    .mov-eyebrow {
        color: var(--red-light);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.1px;
        text-transform: uppercase;
    }

    .mov-header h1 {
        margin: 8px 0;
        color: #fff;
        font-size: clamp(22px, 3vw, 30px);
    }

    .mov-header p {
        margin: 0;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.6;
    }

    /* ===== Estadísticas ===== */
    .mov-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .mov-stat {
        --accent: #c9cad0;
        position: relative;
        overflow: hidden;
        padding: 16px;
        background: linear-gradient(145deg, #1c1e25, #17181e);
    }

    .mov-stat.entrada { --accent: var(--green); }
    .mov-stat.salida { --accent: #ff777d; }
    .mov-stat.interno { --accent: var(--amber); }

    .mov-stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .mov-stat-top span {
        color: var(--muted);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
    }

    .mov-stat-icon {
        display: grid;
        place-items: center;
        width: 30px;
        height: 30px;
        border-radius: 9px;
        color: var(--accent);
        background: color-mix(in srgb, var(--accent) 13%, transparent);
        font-size: 14px;
        font-weight: 800;
    }

    .mov-stat strong {
        display: block;
        margin: 10px 0 9px;
        color: var(--accent);
        font-size: 26px;
        line-height: 1;
    }

    .mov-stat.total strong {
        color: #fff;
    }

    .mov-stat-bar {
        height: 5px;
        overflow: hidden;
        border-radius: 99px;
        background: #30323b;
    }

    .mov-stat-bar span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: var(--accent);
    }

    .mov-stat small {
        display: block;
        margin-top: 6px;
        color: var(--muted);
        font-size: 10px;
    }

    /* ===== Tarjeta ===== */
    .mov-card {
        overflow: hidden;
    }

    .mov-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        padding: 15px 19px;
        border-bottom: 1px solid var(--border);
    }

    .mov-card-header h2 {
        margin: 0;
        color: #fff;
        font-size: 14px;
    }

    .mov-card-header p {
        margin: 3px 0 0;
        color: var(--muted);
        font-size: 11px;
    }

    .mov-conteo {
        padding: 6px 10px;
        border: 1px solid #383b45;
        border-radius: 999px;
        color: #c9cad0;
        background: #20222a;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    /* ===== Filtros ===== */
    .mov-filtros {
        display: grid;
        grid-template-columns: minmax(200px, 1.6fr) repeat(4, minmax(130px, 1fr)) auto;
        align-items: end;
        gap: 11px;
        padding: 17px 19px;
    }

    .mov-field {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .mov-field label {
        color: #d8d9de;
        font-size: 11px;
        font-weight: 750;
    }

    .mov-filtros input,
    .mov-filtros select {
        width: 100%;
        min-height: 42px;
        padding: 0 11px;
        border: 1px solid #383b45;
        border-radius: 8px;
        color: var(--text);
        background: var(--surface-2);
        font: inherit;
        font-size: 12px;
        color-scheme: dark;
    }

    .mov-filtros input::placeholder {
        color: #737681;
    }

    .mov-filtros input:focus-visible,
    .mov-filtros select:focus-visible,
    .mov-button:focus-visible,
    .mov-detail-link:focus-visible,
    .mov-pagination a:focus-visible {
        outline: 2px solid #ff777d;
        outline-offset: 2px;
    }

    .mov-filtros-acciones {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .mov-button {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 0 16px;
        border: 1px solid #c9161e;
        border-radius: 9px;
        color: #fff;
        background: linear-gradient(135deg, #e31b23, #ad1219);
        font: inherit;
        font-size: 12px;
        font-weight: 750;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition: .18s ease;
    }

    .mov-button:hover {
        background: linear-gradient(135deg, #f02a32, #c4161e);
        transform: translateY(-1px);
    }

    .mov-button.ghost {
        border-color: #383b45;
        color: #eee;
        background: #24262e;
    }

    .mov-button.ghost:hover {
        border-color: #585d6a;
        background: #2b2e37;
    }

    .mov-activos {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
        padding: 0 19px 16px;
    }

    .mov-activos-label {
        color: var(--muted);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
    }

    .mov-chip {
        padding: 4px 10px;
        border: 1px solid rgba(227, 27, 35, .35);
        border-radius: 999px;
        color: var(--red-light);
        background: rgba(227, 27, 35, .08);
        font-size: 10px;
        font-weight: 700;
    }

    /* ===== Tabla ===== */
    .mov-table-wrap {
        overflow-x: auto;
    }

    .mov-table {
        width: 100%;
        min-width: 920px;
        border-collapse: collapse;
    }

    .mov-table th,
    .mov-table td {
        padding: 13px 15px;
        border-bottom: 1px solid #272a32;
        text-align: left;
        vertical-align: middle;
    }

    .mov-table th {
        color: var(--muted);
        background: #1c1e25;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .5px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .mov-table td {
        color: #dfe0e5;
        font-size: 12px;
    }

    .mov-table tbody tr {
        transition: background .15s ease;
    }

    .mov-table tbody tr:hover {
        background: rgba(255, 255, 255, .03);
    }

    .mov-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .mov-number {
        color: #fff;
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        font-size: 12px;
        font-weight: 750;
        white-space: nowrap;
    }

    .mov-fecha {
        display: flex;
        flex-direction: column;
        gap: 2px;
        white-space: nowrap;
    }

    .mov-fecha strong {
        color: #eee;
        font-size: 12px;
        font-weight: 700;
    }

    .mov-fecha small {
        color: var(--muted);
        font-size: 10px;
    }

    .mov-type,
    .mov-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 750;
        white-space: nowrap;
    }

    .mov-type::before,
    .mov-status::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .mov-type.entrada {
        color: #9aefb4;
        background: rgba(74, 222, 128, .1);
    }

    .mov-type.salida {
        color: #ff9ba0;
        background: rgba(227, 27, 35, .1);
    }

    .mov-type.interno {
        color: #ffdc91;
        background: rgba(245, 185, 66, .1);
    }

    .mov-status {
        color: #c5c7cf;
        background: rgba(255, 255, 255, .07);
    }

    .mov-status.ok {
        color: #9aefb4;
        background: rgba(74, 222, 128, .1);
    }

    .mov-status.pendiente {
        color: #ffdc91;
        background: rgba(245, 185, 66, .1);
    }

    .mov-status.anulado {
        color: #ff9ba0;
        background: rgba(227, 27, 35, .1);
    }

    .mov-ruta {
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .mov-ruta-punto {
        color: #eee;
        font-weight: 650;
    }

    .mov-ruta-punto.vacio {
        color: #5f626d;
        font-weight: 400;
    }

    .mov-ruta-flecha {
        color: var(--red-light);
        font-size: 13px;
        font-weight: 800;
    }

    .mov-lineas {
        display: inline-flex;
        align-items: center;
        padding: 4px 9px;
        border: 1px solid #383b45;
        border-radius: 8px;
        color: #c9cad0;
        background: #20222a;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .mov-detail-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 11px;
        border: 1px solid rgba(227, 27, 35, .35);
        border-radius: 8px;
        color: #ff9aa0;
        background: rgba(227, 27, 35, .08);
        font-size: 11px;
        font-weight: 750;
        text-decoration: none;
        white-space: nowrap;
        transition: .18s ease;
    }

    .mov-detail-link:hover {
        border-color: var(--red);
        color: #fff;
        background: rgba(227, 27, 35, .2);
    }

    .mov-empty {
        padding: 44px 15px !important;
        color: var(--muted) !important;
        text-align: center !important;
        font-size: 13px !important;
        line-height: 1.7;
    }

    .mov-empty strong {
        display: block;
        margin-bottom: 4px;
        color: #fff;
        font-size: 14px;
    }

    /* ===== Paginación ===== */
    .mov-pagination {
        padding: 14px 19px;
        border-top: 1px solid var(--border);
    }

    .mov-pagination nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .mov-pagination svg {
        width: 16px;
        height: 16px;
    }

    .mov-pagination p {
        margin: 0;
        color: var(--muted);
        font-size: 11px;
    }

    .mov-pagination a,
    .mov-pagination span[aria-current] span,
    .mov-pagination span[aria-disabled] span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        min-height: 34px;
        padding: 0 10px;
        border: 1px solid #383b45;
        border-radius: 8px;
        color: #dfe0e5;
        background: #24262e;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
    }

    .mov-pagination a:hover {
        border-color: var(--red);
        color: #fff;
        background: rgba(227, 27, 35, .13);
    }

    .mov-pagination span[aria-current] span {
        border-color: var(--red);
        color: #fff;
        background: rgba(227, 27, 35, .2);
    }

    .mov-pagination span[aria-disabled] span {
        opacity: .45;
    }

    .mov-pagination .relative.z-0,
    .mov-pagination span.relative {
        display: inline-flex;
        gap: 5px;
    }

    /* ===== Responsive ===== */
    @media (max-width: 1100px) {
        .mov-filtros {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .mov-field.buscar {
            grid-column: 1 / -1;
        }

        .mov-filtros-acciones {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 900px) {
        .mov-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
        .mov-table {
            min-width: 0;
        }

        .mov-table thead {
            display: none;
        }

        .mov-table,
        .mov-table tbody,
        .mov-table tr,
        .mov-table td {
            display: block;
            width: 100%;
        }

        .mov-table tbody tr {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border);
        }

        .mov-table td {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 7px 0;
            border-bottom: 0;
            text-align: right;
        }

        .mov-table td::before {
            content: attr(data-label);
            color: var(--muted);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .4px;
            text-align: left;
            text-transform: uppercase;
        }

        .mov-table td.mov-empty {
            display: block;
        }

        .mov-table td.mov-empty::before {
            content: none;
        }

        .mov-fecha {
            align-items: flex-end;
        }
    }

    @media (max-width: 600px) {
        .mov-header {
            padding: 20px;
        }

        .mov-filtros {
            grid-template-columns: 1fr;
            padding: 15px;
        }

        .mov-filtros-acciones .mov-button {
            flex: 1;
        }

        .mov-activos {
            padding: 0 15px 15px;
        }
    }

    @media (max-width: 420px) {
        .mov-stats {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $filtros = $filtros ?? [];

    $totalBase = max(1, (int) $totalMovimientos);

    $pctEntradas = min(100, round(((int) $totalEntradas / $totalBase) * 100));
    $pctSalidas = min(100, round(((int) $totalSalidas / $totalBase) * 100));
    $pctInternos = min(100, round(((int) $totalInternos / $totalBase) * 100));

    $tipoActivo = collect($tiposMovimiento)->firstWhere('id', $filtros['tipo_movimiento_id'] ?? null);
    $almacenActivo = collect($almacenes)->firstWhere('id', $filtros['almacen_id'] ?? null);

    $hayFiltros = filled($buscar)
        || filled($filtros['tipo_movimiento_id'] ?? null)
        || filled($filtros['almacen_id'] ?? null)
        || filled($filtros['desde'] ?? null)
        || filled($filtros['hasta'] ?? null);

    $formatoFiltro = function ($fecha) {
        try {
            return \Illuminate\Support\Carbon::parse($fecha)->format('d/m/Y');
        } catch (\Throwable $e) {
            return $fecha;
        }
    };
@endphp

<div class="mov-page">

    <header class="mov-header">
        <div>
            <span class="mov-eyebrow">Control de inventario</span>
            <h1>Movimientos</h1>
            <p>Consulta entradas, salidas, traslados, ajustes y otros movimientos registrados.</p>
        </div>
    </header>

    <section class="mov-stats" aria-label="Resumen de movimientos">
        <article class="mov-stat total">
            <div class="mov-stat-top">
                <span>Movimientos encontrados</span>
                <span class="mov-stat-icon" aria-hidden="true">≡</span>
            </div>
            <strong>{{ number_format($totalMovimientos) }}</strong>
            <small>{{ $hayFiltros ? 'Con los filtros aplicados' : 'En todo el registro' }}</small>
        </article>

        <article class="mov-stat entrada">
            <div class="mov-stat-top">
                <span>Entradas</span>
                <span class="mov-stat-icon" aria-hidden="true">↓</span>
            </div>
            <strong>{{ number_format($totalEntradas) }}</strong>
            <div class="mov-stat-bar" aria-hidden="true"><span style="width: {{ $pctEntradas }}%"></span></div>
            <small>{{ $pctEntradas }}% del total</small>
        </article>

        <article class="mov-stat salida">
            <div class="mov-stat-top">
                <span>Salidas</span>
                <span class="mov-stat-icon" aria-hidden="true">↑</span>
            </div>
            <strong>{{ number_format($totalSalidas) }}</strong>
            <div class="mov-stat-bar" aria-hidden="true"><span style="width: {{ $pctSalidas }}%"></span></div>
            <small>{{ $pctSalidas }}% del total</small>
        </article>

        <article class="mov-stat interno">
            <div class="mov-stat-top">
                <span>Movimientos internos</span>
                <span class="mov-stat-icon" aria-hidden="true">⇄</span>
            </div>
            <strong>{{ number_format($totalInternos) }}</strong>
            <div class="mov-stat-bar" aria-hidden="true"><span style="width: {{ $pctInternos }}%"></span></div>
            <small>{{ $pctInternos }}% del total</small>
        </article>
    </section>

    <section class="mov-card">
        <div class="mov-card-header">
            <div>
                <h2>Filtrar movimientos</h2>
                <p>Combina búsqueda, tipo, almacén y rango de fechas.</p>
            </div>
        </div>

        <form action="{{ route('admin.movimientos.index') }}" method="GET" class="mov-filtros">
            <div class="mov-field buscar">
                <label for="filtro-buscar">Buscar</label>
                <input
                    id="filtro-buscar"
                    type="search"
                    name="buscar"
                    value="{{ $buscar }}"
                    placeholder="N.º, producto, lote o motivo"
                >
            </div>

            <div class="mov-field">
                <label for="filtro-tipo">Tipo</label>
                <select id="filtro-tipo" name="tipo_movimiento_id">
                    <option value="">Todos los tipos</option>
                    @foreach ($tiposMovimiento as $tipo)
                        <option
                            value="{{ $tipo->id }}"
                            @selected((string) ($filtros['tipo_movimiento_id'] ?? '') === (string) $tipo->id)
                        >
                            {{ $tipo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mov-field">
                <label for="filtro-almacen">Almacén</label>
                <select id="filtro-almacen" name="almacen_id">
                    <option value="">Todos los almacenes</option>
                    @foreach ($almacenes as $almacen)
                        <option
                            value="{{ $almacen->id }}"
                            @selected((string) ($filtros['almacen_id'] ?? '') === (string) $almacen->id)
                        >
                            {{ $almacen->codigo }} — {{ $almacen->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mov-field">
                <label for="filtro-desde">Desde</label>
                <input
                    id="filtro-desde"
                    type="date"
                    name="desde"
                    value="{{ $filtros['desde'] ?? '' }}"
                >
            </div>

            <div class="mov-field">
                <label for="filtro-hasta">Hasta</label>
                <input
                    id="filtro-hasta"
                    type="date"
                    name="hasta"
                    value="{{ $filtros['hasta'] ?? '' }}"
                >
            </div>

            <div class="mov-filtros-acciones">
                <button type="submit" class="mov-button">Filtrar</button>

                @if ($hayFiltros)
                    <a href="{{ route('admin.movimientos.index') }}" class="mov-button ghost">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>

        @if ($hayFiltros)
            <div class="mov-activos" aria-label="Filtros aplicados">
                <span class="mov-activos-label">Filtros activos:</span>

                @if (filled($buscar))
                    <span class="mov-chip">Búsqueda: “{{ $buscar }}”</span>
                @endif

                @if ($tipoActivo)
                    <span class="mov-chip">Tipo: {{ $tipoActivo->nombre }}</span>
                @endif

                @if ($almacenActivo)
                    <span class="mov-chip">Almacén: {{ $almacenActivo->codigo }}</span>
                @endif

                @if (filled($filtros['desde'] ?? null))
                    <span class="mov-chip">Desde: {{ $formatoFiltro($filtros['desde']) }}</span>
                @endif

                @if (filled($filtros['hasta'] ?? null))
                    <span class="mov-chip">Hasta: {{ $formatoFiltro($filtros['hasta']) }}</span>
                @endif
            </div>
        @endif
    </section>

    <section class="mov-card">
        <div class="mov-card-header">
            <div>
                <h2>Historial de movimientos</h2>
                <p>Ordenados según el registro más reciente del sistema.</p>
            </div>

            <span class="mov-conteo">
                @if ($movimientos->count() > 0)
                    Mostrando {{ $movimientos->firstItem() }}–{{ $movimientos->lastItem() }}
                    de {{ number_format($movimientos->total()) }}
                @else
                    0 resultados
                @endif
            </span>
        </div>

        <div class="mov-table-wrap">
            <table class="mov-table">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Ruta</th>
                        <th>Detalle</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($movimientos as $movimiento)
                        @php
                            $naturaleza = strtolower($movimiento->naturaleza ?? 'interno');
                            $claseNaturaleza = in_array($naturaleza, ['entrada', 'salida'], true)
                                ? $naturaleza
                                : 'interno';

                            $estado = strtolower((string) ($movimiento->estado ?? ''));
                            $claseEstado = match (true) {
                                in_array($estado, ['completado', 'confirmado', 'aprobado', 'procesado', 'finalizado', 'recibido'], true) => 'ok',
                                in_array($estado, ['pendiente', 'borrador', 'en proceso', 'en_proceso'], true) => 'pendiente',
                                in_array($estado, ['anulado', 'cancelado', 'rechazado'], true) => 'anulado',
                                default => '',
                            };

                            $fecha = null;

                            if ($movimiento->fecha_movimiento) {
                                try {
                                    $fecha = \Illuminate\Support\Carbon::parse($movimiento->fecha_movimiento);
                                } catch (\Throwable $e) {
                                    $fecha = null;
                                }
                            }

                            $lineas = (int) $movimiento->cantidad_lineas;
                        @endphp

                        <tr>
                            <td data-label="Número">
                                <span class="mov-number">{{ $movimiento->numero_movimiento }}</span>
                            </td>

                            <td data-label="Fecha">
                                @if ($fecha)
                                    <span class="mov-fecha">
                                        <strong>{{ $fecha->format('d/m/Y') }}</strong>
                                        <small>{{ $fecha->format('H:i') }}</small>
                                    </span>
                                @else
                                    —
                                @endif
                            </td>

                            <td data-label="Tipo">
                                <span class="mov-type {{ $claseNaturaleza }}">
                                    {{ $movimiento->tipo_nombre }}
                                </span>
                            </td>

                            <td data-label="Ruta">
                                <span class="mov-ruta">
                                    <span class="mov-ruta-punto {{ $movimiento->almacen_origen ? '' : 'vacio' }}">
                                        {{ $movimiento->almacen_origen ?: '—' }}
                                    </span>

                                    <span class="mov-ruta-flecha" aria-hidden="true">→</span>

                                    <span class="mov-ruta-punto {{ $movimiento->almacen_destino ? '' : 'vacio' }}">
                                        {{ $movimiento->almacen_destino ?: '—' }}
                                    </span>
                                </span>
                            </td>

                            <td data-label="Detalle">
                                <span class="mov-lineas">
                                    {{ $lineas }} {{ $lineas === 1 ? 'línea' : 'líneas' }}
                                </span>
                            </td>

                            <td data-label="Estado">
                                <span class="mov-status {{ $claseEstado }}">
                                    {{ ucfirst($movimiento->estado ?? 'sin estado') }}
                                </span>
                            </td>

                            <td data-label="Acción">
                                <a
                                    href="{{ route('admin.movimientos.show', $movimiento->id) }}"
                                    class="mov-detail-link"
                                    aria-label="Ver detalle del movimiento {{ $movimiento->numero_movimiento }}"
                                >
                                    Ver detalle →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="mov-empty">
                                <strong>Sin movimientos</strong>
                                No hay movimientos que coincidan con los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($movimientos->hasPages())
            <div class="mov-pagination">
                {{ $movimientos->withQueryString()->links() }}
            </div>
        @endif
    </section>
</div>
@endsection