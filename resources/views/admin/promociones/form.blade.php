@extends('admin.layouts.app')

@section('title', isset($promocion) ? 'Editar promoción' : 'Crear promoción')

@php
    $promocion = $promocion ?? null;
    $editando = $promocion !== null;

    $productosSeleccionados = $productosSeleccionados ?? [];

    $tipoInicial = old('tipo', $promocion->tipo ?? 'combo');
    $modoInicial = old('modo_calculo', $promocion->modo_calculo ?? 'porcentaje');

    $idsIniciales = old('productos', array_keys($productosSeleccionados));
    $cantidadesIniciales = old('cantidades', $productosSeleccionados);

    $inicio = $promocion?->fecha_inicio
        ? \Illuminate\Support\Carbon::parse($promocion->fecha_inicio)->format('Y-m-d\TH:i')
        : now()->format('Y-m-d\TH:i');

    $fin = $promocion?->fecha_fin
        ? \Illuminate\Support\Carbon::parse($promocion->fecha_fin)->format('Y-m-d\TH:i')
        : '';

    $usadas = (int) ($promocion->aplicaciones_usadas ?? 0);

    $limiteInicial = old(
        'limite_tipo',
        isset($promocion->limite_aplicaciones) ? 'cantidad' : 'stock'
    );

    $codigoInicial = old('codigo', $promocion->codigo ?? '');
@endphp

