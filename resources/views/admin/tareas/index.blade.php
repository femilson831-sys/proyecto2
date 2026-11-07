@extends('admin.layouts.app')

@section('title', !empty($registro) ? 'Detalle de tarea' : 'Tareas del personal')

@php
    $registro = $registro ?? null;

    $detalles = collect($detalles ?? []);
    $asignados = collect($asignados ?? []);
    $estados = collect($estados ?? []);

    $buscar = trim((string) ($buscar ?? ''));
    $estadoId = $estadoId ?? null;

    $esDetalle = $registro !== null;
    $hayFiltros = $buscar !== '' || ($estadoId !== null && $estadoId !== '');

    $cantidad = static function ($valor) {
        if ($valor === null || $valor === '') {
            return '—';
        }

        return rtrim(
            rtrim(number_format((float) $valor, 3, ',', '.'), '0'),
            ','
        );
    };

    $leerFecha = static function ($valor) {
        if (!$valor) {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::parse($valor);
        } catch (\Throwable $error) {
            return null;
        }
    };

    $normalizar = static function ($valor) {
        return \Illuminate\Support\Str::ascii(
            \Illuminate\Support\Str::lower(trim((string) $valor))
        );
    };

    $estadoVisual = static function ($nombre) use ($normalizar) {
        $clave = $normalizar($nombre);

        $tono = match ($clave) {
            'completada' => 'green',
            'asignada', 'en proceso' => 'blue',
            'pendiente de revision', 'reconteo solicitado' => 'amber',
            'cancelada' => 'red',
            default => 'slate',
        };

        return [
            'texto' => trim((string) $nombre) ?: 'Sin estado',
            'tono' => $tono,
        ];
    };

    $prioridadVisual = static function ($nombre) use ($normalizar) {
        $clave = $normalizar($nombre ?: 'normal');

        return [
            'texto' => ucfirst($clave),
            'tono' => match ($clave) {
                'urgente' => 'red',
                'alta' => 'amber',
                'normal' => 'blue',
                default => 'slate',
            },
        ];
    };

    $resultadoVisual = static function ($resultado) {
        return match ($resultado) {
            'coincide_referencia' => [
                'texto' => 'Coincide con referencia',
                'tono' => 'green',
            ],
            'con_diferencia' => [
                'texto' => 'Con diferencia',
                'tono' => 'amber',
            ],
            'no_realizada' => [
                'texto' => 'No realizada',
                'tono' => 'red',
            ],
            'sin_reportar' => [
                'texto' => 'Sin informar',
                'tono' => 'slate',
            ],
            'sin_referencia' => [
                'texto' => 'Sin referencia',
                'tono' => 'amber',
            ],
            'reportado' => [
                'texto' => 'Informado',
                'tono' => 'blue',
            ],
            default => [
                'texto' => ucfirst(str_replace('_', ' ', $resultado ?: 'pendiente')),
                'tono' => 'slate',
            ],
        };
    };

    $estaFueraDePlazo = static function ($tarea) use ($leerFecha, $normalizar) {
        $limite = $leerFecha($tarea->fecha_limite ?? null);

        $cerrada = in_array(
            $normalizar($tarea->estado ?? ''),
            ['completada', 'cancelada'],
            true
        );

        return $limite !== null && $limite->isPast() && !$cerrada;
    };

    $visibles = !$esDetalle
        ? (
            is_object($tareas ?? null)
            && method_exists($tareas, 'getCollection')
                ? $tareas->getCollection()
                : collect($tareas ?? [])
        )
        : collect();

    $totalResultados = !$esDetalle
        && is_object($tareas ?? null)
        && method_exists($tareas, 'total')
            ? $tareas->total()
            : $visibles->count();

    $urgentesVisibles = $visibles->filter(
        fn ($tarea) => $normalizar($tarea->prioridad ?? '') === 'urgente'
    )->count();

    $vencidasVisibles = $visibles->filter($estaFueraDePlazo)->count();

    $responsables = $asignados
        ->pluck('nombre')
        ->filter(fn ($nombre) => is_string($nombre) && trim($nombre) !== '')
        ->map(fn ($nombre) => trim($nombre))
        ->unique()
        ->values();

    $extraOriginal = data_get($detalles->first(), 'datos_adicionales', []);

    $extras = is_array($extraOriginal)
        ? $extraOriginal
        : (
            is_string($extraOriginal)
                ? json_decode($extraOriginal, true)
                : []
        );

    $extras = is_array($extras) ? $extras : [];

    $etiquetasControles = [
        'empaque' => 'Estado del empaque',
        'vencimiento' => 'Fechas de vencimiento',
        'limpieza' => 'Limpieza y orden',
        'etiquetado' => 'Etiquetado',
        'conservacion' => 'Conservación',
    ];

    $etiquetasDestino = [
        'interno' => 'Ubicación interna',
        'proveedor' => 'Devolución al proveedor',
        'cuarentena' => 'Separación en cuarentena',
        'baja' => 'Solicitud de baja',
    ];

    $tieneRutaRespuestas = \Illuminate\Support\Facades\Route::has(
        'admin.tareas.respuestas.index'
    );

    $tieneMenuRespuestas = $tieneRutaRespuestas
        && \Illuminate\Support\Facades\View::exists(
            'admin.tareas._menu_respuestas'
        );
@endphp

