@extends('pesaje.layouts.app')

@section('title', 'Registrar pesaje')

@push('styles')
<style>
    .pesaje-create {
        --bg: #131419;
        --surface: #1a1c22;
        --surface-2: #21232a;
        --border: #2d3039;
        --border-hover: #4a4e5b;
        --text: #ececf0;
        --muted: #8e919b;
        --red: #e31b23;
        --red-soft: #ff777d;
        --green: #7be0a3;
        --amber: #f3d38a;
        --radius: 14px;

        color: var(--text);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .pesaje-create *,
    .pesaje-create *::before,
    .pesaje-create *::after { box-sizing: border-box; }

    .pesaje-create [hidden] { display: none !important; }

    /* ---------- Header ---------- */
    .pc-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding: 18px 22px;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background:
            linear-gradient(90deg, rgba(227, 27, 35, .10), transparent 45%),
            var(--bg);
    }

    .pc-heading { display: flex; align-items: center; gap: 14px; }

    .pc-heading-icon {
        width: 46px; height: 46px; min-width: 46px;
        display: grid; place-items: center;
        border-radius: 13px;
        color: var(--red-soft);
        background: rgba(227, 27, 35, .12);
        border: 1px solid rgba(227, 27, 35, .3);
    }
    .pc-heading-icon svg { width: 22px; height: 22px; }

    .pc-eyebrow {
        color: var(--red-soft);
        font-size: 10px; font-weight: 800;
        letter-spacing: 1.2px; text-transform: uppercase;
    }
    .pc-header h1 { margin: 3px 0 2px; font-size: 22px; color: #fff; }
    .pc-header p { margin: 0; font-size: 12px; color: var(--muted); }

    /* ---------- Botones ---------- */
    .pc-button {
        min-height: 42px;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 0 16px;
        border: 1px solid var(--border);
        border-radius: 10px;
        color: #eee; background: var(--surface-2);
        font: inherit; font-size: 12px; font-weight: 700;
        text-decoration: none; cursor: pointer;
        transition: background .15s, border-color .15s, transform .15s;
    }
    .pc-button:hover { border-color: var(--border-hover); background: #2a2d35; }
    .pc-button:active { transform: scale(.98); }
    .pc-button:focus-visible,
    .pc-chip:focus-visible,
    .pc-quick button:focus-visible { outline: 2px solid var(--red-soft); outline-offset: 2px; }

    .pc-button.primary {
        width: 100%; min-height: 50px;
        border-color: var(--red); background: var(--red);
        color: #fff; font-size: 13px;
    }
    .pc-button.primary:hover:not(:disabled) { background: #f2262e; }
    .pc-button:disabled { opacity: .4; cursor: not-allowed; }

    /* ---------- Alertas ---------- */
    .pc-alert, .pc-error {
        padding: 13px 16px;
        border-radius: 10px;
        font-size: 12px; line-height: 1.6;
    }
    .pc-alert { border: 1px solid rgba(243, 211, 138, .3); background: rgba(243, 211, 138, .07); color: var(--amber); }
    .pc-error { border: 1px solid rgba(227, 27, 35, .4); background: rgba(227, 27, 35, .08); color: #ff9ca0; }
    .pc-error ul { margin: 6px 0 0; padding-left: 18px; }

    /* ---------- Layout principal ---------- */
    .pc-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 16px;
        align-items: start;
    }

    .pc-panel {
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background: var(--bg);
        padding: 18px;
    }

    .pc-panel-head {
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; flex-wrap: wrap; margin-bottom: 14px;
    }
    .pc-panel-head h2 { margin: 0; font-size: 15px; color: #fff; }
    .pc-panel-head p { margin: 4px 0 0; font-size: 11px; color: var(--muted); }

    /* ---------- Buscador ---------- */
    .pc-search-wrap { position: relative; width: min(100%, 300px); }
    .pc-search-wrap svg {
        position: absolute; left: 12px; top: 50%;
        width: 16px; height: 16px; transform: translateY(-50%);
        color: var(--muted); pointer-events: none;
    }
    .pc-search-wrap kbd {
        position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
        padding: 1px 6px; border: 1px solid var(--border); border-radius: 5px;
        font: inherit; font-size: 10px; color: var(--muted); pointer-events: none;
    }
    .pc-search {
        width: 100%; min-height: 42px;
        padding: 0 34px 0 36px;
        border: 1px solid var(--border); border-radius: 10px;
        outline: none; color: #eee; background: var(--surface);
        font: inherit; font-size: 13px;
        transition: border-color .15s, box-shadow .15s;
    }
    .pc-search:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(227, 27, 35, .14); }

    /* ---------- Chips de categoría ---------- */
    .pc-chips { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px; margin-bottom: 14px; scrollbar-width: thin; }
    .pc-chip {
        flex: none;
        padding: 7px 13px;
        border: 1px solid var(--border); border-radius: 999px;
        color: var(--muted); background: var(--surface);
        font: inherit; font-size: 11px; font-weight: 700; cursor: pointer;
        transition: all .15s;
    }
    .pc-chip:hover { color: #fff; border-color: var(--border-hover); }
    .pc-chip[aria-pressed="true"] { color: #fff; border-color: var(--red); background: rgba(227, 27, 35, .16); }

    .pc-count { margin: 0 0 10px; font-size: 11px; color: var(--muted); }

    /* ---------- Grid de productos ---------- */
    .pc-product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 12px;
        max-height: 620px;
        overflow-y: auto;
        padding: 3px;
        scrollbar-width: thin;
    }

    .pc-product-option { position: relative; min-width: 0; content-visibility: auto; contain-intrinsic-size: 220px; }
    .pc-product-option input { position: absolute; opacity: 0; pointer-events: none; }

    .pc-product-card {
        height: 100%;
        display: flex; flex-direction: column;
        overflow: hidden; cursor: pointer;
        border: 1px solid var(--border); border-radius: 12px;
        background: var(--surface);
        transition: border-color .15s, transform .15s, box-shadow .15s;
    }
    .pc-product-card:hover { border-color: var(--border-hover); transform: translateY(-2px); }

    .pc-product-option input:checked + .pc-product-card {
        border-color: var(--red);
        box-shadow: 0 0 0 2px rgba(227, 27, 35, .22), 0 8px 24px rgba(0, 0, 0, .3);
    }
    .pc-product-option input:focus-visible + .pc-product-card { outline: 2px solid var(--red-soft); outline-offset: 2px; }

    .pc-check {
        position: absolute; top: 8px; right: 8px; z-index: 2;
        width: 24px; height: 24px; display: grid; place-items: center;
        border-radius: 50%; color: #fff; background: var(--red);
        opacity: 0; transform: scale(.5);
        transition: opacity .15s, transform .2s cubic-bezier(.3, 1.6, .5, 1);
    }
    .pc-check svg { width: 13px; height: 13px; }
    .pc-product-option input:checked + .pc-product-card .pc-check { opacity: 1; transform: scale(1); }

    .pc-product-image {
        position: relative; height: 112px;
        display: grid; place-items: center;
        background: radial-gradient(circle at 50% 10%, rgba(227, 27, 35, .10), transparent 65%), #111216;
    }
    .pc-product-image img { width: 100%; height: 100%; padding: 8px; object-fit: contain; }
    .pc-image-fallback { font-size: 10px; color: #6f737d; }

    .pc-product-content { flex: 1; display: flex; flex-direction: column; gap: 4px; padding: 11px 12px 12px; }
    .pc-product-code { font-size: 10px; font-weight: 700; color: #9b9ea8; }
    .pc-product-name {
        font-size: 12.5px; font-weight: 700; line-height: 1.35; color: #f4f4f6;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .pc-product-category { font-size: 10px; color: var(--muted); }
    .pc-product-price { margin-top: auto; padding-top: 6px; font-size: 13px; font-weight: 800; color: var(--green); }

    .pc-empty {
        grid-column: 1 / -1; padding: 28px; text-align: center;
        border: 1px dashed var(--border); border-radius: 10px;
        font-size: 12px; color: var(--muted);
    }

    /* ---------- Ticket lateral ---------- */
    .pc-ticket { position: sticky; top: 16px; display: flex; flex-direction: column; gap: 16px; }

    .pc-selected {
        display: flex; align-items: center; gap: 12px;
        padding: 12px; min-height: 76px;
        border: 1px dashed var(--border); border-radius: 12px;
        background: var(--surface);
        transition: border-color .2s, background .2s;
    }
    .pc-selected.has-product {
        border-style: solid; border-color: rgba(227, 27, 35, .35);
        background: linear-gradient(135deg, rgba(227, 27, 35, .09), transparent 70%), var(--surface);
    }
    .pc-selected-icon {
        width: 40px; height: 40px; min-width: 40px; display: grid; place-items: center;
        border-radius: 10px; color: var(--red-soft); background: rgba(227, 27, 35, .12);
    }
    .pc-selected-icon svg { width: 20px; height: 20px; }
    .pc-selected-info { min-width: 0; }
    .pc-selected-info small { display: block; font-size: 9px; font-weight: 800; letter-spacing: .7px; text-transform: uppercase; color: var(--muted); }
    .pc-selected-info strong { display: block; margin: 2px 0; font-size: 14px; color: #fff; overflow-wrap: anywhere; }
    .pc-selected-info span { font-size: 11px; color: #b5b8c0; }

    .pc-weight-label { display: block; margin-bottom: 8px; font-size: 11px; font-weight: 700; color: #dedfe4; }

    .pc-weight-field { position: relative; }
    .pc-weight-field input {
        width: 100%; min-height: 62px;
        padding: 0 64px 0 16px;
        border: 1px solid var(--border); border-radius: 12px;
        outline: none; color: #fff; background: var(--surface);
        font: inherit; font-size: 28px; font-weight: 800;
        font-variant-numeric: tabular-nums;
        transition: border-color .15s, box-shadow .15s;
    }
    .pc-weight-field input:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(227, 27, 35, .14); }
    .pc-weight-field input:disabled { opacity: .45; cursor: not-allowed; }
    .pc-weight-unit {
        position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
        font-size: 14px; font-weight: 800; color: var(--red-soft); pointer-events: none;
    }

    .pc-quick { display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; margin-top: 10px; }
    .pc-quick button {
        min-height: 36px; border: 1px solid var(--border); border-radius: 8px;
        color: #ddd; background: var(--surface-2);
        font: inherit; font-size: 11px; font-weight: 700; cursor: pointer;
        transition: background .15s, border-color .15s;
    }
    .pc-quick button:hover:not(:disabled) { border-color: var(--red); background: rgba(227, 27, 35, .12); }
    .pc-quick button:disabled { opacity: .35; cursor: not-allowed; }

    .pc-help { display: block; margin-top: 8px; font-size: 10.5px; line-height: 1.5; color: var(--muted); }
    .pc-help.warn { color: #ff9ca0; }

    .pc-summary { border-top: 1px dashed var(--border); padding-top: 14px; display: grid; gap: 9px; }
    .pc-row { display: flex; justify-content: space-between; gap: 12px; font-size: 12px; color: var(--muted); }
    .pc-row strong { color: #e8e8ec; font-variant-numeric: tabular-nums; text-align: right; overflow-wrap: anywhere; }
    .pc-total {
        display: flex; justify-content: space-between; align-items: baseline; gap: 12px;
        margin-top: 4px; padding: 14px;
        border: 1px solid rgba(123, 224, 163, .25); border-radius: 12px;
        background: rgba(123, 224, 163, .06);
    }
    .pc-total span { font-size: 10px; font-weight: 800; letter-spacing: .7px; text-transform: uppercase; color: var(--muted); }
    .pc-total strong { font-size: 28px; color: var(--green); font-variant-numeric: tabular-nums; }
    .pc-total.bump strong { animation: pcBump .25s ease; }

    @keyframes pcBump { 50% { transform: scale(1.06); } }

    .pc-cancel { text-align: center; font-size: 11px; color: var(--muted); text-decoration: none; }
    .pc-cancel:hover { color: #fff; }

    /* ---------- Responsive ---------- */
    @media (max-width: 1000px) {
        .pc-layout { grid-template-columns: 1fr; }
        .pc-ticket { position: static; }
        .pc-product-grid { max-height: 440px; }
    }
    @media (max-width: 560px) {
        .pc-header, .pc-panel { padding: 14px; }
        .pc-header h1 { font-size: 19px; }
        .pc-header .pc-button { width: 100%; }
        .pc-search-wrap { width: 100%; }
        .pc-product-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .pc-product-image { height: 92px; }
    }
    @media (prefers-reduced-motion: reduce) {
        .pesaje-create * { transition: none !important; animation: none !important; }
    }
</style>
@endpush

@section('content')
@php
    $categorias = $productos->pluck('categoria')->filter()->unique()->sort()->values();
@endphp

<div class="pesaje-create">
    <header class="pc-header">
        <div class="pc-heading">
            <div class="pc-heading-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3v3"/><path d="M5 7h14"/>
                    <path d="M7 7 4 16h6L7 7Z"/><path d="m17 7-3 9h6l-3-9Z"/>
                    <path d="M12 6v13"/><path d="M8 21h8"/>
                </svg>
            </div>
            <div>
                <span class="pc-eyebrow">Operaciones · Pesaje</span>
                <h1>Registrar producto pesado</h1>
                <p>Elige el producto, ingresa el peso y genera la etiqueta.</p>
            </div>
        </div>

        <a href="{{ route('pesaje.index') }}" class="pc-button">Ver historial</a>
    </header>

    @if (session('success'))
        <div class="pc-alert" role="status">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="pc-error" role="alert">
            <strong>Revisa los datos ingresados:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($productos->isEmpty())
        <div class="pc-alert" role="status">
            No hay productos pesables activos con precio vigente. Verifica que el tipo del
            producto tenga activada la opción <strong>Permite pesaje</strong> y que el
            producto tenga un precio de venta vigente.
        </div>
    @else
        <form action="{{ route('pesaje.store') }}" method="POST" id="formPesaje" novalidate>
            @csrf
            <input type="hidden" name="producto_id" id="producto_id" value="{{ old('producto_id') }}">

            <div class="pc-layout">
                {{-- ============ Selector de producto ============ --}}
                <section class="pc-panel">
                    <div class="pc-panel-head">
                        <div>
                            <h2>1. Selecciona el producto</h2>
                            <p>Solo aparecen productos configurados para venderse por peso.</p>
                        </div>

                        <div class="pc-search-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                            </svg>
                            <input type="search" id="buscarProducto" class="pc-search"
                                   placeholder="Buscar nombre o código" autocomplete="off"
                                   aria-label="Buscar producto">
                            <kbd aria-hidden="true">/</kbd>
                        </div>
                    </div>

                    @if ($categorias->count() > 1)
                        <div class="pc-chips" id="chipsCategoria" role="group" aria-label="Filtrar por categoría">
                            <button type="button" class="pc-chip" data-categoria="" aria-pressed="true">Todas</button>
                            @foreach ($categorias as $cat)
                                <button type="button" class="pc-chip"
                                        data-categoria="{{ mb_strtolower($cat, 'UTF-8') }}"
                                        aria-pressed="false">{{ $cat }}</button>
                            @endforeach
                        </div>
                    @endif

                    <p class="pc-count" id="contador" aria-live="polite"></p>

                    <div class="pc-product-grid" id="productosGrid">
                        @foreach ($productos as $producto)
                            <div class="pc-product-option"
                                 data-search="{{ mb_strtolower($producto->nombre . ' ' . $producto->codigo_interno . ' ' . ($producto->categoria ?? ''), 'UTF-8') }}"
                                 data-categoria="{{ mb_strtolower($producto->categoria ?? '', 'UTF-8') }}">

                                <input type="radio"
                                       id="producto_{{ $producto->id }}"
                                       name="producto_opcion"
                                       value="{{ $producto->id }}"
                                       data-nombre="{{ $producto->nombre }}"
                                       data-precio="{{ $producto->precio_venta }}"
                                       data-unidad="{{ $producto->unidad }}"
                                       data-unidad-nombre="{{ $producto->unidad_nombre }}"
                                       @checked(old('producto_id') == $producto->id)>

                                <label for="producto_{{ $producto->id }}" class="pc-product-card">
                                    <span class="pc-check" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m5 12 5 5 9-10"/>
                                        </svg>
                                    </span>

                                    <div class="pc-product-image">
                                        @if ($producto->imagen_url)
                                            <img src="{{ $producto->imagen_url }}"
                                                 alt="{{ $producto->nombre }}"
                                                 loading="lazy" decoding="async">
                                        @endif
                                        <span class="pc-image-fallback" @if ($producto->imagen_url) hidden @endif>
                                            Sin imagen
                                        </span>
                                    </div>

                                    <div class="pc-product-content">
                                        <span class="pc-product-code">{{ $producto->codigo_interno }}</span>
                                        <span class="pc-product-name">{{ $producto->nombre }}</span>
                                        @if ($producto->categoria)
                                            <span class="pc-product-category">{{ $producto->categoria }}</span>
                                        @endif
                                        <span class="pc-product-price">
                                            Bs {{ number_format((float) $producto->precio_venta, 2, ',', '.') }}
                                            / {{ $producto->unidad }}
                                        </span>
                                    </div>
                                </label>
                            </div>
                        @endforeach

                        <div class="pc-empty" id="sinResultados" hidden>
                            No se encontraron productos con ese filtro.
                        </div>
                    </div>
                </section>

                {{-- ============ Ticket / peso ============ --}}
                <aside class="pc-panel pc-ticket" id="ticket">
                    <div class="pc-panel-head" style="margin-bottom:0">
                        <div>
                            <h2>2. Registra el peso</h2>
                            <p>El importe se calcula con el precio vigente.</p>
                        </div>
                    </div>

                    <div class="pc-selected" id="seleccionado">
                        <div class="pc-selected-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 8 12 3 3 8v8l9 5 9-5V8Z"/><path d="m3 8 9 5 9-5"/><path d="M12 13v8"/>
                            </svg>
                        </div>
                        <div class="pc-selected-info">
                            <small>Producto seleccionado</small>
                            <strong id="productoResumen">Ninguno</strong>
                            <span id="unidadResumen">Elige un producto de la lista</span>
                        </div>
                    </div>

                    <div>
                        <label class="pc-weight-label" for="peso" id="pesoLabel">Peso registrado</label>

                        <div class="pc-weight-field">
                            <input type="number" name="peso" id="peso"
                                   value="{{ old('peso') }}"
                                   min="0.001" max="999999999.999" step="0.001"
                                   inputmode="decimal" placeholder="0.000"
                                   autocomplete="off" disabled>
                            <span class="pc-weight-unit" id="unidadSufijo">—</span>
                        </div>

                        <div class="pc-quick" id="rapidos" aria-label="Atajos de peso">
                            <button type="button" data-add="0.1" disabled>+0.1</button>
                            <button type="button" data-add="0.25" disabled>+0.25</button>
                            <button type="button" data-add="0.5" disabled>+0.5</button>
                            <button type="button" data-add="1" disabled>+1</button>
                            <button type="button" data-clear disabled>Limpiar</button>
                        </div>

                        <small class="pc-help" id="pesoAyuda">
                            Selecciona un producto para habilitar el peso.
                        </small>
                    </div>

                    <div class="pc-summary" aria-live="polite">
                        <div class="pc-row"><span>Precio unitario</span><strong id="precioResumen">Bs 0,00</strong></div>
                        <div class="pc-row"><span>Peso</span><strong id="pesoResumen">0,000</strong></div>
                        <div class="pc-total" id="totalBox">
                            <span>Total</span>
                            <strong id="totalResumen">Bs 0,00</strong>
                        </div>
                    </div>

                    <button type="submit" class="pc-button primary" id="btnGuardar" disabled>
                        Registrar pesaje y generar etiqueta
                    </button>

                    <a href="{{ route('pesaje.index') }}" class="pc-cancel">Cancelar</a>
                </aside>
            </div>
        </form>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = (id) => document.getElementById(id);

    const form = $('formPesaje');
    if (!form) return;

    const grid = $('productosGrid');
    const productoId = $('producto_id');
    const pesoInput = $('peso');
    const buscar = $('buscarProducto');
    const chips = $('chipsCategoria');
    const contador = $('contador');
    const sinResultados = $('sinResultados');
    const btnGuardar = $('btnGuardar');
    const rapidos = $('rapidos');
    const ticket = $('ticket');
    const totalBox = $('totalBox');
    const opciones = Array.from(grid.querySelectorAll('.pc-product-option'));

    const el = {
        seleccionado: $('seleccionado'),
        producto: $('productoResumen'),
        unidad: $('unidadResumen'),
        sufijo: $('unidadSufijo'),
        label: $('pesoLabel'),
        ayuda: $('pesoAyuda'),
        precio: $('precioResumen'),
        peso: $('pesoResumen'),
        total: $('totalResumen'),
    };

    const fmt = (v, d) => (Number(v) || 0).toLocaleString('es-BO', {
        minimumFractionDigits: d, maximumFractionDigits: d
    });
    const bs = (v) => 'Bs ' + fmt(v, 2);

    let precio = 0;
    let unidad = '';
    let categoriaActiva = '';
    let ultimoTotal = null;

    /* ---------- Cálculo ---------- */
    function recalcular() {
        const peso = Number(pesoInput.value) || 0;
        const total = Math.round((peso * precio + Number.EPSILON) * 100) / 100;

        el.peso.textContent = fmt(peso, 3) + (unidad ? ' ' + unidad : '');
        el.total.textContent = bs(total);

        if (ultimoTotal !== null && total !== ultimoTotal && total > 0) {
            totalBox.classList.remove('bump');
            void totalBox.offsetWidth;
            totalBox.classList.add('bump');
        }
        ultimoTotal = total;

        btnGuardar.disabled = !(productoId.value && peso > 0);
    }

    /* ---------- Selección ---------- */
    function seleccionar(radio, enfocar) {
        if (!radio) return;

        const d = radio.dataset;
        productoId.value = radio.value;
        precio = Number(d.precio) || 0;
        unidad = d.unidad || '';

        el.seleccionado.classList.add('has-product');
        el.producto.textContent = d.nombre || 'Producto';
        el.unidad.textContent = (d.unidadNombre || unidad || 'Sin unidad')
            + (unidad && d.unidadNombre ? ' (' + unidad + ')' : '');
        el.precio.textContent = bs(precio) + (unidad ? ' / ' + unidad : '');
        el.sufijo.textContent = unidad || '—';
        el.label.textContent = unidad ? 'Peso registrado (' + unidad + ')' : 'Peso registrado';
        el.ayuda.textContent = unidad
            ? 'Ingresa el peso en ' + unidad + '. Admite hasta 3 decimales.'
            : 'Ingresa el peso medido del producto.';
        el.ayuda.classList.remove('warn');

        pesoInput.disabled = false;
        rapidos.querySelectorAll('button').forEach((b) => (b.disabled = false));

        recalcular();

        if (enfocar) {
            if (window.matchMedia('(max-width: 1000px)').matches) {
                ticket.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            pesoInput.focus({ preventScroll: true });
            pesoInput.select();
        }
    }

    grid.addEventListener('change', function (e) {
        if (e.target.matches('input[name="producto_opcion"]')) {
            seleccionar(e.target, true);
        }
    });

    /* Imágenes rotas (error no burbujea → captura) */
    grid.addEventListener('error', function (e) {
        const img = e.target;
        if (img.tagName !== 'IMG') return;
        img.style.display = 'none';
        const fb = img.parentElement.querySelector('.pc-image-fallback');
        if (fb) fb.hidden = false;
    }, true);

    /* ---------- Filtros ---------- */
    function filtrar() {
        const texto = buscar.value.trim().toLocaleLowerCase('es');
        let visibles = 0;

        opciones.forEach(function (op) {
            const ok = (!categoriaActiva || op.dataset.categoria === categoriaActiva)
                && (!texto || op.dataset.search.includes(texto));
            op.hidden = !ok;
            if (ok) visibles++;
        });

        sinResultados.hidden = visibles > 0;
        contador.textContent = visibles + ' de ' + opciones.length + ' productos';
        return visibles;
    }

    let t;
    buscar.addEventListener('input', function () {
        clearTimeout(t);
        t = setTimeout(filtrar, 90);
    });

    buscar.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            buscar.value = '';
            filtrar();
        }
        if (e.key === 'Enter') {
            e.preventDefault();
            const unico = opciones.filter((o) => !o.hidden);
            if (unico.length === 1) {
                const r = unico[0].querySelector('input');
                r.checked = true;
                seleccionar(r, true);
            }
        }
    });

    chips?.addEventListener('click', function (e) {
        const chip = e.target.closest('.pc-chip');
        if (!chip) return;
        categoriaActiva = chip.dataset.categoria;
        chips.querySelectorAll('.pc-chip').forEach((c) =>
            c.setAttribute('aria-pressed', c === chip ? 'true' : 'false'));
        filtrar();
    });

    /* Atajo "/" para buscar */
    document.addEventListener('keydown', function (e) {
        const tag = (e.target.tagName || '').toLowerCase();
        if (e.key === '/' && !['input', 'textarea', 'select'].includes(tag)) {
            e.preventDefault();
            buscar.focus();
        }
    });

    /* ---------- Peso ---------- */
    pesoInput.addEventListener('input', recalcular);

    rapidos.addEventListener('click', function (e) {
        const btn = e.target.closest('button');
        if (!btn || btn.disabled) return;

        if (btn.hasAttribute('data-clear')) {
            pesoInput.value = '';
        } else {
            const actual = Number(pesoInput.value) || 0;
            pesoInput.value = (Math.round((actual + Number(btn.dataset.add)) * 1000) / 1000).toFixed(3);
        }
        recalcular();
        pesoInput.focus();
    });

    /* ---------- Envío ---------- */
    form.addEventListener('submit', function (e) {
        const peso = Number(pesoInput.value) || 0;

        if (!productoId.value || peso <= 0) {
            e.preventDefault();
            el.ayuda.textContent = !productoId.value
                ? 'Primero selecciona un producto.'
                : 'Ingresa un peso mayor a 0.';
            el.ayuda.classList.add('warn');
            return;
        }

        btnGuardar.disabled = true;
        btnGuardar.textContent = 'Registrando…';
    });

    /* Al volver con "atrás" el botón no debe quedar bloqueado */
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            btnGuardar.textContent = 'Registrar pesaje y generar etiqueta';
            recalcular();
        }
    });

    /* ---------- Estado inicial (old input) ---------- */
    filtrar();

    const previo = grid.querySelector('input[name="producto_opcion"]:checked');
    if (previo) {
        seleccionar(previo, false);
        previo.closest('.pc-product-option').scrollIntoView({ block: 'nearest' });
    }
});
</script>
@endsection