@push('styles')
<style>
    .promo-page {
        --bg: #101116;
        --card: #181a20;
        --soft: #21242c;
        --border: #2e323d;
        --border-strong: #434857;
        --muted: #a8adba;
        --text: #f5f6f9;
        --brand: #e31b23;
        --brand-strong: #ff4d55;
        --brand-soft: rgba(227, 27, 35, .14);
        --ok: #4ade80;
        --ok-soft: rgba(74, 222, 128, .12);
        --radius: 14px;
        --radius-sm: 10px;
        --shadow: 0 1px 0 rgba(255,255,255,.03), 0 8px 24px -12px rgba(0,0,0,.6);

        display: grid;
        gap: 20px;
        color: var(--text);
    }

    .promo-page * { box-sizing: border-box; }

    /* ---------- Header ---------- */
    .promo-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        padding: 26px 28px;
        border: 1px solid var(--border);
        border-radius: 18px;
        background:
            radial-gradient(circle at 100% 0, rgba(227, 27, 35, .18), transparent 60%),
            var(--card);
        box-shadow: var(--shadow);
    }

    .promo-eyebrow {
        color: #ff858c;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.3px;
        text-transform: uppercase;
    }

    .promo-header h1 { margin: 8px 0 6px; font-size: 28px; }

    .promo-help {
        margin: 0;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.7;
    }

    /* ---------- Stepper ---------- */
    .promo-stepper {
        position: sticky;
        top: 0;
        z-index: 20;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        padding: 12px;
        margin: 0 -8px;
        background: color-mix(in srgb, var(--bg) 85%, transparent);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-bottom: 1px solid var(--border);
    }

    .promo-stepper a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border: 1px solid var(--border);
        border-radius: 999px;
        color: var(--muted);
        background: var(--card);
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all .2s ease;
    }

    .promo-stepper a:hover {
        color: #fff;
        border-color: var(--border-strong);
    }

    .promo-stepper a.is-active {
        color: #fff;
        border-color: var(--brand);
        background: var(--brand-soft);
    }

    .promo-stepper a.is-active b {
        background: var(--brand);
        color: #fff;
    }

    .promo-stepper a.is-done b {
        background: var(--ok);
        color: #0b1f12;
    }

    .promo-stepper b {
        display: grid;
        place-items: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        color: var(--brand-strong);
        background: var(--brand-soft);
        font-size: 10px;
        transition: all .2s ease;
    }

    /* ---------- Layout ---------- */
    .promo-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        align-items: start;
        gap: 20px;
    }

    .promo-main { display: grid; gap: 18px; min-width: 0; }

    .promo-card {
        min-width: 0;
        padding: 24px;
        border: 1px solid var(--border);
        border-radius: 16px;
        background: var(--card);
        scroll-margin-top: 80px;
        box-shadow: var(--shadow);
    }

    .promo-section-header {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 22px;
        padding-bottom: 18px;
        border-bottom: 1px solid var(--border);
    }

    .promo-step {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        width: 34px;
        height: 34px;
        border: 1px solid rgba(227, 27, 35, .4);
        border-radius: var(--radius-sm);
        color: #ff9299;
        background: var(--brand-soft);
        font-size: 13px;
        font-weight: 800;
    }

    .promo-card h2 { margin: 0 0 4px; font-size: 17px; }

    /* ---------- Campos ---------- */
    .promo-fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .promo-wide { grid-column: 1 / -1; }

    .promo-field label {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
    }

    .promo-field .promo-hint {
        display: block;
        margin-top: 6px;
        color: var(--muted);
        font-size: 11px;
    }

    .promo-page input:not(.promo-sr),
    .promo-page select,
    .promo-page textarea {
        width: 100%;
        min-height: 44px;
        padding: 11px 13px;
        border: 1px solid var(--border-strong);
        border-radius: var(--radius-sm);
        color: var(--text);
        background: var(--soft);
        font-family: inherit;
        font-size: 13px;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .promo-page textarea { resize: vertical; }

    .promo-page input:not(.promo-sr):focus,
    .promo-page select:focus,
    .promo-page textarea:focus {
        outline: none;
        border-color: var(--brand);
        box-shadow: 0 0 0 3px var(--brand-soft);
    }

    .promo-page input[readonly] { cursor: default; }

    .promo-page input.is-invalid,
    .promo-page select.is-invalid,
    .promo-page textarea.is-invalid {
        border-color: #ff6b73 !important;
        box-shadow: 0 0 0 3px rgba(255, 107, 115, .12);
    }

    .promo-field .promo-field-error {
        display: block;
        margin-top: 6px;
        color: #ff8a90;
        font-size: 11px;
        font-weight: 600;
    }

    .promo-sr {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        pointer-events: none;
    }

    /* ---------- Tarjetas de tipo ---------- */
    .promo-types {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .promo-type {
        position: relative;
        display: grid;
        gap: 8px;
        padding: 16px;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background: var(--soft);
        cursor: pointer;
        transition: all .2s ease;
    }

    .promo-type:hover {
        border-color: var(--border-strong);
        transform: translateY(-1px);
    }

    .promo-type-icon {
        display: grid;
        place-items: center;
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        background: #2d303a;
        color: #c5c9d3;
        transition: all .2s ease;
    }

    .promo-type strong { font-size: 13px; }
    .promo-type small { color: var(--muted); font-size: 11px; line-height: 1.5; }

    .promo-sr:checked + .promo-type {
        border-color: var(--brand);
        background: linear-gradient(180deg, var(--brand-soft), var(--soft));
        box-shadow: 0 0 0 1px var(--brand);
    }

    .promo-sr:checked + .promo-type .promo-type-icon {
        background: var(--brand);
        color: #fff;
        box-shadow: 0 0 0 3px var(--brand-soft);
    }

    .promo-sr:focus-visible + .promo-type {
        outline: 2px solid #ff777d;
        outline-offset: 2px;
    }

    .promo-type-description {
        margin-top: 16px;
        padding: 13px 15px;
        border-left: 3px solid var(--brand);
        border-radius: 8px;
        color: #c9cdd7;
        background: #20222a;
        font-size: 12px;
        line-height: 1.7;
    }

    /* ---------- Código automático ---------- */
    .promo-code {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 8px;
    }

    .promo-code input {
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace !important;
        font-size: 14px !important;
        font-weight: 800;
        letter-spacing: 1.5px;
        color: #ffb7bc !important;
        background: rgba(227, 27, 35, .07) !important;
        border-style: dashed !important;
        border-color: rgba(227, 27, 35, .45) !important;
    }

    .promo-icon-btn {
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        border: 1px solid var(--border-strong);
        border-radius: var(--radius-sm);
        color: #fff;
        background: #272930;
        font-size: 15px;
        cursor: pointer;
        transition: background .15s ease;
    }

    .promo-icon-btn:hover { background: #343740; }
    .promo-icon-btn:disabled { opacity: .4; cursor: not-allowed; }

    /* ---------- Botones ---------- */
    .promo-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 44px;
        padding: 10px 16px;
        border: 1px solid var(--border-strong);
        border-radius: var(--radius-sm);
        color: #fff;
        background: #272930;
        text-decoration: none;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s ease;
    }

    .promo-button:hover { background: #343740; }

    .promo-button.primary {
        position: relative;
        overflow: hidden;
        border-color: #d31b25;
        background: linear-gradient(180deg, #ea2a32, #c9161f);
    }

    .promo-button.primary:hover:not(:disabled) {
        filter: brightness(1.1);
        transform: translateY(-1px);
    }

    .promo-button.primary:active:not(:disabled) {
        transform: translateY(0);
    }

    .promo-button:disabled { opacity: .45; cursor: not-allowed; filter: none; transform: none; }

    .promo-button.is-loading {
        pointer-events: none;
        opacity: .7;
    }

    .promo-button.is-loading::after {
        content: '';
        width: 14px;
        height: 14px;
        margin-left: 8px;
        border: 2px solid rgba(255,255,255,.35);
        border-top-color: #fff;
        border-radius: 50%;
        animation: promo-spin .7s linear infinite;
    }

    @keyframes promo-spin { to { transform: rotate(360deg); } }

    /* ---------- Productos ---------- */
    .promo-picker {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 10px;
        margin-top: 12px;
    }

    .promo-table-wrap {
        margin-top: 18px;
        overflow-x: auto;
        border: 1px solid var(--border);
        border-radius: var(--radius);
    }

    .promo-table {
        width: 100%;
        min-width: 620px;
        border-collapse: collapse;
    }

    .promo-table th,
    .promo-table td {
        padding: 14px 12px;
        border-bottom: 1px solid var(--border);
        text-align: left;
        font-size: 12px;
    }

    .promo-table tbody tr {
        transition: background .15s ease;
    }

    .promo-table tbody tr:hover {
        background: rgba(255,255,255,.02);
    }

    .promo-table tbody tr:last-child td { border-bottom: 0; }

    .promo-table th {
        color: var(--muted);
        background: #1e2026;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .promo-table small { display: block; margin-top: 5px; color: var(--muted); }
    .promo-table input[type="number"] { width: 112px; }

    .promo-remove {
        padding: 7px 11px;
        border: 1px solid rgba(255, 150, 157, .3);
        border-radius: 8px;
        color: #ff969d;
        background: transparent;
        font-family: inherit;
        font-size: 11px;
        cursor: pointer;
        opacity: .7;
        transition: all .15s ease;
    }

    .promo-table tbody tr:hover .promo-remove { opacity: 1; }
    .promo-remove:hover { background: rgba(227, 27, 35, .12); }

    .promo-empty {
        padding: 44px 20px !important;
        color: var(--muted);
        text-align: center !important;
        font-size: 13px;
    }

    .promo-empty::before {
        content: '🛒';
        display: block;
        font-size: 28px;
        margin-bottom: 10px;
        opacity: .6;
    }

    .promo-products-footer {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 16px;
        font-size: 12px;
    }

    .promo-products-footer span { color: var(--muted); }

    .promo-note {
        margin-top: 20px;
        padding: 13px 15px;
        border-radius: var(--radius-sm);
        color: #c5c9d3;
        background: #22242c;
        font-size: 12px;
        line-height: 1.7;
    }

    .promo-note.error { color: #ffadb3; background: rgba(227, 27, 35, .1); }
    .promo-note.ok { color: var(--ok); background: var(--ok-soft); }

    /* ---------- Resumen ---------- */
    .promo-summary {
        position: sticky;
        top: 80px;
        padding: 0 !important;
        overflow: hidden;
        box-shadow: 0 1px 0 rgba(255,255,255,.03), 0 24px 48px -24px rgba(0,0,0,.8);
    }

    .promo-summary-head {
        padding: 22px 24px 18px;
        border-bottom: 1px dashed var(--border);
        background: radial-gradient(circle at 100% 0, rgba(227, 27, 35, .14), transparent 70%);
    }

    .promo-summary-head h2 { margin: 10px 0 0; }

    .promo-badge {
        display: inline-flex;
        margin-top: 12px;
        padding: 6px 11px;
        border: 1px solid var(--border-strong);
        border-radius: 999px;
        color: #d5d8e1;
        background: #24262e;
        font-size: 11px;
    }

    .promo-summary-body { padding: 8px 24px 24px; }

    .promo-summary-code {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-top: 18px;
        padding: 11px 13px;
        border: 1px dashed rgba(227, 27, 35, .45);
        border-radius: var(--radius-sm);
        background: rgba(227, 27, 35, .06);
        font-size: 11px;
        color: var(--muted);
    }

    .promo-summary-code strong {
        color: #ffb7bc;
        font-family: ui-monospace, Menlo, Consolas, monospace;
        font-size: 13px;
        letter-spacing: 1px;
    }

    .promo-summary-row {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        margin-top: 16px;
        font-size: 13px;
    }

    .promo-summary-row span { color: var(--muted); }
    .promo-summary-row strong { text-align: right; }
    .promo-saving { color: var(--ok); }

    .promo-total {
        position: relative;
        overflow: hidden;
        margin: 20px 0;
        padding: 22px 20px;
        border: 1px solid rgba(227, 27, 35, .32);
        border-radius: var(--radius);
        background: linear-gradient(180deg, rgba(227, 27, 35, .10), rgba(227, 27, 35, .04));
    }

    .promo-total::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 100% 0, rgba(227,27,35,.18), transparent 60%);
        pointer-events: none;
    }

    .promo-total span {
        position: relative;
        color: #ffb7bc;
        font-size: 12px;
    }

    .promo-total strong {
        position: relative;
        display: block;
        margin-top: 6px;
        font-size: 34px;
        font-weight: 800;
        letter-spacing: -.5px;
        line-height: 1.1;
        overflow-wrap: anywhere;
    }

    .promo-total small { position: relative; color: var(--ok); font-size: 12px; font-weight: 700; }

    .promo-actions { display: grid; gap: 10px; margin-top: 20px; }

    .promo-errors {
        padding: 16px 18px;
        border: 1px solid rgba(255, 119, 125, .4);
        border-radius: var(--radius-sm);
        color: #ffb5b9;
        background: rgba(227, 27, 35, .07);
        font-size: 13px;
    }

    .promo-errors ul { margin: 8px 0 0; padding-left: 20px; line-height: 1.8; }

    .promo-page [hidden] { display: none !important; }

    @media (max-width: 1100px) {
        .promo-layout { grid-template-columns: 1fr; }
        .promo-summary { position: static; }
        .promo-types { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 900px) {
        .promo-stepper {
            overflow-x: auto;
            flex-wrap: nowrap;
            scrollbar-width: none;
        }
        .promo-stepper::-webkit-scrollbar { display: none; }
        .promo-stepper a { white-space: nowrap; flex-shrink: 0; }
    }

    @media (max-width: 600px) {
        .promo-header { flex-direction: column; align-items: stretch; padding: 20px; }
        .promo-header h1 { font-size: 23px; }
        .promo-fields, .promo-picker, .promo-types { grid-template-columns: 1fr; }
        .promo-wide { grid-column: auto; }
        .promo-card { padding: 18px; }
        .promo-summary-body { padding: 8px 18px 20px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .promo-page *,
        .promo-page *::before,
        .promo-page *::after {
            animation-duration: .01ms !important;
            transition-duration: .01ms !important;
        }
    }
</style>
@endpush

@section('content')
<div class="promo-page">

    <header class="promo-header">
        <div>
            <span class="promo-eyebrow">Minisúper NANOS · Promociones</span>
            <h1>{{ $editando ? 'Editar promoción' : 'Crear promoción' }}</h1>
            <p class="promo-help">Combina productos o configura descuentos individuales.</p>
        </div>

        <a href="{{ route('admin.promociones.index') }}" class="promo-button">
            ← Volver a promociones
        </a>
    </header>

    <nav class="promo-stepper" aria-label="Progreso del formulario">
        <a href="#sec-tipo" data-step="1"><b>1</b> Tipo</a>
        <a href="#sec-info" data-step="2"><b>2</b> Información</a>
        <a href="#sec-productos" data-step="3"><b>3</b> Productos</a>
        <a href="#sec-precio" data-step="4"><b>4</b> Precio</a>
        <a href="#sec-vigencia" data-step="5"><b>5</b> Vigencia</a>
    </nav>

    @if ($errors->any())
        <div class="promo-errors" role="alert">
            <strong>Corrige estos datos:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        id="promoForm"
        action="{{ $editando
            ? route('admin.promociones.update', $promocion->id)
            : route('admin.promociones.store') }}"
        method="POST"
    >
        @csrf
        @if ($editando) @method('PUT') @endif

        <div class="promo-layout">
            <div class="promo-main">

                {{-- 1. TIPO --}}
                <section class="promo-card" id="sec-tipo">
                    <div class="promo-section-header">
                        <span class="promo-step">1</span>
                        <div>
                            <h2>Tipo de promoción</h2>
                            <p class="promo-help">Selecciona cómo quieres ofrecer el beneficio.</p>
                        </div>
                    </div>

                    <div class="promo-types" role="radiogroup" aria-label="Tipo de promoción">
                        @foreach ([
                            'combo' => [
                                '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="8" height="8" rx="1.5"/><rect x="13" y="9" width="8" height="8" rx="1.5"/></svg>',
                                'Combo',
                                'Combina varios productos con un precio conjunto.'
                            ],
                            'descuento_porcentaje' => [
                                '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="7" cy="7" r="2.5"/><circle cx="17" cy="17" r="2.5"/><path d="M19 5 5 19"/></svg>',
                                'Descuento %',
                                'Un producto con porcentaje de descuento.'
                            ],
                            'descuento_monto' => [
                                '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h12M6 8h12M6 20l6-8"/><path d="M12 12c3 0 3 4 0 4H6"/></svg>',
                                'Descuento en Bs',
                                'Un producto con monto descontado.'
                            ],
                            'precio_fijo' => [
                                '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12 12 3h7a2 2 0 0 1 2 2v7l-9 9-9-9Z"/><circle cx="16" cy="8" r="1.3" fill="currentColor"/></svg>',
                                'Precio especial',
                                'Un producto con precio final fijo.'
                            ],
                        ] as $valor => [$icono, $titulo, $detalle])
                            <label>
                                <input
                                    class="promo-sr"
                                    type="radio"
                                    name="tipo"
                                    value="{{ $valor }}"
                                    @checked($tipoInicial === $valor)
                                    required
                                >
                                <span class="promo-type">
                                    <span class="promo-type-icon">{!! $icono !!}</span>
                                    <strong>{{ $titulo }}</strong>
                                    <small>{{ $detalle }}</small>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <div id="promoTipoDescripcion" class="promo-type-description"></div>
                </section>

                {{-- 2. INFORMACIÓN --}}
                <section class="promo-card" id="sec-info">
                    <div class="promo-section-header">
                        <span class="promo-step">2</span>
                        <div>
                            <h2>Información general</h2>
                            <p class="promo-help">Define el nombre y las condiciones de la promoción.</p>
                        </div>
                    </div>

                    <div class="promo-fields">
                        <div class="promo-field promo-wide">
                            <label for="promoNombre">Nombre de la promoción</label>
                            <input
                                id="promoNombre"
                                name="nombre"
                                value="{{ old('nombre', $promocion->nombre ?? '') }}"
                                maxlength="150"
                                placeholder="Ejemplo: Combo Coca-Cola y papas"
                                required
                            >
                        </div>

                        <div class="promo-field">
                            <label for="promoCodigo">Código de la promoción</label>

                            <div class="promo-code">
                                <input
                                    id="promoCodigo"
                                    name="codigo"
                                    value="{{ $codigoInicial }}"
                                    maxlength="40"
                                    readonly
                                    aria-describedby="promoCodigoHint"
                                >

                                <button
                                    type="button"
                                    id="promoCodigoRegenerar"
                                    class="promo-icon-btn"
                                    title="Generar otro código"
                                    aria-label="Generar otro código"
                                    @disabled($editando)
                                >↻</button>

                                <button
                                    type="button"
                                    id="promoCodigoCopiar"
                                    class="promo-icon-btn"
                                    title="Copiar código"
                                    aria-label="Copiar código"
                                >⧉</button>
                            </div>

                            <span class="promo-hint" id="promoCodigoHint">
                                {{ $editando
                                    ? 'El código no cambia al editar la promoción.'
                                    : 'Se genera automáticamente según el tipo de promoción.' }}
                            </span>
                        </div>

                        <div class="promo-field">
                            <label for="promoEstado">Estado</label>
                            <select id="promoEstado" name="estado">
                                <option value="activa" @selected(old('estado', $promocion->estado ?? 'activa') === 'activa')>
                                    Activa
                                </option>
                                <option value="inactiva" @selected(old('estado', $promocion->estado ?? 'activa') === 'inactiva')>
                                    Inactiva
                                </option>
                            </select>
                        </div>

                        <div class="promo-field promo-wide">
                            <label for="promoDescripcion">Descripción y condiciones opcionales</label>
                            <textarea
                                id="promoDescripcion"
                                name="descripcion"
                                rows="3"
                                maxlength="5000"
                                placeholder="Describe qué incluye la promoción"
                            >{{ old('descripcion', $promocion->descripcion ?? '') }}</textarea>
                        </div>
                    </div>
                </section>

                {{-- 3. PRODUCTOS --}}
                <section class="promo-card" id="sec-productos">
                    <div class="promo-section-header">
                        <span class="promo-step">3</span>
                        <div>
                            <h2 id="promoProductosTitulo">Productos que combinarás</h2>
                            <p class="promo-help" id="promoProductosAyuda">
                                Agrega dos o más productos distintos.
                            </p>
                        </div>
                    </div>

                    <div class="promo-field">
                        <label for="promoBuscar">Buscar producto</label>
                        <input
                            id="promoBuscar"
                            type="search"
                            placeholder="Nombre o código del producto"
                            autocomplete="off"
                        >
                    </div>

                    <div class="promo-picker">
                        <select id="promoSelector" aria-label="Producto para agregar"></select>

                        <button id="promoAgregar" type="button" class="promo-button">
                            + Agregar producto
                        </button>
                    </div>

                    <div class="promo-table-wrap">
                        <table class="promo-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad incluida</th>
                                    <th>Precio normal</th>
                                    <th>Subtotal</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="promoProductosBody"></tbody>
                        </table>
                    </div>

                    <div class="promo-products-footer">
                        <span id="promoProductosCount">0 productos seleccionados</span>
                        <strong id="promoSubtotalProductos">Total normal: Bs 0,00</strong>
                    </div>

                    <p class="promo-help" style="margin-top:15px;">
                        Cada aplicación incluye todas las cantidades de esta lista.
                        Los pesables permiten tres decimales; las unidades y paquetes,
                        solamente cantidades enteras.
                    </p>
                </section>

                {{-- 4. PRECIO --}}
                <section class="promo-card" id="sec-precio">
                    <div class="promo-section-header">
                        <span class="promo-step">4</span>
                        <div>
                            <h2>Descuento y precio final</h2>
                            <p class="promo-help">Compara el total normal con el precio de la promoción.</p>
                        </div>
                    </div>

                    <div class="promo-fields">
                        <div class="promo-field" id="promoModoGrupo">
                            <label for="promoModo">¿Cómo calcularás el precio del combo?</label>
                            <select id="promoModo" name="modo_calculo">
                                <option value="porcentaje" @selected($modoInicial === 'porcentaje')>
                                    Aplicar descuento por porcentaje
                                </option>
                                <option value="monto" @selected($modoInicial === 'monto')>
                                    Descontar un monto en Bs
                                </option>
                                <option value="precio_fijo" @selected($modoInicial === 'precio_fijo')>
                                    Establecer el precio final
                                </option>
                            </select>
                        </div>

                        <div class="promo-field" id="promoDescuentoGrupo">
                            <label for="promoDescuento" id="promoDescuentoLabel">Descuento</label>
                            <input
                                id="promoDescuento"
                                name="valor_descuento"
                                type="number"
                                min="0.01"
                                step="0.01"
                                value="{{ old('valor_descuento', $promocion->valor_descuento ?? '') }}"
                                placeholder="Ejemplo: 15"
                            >
                        </div>

                        <div class="promo-field" id="promoPrecioGrupo">
                            <label for="promoPrecio">Precio final de la promoción · Bs</label>
                            <input
                                id="promoPrecio"
                                name="precio_promocional"
                                type="number"
                                min="0.01"
                                max="9999999999.99"
                                step="0.01"
                                value="{{ old('precio_promocional', $promocion->precio_promocional ?? '') }}"
                                placeholder="Ejemplo: 18.00"
                            >
                        </div>
                    </div>
                </section>

                {{-- 5. VIGENCIA --}}
                <section class="promo-card" id="sec-vigencia">
                    <div class="promo-section-header">
                        <span class="promo-step">5</span>
                        <div>
                            <h2>Disponibilidad y vigencia</h2>
                            <p class="promo-help">Define las fechas y el cupo de aplicaciones.</p>
                        </div>
                    </div>

                    <div class="promo-fields">
                        <div class="promo-field">
                            <label for="promoLimiteTipo">Disponibilidad</label>
                            <select id="promoLimiteTipo" name="limite_tipo">
                                <option value="stock" @selected($limiteInicial === 'stock')>
                                    Según stock disponible
                                </option>
                                <option value="cantidad" @selected($limiteInicial === 'cantidad')>
                                    Limitar cantidad de aplicaciones
                                </option>
                            </select>
                        </div>

                        <div class="promo-field" id="promoCupoGrupo">
                            <label for="promoCupo">Cupo total de aplicaciones</label>
                            <input
                                id="promoCupo"
                                name="limite_aplicaciones"
                                type="number"
                                min="{{ max(1, $usadas) }}"
                                max="2147483647"
                                step="1"
                                value="{{ old('limite_aplicaciones', $promocion->limite_aplicaciones ?? '') }}"
                                placeholder="Ejemplo: 20"
                            >
                        </div>

                        <div class="promo-field">
                            <label for="promoInicio">Fecha de inicio</label>
                            <input
                                id="promoInicio"
                                name="fecha_inicio"
                                type="datetime-local"
                                value="{{ old('fecha_inicio', $inicio) }}"
                                required
                            >
                        </div>

                        <div class="promo-field">
                            <label for="promoFin">Fecha final opcional</label>
                            <input
                                id="promoFin"
                                name="fecha_fin"
                                type="datetime-local"
                                value="{{ old('fecha_fin', $fin) }}"
                            >
                        </div>
                    </div>

                    <div class="promo-note">
                        Si un combo incluye 2 gaseosas y 1 kg de papa, un cupo de 10
                        permite aplicar ese combo completo diez veces, siempre que
                        exista stock suficiente.
                    </div>
                </section>
            </div>

            {{-- RESUMEN --}}
            <aside class="promo-card promo-summary">
                <div class="promo-summary-head">
                    <span class="promo-eyebrow">Vista previa</span>
                    <h2>Resumen de la promoción</h2>
                    <span id="promoResumenTipo" class="promo-badge">Combinar productos</span>
                </div>

                <div class="promo-summary-body">
                    <div class="promo-summary-code">
                        <span>Código</span>
                        <strong id="promoResumenCodigo">—</strong>
                    </div>

                    <div class="promo-summary-row">
                        <span>Total sin promoción</span>
                        <strong id="promoTotalNormal">Bs 0,00</strong>
                    </div>

                    <div class="promo-summary-row">
                        <span>Ahorro del cliente</span>
                        <strong id="promoTotalAhorro" class="promo-saving">—</strong>
                    </div>

                    <div class="promo-total">
                        <span>El cliente pagará por aplicación</span>
                        <strong id="promoTotalFinal">—</strong>
                        <small id="promoPorcentajeAhorro"></small>
                    </div>

                    <div class="promo-summary-row" style="margin-top:0">
                        <span>Aplicaciones según stock</span>
                        <strong id="promoCapacidadStock">0</strong>
                    </div>

                    <div class="promo-summary-row">
                        <span>Aplicaciones utilizadas</span>
                        <strong>{{ $usadas }}</strong>
                    </div>

                    <div class="promo-summary-row">
                        <span>Disponibles con el cupo</span>
                        <strong id="promoDisponibles">0</strong>
                    </div>

                    <p class="promo-help" style="margin-top:20px;">
                        La disponibilidad es una referencia calculada con el stock total
                        registrado en los almacenes. Crear una promoción no reserva ni
                        descuenta inventario.
                    </p>

                    <div id="promoMensaje" class="promo-note" role="status" aria-live="polite">
                        Selecciona los productos y configura el descuento.
                    </div>

                    <div class="promo-actions">
                        <button id="promoGuardar" type="submit" class="promo-button primary">
                            {{ $editando ? 'Guardar cambios' : 'Crear promoción' }}
                        </button>

                        <a href="{{ route('admin.promociones.index') }}" class="promo-button">
                            Cancelar
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </form>
</div>

<script>
(() => {
    'use strict';

    const catalogo = {{ \Illuminate\Support\Js::from($productos) }};
    const idsIniciales = {{ \Illuminate\Support\Js::from($idsIniciales) }};
    const cantidadesIniciales = {{ \Illuminate\Support\Js::from($cantidadesIniciales) }};
    const editando = {{ $editando ? 'true' : 'false' }};

    const usadas = BigInt({{ $usadas }});
    const maxCentavos = 999999999999n;

    const seleccionados = new Map();
    const $ = id => document.getElementById(id);

    let ultimoModoCombo = $('promoModo').value;

    const tipos = {
        combo: {
            titulo: 'Combinar productos / combo',
            prefijo: 'COMBO',
            descripcion:
                'Combina dos o más productos distintos. Define la cantidad de cada uno y aplica un porcentaje, un descuento en Bs o un precio final al conjunto.'
        },
        descuento_porcentaje: {
            titulo: 'Descuento por porcentaje',
            prefijo: 'DESC',
            descripcion:
                'Selecciona un producto y define su cantidad por aplicación. Ingresa el porcentaje que descontarás, por ejemplo 15 %.'
        },
        descuento_monto: {
            titulo: 'Descuento por monto',
            prefijo: 'DMON',
            descripcion:
                'Selecciona un producto y define cuánto descontarás en Bs sobre la cantidad configurada.'
        },
        precio_fijo: {
            titulo: 'Precio especial',
            prefijo: 'PFIJ',
            descripcion:
                'Selecciona un producto y establece el precio final para la cantidad configurada.'
        }
    };

    /* ---------- Tipo actual ---------- */
    function tipoActual() {
        const marcado = document.querySelector('input[name="tipo"]:checked');
        return marcado ? marcado.value : 'combo';
    }

    /* ---------- Código automático ---------- */
    const ALFABETO = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    function sufijoAleatorio(longitud = 5) {
        const bytes = new Uint32Array(longitud);
        crypto.getRandomValues(bytes);

        return Array.from(bytes, n => ALFABETO[n % ALFABETO.length]).join('');
    }

    let sufijoCodigo = null;
    let codigoManual = false;

    function periodo() {
        const ahora = new Date();

        return String(ahora.getFullYear()).slice(-2)
            + String(ahora.getMonth() + 1).padStart(2, '0');
    }

    function construirCodigo() {
        const prefijo = (tipos[tipoActual()] || tipos.combo).prefijo;

        return prefijo + '-' + periodo() + '-' + sufijoCodigo;
    }

    function actualizarCodigo(regenerar = false) {
        const campo = $('promoCodigo');

        if (editando || (campo.value && !regenerar && !codigoManual)) {
            $('promoResumenCodigo').textContent = campo.value || '—';
            return;
        }

        if (regenerar || sufijoCodigo === null) {
            sufijoCodigo = sufijoAleatorio();
        }

        campo.value = construirCodigo();
        $('promoResumenCodigo').textContent = campo.value;
    }

    function sincronizarCodigoInicial() {
        const valor = $('promoCodigo').value;

        if (!editando && valor) {
            const partes = valor.split('-');
            sufijoCodigo = partes[2] || sufijoAleatorio();
            codigoManual = false;

            $('promoCodigo').value = construirCodigo();
        }

        actualizarCodigo();
    }

    /* ---------- Utilidades numéricas ---------- */
    function escalado(valor, decimales) {
        const texto = String(valor ?? '').trim();
        const patron = new RegExp('^\\d+(?:\\.\\d{1,' + decimales + '})?$');

        if (!patron.test(texto)) {
            return null;
        }

        const [entero, fraccion = ''] = texto.split('.');

        return BigInt(entero) * (10n ** BigInt(decimales))
            + BigInt(fraccion.padEnd(decimales, '0'));
    }

    function moneda(centavos) {
        const entero = centavos / 100n;
        const fraccion = String(centavos % 100n).padStart(2, '0');

        return 'Bs ' + entero.toLocaleString('es-BO') + ',' + fraccion;
    }

    function modoActual() {
        switch (tipoActual()) {
            case 'descuento_porcentaje': return 'porcentaje';
            case 'descuento_monto':      return 'monto';
            case 'precio_fijo':          return 'precio_fijo';
            default:                     return $('promoModo').value;
        }
    }

    /* ---------- Validación visual por campo ---------- */
    function marcarCampo(id, invalido) {
        const el = $(id);
        if (!el) return;

        el.classList.toggle('is-invalid', Boolean(invalido));
        el.setAttribute('aria-invalid', invalido ? 'true' : 'false');
    }

    /* ---------- Selector de productos ---------- */
    function actualizarSelector() {
        const busqueda = $('promoBuscar').value.trim().toLocaleLowerCase();
        const selector = $('promoSelector');

        selector.replaceChildren(new Option('Selecciona un producto', ''));

        const individual = tipoActual() !== 'combo';

        for (const producto of catalogo) {
            const texto = (producto.codigo_interno + ' ' + producto.nombre).toLocaleLowerCase();

            if (!texto.includes(busqueda)) {
                continue;
            }

            const agregado = seleccionados.has(String(producto.id));
            const inactivo = producto.estado !== 'activo';

            const precio = escalado(producto.precio_venta, 2);
            const sinPrecio = precio === null || precio <= 0n;

            const limiteIndividual = individual && seleccionados.size >= 1 && !agregado;

            let etiqueta = producto.codigo_interno + ' · ' + producto.nombre;

            if (agregado) {
                etiqueta += ' · Agregado';
            } else if (inactivo) {
                etiqueta += ' · Inactivo';
            } else if (sinPrecio) {
                etiqueta += ' · Sin precio vigente válido';
            }

            const opcion = new Option(etiqueta, producto.id);
            opcion.disabled = agregado || inactivo || sinPrecio || limiteIndividual;

            selector.add(opcion);
        }

        $('promoAgregar').disabled = individual && seleccionados.size >= 1;
    }

    function agregarProducto(id, cantidad = '1', restaurando = false) {
        const clave = String(id);
        const producto = catalogo.find(item => String(item.id) === clave);

        if (!producto || seleccionados.has(clave)) {
            return;
        }

        if (!restaurando && tipoActual() !== 'combo' && seleccionados.size >= 1) {
            return;
        }

        const fila = document.createElement('tr');

        fila.innerHTML = `
            <td>
                <strong class="producto-nombre"></strong>
                <small class="producto-codigo"></small>
                <small class="producto-stock"></small>
                <input type="hidden" class="producto-id" name="productos[]">
            </td>
            <td>
                <input type="number" class="producto-cantidad" required>
                <small class="producto-unidad"></small>
            </td>
            <td class="producto-precio"></td>
            <td class="producto-subtotal"></td>
            <td>
                <button type="button" class="promo-remove">Quitar</button>
            </td>
        `;

        fila.querySelector('.producto-nombre').textContent = producto.nombre;
        fila.querySelector('.producto-codigo').textContent = producto.codigo_interno;
        fila.querySelector('.producto-stock').textContent =
            'Stock registrado: ' + producto.stock_actual + ' ' + (producto.unidad || '');
        fila.querySelector('.producto-unidad').textContent = producto.unidad || '';
        fila.querySelector('.producto-id').value = producto.id;

        const input = fila.querySelector('.producto-cantidad');
        const pesable = Number(producto.es_pesable) === 1;

        input.name = 'cantidades[' + producto.id + ']';
        input.min = pesable ? '0.001' : '1';
        input.step = pesable ? '0.001' : '1';
        input.max = '999999999.999';
        input.value = cantidad;

        input.addEventListener('input', actualizarResumen);

        fila.querySelector('.promo-remove').addEventListener('click', () => {
            seleccionados.delete(clave);
            fila.remove();

            actualizarSelector();
            actualizarResumen();
        });

        seleccionados.set(clave, { producto, fila, input });

        $('promoProductosBody').append(fila);

        actualizarSelector();
        actualizarResumen();
    }

    /* ---------- Campos según tipo ---------- */
    function actualizarCampos() {
        const tipo = tipoActual();
        const combo = tipo === 'combo';
        const configuracion = tipos[tipo] || tipos.combo;

        $('promoTipoDescripcion').textContent = configuracion.descripcion;
        $('promoResumenTipo').textContent = configuracion.titulo;

        $('promoProductosTitulo').textContent = combo
            ? 'Productos que combinarás'
            : 'Producto de la promoción';

        $('promoProductosAyuda').textContent = combo
            ? 'Agrega dos o más productos distintos y define sus cantidades.'
            : 'Selecciona un producto y define la cantidad por aplicación.';

        if (!combo) {
            $('promoModo').value = modoActual();
        }

        const modo = modoActual();
        const fijo = modo === 'precio_fijo';

        $('promoModoGrupo').hidden = !combo;

        $('promoDescuentoGrupo').hidden = fijo;
        $('promoDescuento').disabled = fijo;
        $('promoDescuento').required = !fijo;
        $('promoDescuento').max = modo === 'porcentaje' ? '100' : '9999999999.99';

        $('promoDescuentoLabel').textContent = modo === 'porcentaje'
            ? 'Porcentaje de descuento · %'
            : 'Monto que descontarás · Bs';

        $('promoPrecioGrupo').hidden = !fijo;
        $('promoPrecio').disabled = !fijo;
        $('promoPrecio').required = fijo;

        const limitado = $('promoLimiteTipo').value === 'cantidad';

        $('promoCupoGrupo').hidden = !limitado;
        $('promoCupo').disabled = !limitado;
        $('promoCupo').required = limitado;

        actualizarCodigo();
        actualizarSelector();
        actualizarResumen();
    }

    /* ---------- Resumen y validación ---------- */
    function actualizarResumen() {
        const vacia = $('promoProductosBody').querySelector('.promo-fila-vacia');

        if (vacia) {
            vacia.remove();
        }

        if (seleccionados.size === 0) {
            const fila = document.createElement('tr');

            fila.className = 'promo-fila-vacia';
            fila.innerHTML = `
                <td colspan="5" class="promo-empty">
                    Busca y agrega los productos de la promoción.
                </td>
            `;

            $('promoProductosBody').append(fila);
        }

        let total = 0n;
        let capacidad = null;
        let error = '';

        for (const { producto, fila, input } of seleccionados.values()) {
            const cantidad = escalado(input.value, 3);
            const precio = escalado(producto.precio_venta, 2);
            const stock = escalado(producto.stock_actual, 3) ?? 0n;
            const pesable = Number(producto.es_pesable) === 1;

            fila.querySelector('.producto-precio').textContent =
                precio === null ? 'Sin precio' : moneda(precio);

            if (
                cantidad === null
                || cantidad <= 0n
                || cantidad > 999999999999n
                || (!pesable && cantidad % 1000n !== 0n)
            ) {
                error = 'Revisa la cantidad de ' + producto.nombre + '.';
                fila.querySelector('.producto-subtotal').textContent = '—';
                capacidad = 0n;
                continue;
            }

            if (producto.estado !== 'activo' || precio === null || precio <= 0n) {
                error = producto.nombre
                    + ' debe estar activo y tener un precio vigente mayor que cero.';
                fila.querySelector('.producto-subtotal').textContent = '—';
                capacidad = 0n;
                continue;
            }

            const subtotal = (cantidad * precio + 500n) / 1000n;

            fila.querySelector('.producto-subtotal').textContent = moneda(subtotal);

            total += subtotal;

            const aplicaciones = stock / cantidad;

            if (capacidad === null || aplicaciones < capacidad) {
                capacidad = aplicaciones;
            }
        }

        capacidad ??= 0n;

        const combo = tipoActual() === 'combo';

        if (combo && seleccionados.size < 2) {
            error = 'Para combinar productos agrega al menos dos productos distintos.';
        } else if (!combo && seleccionados.size !== 1) {
            error = 'La promoción individual requiere exactamente un producto. Quita los productos adicionales.';
        }

        if (total <= 0n) {
            error ||= 'El total normal debe ser mayor que cero.';
        }

        if (total > maxCentavos) {
            error = 'El total supera el máximo permitido.';
        }

        let descuento = 0n;
        let final = total;

        const modo = modoActual();

        if (modo === 'porcentaje') {
            const porcentaje = escalado($('promoDescuento').value, 2);
            const invalido = porcentaje === null || porcentaje <= 0n || porcentaje > 10000n;

            marcarCampo('promoDescuento', invalido);

            if (invalido) {
                error ||= 'Ingresa un porcentaje entre 0,01 y 100.';
            } else {
                descuento = (total * porcentaje + 5000n) / 10000n;
                final = total - descuento;
            }
        } else if (modo === 'monto') {
            const monto = escalado($('promoDescuento').value, 2);
            const invalido = monto === null || monto <= 0n || monto > total;

            marcarCampo('promoDescuento', invalido);

            if (invalido) {
                error ||= 'El descuento debe ser mayor que cero y no superar el total normal.';
            } else {
                descuento = monto;
                final = total - descuento;
            }
        } else {
            const precioFinal = escalado($('promoPrecio').value, 2);
            const invalido = precioFinal === null || precioFinal <= 0n || precioFinal > total;

            marcarCampo('promoPrecio', invalido);
            marcarCampo('promoDescuento', false);

            if (invalido) {
                error ||= 'El precio final debe ser mayor que cero y no superar el total normal.';
            } else {
                final = precioFinal;
                descuento = total - final;
            }
        }

        let disponibles = capacidad;

        if ($('promoLimiteTipo').value === 'cantidad') {
            const texto = $('promoCupo').value.trim();
            const cupo = /^\d+$/.test(texto) ? BigInt(texto) : null;
            const invalido = cupo === null || cupo < 1n || cupo < usadas || cupo > 2147483647n;

            marcarCampo('promoCupo', invalido);

            if (invalido) {
                error ||= 'El cupo debe ser un entero positivo y no ser menor que las aplicaciones utilizadas.';
            } else {
                const restantes = cupo - usadas;
                disponibles = restantes < capacidad ? restantes : capacidad;
            }
        } else {
            marcarCampo('promoCupo', false);
        }

        $('promoTotalNormal').textContent = moneda(total);
        $('promoSubtotalProductos').textContent = 'Total normal: ' + moneda(total);
        $('promoTotalAhorro').textContent = error ? '—' : moneda(descuento);
        $('promoTotalFinal').textContent = error ? '—' : moneda(final);

        if (!error && total > 0n && descuento > 0n) {
            const decimas = (descuento * 1000n + total / 2n) / total;
            $('promoPorcentajeAhorro').textContent =
                'Ahorro de ' + (Number(decimas) / 10).toLocaleString('es-BO') + ' %';
        } else {
            $('promoPorcentajeAhorro').textContent = '';
        }

        $('promoCapacidadStock').textContent = capacidad.toLocaleString('es-BO');
        $('promoDisponibles').textContent = disponibles.toLocaleString('es-BO');

        $('promoProductosCount').textContent = seleccionados.size
            + (seleccionados.size === 1
                ? ' producto seleccionado'
                : ' productos seleccionados');

        $('promoGuardar').disabled = Boolean(error);

        $('promoMensaje').classList.toggle('error', Boolean(error));
        $('promoMensaje').classList.toggle('ok', !error);

        $('promoMensaje').textContent = error || (
            disponibles === 0n
                ? 'Configuración válida. Actualmente el stock o el cupo no permiten aplicaciones.'
                : 'Configuración válida. Los precios se comprobarán nuevamente al guardar.'
        );

        return !error;
    }

    /* ---------- Eventos ---------- */
    $('promoAgregar').addEventListener('click', () => {
        const id = $('promoSelector').value;

        if (id) {
            agregarProducto(id);
        }
    });

    $('promoBuscar').addEventListener('input', actualizarSelector);

    document.querySelectorAll('input[name="tipo"]').forEach(radio => {
        radio.addEventListener('change', () => {
            if (tipoActual() === 'combo') {
                $('promoModo').value = ultimoModoCombo;
            }

            actualizarCampos();
        });
    });

    $('promoModo').addEventListener('change', () => {
        if (tipoActual() === 'combo') {
            ultimoModoCombo = $('promoModo').value;
        }

        actualizarCampos();
    });

    $('promoLimiteTipo').addEventListener('change', actualizarCampos);

    ['promoDescuento', 'promoPrecio', 'promoCupo'].forEach(id => {
        $(id).addEventListener('input', actualizarResumen);
    });

    $('promoCodigoRegenerar').addEventListener('click', () => {
        if (editando) {
            return;
        }

        codigoManual = true;
        actualizarCodigo(true);
    });

    $('promoCodigoCopiar').addEventListener('click', async () => {
        const boton = $('promoCodigoCopiar');
        const codigo = $('promoCodigo').value;

        try {
            await navigator.clipboard.writeText(codigo);
        } catch (e) {
            $('promoCodigo').select();
            document.execCommand('copy');
        }

        boton.textContent = '✓';
        setTimeout(() => { boton.textContent = '⧉'; }, 1200);
    });

    $('promoForm').addEventListener('submit', event => {
        if (!actualizarResumen()) {
            event.preventDefault();
            return;
        }

        $('promoGuardar').classList.add('is-loading');
    });

    /* ---------- Stepper: sección activa / completada ---------- */
    const secciones = ['sec-tipo', 'sec-info', 'sec-productos', 'sec-precio', 'sec-vigencia']
        .map(id => $(id))
        .filter(Boolean);

    const stepperLinks = Array.from(document.querySelectorAll('.promo-stepper a'));

    if ('IntersectionObserver' in window && secciones.length) {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;

                const idx = secciones.indexOf(entry.target);

                stepperLinks.forEach((a, i) => {
                    a.classList.toggle('is-active', i === idx);
                    a.classList.toggle('is-done', i < idx);
                });
            });
        }, { rootMargin: '-30% 0px -60% 0px', threshold: 0 });

        secciones.forEach(s => observer.observe(s));
    }

    /* ---------- Inicio ---------- */
    for (const id of Object.values(idsIniciales || {})) {
        agregarProducto(id, cantidadesIniciales?.[id] ?? '1', true);
    }

    sincronizarCodigoInicial();
    actualizarCampos();
})();
</script>
@endsection