@push('styles')
<style>
    .tk-page {
        --tk-surface: #17191f;
        --tk-soft: #1d2028;
        --tk-deep: #12141a;
        --tk-border: #303541;
        --tk-text: #ecf0f8;
        --tk-muted: #989faf;
        --tk-red: #e31b23;

        display: grid;
        gap: 20px;
        min-width: 0;
        color: var(--tk-text);
    }

    .tk-page *,
    .tk-page *::before,
    .tk-page *::after {
        box-sizing: border-box;
    }

    .tk-page a,
    .tk-page button,
    .tk-page input,
    .tk-page select {
        -webkit-tap-highlight-color: transparent;
    }

    .tk-page svg {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .tk-sprite {
        position: absolute;
        width: 0 !important;
        height: 0 !important;
        overflow: hidden;
        pointer-events: none;
    }

    .tk-header {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 22px;
        padding: 26px;
        border: 1px solid var(--tk-border);
        border-radius: 18px;
        background:
            radial-gradient(
                ellipse at right top,
                rgba(227, 27, 35, .09),
                transparent 60%
            ),
            linear-gradient(
                135deg,
                rgba(255, 255, 255, .025),
                transparent
            ),
            var(--tk-surface);
        overflow: hidden;
    }

    .tk-header::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #e31b23, #84212b, transparent);
    }

    .tk-header-main {
        display: flex;
        align-items: center;
        gap: 17px;
        min-width: 0;
    }

    .tk-header-icon {
        display: grid;
        place-items: center;
        width: 58px;
        height: 58px;
        flex-shrink: 0;
        border: 1px solid #e31b2338;
        border-radius: 16px;
        color: #ff8990;
        background: #e31b2310;
    }

    .tk-header-icon svg {
        width: 27px;
        height: 27px;
    }

    .tk-eyebrow {
        color: #ff8990;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    .tk-header h1 {
        margin: 7px 0;
        color: #fff;
        font-size: clamp(23px, 3vw, 31px);
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -.7px;
        overflow-wrap: anywhere;
    }

    .tk-header p {
        margin: 0;
        max-width: 620px;
        color: var(--tk-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .tk-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .tk-button,
    .tk-page .ta-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 10px 15px;
        border: 1px solid #3b4150;
        border-radius: 10px;
        color: #e1e6f1;
        background: #242832;
        font: inherit;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.4;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition:
            background .18s ease,
            border-color .18s ease,
            transform .18s ease;
    }

    .tk-button:hover,
    .tk-page .ta-button:hover {
        color: #fff;
        border-color: #596174;
        background: #2d3340;
        transform: translateY(-1px);
    }

    .tk-button:focus-visible,
    .tk-page .ta-button:focus-visible {
        outline: 2px solid #ff8990;
        outline-offset: 3px;
    }

    .tk-button.primary {
        border-color: #e31b23;
        color: #fff;
        background: #ce1b25;
        box-shadow: 0 5px 18px #e31b2318;
    }

    .tk-button.primary:hover {
        border-color: #fa424b;
        background: #e0232d;
    }

    .tk-button.small {
        min-height: 35px;
        padding: 7px 11px;
        font-size: 11px;
    }

    .tk-button.block {
        width: 100%;
    }

    .tk-tabs {
        padding: 7px;
        border: 1px solid var(--tk-border);
        border-radius: 13px;
        background: var(--tk-surface);
        overflow-x: auto;
    }

    .tk-tabs .ta-actions,
    .tk-tabs-inner {
        display: flex;
        justify-content: flex-start !important;
        flex-wrap: nowrap;
        align-items: center;
        gap: 7px;
        width: max-content;
        min-width: 100%;
    }

    .tk-tabs .ta-button,
    .tk-tabs .tk-button {
        min-height: 39px;
        border-color: transparent;
        background: transparent;
        box-shadow: none;
    }

    .tk-tabs .ta-button:first-child,
    .tk-tabs .tk-button.active {
        border-color: #3b4150;
        color: #fff;
        background: #292e39;
    }

    .tk-tabs .ta-button.primary {
        color: #ff9ba1;
        background: #e31b2310;
    }

    .tk-page .ta-badge {
        display: inline-grid;
        place-items: center;
        min-width: 23px;
        min-height: 23px;
        padding: 2px 6px;
        border-radius: 7px;
        background: #ffffff10;
        font-size: 10px;
    }

    .tk-alert {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        padding: 15px 17px;
        border: 1px solid;
        border-radius: 12px;
        font-size: 12px;
        line-height: 1.7;
    }

    .tk-alert.success {
        border-color: #40c88440;
        color: #a5e9c3;
        background: #40c8840c;
    }

    .tk-alert.error {
        border-color: #ed5a6740;
        color: #ffabb2;
        background: #ed5a670c;
    }

    .tk-alert ul {
        margin: 6px 0 0;
        padding-left: 19px;
    }

    .tk-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .tk-stat {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        padding: 18px;
        border: 1px solid var(--tk-border);
        border-radius: 14px;
        background: var(--tk-surface);
        min-width: 0;
    }

    .tk-stat-label {
        display: block;
        margin-bottom: 9px;
        color: var(--tk-muted);
        font-size: 11px;
        line-height: 1.5;
    }

    .tk-stat-value {
        display: block;
        color: #f7f8fc;
        font-size: 27px;
        font-weight: 800;
        line-height: 1.15;
        letter-spacing: -.6px;
        overflow-wrap: anywhere;
    }

    .tk-stat-value.text {
        font-size: 16px;
        line-height: 1.5;
        letter-spacing: 0;
    }

    .tk-stat-note {
        display: block;
        margin-top: 7px;
        color: #727c8e;
        font-size: 10px;
    }

    .tk-stat-icon {
        display: grid;
        place-items: center;
        width: 35px;
        height: 35px;
        flex-shrink: 0;
        border-radius: 10px;
        color: #b1bfdc;
        background: #819bd716;
    }

    .tk-stat-icon.red {
        color: #ff939b;
        background: #e31b2314;
    }

    .tk-stat-icon.amber {
        color: #efc376;
        background: #efc37612;
    }

    .tk-card {
        min-width: 0;
        border: 1px solid var(--tk-border);
        border-radius: 15px;
        background: var(--tk-surface);
        overflow: hidden;
    }

    .tk-card-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding: 19px 22px;
        border-bottom: 1px solid #2b303a;
    }

    .tk-card-head h2 {
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 0;
        color: #edf1fa;
        font-size: 14px;
        font-weight: 750;
    }

    .tk-card-head h2 svg {
        color: #929eb6;
    }

    .tk-card-head p,
    .tk-muted {
        margin: 5px 0 0;
        color: var(--tk-muted);
        font-size: 11px;
        line-height: 1.6;
    }

    .tk-card-body {
        padding: 22px;
    }

    .tk-filter {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) minmax(180px, 1fr) auto;
        align-items: end;
        gap: 15px;
    }

    .tk-field {
        display: grid;
        gap: 8px;
        min-width: 0;
        color: #cdd4e3;
        font-size: 11px;
        font-weight: 700;
    }

    .tk-field input,
    .tk-field select {
        width: 100%;
        min-height: 43px;
        padding: 10px 12px;
        border: 1px solid #3a4150;
        border-radius: 9px;
        outline: none;
        color: #edf1fa;
        background: #20252e;
        font: inherit;
        font-size: 12px;
        font-weight: 400;
        color-scheme: dark;
    }

    .tk-field input:focus,
    .tk-field select:focus {
        border-color: #ed626c;
        box-shadow: 0 0 0 3px #e31b2312;
    }

    .tk-search {
        position: relative;
    }

    .tk-search svg {
        position: absolute;
        top: 13px;
        left: 12px;
        width: 17px;
        height: 17px;
        color: #8490a5;
        pointer-events: none;
    }

    .tk-search input {
        padding-left: 38px;
    }

    .tk-filter-summary {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px solid #2b303a;
        color: var(--tk-muted);
        font-size: 11px;
    }

    .tk-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        max-width: 100%;
        padding: 6px 10px;
        border: 1px solid #394252;
        border-radius: 7px;
        color: #cbd6ec;
        background: #202733;
        font-size: 11px;
        overflow-wrap: anywhere;
    }

    .tk-table-wrap {
        overflow-x: auto;
    }

    .tk-table {
        width: 100%;
        min-width: 780px;
        border-collapse: collapse;
    }

    .tk-table th {
        padding: 13px 18px;
        color: #939fb4;
        background: #1b1f27;
        text-align: left;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .8px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .tk-table td {
        padding: 17px 18px;
        border-bottom: 1px solid #292f39;
        color: #d6ddeb;
        font-size: 12px;
        line-height: 1.5;
        vertical-align: middle;
    }

    .tk-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .tk-table tbody tr:hover td {
        background: #ffffff02;
    }

    .tk-task-cell {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 230px;
    }

    .tk-task-symbol {
        display: grid;
        place-items: center;
        width: 37px;
        height: 37px;
        flex-shrink: 0;
        border: 1px solid #374052;
        border-radius: 10px;
        color: #acbedf;
        background: #202835;
    }

    .tk-code {
        display: block;
        margin-bottom: 4px;
        color: #f0f3fa;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
        overflow-wrap: anywhere;
    }

    .tk-code:hover {
        color: #ff979e;
    }

    .tk-task-title {
        display: block;
        max-width: 340px;
        color: var(--tk-muted);
        font-size: 11px;
        overflow-wrap: anywhere;
    }

    .tk-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 9px;
        border: 1px solid;
        border-radius: 7px;
        font-size: 10px;
        font-weight: 700;
        line-height: 1.4;
    }

    .tk-badge::before {
        content: "";
        width: 5px;
        height: 5px;
        flex-shrink: 0;
        border-radius: 50%;
        background: currentColor;
    }

    .tk-badge.slate {
        border-color: #8994ab30;
        color: #b4bfd2;
        background: #8994ab0c;
    }

    .tk-badge.blue {
        border-color: #75a4ef35;
        color: #a8c8ff;
        background: #75a4ef0c;
    }

    .tk-badge.green {
        border-color: #57ce9235;
        color: #9ae5bb;
        background: #57ce920c;
    }

    .tk-badge.amber {
        border-color: #edbd6835;
        color: #edc980;
        background: #edbd680c;
    }

    .tk-badge.red {
        border-color: #f0758035;
        color: #ffadb4;
        background: #f075800c;
    }

    .tk-date {
        display: block;
        color: #dce3f1;
        font-size: 11px;
        font-variant-numeric: tabular-nums;
    }

    .tk-date-sub {
        display: block;
        margin-top: 4px;
        color: #8995aa;
        font-size: 10px;
    }

    .tk-date-sub.overdue {
        color: #efa0a7;
    }

    .tk-empty {
        display: grid;
        justify-items: center;
        gap: 12px;
        padding: 52px 22px;
        text-align: center;
    }

    .tk-empty-icon {
        display: grid;
        place-items: center;
        width: 60px;
        height: 60px;
        margin-bottom: 3px;
        border: 1px solid #394252;
        border-radius: 17px;
        color: #91a4c6;
        background: #222a37;
    }

    .tk-empty-icon svg {
        width: 27px;
        height: 27px;
    }

    .tk-empty h3 {
        margin: 0;
        color: #edf2fb;
        font-size: 17px;
    }

    .tk-empty p {
        max-width: 410px;
        margin: 0 0 5px;
        color: var(--tk-muted);
        font-size: 12px;
        line-height: 1.8;
    }

    .tk-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        padding: 17px 22px;
        border-top: 1px solid #2b303a;
        color: var(--tk-muted);
        font-size: 11px;
    }

    .tk-pagination-links {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-wrap: wrap;
    }

    .tk-page-link {
        display: inline-grid;
        place-items: center;
        min-width: 33px;
        height: 33px;
        padding: 0 9px;
        border: 1px solid #343d4b;
        border-radius: 7px;
        color: #c6d1e7;
        background: #202631;
        text-decoration: none;
    }

    .tk-page-link.current {
        border-color: #d92b35;
        color: #fff;
        background: #c51f29;
    }

    .tk-page-link.disabled {
        color: #606b7e;
        background: transparent;
    }

    .tk-detail-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 20px;
        align-items: start;
    }

    .tk-detail-main,
    .tk-sidebar {
        display: grid;
        gap: 20px;
        min-width: 0;
    }

    .tk-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .tk-data-label {
        display: block;
        margin-bottom: 7px;
        color: #8895ab;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
    }

    .tk-data-value {
        display: block;
        color: #e5ebf6;
        font-size: 13px;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }

    .tk-description {
        margin: 0;
        color: #c6cfdf;
        font-size: 13px;
        line-height: 1.9;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .tk-instructions {
        padding: 16px;
        border: 1px solid #343d4d;
        border-radius: 10px;
        background: #1d2531;
    }

    .tk-number {
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
        font-weight: 700;
    }

    .tk-unit {
        margin-left: 3px;
        color: #8796af;
        font-size: 10px;
        font-weight: 400;
    }

    .tk-subtext {
        display: block;
        margin-top: 5px;
        color: #8997ae;
        font-size: 10px;
    }

    .tk-subtext.difference {
        color: #efc17c;
    }

    .tk-route-line {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #c3d0e7;
        font-size: 11px;
        white-space: nowrap;
    }

    .tk-route-line svg {
        width: 13px;
        height: 13px;
        color: #6d7b93;
    }

    .tk-person {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 11px 0;
    }

    .tk-person + .tk-person {
        border-top: 1px solid #2b323f;
    }

    .tk-avatar {
        display: grid;
        place-items: center;
        width: 39px;
        height: 39px;
        flex-shrink: 0;
        border: 1px solid #e34d5c35;
        border-radius: 11px;
        color: #ff9ba5;
        background: #e34d5c0c;
        font-size: 14px;
        font-weight: 800;
    }

    .tk-person strong {
        display: block;
        color: #dfe6f3;
        font-size: 12px;
        line-height: 1.6;
    }

    .tk-timeline {
        display: grid;
        gap: 19px;
    }

    .tk-timeline-item {
        position: relative;
        padding-left: 19px;
    }

    .tk-timeline-item::before {
        content: "";
        position: absolute;
        left: 0;
        top: 5px;
        width: 7px;
        height: 7px;
        border: 1px solid #8295b5;
        border-radius: 50%;
        background: #26334a;
    }

    .tk-timeline-item strong {
        display: block;
        margin-bottom: 5px;
        color: #bdc9de;
        font-size: 11px;
    }

    .tk-timeline-item span {
        color: #8796ad;
        font-size: 11px;
    }

    .tk-location {
        padding: 13px;
        border: 1px solid #343d4b;
        border-radius: 10px;
        background: #1d2530;
    }

    .tk-location + .tk-location {
        margin-top: 12px;
    }

    .tk-location strong {
        display: block;
        margin-top: 6px;
        color: #dce5f6;
        font-size: 12px;
        line-height: 1.6;
    }

    .tk-response-card {
        border-color: #445579;
        background:
            radial-gradient(circle at top right, #6c92e414, transparent 65%),
            var(--tk-surface);
    }

    .tk-response-card p {
        margin: 0 0 17px;
        color: #a4b2ca;
        font-size: 12px;
        line-height: 1.8;
    }

    .tk-controls {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    @media (max-width: 1100px) {
        .tk-header {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .tk-detail-layout {
            grid-template-columns: minmax(0, 1fr);
        }

        .tk-sidebar {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .tk-filter {
            grid-template-columns: minmax(0, 1.4fr) minmax(170px, 1fr);
        }

        .tk-filter > .tk-actions {
            grid-column: 1 / -1;
            justify-content: flex-end;
        }
    }

    @media (max-width: 720px) {
        .tk-header,
        .tk-card-body {
            padding: 19px;
        }

        .tk-header-main {
            gap: 12px;
        }

        .tk-header-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
        }

        .tk-header-icon svg {
            width: 23px;
            height: 23px;
        }

        .tk-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .tk-stat {
            padding: 14px;
        }

        .tk-stat-value {
            font-size: 24px;
        }

        .tk-stat-icon {
            width: 29px;
            height: 29px;
        }

        .tk-card-head {
            padding: 17px 19px;
        }

        .tk-list-table {
            min-width: 0;
        }

        .tk-list-table thead {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip-path: inset(50%);
        }

        .tk-list-table tbody {
            display: block;
            padding: 12px;
        }

        .tk-list-table tr {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 13px;
            margin-bottom: 12px;
            padding: 16px;
            border: 1px solid #343c4b;
            border-radius: 12px;
            background: #1c222d;
        }

        .tk-list-table tr:last-child {
            margin-bottom: 0;
        }

        .tk-list-table td {
            display: block;
            padding: 0;
            border: 0;
            min-width: 0;
        }

        .tk-list-table td::before {
            content: attr(data-label);
            display: block;
            margin-bottom: 7px;
            color: #8190a9;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .6px;
            text-transform: uppercase;
        }

        .tk-list-table .tk-task-column,
        .tk-list-table .tk-action-column {
            grid-column: 1 / -1;
        }

        .tk-list-table .tk-task-column::before,
        .tk-list-table .tk-action-column::before {
            display: none;
        }

        .tk-task-cell {
            min-width: 0;
        }

        .tk-list-table .tk-action-column .tk-button {
            width: 100%;
        }

        .tk-pagination {
            padding: 16px;
        }
    }

    @media (max-width: 520px) {
        .tk-filter,
        .tk-sidebar,
        .tk-info-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .tk-filter > .tk-actions {
            grid-column: auto;
        }

        .tk-header > .tk-actions {
            width: 100%;
        }

        .tk-header > .tk-actions > .tk-button {
            flex: 1;
        }

        .tk-stat-label {
            font-size: 10px;
        }

        .tk-stat-value.text {
            font-size: 13px;
        }

        .tk-stat-icon {
            display: none;
        }

        .tk-pagination {
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="tk-page">
    <svg
        class="tk-sprite"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
    >
        <symbol id="tk-clipboard" viewBox="0 0 24 24">
            <rect x="5" y="5" width="14" height="16" rx="2"/>
            <rect x="9" y="2" width="6" height="5" rx="1"/>
            <path d="M9 12h6M9 16h6"/>
        </symbol>

        <symbol id="tk-plus" viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14"/>
        </symbol>

        <symbol id="tk-back" viewBox="0 0 24 24">
            <path d="m12 5-7 7 7 7M5 12h14"/>
        </symbol>

        <symbol id="tk-arrow" viewBox="0 0 24 24">
            <path d="M5 12h14m-6-6 6 6-6 6"/>
        </symbol>

        <symbol id="tk-search" viewBox="0 0 24 24">
            <circle cx="10.5" cy="10.5" r="6.5"/>
            <path d="m16 16 5 5"/>
        </symbol>

        <symbol id="tk-clock" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 7v5l3 2"/>
        </symbol>

        <symbol id="tk-user" viewBox="0 0 24 24">
            <circle cx="12" cy="8" r="4"/>
            <path d="M4 21v-2a8 8 0 0 1 16 0v2"/>
        </symbol>

        <symbol id="tk-package" viewBox="0 0 24 24">
            <path d="m12 3 9 5-9 5-9-5 9-5Z"/>
            <path d="M3 8v9l9 5 9-5V8M12 13v9M7.5 5.5l9 5"/>
        </symbol>

        <symbol id="tk-location" viewBox="0 0 24 24">
            <path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0Z"/>
            <circle cx="12" cy="10" r="2.5"/>
        </symbol>

        <symbol id="tk-check" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="9"/>
            <path d="m8 12 3 3 5-6"/>
        </symbol>

        <symbol id="tk-alert" viewBox="0 0 24 24">
            <path d="m12 3 10 18H2L12 3Z"/>
            <path d="M12 9v5M12 17h.01"/>
        </symbol>

        <symbol id="tk-message" viewBox="0 0 24 24">
            <path d="M21 11a8 8 0 0 1-8 8H7l-5 3V7a5 5 0 0 1 5-5h6a8 8 0 0 1 8 9Z"/>
            <path d="M7 8h9M7 12h6"/>
        </symbol>

        <symbol id="tk-list" viewBox="0 0 24 24">
            <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
        </symbol>

        <symbol id="tk-image" viewBox="0 0 24 24">
            <rect x="3" y="3" width="18" height="18" rx="3"/>
            <circle cx="8" cy="8" r="1.5"/>
            <path d="m21 16-5-5-7 7-3-3-3 3"/>
        </symbol>
    </svg>

    <header class="tk-header">
        <div class="tk-header-main">
            <div class="tk-header-icon">
                <svg aria-hidden="true">
                    <use href="#tk-clipboard"></use>
                </svg>
            </div>

            <div>
                <span class="tk-eyebrow">Organización del personal</span>

                <h1>
                    {{ $esDetalle ? $registro->codigo : 'Tareas del personal' }}
                </h1>

                <p>
                    {{ $esDetalle
                        ? ($registro->titulo ?: $registro->tipo)
                        : 'Asigna actividades y consulta el trabajo realizado por tu equipo.' }}
                </p>
            </div>
        </div>

        <div class="tk-actions">
            @if ($esDetalle)
                <a
                    href="{{ route('admin.tareas.index') }}"
                    class="tk-button"
                >
                    <svg aria-hidden="true">
                        <use href="#tk-back"></use>
                    </svg>
                    Volver
                </a>
            @endif

            <a
                href="{{ route('admin.tareas.create') }}"
                class="tk-button primary"
            >
                <svg aria-hidden="true">
                    <use href="#tk-plus"></use>
                </svg>
                Crear tarea
            </a>
        </div>
    </header>

    <div class="tk-tabs">
        @if ($tieneMenuRespuestas)
            @include('admin.tareas._menu_respuestas')
        @else
            <nav class="tk-tabs-inner" aria-label="Secciones de tareas">
                <a
                    href="{{ route('admin.tareas.index') }}"
                    class="tk-button active"
                >
                    <svg aria-hidden="true">
                        <use href="#tk-clipboard"></use>
                    </svg>
                    Tareas
                </a>

                @if ($tieneRutaRespuestas)
                    <a
                        href="{{ route('admin.tareas.respuestas.index') }}"
                        class="tk-button"
                    >
                        Respuestas
                    </a>

                    <a
                        href="{{ route('admin.tareas.respuestas.index', [
                            'vista' => 'notificaciones',
                        ]) }}"
                        class="tk-button"
                    >
                        Notificaciones
                    </a>
                @endif
            </nav>
        @endif
    </div>

    @if (session('success'))
        <div class="tk-alert success" role="status">
            <svg aria-hidden="true">
                <use href="#tk-check"></use>
            </svg>

            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="tk-alert error" role="alert">
            <svg aria-hidden="true">
                <use href="#tk-alert"></use>
            </svg>

            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="tk-alert error" role="alert">
            <svg aria-hidden="true">
                <use href="#tk-alert"></use>
            </svg>

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

    @if ($esDetalle)
        @php
            $estadoActual = $estadoVisual($registro->estado ?? '');
            $prioridadActual = $prioridadVisual($registro->prioridad ?? 'normal');

            $evidenciaSolicitada = in_array(
                $registro->requiere_evidencia ?? false,
                [true, 1, '1', 't', 'true'],
                true
            );

            $ubicacionesOrigen = $detalles
                ->map(fn ($detalle) => $detalle->origen ?? $detalle->ubicacion ?? null)
                ->filter()
                ->unique()
                ->values();

            $ubicacionesDestino = $detalles
                ->pluck('destino')
                ->filter()
                ->unique()
                ->values();
        @endphp

        <section class="tk-stats" aria-label="Resumen de la tarea">
            <article class="tk-stat">
                <div>
                    <span class="tk-stat-label">Estado actual</span>

                    <span class="tk-badge {{ $estadoActual['tono'] }}">
                        {{ $estadoActual['texto'] }}
                    </span>

                    <span class="tk-stat-note">
                        {{ $registro->tipo }}
                    </span>
                </div>

                <span class="tk-stat-icon">
                    <svg aria-hidden="true">
                        <use href="#tk-clipboard"></use>
                    </svg>
                </span>
            </article>

            <article class="tk-stat">
                <div>
                    <span class="tk-stat-label">Prioridad</span>

                    <span class="tk-badge {{ $prioridadActual['tono'] }}">
                        {{ $prioridadActual['texto'] }}
                    </span>

                    <span class="tk-stat-note">
                        Nivel de atención asignado
                    </span>
                </div>

                <span class="tk-stat-icon amber">
                    <svg aria-hidden="true">
                        <use href="#tk-alert"></use>
                    </svg>
                </span>
            </article>

            <article class="tk-stat">
                <div>
                    <span class="tk-stat-label">Responsables</span>
                    <strong class="tk-stat-value">{{ $responsables->count() }}</strong>
                    <span class="tk-stat-note">Personas asignadas</span>
                </div>

                <span class="tk-stat-icon">
                    <svg aria-hidden="true">
                        <use href="#tk-user"></use>
                    </svg>
                </span>
            </article>

            <article class="tk-stat">
                <div>
                    <span class="tk-stat-label">Evidencia</span>

                    <strong class="tk-stat-value text">
                        {{ $evidenciaSolicitada ? 'Requerida' : 'Opcional' }}
                    </strong>

                    <span class="tk-stat-note">
                        Fotografías o documentos
                    </span>
                </div>

                <span class="tk-stat-icon">
                    <svg aria-hidden="true">
                        <use href="#tk-image"></use>
                    </svg>
                </span>
            </article>
        </section>

        <div class="tk-detail-layout">
            <main class="tk-detail-main">
                <section class="tk-card">
                    <div class="tk-card-head">
                        <h2>
                            <svg aria-hidden="true">
                                <use href="#tk-list"></use>
                            </svg>
                            Información de la actividad
                        </h2>
                    </div>

                    <div class="tk-card-body">
                        <div class="tk-info-grid">
                            <div>
                                <span class="tk-data-label">Tipo de tarea</span>
                                <strong class="tk-data-value">
                                    {{ $registro->tipo }}
                                </strong>
                            </div>

                            <div>
                                <span class="tk-data-label">Código de seguimiento</span>
                                <strong class="tk-data-value">
                                    {{ $registro->codigo }}
                                </strong>
                            </div>
                        </div>

                        <div style="margin-top:22px;">
                            <span class="tk-data-label">Instrucciones para el empleado</span>

                            <div class="tk-instructions">
                                <p class="tk-description">{{ $registro->descripcion ?: 'Sin instrucciones adicionales.' }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="tk-card">
                    <div class="tk-card-head">
                        <div>
                            <h2>
                                <svg aria-hidden="true">
                                    <use href="#tk-package"></use>
                                </svg>
                                Productos y lotes
                            </h2>

                            <p>Consulta las cantidades solicitadas e informadas.</p>
                        </div>

                        <span class="tk-chip">
                            {{ $detalles->count() }}
                            {{ $detalles->count() === 1 ? 'detalle' : 'detalles' }}
                        </span>
                    </div>

                    @if ($detalles->isNotEmpty())
                        <div class="tk-table-wrap" tabindex="0" aria-label="Detalle de productos y lotes">
                            <table class="tk-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Producto</th>
                                        <th scope="col">Lote</th>
                                        <th scope="col">Ubicaciones</th>
                                        <th scope="col">Referencia</th>
                                        <th scope="col">Solicitado</th>
                                        <th scope="col">Informado</th>
                                        <th scope="col">Resultado</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($detalles as $detalle)
                                        @php
                                            $resultadoDetalle = $resultadoVisual(
                                                $detalle->resultado ?? 'pendiente'
                                            );

                                            $referencia = $detalle->cantidad_sistema ?? null;
                                            $solicitada = $detalle->cantidad_solicitada ?? null;
                                            $realizada = $detalle->cantidad_realizada ?? null;
                                            $diferencia = $detalle->diferencia ?? null;

                                            $origenDetalle = $detalle->origen
                                                ?? $detalle->ubicacion
                                                ?? null;
                                        @endphp

                                        <tr>
                                            <td>
                                                <strong>
                                                    {{ $detalle->producto ?: 'Revisión de ubicación' }}
                                                </strong>

                                                @if (!empty($detalle->codigo_interno))
                                                    <span class="tk-subtext">
                                                        {{ $detalle->codigo_interno }}
                                                    </span>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="tk-chip">
                                                    {{ $detalle->codigo_lote ?: 'No corresponde' }}
                                                </span>
                                            </td>

                                            <td>
                                                <div class="tk-route-line">
                                                    <span>{{ $origenDetalle ?: '—' }}</span>

                                                    @if (!empty($detalle->destino))
                                                        <svg aria-hidden="true">
                                                            <use href="#tk-arrow"></use>
                                                        </svg>

                                                        <span>{{ $detalle->destino }}</span>
                                                    @endif
                                                </div>
                                            </td>

                                            <td>
                                                <span class="tk-number">
                                                    {{ $cantidad($referencia) }}
                                                </span>

                                                @if ($referencia !== null)
                                                    <span class="tk-unit">{{ $detalle->unidad }}</span>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="tk-number">
                                                    {{ $cantidad($solicitada) }}
                                                </span>

                                                @if ($solicitada !== null)
                                                    <span class="tk-unit">{{ $detalle->unidad }}</span>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="tk-number">
                                                    {{ $cantidad($realizada) }}
                                                </span>

                                                @if ($realizada !== null)
                                                    <span class="tk-unit">{{ $detalle->unidad }}</span>
                                                @endif

                                                @if ($diferencia !== null)
                                                    <span class="tk-subtext {{ (float) $diferencia !== 0.0 ? 'difference' : '' }}">
                                                        Diferencia:
                                                        {{ (float) $diferencia > 0 ? '+' : '' }}{{ $cantidad($diferencia) }}
                                                        {{ $detalle->unidad }}
                                                    </span>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="tk-badge {{ $resultadoDetalle['tono'] }}">
                                                    {{ $resultadoDetalle['texto'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tk-card-body" style="padding-top:14px;padding-bottom:14px;">
                            <p class="tk-muted">
                                La referencia corresponde a la cantidad guardada en la tarea.
                                Las diferencias deben revisarse considerando los movimientos
                                posteriores.
                            </p>
                        </div>
                    @else
                        <div class="tk-empty">
                            <div class="tk-empty-icon">
                                <svg aria-hidden="true">
                                    <use href="#tk-package"></use>
                                </svg>
                            </div>

                            <h3>Sin detalles registrados</h3>
                            <p>Esta tarea no tiene productos o ubicaciones en su detalle.</p>
                        </div>
                    @endif
                </section>

                @if (
                    !empty($extras['motivo'])
                    || !empty($extras['controles'])
                    || !empty($extras['destino_tipo'])
                )
                    <section class="tk-card">
                        <div class="tk-card-head">
                            <h2>
                                <svg aria-hidden="true">
                                    <use href="#tk-list"></use>
                                </svg>
                                Indicaciones específicas
                            </h2>
                        </div>

                        <div class="tk-card-body">
                            @if (!empty($extras['destino_tipo']))
                                <div style="margin-bottom:20px;">
                                    <span class="tk-data-label">Destino de la operación</span>

                                    <strong class="tk-data-value">
                                        {{ $etiquetasDestino[$extras['destino_tipo']]
                                            ?? ucfirst(str_replace('_', ' ', $extras['destino_tipo'])) }}
                                    </strong>
                                </div>
                            @endif

                            @if (!empty($extras['motivo']))
                                <div style="margin-bottom:20px;">
                                    <span class="tk-data-label">Motivo</span>
                                    <p class="tk-description">{{ $extras['motivo'] }}</p>
                                </div>
                            @endif

                            @if (!empty($extras['controles']))
                                <span class="tk-data-label">Aspectos que se revisarán</span>

                                <div class="tk-controls">
                                    @foreach (collect($extras['controles'])->filter(fn ($control) => is_string($control)) as $control)
                                        <span class="tk-chip">
                                            <svg aria-hidden="true">
                                                <use href="#tk-check"></use>
                                            </svg>

                                            {{ $etiquetasControles[$control] ?? ucfirst($control) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </section>
                @endif
            </main>

            <aside class="tk-sidebar">
                @if ($tieneRutaRespuestas)
                    <section class="tk-card tk-response-card">
                        <div class="tk-card-head">
                            <h2>
                                <svg aria-hidden="true">
                                    <use href="#tk-message"></use>
                                </svg>
                                Respuestas del empleado
                            </h2>
                        </div>

                        <div class="tk-card-body">
                            <p>
                                Consulta lo realizado, las cantidades informadas,
                                las observaciones y las evidencias enviadas.
                            </p>

                            <a
                                href="{{ route('admin.tareas.respuestas.index', [
                                    'tarea_id' => $registro->id,
                                ]) }}"
                                class="tk-button block"
                            >
                                Ver respuestas
                                <svg aria-hidden="true">
                                    <use href="#tk-arrow"></use>
                                </svg>
                            </a>
                        </div>
                    </section>
                @endif

                <section class="tk-card">
                    <div class="tk-card-head">
                        <h2>
                            <svg aria-hidden="true">
                                <use href="#tk-user"></use>
                            </svg>
                            Responsables
                        </h2>
                    </div>

                    <div class="tk-card-body" style="padding-top:10px;padding-bottom:10px;">
                        @forelse ($responsables as $nombreResponsable)
                            <div class="tk-person">
                                <span class="tk-avatar" aria-hidden="true">
                                    {{ mb_strtoupper(mb_substr($nombreResponsable, 0, 1, 'UTF-8'), 'UTF-8') }}
                                </span>

                                <div>
                                    <strong>{{ $nombreResponsable }}</strong>
                                    <span class="tk-muted">Empleado asignado</span>
                                </div>
                            </div>
                        @empty
                            <p class="tk-muted" style="padding:10px 0;">
                                La tarea todavía no tiene un responsable.
                            </p>
                        @endforelse
                    </div>
                </section>

                <section class="tk-card">
                    <div class="tk-card-head">
                        <h2>
                            <svg aria-hidden="true">
                                <use href="#tk-location"></use>
                            </svg>
                            Almacenes y ubicaciones
                        </h2>
                    </div>

                    <div class="tk-card-body">
                        <div class="tk-location">
                            <span class="tk-data-label">Lugar de trabajo / origen</span>

                            <strong>{{ $registro->almacen_origen ?: 'No especificado' }}</strong>

                            @if ($ubicacionesOrigen->isNotEmpty())
                                <p class="tk-muted">
                                    {{ $ubicacionesOrigen->implode(' · ') }}
                                </p>
                            @endif
                        </div>

                        @if (!empty($registro->almacen_destino))
                            <div class="tk-location">
                                <span class="tk-data-label">Destino</span>

                                <strong>{{ $registro->almacen_destino }}</strong>

                                @if ($ubicacionesDestino->isNotEmpty())
                                    <p class="tk-muted">
                                        {{ $ubicacionesDestino->implode(' · ') }}
                                    </p>
                                @endif
                            </div>
                        @endif

                        @if (!empty($registro->proveedor))
                            <div class="tk-location">
                                <span class="tk-data-label">Proveedor de destino</span>
                                <strong>{{ $registro->proveedor }}</strong>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="tk-card">
                    <div class="tk-card-head">
                        <h2>
                            <svg aria-hidden="true">
                                <use href="#tk-clock"></use>
                            </svg>
                            Fechas de la tarea
                        </h2>
                    </div>

                    <div class="tk-card-body">
                        <div class="tk-timeline">
                            @foreach ([
                                'Creación' => $registro->fecha_creacion ?? null,
                                'Inicio' => $registro->fecha_inicio ?? null,
                                'Fecha límite' => $registro->fecha_limite ?? null,
                                'Finalización' => $registro->fecha_finalizacion ?? null,
                            ] as $etiquetaFecha => $valorFecha)
                                @php
                                    $fechaObjeto = $leerFecha($valorFecha);
                                @endphp

                                <div class="tk-timeline-item">
                                    <strong>{{ $etiquetaFecha }}</strong>

                                    <span>
                                        {{ $fechaObjeto
                                            ? $fechaObjeto->format('d/m/Y · H:i')
                                            : ($etiquetaFecha === 'Fecha límite'
                                                ? 'Sin fecha límite'
                                                : 'Sin registrar') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        @if ($estaFueraDePlazo($registro))
                            <p class="tk-date-sub overdue" style="margin-top:20px;">
                                El plazo de esta tarea ya venció.
                            </p>
                        @endif
                    </div>
                </section>
            </aside>
        </div>
    @else
        <section class="tk-stats" aria-label="Resumen del listado">
            <article class="tk-stat">
                <div>
                    <span class="tk-stat-label">Resultados</span>
                    <strong class="tk-stat-value">{{ number_format($totalResultados, 0, ',', '.') }}</strong>
                    <span class="tk-stat-note">
                        {{ $hayFiltros ? 'Según los filtros aplicados' : 'Tareas registradas' }}
                    </span>
                </div>

                <span class="tk-stat-icon">
                    <svg aria-hidden="true">
                        <use href="#tk-clipboard"></use>
                    </svg>
                </span>
            </article>

            <article class="tk-stat">
                <div>
                    <span class="tk-stat-label">Mostradas</span>
                    <strong class="tk-stat-value">{{ $visibles->count() }}</strong>
                    <span class="tk-stat-note">En esta página</span>
                </div>

                <span class="tk-stat-icon">
                    <svg aria-hidden="true">
                        <use href="#tk-list"></use>
                    </svg>
                </span>
            </article>

            <article class="tk-stat">
                <div>
                    <span class="tk-stat-label">Prioridad urgente</span>
                    <strong class="tk-stat-value">{{ $urgentesVisibles }}</strong>
                    <span class="tk-stat-note">En esta página</span>
                </div>

                <span class="tk-stat-icon red">
                    <svg aria-hidden="true">
                        <use href="#tk-alert"></use>
                    </svg>
                </span>
            </article>

            <article class="tk-stat">
                <div>
                    <span class="tk-stat-label">Plazo vencido</span>
                    <strong class="tk-stat-value">{{ $vencidasVisibles }}</strong>
                    <span class="tk-stat-note">Tareas abiertas en esta página</span>
                </div>

                <span class="tk-stat-icon amber">
                    <svg aria-hidden="true">
                        <use href="#tk-clock"></use>
                    </svg>
                </span>
            </article>
        </section>

        <section class="tk-card">
            <div class="tk-card-head">
                <div>
                    <h2>
                        <svg aria-hidden="true">
                            <use href="#tk-search"></use>
                        </svg>
                        Buscar y filtrar
                    </h2>

                    <p>Localiza una tarea por su código, título o estado.</p>
                </div>
            </div>

            <div class="tk-card-body">
                <form
                    method="GET"
                    action="{{ route('admin.tareas.index') }}"
                    class="tk-filter"
                >
                    <label class="tk-field" for="tkBuscar">
                        Código o título

                        <span class="tk-search">
                            <svg aria-hidden="true">
                                <use href="#tk-search"></use>
                            </svg>

                            <input
                                id="tkBuscar"
                                type="search"
                                name="buscar"
                                value="{{ $buscar }}"
                                maxlength="100"
                                placeholder="Buscar una tarea..."
                            >
                        </span>
                    </label>

                    <label class="tk-field" for="tkEstado">
                        Estado de la tarea

                        <select id="tkEstado" name="estado_id">
                            <option value="">Todos los estados</option>

                            @foreach ($estados as $estado)
                                <option
                                    value="{{ $estado->id }}"
                                    @selected((string) $estadoId === (string) $estado->id)
                                >
                                    {{ $estado->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <div class="tk-actions">
                        <a
                            href="{{ route('admin.tareas.index') }}"
                            class="tk-button"
                        >
                            Limpiar
                        </a>

                        <button type="submit" class="tk-button primary">
                            <svg aria-hidden="true">
                                <use href="#tk-search"></use>
                            </svg>
                            Buscar
                        </button>
                    </div>
                </form>

                @if ($hayFiltros)
                    <div class="tk-filter-summary">
                        <span>Filtros activos:</span>

                        @if ($buscar !== '')
                            <span class="tk-chip">Búsqueda: {{ $buscar }}</span>
                        @endif

                        @if ($estadoId !== null && $estadoId !== '')
                            <span class="tk-chip">
                                Estado:
                                {{ $estados->firstWhere('id', $estadoId)?->nombre ?? 'Seleccionado' }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </section>

        <section class="tk-card">
            <div class="tk-card-head">
                <div>
                    <h2>
                        <svg aria-hidden="true">
                            <use href="#tk-clipboard"></use>
                        </svg>
                        Listado de tareas
                    </h2>

                    <p>Abre una tarea para consultar sus instrucciones y respuestas.</p>
                </div>

                <span class="tk-chip">{{ $totalResultados }} resultados</span>
            </div>

            @if ($visibles->isNotEmpty())
                <div class="tk-table-wrap">
                    <table class="tk-table tk-list-table">
                        <thead>
                            <tr>
                                <th scope="col">Tarea</th>
                                <th scope="col">Tipo</th>
                                <th scope="col">Prioridad</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Fecha límite</th>
                                <th scope="col">Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($visibles as $tarea)
                                @php
                                    $estadoFila = $estadoVisual($tarea->estado ?? '');
                                    $prioridadFila = $prioridadVisual($tarea->prioridad ?? 'normal');
                                    $fechaLimite = $leerFecha($tarea->fecha_limite ?? null);
                                    $fueraDePlazo = $estaFueraDePlazo($tarea);
                                @endphp

                                <tr>
                                    <td class="tk-task-column" data-label="Tarea">
                                        <div class="tk-task-cell">
                                            <span class="tk-task-symbol">
                                                <svg aria-hidden="true">
                                                    <use href="#tk-clipboard"></use>
                                                </svg>
                                            </span>

                                            <div>
                                                <a
                                                    href="{{ route('admin.tareas.show', $tarea->id) }}"
                                                    class="tk-code"
                                                >
                                                    {{ $tarea->codigo }}
                                                </a>

                                                <span class="tk-task-title">
                                                    {{ $tarea->titulo ?: 'Tarea sin título' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td data-label="Tipo">
                                        {{ $tarea->tipo }}
                                    </td>

                                    <td data-label="Prioridad">
                                        <span class="tk-badge {{ $prioridadFila['tono'] }}">
                                            {{ $prioridadFila['texto'] }}
                                        </span>
                                    </td>

                                    <td data-label="Estado">
                                        <span class="tk-badge {{ $estadoFila['tono'] }}">
                                            {{ $estadoFila['texto'] }}
                                        </span>
                                    </td>

                                    <td data-label="Fecha límite">
                                        @if ($fechaLimite)
                                            <span class="tk-date">
                                                {{ $fechaLimite->format('d/m/Y') }}
                                            </span>

                                            <span class="tk-date-sub {{ $fueraDePlazo ? 'overdue' : '' }}">
                                                {{ $fechaLimite->format('H:i') }}
                                                {{ $fueraDePlazo ? ' · Plazo vencido' : '' }}
                                            </span>
                                        @else
                                            <span class="tk-muted">Sin fecha límite</span>
                                        @endif
                                    </td>

                                    <td class="tk-action-column" data-label="Acción">
                                        <a
                                            href="{{ route('admin.tareas.show', $tarea->id) }}"
                                            class="tk-button small"
                                            aria-label="Ver detalle de {{ $tarea->codigo }}"
                                        >
                                            Ver detalle

                                            <svg aria-hidden="true">
                                                <use href="#tk-arrow"></use>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if (
                    is_object($tareas ?? null)
                    && method_exists($tareas, 'lastPage')
                )
                    <div class="tk-pagination">
                        <span>
                            Mostrando
                            {{ $tareas->firstItem() ?? 0 }}–{{ $tareas->lastItem() ?? 0 }}
                            de {{ $totalResultados }}
                        </span>

                        @if ($tareas->lastPage() > 1)
                            @php
                                $paginaActual = $tareas->currentPage();
                                $ultimaPagina = $tareas->lastPage();
                                $inicioPaginas = max(1, $paginaActual - 2);
                                $finPaginas = min($ultimaPagina, $paginaActual + 2);
                            @endphp

                            <nav class="tk-pagination-links" aria-label="Paginación de tareas">
                                @if ($tareas->onFirstPage())
                                    <span class="tk-page-link disabled" aria-disabled="true">
                                        ‹
                                    </span>
                                @else
                                    <a
                                        href="{{ $tareas->previousPageUrl() }}"
                                        class="tk-page-link"
                                        aria-label="Página anterior"
                                    >
                                        ‹
                                    </a>
                                @endif

                                @if ($inicioPaginas > 1)
                                    <a href="{{ $tareas->url(1) }}" class="tk-page-link">
                                        1
                                    </a>

                                    @if ($inicioPaginas > 2)
                                        <span aria-hidden="true">…</span>
                                    @endif
                                @endif

                                @for ($pagina = $inicioPaginas; $pagina <= $finPaginas; $pagina++)
                                    @if ($pagina === $paginaActual)
                                        <span class="tk-page-link current" aria-current="page">
                                            {{ $pagina }}
                                        </span>
                                    @else
                                        <a
                                            href="{{ $tareas->url($pagina) }}"
                                            class="tk-page-link"
                                            aria-label="Ir a la página {{ $pagina }}"
                                        >
                                            {{ $pagina }}
                                        </a>
                                    @endif
                                @endfor

                                @if ($finPaginas < $ultimaPagina)
                                    @if ($finPaginas < $ultimaPagina - 1)
                                        <span aria-hidden="true">…</span>
                                    @endif

                                    <a
                                        href="{{ $tareas->url($ultimaPagina) }}"
                                        class="tk-page-link"
                                    >
                                        {{ $ultimaPagina }}
                                    </a>
                                @endif

                                @if ($tareas->hasMorePages())
                                    <a
                                        href="{{ $tareas->nextPageUrl() }}"
                                        class="tk-page-link"
                                        aria-label="Página siguiente"
                                    >
                                        ›
                                    </a>
                                @else
                                    <span class="tk-page-link disabled" aria-disabled="true">
                                        ›
                                    </span>
                                @endif
                            </nav>
                        @endif
                    </div>
                @endif
            @else
                <div class="tk-empty">
                    <div class="tk-empty-icon">
                        <svg aria-hidden="true">
                            <use href="#tk-clipboard"></use>
                        </svg>
                    </div>

                    <h3>
                        {{ $hayFiltros
                            ? 'No encontramos tareas con esos filtros'
                            : 'Todavía no hay tareas registradas' }}
                    </h3>

                    <p>
                        {{ $hayFiltros
                            ? 'Prueba con otro código, título o estado para encontrar la tarea.'
                            : 'Crea la primera tarea y asigna una actividad a un integrante del equipo.' }}
                    </p>

                    @if ($hayFiltros)
                        <a
                            href="{{ route('admin.tareas.index') }}"
                            class="tk-button"
                        >
                            Limpiar filtros
                        </a>
                    @else
                        <a
                            href="{{ route('admin.tareas.create') }}"
                            class="tk-button primary"
                        >
                            <svg aria-hidden="true">
                                <use href="#tk-plus"></use>
                            </svg>

                            Crear primera tarea
                        </a>
                    @endif
                </div>
            @endif
        </section>
    @endif
</div>
@endsection