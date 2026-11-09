@extends('pesaje.layouts.app')

@section('title', 'Etiqueta de pesaje')

@push('styles')
<style>
    .etiqueta-page {
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

        max-width: 520px;
        margin: 0 auto;
        color: var(--text);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .etiqueta-page *,
    .etiqueta-page *::before,
    .etiqueta-page *::after { box-sizing: border-box; }

    /* ---------- Barra superior ---------- */
    .etiqueta-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .etiqueta-actions-right { display: flex; gap: 8px; flex-wrap: wrap; }

    .etiqueta-button {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 15px;
        border: 1px solid var(--border);
        border-radius: 10px;
        color: #eee;
        background: var(--surface-2);
        font: inherit;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: background .15s, border-color .15s, transform .15s;
    }
    .etiqueta-button svg { width: 15px; height: 15px; }
    .etiqueta-button:hover { border-color: var(--border-hover); background: #2a2d35; }
    .etiqueta-button:active { transform: scale(.98); }
    .etiqueta-button:focus-visible,
    .etiqueta-select:focus-visible { outline: 2px solid var(--red-soft); outline-offset: 2px; }

    .etiqueta-button.primary { border-color: var(--red); background: var(--red); color: #fff; }
    .etiqueta-button.primary:hover { background: #f2262e; }

    /* ---------- Etiqueta (tipo ticket) ---------- */
    .etiqueta-card {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: 16px;
        background: var(--bg);
    }

    .etiqueta-card::before {
        content: "";
        position: absolute;
        inset: 0 0 auto 0;
        height: 3px;
        background: linear-gradient(90deg, var(--red), transparent);
    }

    .etiqueta-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        padding: 22px 22px 0;
    }

    .etiqueta-numero {
        color: var(--red-soft);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .etiqueta-estado {
        padding: 4px 10px;
        border: 1px solid rgba(123, 224, 163, .3);
        border-radius: 999px;
        color: var(--green);
        background: rgba(123, 224, 163, .08);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .5px;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .etiqueta-estado.anulado,
    .etiqueta-estado.cancelado { color: #ff9ca0; border-color: rgba(227, 27, 35, .4); background: rgba(227, 27, 35, .08); }
    .etiqueta-estado.impreso,
    .etiqueta-estado.vendido { color: #f3d38a; border-color: rgba(243, 211, 138, .3); background: rgba(243, 211, 138, .07); }

    .etiqueta-producto { padding: 8px 22px 0; }
    .etiqueta-producto h1 {
        margin: 0;
        color: #fff;
        font-size: 22px;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }
    .etiqueta-codigo-prod { margin-top: 6px; color: var(--muted); font-size: 12px; font-family: ui-monospace, Menlo, Consolas, monospace; }

    /* Total destacado */
    .etiqueta-total-box {
        margin: 18px 22px 0;
        padding: 16px 18px;
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 12px;
        border: 1px solid rgba(123, 224, 163, .25);
        border-radius: 14px;
        background: rgba(123, 224, 163, .06);
    }
    .etiqueta-total-box span {
        color: var(--muted);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .8px;
        text-transform: uppercase;
    }
    .etiqueta-total-box strong {
        color: var(--green);
        font-size: 34px;
        line-height: 1;
        font-variant-numeric: tabular-nums;
    }

    /* Datos */
    .etiqueta-info {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        padding: 14px 22px 0;
    }
    .etiqueta-dato {
        padding: 11px 12px;
        border: 1px solid var(--border);
        border-radius: 11px;
        background: var(--surface);
        min-width: 0;
    }
    .etiqueta-dato span {
        display: block;
        margin-bottom: 4px;
        color: var(--muted);
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
    }
    .etiqueta-dato strong {
        display: block;
        color: #fff;
        font-size: 13px;
        font-variant-numeric: tabular-nums;
        overflow-wrap: anywhere;
    }

    /* Perforación */
    .etiqueta-cut {
        position: relative;
        margin: 20px 0 0;
        border-top: 2px dashed var(--border);
    }
    .etiqueta-cut::before,
    .etiqueta-cut::after {
        content: "";
        position: absolute;
        top: -11px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--page-bg, #0e0f13);
        border: 1px solid var(--border);
    }
    .etiqueta-cut::before { left: -11px; }
    .etiqueta-cut::after { right: -11px; }

    /* Código de barras */
    .etiqueta-barcode {
        margin: 0;
        padding: 18px 22px 22px;
        text-align: center;
    }
    .etiqueta-barcode-box {
        padding: 14px 12px 10px;
        border-radius: 12px;
        background: #fff;
    }
    .etiqueta-barcode svg { display: block; max-width: 100%; height: auto; margin: 0 auto; }
    .etiqueta-codigo {
        margin-top: 4px;
        color: #111;
        font-family: ui-monospace, Menlo, Consolas, monospace;
        font-size: 14px;
        letter-spacing: 3px;
    }

    /* ---------- Opciones de impresión ---------- */
    .etiqueta-print-opts {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        padding: 12px 14px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: var(--surface);
        font-size: 12px;
        color: var(--muted);
    }
    .etiqueta-select {
        min-height: 38px;
        padding: 0 10px;
        border: 1px solid var(--border);
        border-radius: 9px;
        color: #eee;
        background: var(--surface-2);
        font: inherit;
        font-size: 12px;
        font-weight: 700;
    }

    .etiqueta-toast {
        position: fixed;
        left: 50%;
        bottom: 24px;
        transform: translate(-50%, 20px);
        padding: 10px 16px;
        border: 1px solid var(--border);
        border-radius: 10px;
        color: #fff;
        background: #22242a;
        font-size: 12px;
        font-weight: 700;
        opacity: 0;
        pointer-events: none;
        transition: opacity .2s, transform .2s;
        z-index: 50;
    }
    .etiqueta-toast.show { opacity: 1; transform: translate(-50%, 0); }

    /* ---------- Responsive ---------- */
    @media (max-width: 540px) {
        .etiqueta-info { grid-template-columns: 1fr 1fr; }
        .etiqueta-total-box strong { font-size: 28px; }
        .etiqueta-actions > *,
        .etiqueta-actions-right { width: 100%; }
        .etiqueta-actions-right .etiqueta-button { flex: 1; }
        .etiqueta-actions > .etiqueta-button { width: 100%; }
    }

    /* ---------- Impresión ---------- */
    @media print {
        html, body { background: #fff !important; }

        body * { visibility: hidden !important; }

        .etiqueta-card,
        .etiqueta-card * { visibility: visible !important; }

        .etiqueta-card {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            max-width: none;
            border: 1px solid #222;
            border-radius: 0;
            color: #111;
            background: #fff;
            break-inside: avoid;
        }
        .etiqueta-card::before { display: none; }

        .etiqueta-head { padding: 3mm 3mm 0; }
        .etiqueta-producto { padding: 1mm 3mm 0; }
        .etiqueta-producto h1 { color: #111; font-size: 13pt; }
        .etiqueta-codigo-prod { color: #333; font-size: 8pt; }
        .etiqueta-numero { color: #111 !important; font-size: 8pt; }

        .etiqueta-estado { display: none !important; }

        .etiqueta-total-box {
            margin: 2mm 3mm 0;
            padding: 2mm 3mm;
            border: 1.5px solid #111;
            border-radius: 2mm;
            background: #fff;
        }
        .etiqueta-total-box span { color: #333; font-size: 7pt; }
        .etiqueta-total-box strong { color: #111; font-size: 20pt; }

        .etiqueta-info { padding: 2mm 3mm 0; gap: 1.5mm; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .etiqueta-dato { padding: 1.5mm 2mm; border: 1px solid #bbb; border-radius: 1.5mm; background: #fff; }
        .etiqueta-dato span { color: #444; font-size: 6pt; margin-bottom: 1px; }
        .etiqueta-dato strong { color: #111; font-size: 9pt; }
        .etiqueta-dato.no-print { display: none !important; }

        .etiqueta-cut { margin-top: 2mm; border-top: 1px dashed #777; }
        .etiqueta-cut::before,
        .etiqueta-cut::after { display: none; }

        .etiqueta-barcode { padding: 2mm 3mm 3mm; }
        .etiqueta-barcode-box { padding: 0; }
        .etiqueta-codigo { font-size: 9pt; letter-spacing: 2px; }

        .etiqueta-actions,
        .etiqueta-print-opts,
        .etiqueta-toast { display: none !important; }
    }
</style>

{{-- @page dinámico (se reemplaza desde JS según el tamaño elegido) --}}
<style id="pageSize">@page { margin: 6mm; }</style>
@endpush

@section('content')
@php
    $estado = strtolower($registro->estado ?? 'generado');
    $fecha = $registro->fecha
        ? \Illuminate\Support\Carbon::parse($registro->fecha)->format('d/m/Y H:i')
        : '—';
@endphp

<div class="etiqueta-page">
    <div class="etiqueta-actions">
        <a href="{{ route('pesaje.index') }}" class="etiqueta-button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            Historial
        </a>

        <div class="etiqueta-actions-right">
            <a href="{{ route('pesaje.create') }}" class="etiqueta-button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Nuevo pesaje
            </a>

            <button type="button" class="etiqueta-button primary" id="btnImprimir">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M6 9V3h12v6"/><rect x="6" y="14" width="12" height="7" rx="1"/>
                    <path d="M6 18H4a1 1 0 0 1-1-1v-6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6a1 1 0 0 1-1 1h-2"/>
                </svg>
                Imprimir
            </button>
        </div>
    </div>

    <article class="etiqueta-card">
        <div class="etiqueta-head">
            <span class="etiqueta-numero">{{ $registro->numero_pesaje }}</span>
            <span class="etiqueta-estado {{ $estado }}">{{ ucfirst($estado) }}</span>
        </div>

        <div class="etiqueta-producto">
            <h1>{{ $registro->producto }}</h1>
            <div class="etiqueta-codigo-prod">{{ $registro->codigo_interno }}</div>
        </div>

        <div class="etiqueta-total-box">
            <span>Total a cobrar</span>
            <strong>Bs {{ number_format((float) $registro->importe, 2, ',', '.') }}</strong>
        </div>

        <div class="etiqueta-info">
            <div class="etiqueta-dato">
                <span>Peso</span>
                <strong>
                    {{ number_format((float) $registro->peso, 3, ',', '.') }}
                    {{ $registro->unidad }}
                </strong>
            </div>

            <div class="etiqueta-dato">
                <span>Precio / {{ $registro->unidad }}</span>
                <strong>Bs {{ number_format((float) $registro->precio_por_unidad, 2, ',', '.') }}</strong>
            </div>

            <div class="etiqueta-dato">
                <span>Fecha</span>
                <strong>{{ $fecha }}</strong>
            </div>
        </div>

        <div class="etiqueta-cut" aria-hidden="true"></div>

        <div class="etiqueta-barcode">
            <div class="etiqueta-barcode-box">
                <svg id="codigoBarras" role="img" aria-label="Código de barras {{ $registro->codigo_barra }}"></svg>
                <div class="etiqueta-codigo">{{ $registro->codigo_barra }}</div>
            </div>
        </div>
    </article>

    <div class="etiqueta-print-opts">
        <label for="tamanoEtiqueta">Tamaño de impresión</label>
        <select id="tamanoEtiqueta" class="etiqueta-select">
            <option value="58">Térmica 58 mm</option>
            <option value="80">Térmica 80 mm</option>
            <option value="a6">A6</option>
            <option value="a4">A4 / Carta (normal)</option>
        </select>
    </div>
</div>

<div class="etiqueta-toast" id="toast" role="status" aria-live="polite"></div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const codigo = @json((string) $registro->codigo_barra);
    const svg = document.getElementById('codigoBarras');
    const toast = document.getElementById('toast');
    const selectTamano = document.getElementById('tamanoEtiqueta');
    const pageStyle = document.getElementById('pageSize');
    const btnImprimir = document.getElementById('btnImprimir');

    function aviso(texto) {
        toast.textContent = texto;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2200);
    }

    /* ---------- Código de barras (EAN13 y, si no es válido, CODE128) ---------- */
    function dibujarBarras() {
        if (!window.JsBarcode) {
            svg.outerHTML = '<p style="color:#111;font-family:monospace;margin:0">No se pudo cargar el generador de barras.</p>';
            return;
        }

        const opciones = { displayValue: false, height: 64, margin: 4, width: 2 };

        try {
            JsBarcode(svg, codigo, Object.assign({ format: 'EAN13' }, opciones));
        } catch (e) {
            try {
                JsBarcode(svg, codigo, Object.assign({ format: 'CODE128' }, opciones));
            } catch (e2) {
                svg.outerHTML = '<p style="color:#111;font-family:monospace;margin:0">Código no válido para barras.</p>';
            }
        }
    }
    dibujarBarras();

    /* ---------- Tamaño de impresión ---------- */
    const tamanos = {
        '58':  '@page { size: 58mm auto; margin: 0; }',
        '80':  '@page { size: 80mm auto; margin: 0; }',
        'a6':  '@page { size: A6; margin: 6mm; }',
        'a4':  '@page { margin: 10mm; }'
    };

    function aplicarTamano(valor) {
        pageStyle.textContent = tamanos[valor] || tamanos.a4;
        try { localStorage.setItem('pesaje_tamano_etiqueta', valor); } catch (e) {}
    }

    let guardado = '58';
    try { guardado = localStorage.getItem('pesaje_tamano_etiqueta') || '58'; } catch (e) {}
    if (!tamanos[guardado]) guardado = '58';

    selectTamano.value = guardado;
    aplicarTamano(guardado);

    selectTamano.addEventListener('change', function () {
        aplicarTamano(selectTamano.value);
    });

    /* ---------- Imprimir ---------- */
    btnImprimir.addEventListener('click', function () {
        window.print();
    });

    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'p') {
            e.preventDefault();
            window.print();
        }
    });

    /* Autoimpresión opcional: /pesajes/{id}?imprimir=1 */
    if (new URLSearchParams(location.search).get('imprimir') === '1') {
        setTimeout(() => window.print(), 400);
    }
});
</script>
@endsection