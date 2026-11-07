@extends('admin.layouts.app')

@section('title', 'Registrar salida de inventario')

@push('styles')
<style>
    .salida-page {
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
        --amber: #f5b942;
        --radius: 16px;
        --radius-sm: 10px;

        display: flex;
        flex-direction: column;
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
        color: var(--text);
        font-variant-numeric: tabular-nums;
    }

    .salida-page *,
    .salida-page *::before,
    .salida-page *::after { box-sizing: border-box; }

    /* ---------- Header ---------- */
    .salida-header {
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
            radial-gradient(ellipse at 0% 130%, rgba(245, 185, 66, .07), transparent 45%),
            var(--bg);
    }

    .salida-header::before {
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

    .salida-header > * { position: relative; z-index: 1; }

    .salida-eyebrow {
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

    .salida-eyebrow::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--red);
        box-shadow: 0 0 10px var(--red);
    }

    .salida-header h1 {
        margin: 0 0 8px;
        color: #fff;
        font-size: clamp(25px, 3vw, 31px);
        font-weight: 800;
        letter-spacing: -.7px;
        line-height: 1.05;
    }

    .salida-header p {
        max-width: 560px;
        margin: 0;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.6;
    }

    /* ---------- Botones ---------- */
    .salida-link,
    .salida-button {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 17px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        color: #dedee4;
        background: var(--panel-2);
        box-shadow: 0 1px 0 rgba(255,255,255,.04) inset;
        font: inherit;
        font-size: 12px;
        font-weight: 750;
        text-decoration: none;
        cursor: pointer;
        transition: transform .15s, background .15s, border-color .15s, color .15s, filter .15s, box-shadow .15s;
    }

    .salida-link svg,
    .salida-button svg { width: 14px; height: 14px; flex-shrink: 0; }

    .salida-link.volver svg { transition: transform .15s; }
    .salida-link.volver:hover svg { transform: translateX(-3px); }

    .salida-link:hover,
    .salida-button:hover {
        color: #fff;
        background: #2a2d37;
        border-color: #41454f;
    }

    .salida-link:focus-visible,
    .salida-button:focus-visible,
    .salida-quitar:focus-visible,
    .salida-max:focus-visible {
        outline: 2px solid var(--red-light);
        outline-offset: 2px;
    }

    .salida-button.primary {
        min-width: 180px;
        border-color: var(--red);
        color: #fff;
        background: linear-gradient(180deg, #e6272f 0%, #c91820 100%);
        box-shadow: 0 1px 0 rgba(255,255,255,.14) inset, 0 8px 20px -9px rgba(227,27,35,.7);
    }

    .salida-button.primary:hover {
        transform: translateY(-1px);
        filter: brightness(1.08);
        border-color: var(--red);
        background: linear-gradient(180deg, #e6272f 0%, #c91820 100%);
        box-shadow: 0 1px 0 rgba(255,255,255,.2) inset, 0 12px 24px -9px rgba(227,27,35,.8);
    }

    .salida-button.primary:active { transform: translateY(0); }

    .salida-button:disabled {
        opacity: .55;
        cursor: not-allowed;
        transform: none;
        filter: none;
    }

    .salida-button.primary:disabled { cursor: wait; }

    /* ---------- Alertas ---------- */
    .salida-alerta {
        display: flex;
        gap: 12px;
        padding: 14px 16px;
        border: 1px solid rgba(227, 27, 35, .35);
        border-radius: var(--radius-sm);
        color: #ff9297;
        background: rgba(227, 27, 35, .07);
        font-size: 12.5px;
        line-height: 1.55;
    }

    .salida-alerta::before {
        content: "";
        flex-shrink: 0;
        width: 4px;
        border-radius: 4px;
        background: currentColor;
        opacity: .8;
    }

    .salida-alerta[hidden] { display: none; }
    .salida-alerta strong { color: #fff; }
    .salida-alerta ul { margin: 7px 0 0; padding-left: 20px; }
    .salida-alerta li + li { margin-top: 3px; }

    /* ---------- Layout ---------- */
    .salida-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 20px;
        align-items: start;
    }

    .salida-card {
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background: var(--bg);
    }

    .salida-seccion { padding: 24px; }

    .salida-seccion + .salida-seccion { border-top: 1px solid var(--border-soft); }

    .salida-seccion-titulo {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 20px;
    }

    .salida-numero {
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        display: grid;
        place-items: center;
        border: 1px solid rgba(227, 27, 35, .3);
        border-radius: 10px;
        color: var(--red-light);
        background: var(--red-soft);
        font-size: 13px;
        font-weight: 800;
    }

    .salida-seccion-titulo h2 {
        margin: 0 0 4px;
        color: #f4f4f6;
        font-size: 16px;
        font-weight: 760;
        letter-spacing: -.2px;
    }

    .salida-seccion-titulo p {
        max-width: 600px;
        margin: 0;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.55;
    }

    /* ---------- Campos ---------- */
    .salida-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .salida-campo { min-width: 0; }
    .salida-campo.completo { grid-column: 1 / -1; }

    .salida-campo label,
    .salida-label {
        display: block;
        margin-bottom: 7px;
        color: #b9bbc4;
        font-size: 10.5px;
        font-weight: 750;
        letter-spacing: .5px;
        text-transform: uppercase;
    }

    .salida-req { color: var(--red-light); }

    .salida-control {
        width: 100%;
        min-height: 44px;
        padding: 0 13px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        outline: none;
        color: var(--text);
        background: var(--panel-2);
        color-scheme: dark;
        font: inherit;
        font-size: 12.5px;
        transition: border-color .15s, box-shadow .15s, background .15s, opacity .15s;
    }

    .salida-control::placeholder { color: #70737f; }
    .salida-control:hover:not(:disabled) { border-color: #3d414c; }

    .salida-control:focus {
        border-color: #c4252c;
        background: #24262e;
        box-shadow: 0 0 0 3px rgba(227, 27, 35, .14);
    }

    .salida-control:disabled {
        opacity: .55;
        cursor: not-allowed;
        border-style: dashed;
    }

    select.salida-control {
        appearance: none;
        -webkit-appearance: none;
        padding-right: 36px;
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%239a9ca6' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
    }

    select.salida-control option {
        color: var(--text);
        background: var(--panel-2);
    }

    .salida-ayuda {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-top: 7px;
        color: var(--muted-2);
        font-size: 11px;
        line-height: 1.5;
    }

    .salida-contador { font-variant-numeric: tabular-nums; white-space: nowrap; }

    /* ---------- Líneas ---------- */
    .salida-lineas {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .salida-linea {
        position: relative;
        overflow: hidden;
        padding: 18px 18px 18px 22px;
        border: 1px solid var(--border);
        border-radius: 14px;
        background:
            radial-gradient(ellipse at 100% 0%, rgba(255,255,255,.025), transparent 55%),
            linear-gradient(180deg, var(--panel) 0%, var(--bg) 100%);
        animation: salida-entrada .25s ease;
        transition: border-color .15s, box-shadow .15s;
    }

    .salida-linea::before {
        content: "";
        position: absolute;
        top: 0; bottom: 0; left: 0;
        width: 3px;
        background: linear-gradient(180deg, var(--red), rgba(227, 27, 35, .25));
    }

    .salida-linea.invalida {
        border-color: rgba(227, 27, 35, .55);
        box-shadow: 0 0 0 3px rgba(227, 27, 35, .1);
    }

    @keyframes salida-entrada {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .salida-linea-cabecera {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .salida-linea-titulo {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .salida-linea-badge {
        flex-shrink: 0;
        padding: 4px 10px;
        border: 1px solid var(--border);
        border-radius: 999px;
        color: #c4c6ce;
        background: var(--panel-2);
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: .3px;
        white-space: nowrap;
    }

    .linea-nombre {
        overflow: hidden;
        color: #f2f2f4;
        font-size: 13px;
        font-weight: 750;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .salida-quitar {
        flex-shrink: 0;
        min-height: 32px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0 11px;
        border: 1px solid rgba(227, 27, 35, .3);
        border-radius: 8px;
        color: #ff9297;
        background: rgba(227, 27, 35, .07);
        font: inherit;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: background .15s, border-color .15s, color .15s;
    }

    .salida-quitar svg { width: 12px; height: 12px; }

    .salida-quitar:hover:not(:disabled) {
        color: #fff;
        border-color: rgba(227, 27, 35, .5);
        background: rgba(227, 27, 35, .18);
    }

    .salida-quitar:disabled {
        opacity: .35;
        cursor: not-allowed;
    }

    .salida-linea-cuerpo {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 14px;
    }

    .salida-linea-cuerpo .c-producto { grid-column: span 7; }
    .salida-linea-cuerpo .c-lote     { grid-column: span 5; }
    .salida-linea-cuerpo .c-cantidad { grid-column: span 5; }
    .salida-linea-cuerpo .c-info     { grid-column: span 7; }

    /* Cantidad con unidad y botón "Todo" */
    .salida-cantidad-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .salida-cantidad-wrap .salida-control {
        padding-right: 112px;
        font-weight: 750;
        font-size: 14px;
    }

    .cantidad-unidad {
        position: absolute;
        right: 62px;
        max-width: 44px;
        overflow: hidden;
        color: var(--muted-2);
        font-size: 11px;
        font-weight: 650;
        text-overflow: ellipsis;
        white-space: nowrap;
        pointer-events: none;
    }

    .salida-max {
        position: absolute;
        right: 6px;
        min-height: 30px;
        padding: 0 10px;
        border: 1px solid rgba(227, 27, 35, .3);
        border-radius: 7px;
        color: #ff9aa0;
        background: rgba(227, 27, 35, .09);
        font: inherit;
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: .3px;
        cursor: pointer;
        transition: background .15s, color .15s;
    }

    .salida-max:hover:not(:disabled) {
        color: #fff;
        background: rgba(227, 27, 35, .22);
    }

    .salida-max:disabled { opacity: .4; cursor: not-allowed; }

    /* Opciones rápidas de cantidad */
    .salida-rapidas {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }

    .salida-rapidas[hidden] { display: none; }

    .salida-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-height: 30px;
        padding: 0 11px;
        border: 1px solid var(--border);
        border-radius: 999px;
        color: #c4c6ce;
        background: var(--panel-2);
        font: inherit;
        font-size: 11px;
        font-weight: 750;
        cursor: pointer;
        transition: background .15s, border-color .15s, color .15s;
    }

    .salida-chip b {
        color: var(--muted-2);
        font-size: 10.5px;
        font-weight: 700;
    }

    .salida-chip:hover:not(:disabled) {
        color: #fff;
        border-color: rgba(227, 27, 35, .45);
        background: rgba(227, 27, 35, .1);
    }

    .salida-chip.activo {
        color: #fff;
        border-color: rgba(227, 27, 35, .6);
        background: rgba(227, 27, 35, .2);
    }

    .salida-chip.activo b { color: #ffb3b7; }
    .salida-chip:disabled { opacity: .35; cursor: not-allowed; }
    .salida-chip:focus-visible { outline: 2px solid var(--red-light); outline-offset: 2px; }

    /* Panel de información del lote */
    .salida-lote-info {
        --c: var(--muted-2);
        height: 100%;
        min-height: 44px;
        padding: 11px 14px;
        border: 1px solid color-mix(in srgb, var(--c) 28%, transparent);
        border-radius: var(--radius-sm);
        color: color-mix(in srgb, var(--c) 55%, #fff);
        background: color-mix(in srgb, var(--c) 7%, transparent);
        font-size: 11.5px;
        line-height: 1.55;
        transition: border-color .15s, background .15s, color .15s;
    }

    .salida-lote-info[data-estado="vacio"] { --c: #8a8c97; color: var(--muted); }
    .salida-lote-info[data-estado="ok"]    { --c: var(--green); }
    .salida-lote-info[data-estado="aviso"] { --c: var(--amber); }
    .salida-lote-info[data-estado="error"] { --c: #ff6b72; }

    .lote-info-texto p { margin: 0; }
    .lote-info-texto p + p { margin-top: 3px; }

    .salida-medidor {
        height: 5px;
        margin-top: 10px;
        overflow: hidden;
        border-radius: 999px;
        background: rgba(255,255,255,.07);
    }

    .salida-medidor[hidden] { display: none; }

    .salida-medidor > div {
        height: 100%;
        border-radius: 999px;
        background: var(--c);
        transition: width .25s ease;
    }

    /* Agregar línea */
    .salida-agregar {
        width: 100%;
        min-height: 48px;
        margin-top: 14px;
        border-style: dashed;
        border-color: #3a3d48;
        color: #c4c6ce;
        background: transparent;
        box-shadow: none;
    }

    .salida-agregar:hover:not(:disabled) {
        color: #fff;
        border-color: rgba(227, 27, 35, .5);
        background: rgba(227, 27, 35, .06);
    }

    /* ---------- Acciones ---------- */
    .salida-acciones {
        position: sticky;
        bottom: 0;
        z-index: 5;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding: 16px 24px;
        border-top: 1px solid var(--border);
        background: rgba(26, 28, 35, .92);
        -webkit-backdrop-filter: blur(8px);
        backdrop-filter: blur(8px);
    }

    .salida-acciones-derecha { display: flex; gap: 10px; flex-wrap: wrap; }

    /* ---------- Resumen lateral ---------- */
    .salida-aside {
        position: sticky;
        top: 16px;
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background:
            radial-gradient(ellipse at 100% 0%, rgba(227, 27, 35, .09), transparent 60%),
            linear-gradient(180deg, var(--panel) 0%, var(--bg) 100%);
    }

    .salida-aside-cabecera {
        padding: 18px 20px;
        border-bottom: 1px solid var(--border-soft);
    }

    .salida-aside-cabecera h2 {
        margin: 0 0 4px;
        color: #f4f4f6;
        font-size: 15px;
        font-weight: 760;
    }

    .salida-aside-cabecera p {
        margin: 0;
        color: var(--muted);
        font-size: 11.5px;
    }

    .salida-datos { margin: 0; padding: 6px 20px; }

    .salida-datos div {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        padding: 11px 0;
        border-bottom: 1px solid var(--border-soft);
        font-size: 12px;
    }

    .salida-datos div:last-child { border-bottom: 0; }
    .salida-datos dt { color: var(--muted); }

    .salida-datos dd {
        max-width: 60%;
        margin: 0;
        overflow: hidden;
        color: #f2f2f4;
        font-weight: 700;
        text-align: right;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .salida-lista-titulo {
        padding: 12px 20px 0;
        color: #8a8c97;
        border-top: 1px solid var(--border-soft);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .8px;
        text-transform: uppercase;
    }

    .salida-lista {
        margin: 0;
        padding: 8px 20px 18px;
        list-style: none;
    }

    .salida-lista li {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 2px 12px;
        padding: 10px 0;
        border-bottom: 1px dashed var(--border-soft);
    }

    .salida-lista li:last-child { border-bottom: 0; }

    .salida-lista strong {
        overflow: hidden;
        color: #f0f0f2;
        font-size: 12px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .salida-lista b {
        color: var(--amber);
        font-size: 12.5px;
        font-weight: 800;
        white-space: nowrap;
    }

    .salida-lista span {
        grid-column: 1 / -1;
        color: var(--muted-2);
        font-size: 10.5px;
        font-family: ui-monospace, "SF Mono", Menlo, monospace;
    }

    .salida-lista .vacio {
        display: block;
        padding: 14px 0 4px;
        color: var(--muted-2);
        font-size: 12px;
        text-align: center;
        font-family: inherit;
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 1020px) {
        .salida-layout { grid-template-columns: 1fr; }
        .salida-aside { display: none; }
    }

    @media (max-width: 760px) {
        .salida-linea-cuerpo .c-producto,
        .salida-linea-cuerpo .c-lote,
        .salida-linea-cuerpo .c-cantidad,
        .salida-linea-cuerpo .c-info { grid-column: 1 / -1; }
    }

    @media (max-width: 620px) {
        .salida-header {
            align-items: stretch;
            flex-direction: column;
            padding: 22px 18px;
        }

        .salida-header .salida-link { width: 100%; }
        .salida-seccion { padding: 18px 16px; }
        .salida-grid { grid-template-columns: 1fr; }
        .salida-campo.completo { grid-column: auto; }
        .salida-linea { padding: 16px 14px 16px 18px; }

        .salida-acciones {
            flex-direction: column-reverse;
            align-items: stretch;
            padding: 14px 16px;
        }

        .salida-acciones-derecha { width: 100%; }
        .salida-acciones > .salida-link,
        .salida-acciones-derecha > * { width: 100%; }
    }

    @media (prefers-reduced-motion: reduce) {
        .salida-page * {
            transition: none !important;
            animation: none !important;
        }
    }
</style>
@endpush

@section('content')
@php
    $datosAntiguos = old('items', []);
@endphp

<div class="salida-page">

    {{-- ========== Header ========== --}}
    <header class="salida-header">
        <div>
            <span class="salida-eyebrow">Inventario · Movimiento de salida</span>
            <h1>Registrar salida</h1>
            <p>Selecciona el almacén, el producto y el lote del que saldrá la cantidad.</p>
        </div>

        <a href="{{ route('admin.inventario.index') }}" class="salida-link volver">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Volver al inventario
        </a>
    </header>

    @if (session('error'))
        <div class="salida-alerta" role="alert">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="salida-alerta" role="alert">
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

    <div id="salida-js-error" class="salida-alerta" role="alert" tabindex="-1" hidden>
        <span id="salida-js-error-texto"></span>
    </div>

    <form
        id="form-salida"
        action="{{ route('admin.inventario.salida.store') }}"
        method="POST"
        autocomplete="off"
        novalidate
    >
        @csrf

        <div class="salida-layout">

            {{-- ========== Columna principal ========== --}}
            <div class="salida-card">

                {{-- 1. Datos de la salida --}}
                <section class="salida-seccion">
                    <div class="salida-seccion-titulo">
                        <span class="salida-numero">1</span>
                        <div>
                            <h2>Datos de la salida</h2>
                            <p>El tipo elegido se guardará como motivo del movimiento.</p>
                        </div>
                    </div>

                    <div class="salida-grid">
                        <div class="salida-campo">
                            <label for="almacen_id">Almacén de salida <span class="salida-req">*</span></label>
                            <select name="almacen_id" id="almacen_id" class="salida-control" required>
                                <option value="">Selecciona un almacén</option>
                                @foreach ($almacenes as $almacen)
                                    <option
                                        value="{{ $almacen->id }}"
                                        @selected((string) old('almacen_id') === (string) $almacen->id)
                                    >
                                        {{ $almacen->codigo }} — {{ $almacen->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="salida-campo">
                            <label for="tipo_movimiento_id">Tipo de salida <span class="salida-req">*</span></label>
                            <select
                                name="tipo_movimiento_id"
                                id="tipo_movimiento_id"
                                class="salida-control"
                                required
                            >
                                <option value="">Selecciona el tipo</option>
                                @foreach ($tiposSalida as $tipo)
                                    <option
                                        value="{{ $tipo->id }}"
                                        @selected((string) old('tipo_movimiento_id') === (string) $tipo->id)
                                    >
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="salida-campo completo">
                            <label for="motivo">Detalle o descripción <span class="salida-req">*</span></label>
                            <input
                                type="text"
                                name="motivo"
                                id="motivo"
                                class="salida-control"
                                value="{{ old('motivo') }}"
                                maxlength="255"
                                required
                                placeholder="Ej.: Productos dañados durante el almacenamiento"
                            >
                            <div class="salida-ayuda">
                                <span>Explica brevemente por qué sale la mercadería.</span>
                                <span class="salida-contador" id="motivo-contador">0 / 255</span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- 2. Productos y lotes --}}
                <section class="salida-seccion">
                    <div class="salida-seccion-titulo">
                        <span class="salida-numero">2</span>
                        <div>
                            <h2>Productos y lotes</h2>
                            <p>
                                Elige el producto y luego selecciona el lote específico.
                                Puedes agregar hasta cinco productos.
                            </p>
                        </div>
                    </div>

                    <div id="salida-lineas" class="salida-lineas"></div>

                    <button type="button" id="agregar-linea" class="salida-button salida-agregar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                        <span id="agregar-texto">Agregar otro producto</span>
                    </button>
                </section>

                <div class="salida-acciones">
                    <a href="{{ route('admin.inventario.index') }}" class="salida-link">
                        Cancelar
                    </a>

                    <div class="salida-acciones-derecha">
                        <button type="submit" id="guardar-salida" class="salida-button primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                            <span id="guardar-texto">Registrar salida</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ========== Resumen lateral ========== --}}
            <aside class="salida-aside" aria-label="Resumen de la salida" aria-live="polite">
                <div class="salida-aside-cabecera">
                    <h2>Resumen</h2>
                    <p>Revisa lote y cantidad antes de registrar.</p>
                </div>

                <dl class="salida-datos">
                    <div><dt>Almacén</dt><dd id="res-almacen">—</dd></div>
                    <div><dt>Tipo de salida</dt><dd id="res-tipo">—</dd></div>
                    <div><dt>Productos</dt><dd><span id="total-lineas">0</span> / 5</dd></div>
                </dl>

                <div class="salida-lista-titulo">Saldrá del inventario</div>
                <ul class="salida-lista" id="res-lista"></ul>
            </aside>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const existencias = {{ \Illuminate\Support\Js::from($existencias) }};
    const lotes = {{ \Illuminate\Support\Js::from($lotes) }};
    const itemsAntiguos = Object.values({{ \Illuminate\Support\Js::from($datosAntiguos) }} || {});
    const almacenAntiguo = {{ \Illuminate\Support\Js::from(old('almacen_id', '')) }};

    const MAX_LINEAS = 5;
    const DIAS_AVISO_VENCIMIENTO = 30;

    const almacenSelect = document.getElementById('almacen_id');
    const tipoSelect = document.getElementById('tipo_movimiento_id');
    const motivoInput = document.getElementById('motivo');
    const motivoContador = document.getElementById('motivo-contador');
    const lineasContenedor = document.getElementById('salida-lineas');
    const agregarBoton = document.getElementById('agregar-linea');
    const agregarTexto = document.getElementById('agregar-texto');
    const totalLineas = document.getElementById('total-lineas');
    const formulario = document.getElementById('form-salida');
    const guardarBoton = document.getElementById('guardar-salida');
    const guardarTexto = document.getElementById('guardar-texto');
    const bannerError = document.getElementById('salida-js-error');
    const bannerTexto = document.getElementById('salida-js-error-texto');
    const resAlmacen = document.getElementById('res-almacen');
    const resTipo = document.getElementById('res-tipo');
    const resLista = document.getElementById('res-lista');

    const formatoCantidad = (valor) =>
        Number(valor || 0).toLocaleString('es-BO', { maximumFractionDigits: 3 });

    /* ---------- Fechas ---------- */
    function soloFecha(valor) {
        const m = String(valor || '').match(/^(\d{4})-(\d{2})-(\d{2})/);
        return m ? `${m[1]}-${m[2]}-${m[3]}` : '';
    }

    function diasHasta(valor) {
        const f = soloFecha(valor);
        if (!f) return null;

        const [a, m, d] = f.split('-').map(Number);
        const fecha = new Date(a, m - 1, d, 12, 0, 0);
        const hoy = new Date();
        hoy.setHours(12, 0, 0, 0);

        return Math.round((fecha - hoy) / 86400000);
    }

    function fechaCorta(valor) {
        const f = soloFecha(valor);
        if (!f) return '';
        const [a, m, d] = f.split('-');
        return `${d}/${m}/${a}`;
    }

    /* ---------- Banner de error ---------- */
    function mostrarError(mensaje, campo = null, linea = null) {
        bannerTexto.textContent = mensaje;
        bannerError.hidden = false;

        lineasContenedor.querySelectorAll('.salida-linea.invalida')
            .forEach((l) => l.classList.remove('invalida'));

        if (linea) linea.classList.add('invalida');

        bannerError.scrollIntoView({ behavior: 'smooth', block: 'center' });

        if (campo && typeof campo.focus === 'function') {
            setTimeout(() => campo.focus({ preventScroll: true }), 250);
        } else {
            bannerError.focus({ preventScroll: true });
        }
    }

    function ocultarError() {
        bannerError.hidden = true;
        lineasContenedor.querySelectorAll('.salida-linea.invalida')
            .forEach((l) => l.classList.remove('invalida'));
    }

    /* ---------- Resumen lateral ---------- */
    function textoSeleccionado(select) {
        const opcion = select.selectedOptions[0];
        return opcion && opcion.value ? opcion.textContent.trim() : '—';
    }

    function actualizarResumen() {
        resAlmacen.textContent = textoSeleccionado(almacenSelect);
        resAlmacen.title = resAlmacen.textContent;
        resTipo.textContent = textoSeleccionado(tipoSelect);
        resTipo.title = resTipo.textContent;

        resLista.replaceChildren();

        let hay = false;

        lineasContenedor.querySelectorAll('.salida-linea').forEach((linea) => {
            const producto = linea.querySelector('.producto-select').selectedOptions[0];
            const lote = linea.querySelector('.lote-select').selectedOptions[0];
            const cantidad = Number(linea.querySelector('.cantidad-input').value || 0);

            if (!producto || !producto.value) return;

            hay = true;

            const li = document.createElement('li');
            const nombre = document.createElement('strong');
            const cant = document.createElement('b');
            const detalle = document.createElement('span');

            nombre.textContent = producto.dataset.nombre;
            nombre.title = producto.dataset.nombre;
            cant.textContent = cantidad > 0
                ? `− ${formatoCantidad(cantidad)} ${producto.dataset.unidad}`
                : '—';
            detalle.textContent = lote && lote.value
                ? `Lote ${lote.dataset.codigo}`
                : 'Lote sin seleccionar';

            li.append(nombre, cant, detalle);
            resLista.appendChild(li);
        });

        if (!hay) {
            const li = document.createElement('li');
            const vacio = document.createElement('span');
            vacio.className = 'vacio';
            vacio.textContent = 'Aún no hay productos seleccionados.';
            li.appendChild(vacio);
            resLista.appendChild(li);
        }
    }

    function actualizarContador() {
        const lineas = lineasContenedor.querySelectorAll('.salida-linea');

        totalLineas.textContent = lineas.length;
        agregarBoton.disabled = lineas.length >= MAX_LINEAS;
        agregarTexto.textContent = lineas.length >= MAX_LINEAS
            ? `Límite de ${MAX_LINEAS} productos alcanzado`
            : 'Agregar otro producto';

        lineas.forEach((linea) => {
            linea.querySelector('.salida-quitar').disabled = lineas.length === 1;
        });

        actualizarResumen();
    }

    /* ---------- Panel de información del lote ---------- */
    function pintarInfo(linea, estado, mensajes, porcentaje = null) {
        const info = linea.querySelector('.lote-info');
        const texto = info.querySelector('.lote-info-texto');
        const medidor = info.querySelector('.salida-medidor');

        info.dataset.estado = estado;
        texto.replaceChildren();

        mensajes.forEach((mensaje) => {
            const p = document.createElement('p');
            p.textContent = mensaje;
            texto.appendChild(p);
        });

        if (porcentaje === null) {
            medidor.hidden = true;
        } else {
            medidor.hidden = false;
            medidor.firstElementChild.style.width = `${porcentaje}%`;
        }
    }

    /* ---------- Carga de datos ---------- */
    function cargarProductos(linea, inventarioId = '') {
        const productoSelect = linea.querySelector('.producto-select');
        const almacenId = almacenSelect.value;

        productoSelect.replaceChildren(
            new Option(
                almacenId ? 'Selecciona un producto' : 'Selecciona primero un almacén',
                ''
            )
        );
        productoSelect.disabled = !almacenId;

        existencias
            .filter((existencia) => String(existencia.almacen_id) === String(almacenId))
            .forEach((existencia) => {
                const unidad = existencia.unidad_medida || existencia.unidad_nombre || '';
                const disponible = formatoCantidad(existencia.stock_actual);
                const opcion = new Option(
                    `${existencia.codigo_interno} — ${existencia.producto} · ${disponible} ${unidad}`,
                    existencia.inventario_id
                );

                opcion.dataset.productoId = existencia.producto_id;
                opcion.dataset.nombre = existencia.producto;
                opcion.dataset.stock = existencia.stock_actual;
                opcion.dataset.unidad = unidad;
                opcion.dataset.pesable = existencia.permite_pesaje ? '1' : '0';

                productoSelect.add(opcion);
            });

        if (inventarioId) {
            productoSelect.value = String(inventarioId);
        }

        cargarLotes(linea);
    }

    function cargarLotes(linea, loteId = '') {
        const productoSelect = linea.querySelector('.producto-select');
        const loteSelect = linea.querySelector('.lote-select');
        const cantidadInput = linea.querySelector('.cantidad-input');

        const opcionProducto = productoSelect.selectedOptions[0];
        const tieneProducto = Boolean(opcionProducto && opcionProducto.value);

        loteSelect.replaceChildren(
            new Option(
                tieneProducto ? 'Selecciona un lote' : 'Selecciona primero el producto',
                ''
            )
        );
        loteSelect.disabled = !tieneProducto;
        cantidadInput.value = '';
        cantidadInput.removeAttribute('max');

        if (!tieneProducto) {
            refrescarLinea(linea);
            actualizarResumen();
            return;
        }

        const productoId = opcionProducto.dataset.productoId;
        const almacenId = almacenSelect.value;

        // Orden FEFO: primero los que vencen antes; sin vencimiento al final.
        const lotesDisponibles = lotes
            .filter((lote) =>
                String(lote.producto_id) === String(productoId) &&
                String(lote.almacen_id) === String(almacenId) &&
                Number(lote.cantidad_actual) > 0
            )
            .sort((a, b) => {
                const fa = soloFecha(a.fecha_vencimiento) || '9999-99-99';
                const fb = soloFecha(b.fecha_vencimiento) || '9999-99-99';
                return fa.localeCompare(fb);
            });

        lotesDisponibles.forEach((lote) => {
            const vencimiento = lote.fecha_vencimiento
                ? `Vence: ${fechaCorta(lote.fecha_vencimiento)}`
                : 'Sin fecha de vencimiento';

            const opcion = new Option(
                `${lote.codigo_lote} · Disponible: ${formatoCantidad(lote.cantidad_actual)} · ${vencimiento}`,
                lote.id
            );

            opcion.dataset.codigo = lote.codigo_lote;
            opcion.dataset.stock = lote.cantidad_actual;
            opcion.dataset.vencimiento = soloFecha(lote.fecha_vencimiento);
            loteSelect.add(opcion);
        });

        if (loteId) {
            loteSelect.value = String(loteId);
        } else if (lotesDisponibles.length === 1) {
            loteSelect.value = String(lotesDisponibles[0].id);
        }

        refrescarLinea(linea);
        actualizarResumen();
    }

    /* ---------- Opciones rápidas de cantidad ---------- */
    function actualizarRapidas(linea, maximo, pesable, cantidad) {
        const contenedor = linea.querySelector('.salida-rapidas');

        if (maximo <= 0) {
            contenedor.hidden = true;
            return;
        }

        contenedor.hidden = false;
        const minimo = pesable ? 0.001 : 1;

        contenedor.querySelectorAll('.salida-chip').forEach((chip) => {
            let valor = maximo * Number(chip.dataset.fraccion);
            valor = pesable ? Math.round(valor * 1000) / 1000 : Math.floor(valor);

            chip.dataset.valor = valor;
            chip.disabled = valor < minimo;
            chip.querySelector('b').textContent = chip.disabled ? '' : formatoCantidad(valor);
            chip.classList.toggle('activo', !chip.disabled && cantidad === valor);
        });
    }

    function refrescarLinea(linea) {
        const productoSelect = linea.querySelector('.producto-select');
        const loteSelect = linea.querySelector('.lote-select');
        const cantidadInput = linea.querySelector('.cantidad-input');
        const unidadEl = linea.querySelector('.cantidad-unidad');
        const maxBoton = linea.querySelector('.salida-max');
        const nombreEl = linea.querySelector('.linea-nombre');

        const producto = productoSelect.selectedOptions[0];
        const lote = loteSelect.selectedOptions[0];
        const tieneProducto = Boolean(producto && producto.value);
        const tieneLote = Boolean(lote && lote.value);

        nombreEl.textContent = tieneProducto
            ? producto.dataset.nombre
            : 'Sin producto seleccionado';
        unidadEl.textContent = tieneProducto ? producto.dataset.unidad : '';

        if (!tieneProducto || !tieneLote) {
            cantidadInput.removeAttribute('max');
            maxBoton.disabled = true;
            linea.querySelector('.salida-rapidas').hidden = true;

            let mensaje;
            let estado = 'vacio';

            if (!almacenSelect.value) {
                mensaje = 'Selecciona primero un almacén de salida.';
            } else if (!tieneProducto) {
                mensaje = 'Selecciona un producto para ver sus lotes.';
            } else if (loteSelect.options.length <= 1) {
                mensaje = 'Este producto no tiene lotes disponibles en este almacén.';
                estado = 'aviso';
            } else {
                mensaje = 'Selecciona un lote para consultar su cantidad disponible.';
            }

            pintarInfo(linea, estado, [mensaje]);
            return;
        }

        const stockLote = Number(lote.dataset.stock || 0);
        const stockProducto = Number(producto.dataset.stock || 0);
        const maximo = Math.min(stockLote, stockProducto);
        const unidad = producto.dataset.unidad || '';
        const pesable = producto.dataset.pesable === '1';
        const cantidad = Number(cantidadInput.value || 0);

        cantidadInput.max = maximo;
        cantidadInput.step = pesable ? '0.001' : '1';
        cantidadInput.min = pesable ? '0.001' : '1';
        maxBoton.disabled = maximo <= 0;

        const mensajes = [
            `Disponible en este lote: ${formatoCantidad(stockLote)} ${unidad}.`,
            `Stock total del producto en el almacén: ${formatoCantidad(stockProducto)} ${unidad}.`,
        ];

        let estado = 'ok';

        if (cantidad > maximo) {
            estado = 'error';
            mensajes.push(`La cantidad supera lo disponible (máximo ${formatoCantidad(maximo)} ${unidad}).`);
        } else if (cantidad > 0) {
            mensajes.push(`Quedarán ${formatoCantidad(stockLote - cantidad)} ${unidad} en el lote.`);
        }

        const dias = diasHasta(lote.dataset.vencimiento);

        if (dias !== null) {
            if (dias < 0) {
                if (estado !== 'error') estado = 'aviso';
                mensajes.push(`Este lote venció hace ${Math.abs(dias)} día(s).`);
            } else if (dias <= DIAS_AVISO_VENCIMIENTO) {
                if (estado !== 'error') estado = 'aviso';
                mensajes.push(dias === 0 ? 'Este lote vence hoy.' : `Este lote vence en ${dias} día(s).`);
            }
        }

        // Sugerencia FEFO: otro lote vence antes que el elegido.
        const conVencimiento = Array.from(loteSelect.options)
            .filter((o) => o.value && o.dataset.vencimiento);

        if (conVencimiento.length) {
            const primero = conVencimiento.reduce((a, b) =>
                a.dataset.vencimiento <= b.dataset.vencimiento ? a : b
            );
            const propio = lote.dataset.vencimiento;

            if (primero !== lote && (!propio || propio > primero.dataset.vencimiento)) {
                if (estado === 'ok') estado = 'aviso';
                mensajes.push(
                    `Hay un lote que vence antes: ${primero.dataset.codigo} (${fechaCorta(primero.dataset.vencimiento)}).`
                );
            }
        }

        actualizarRapidas(linea, maximo, pesable, cantidad);

        const porcentaje = maximo > 0 ? Math.min(100, (cantidad / maximo) * 100) : 0;

        pintarInfo(linea, estado, mensajes, porcentaje);
    }

    /* ---------- Líneas ---------- */
    function agregarLinea(datos = {}) {
        const indice = lineasContenedor.querySelectorAll('.salida-linea').length;
        const linea = document.createElement('article');

        linea.className = 'salida-linea';
        linea.innerHTML = `
            <div class="salida-linea-cabecera">
                <div class="salida-linea-titulo">
                    <span class="salida-linea-badge">Producto ${indice + 1}</span>
                    <span class="linea-nombre">Sin producto seleccionado</span>
                </div>
                <button type="button" class="salida-quitar" aria-label="Quitar producto">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14M10 11v6M14 11v6"/></svg>
                    Quitar
                </button>
            </div>

            <div class="salida-linea-cuerpo">
                <div class="salida-campo c-producto">
                    <label>Producto <span class="salida-req">*</span></label>
                    <select name="items[${indice}][inventario_id]" class="salida-control producto-select" required>
                        <option value="">Selecciona un producto</option>
                    </select>
                </div>

                <div class="salida-campo c-lote">
                    <label>Lote de salida <span class="salida-req">*</span></label>
                    <select name="items[${indice}][lote_id]" class="salida-control lote-select" required>
                        <option value="">Selecciona primero el producto</option>
                    </select>
                </div>

                <div class="salida-campo c-cantidad">
                    <label>Cantidad que saldrá <span class="salida-req">*</span></label>
                    <div class="salida-cantidad-wrap">
                        <input
                            type="number"
                            name="items[${indice}][cantidad]"
                            class="salida-control cantidad-input"
                            min="0.001"
                            step="0.001"
                            required
                            inputmode="decimal"
                            placeholder="Ej.: 2"
                        >
                        <span class="cantidad-unidad"></span>
                        <button type="button" class="salida-max" disabled title="Sacar todo el lote">Todo</button>
                    </div>
                    <div class="salida-rapidas" hidden>
                        <button type="button" class="salida-chip" data-fraccion="0.25">¼ <b></b></button>
                        <button type="button" class="salida-chip" data-fraccion="0.5">½ <b></b></button>
                        <button type="button" class="salida-chip" data-fraccion="0.75">¾ <b></b></button>
                    </div>
                </div>

                <div class="c-info">
                    <div class="salida-lote-info lote-info" data-estado="vacio">
                        <div class="lote-info-texto"><p>Primero selecciona un producto para ver sus lotes.</p></div>
                        <div class="salida-medidor" hidden><div></div></div>
                    </div>
                </div>
            </div>
        `;

        lineasContenedor.appendChild(linea);

        const productoSelect = linea.querySelector('.producto-select');
        const loteSelect = linea.querySelector('.lote-select');
        const cantidadInput = linea.querySelector('.cantidad-input');
        const maxBoton = linea.querySelector('.salida-max');

        cargarProductos(linea, datos.inventario_id || '');

        productoSelect.addEventListener('change', () => {
            ocultarError();
            cargarLotes(linea);
        });

        loteSelect.addEventListener('change', () => {
            ocultarError();
            refrescarLinea(linea);
            actualizarResumen();
        });

        cantidadInput.addEventListener('input', () => {
            refrescarLinea(linea);
            actualizarResumen();
        });

        maxBoton.addEventListener('click', () => {
            if (cantidadInput.max) {
                cantidadInput.value = cantidadInput.max;
                cantidadInput.dispatchEvent(new Event('input', { bubbles: true }));
                cantidadInput.focus();
            }
        });

        linea.querySelectorAll('.salida-chip').forEach((chip) => {
            chip.addEventListener('click', () => {
                if (chip.disabled) return;
                cantidadInput.value = chip.dataset.valor;
                cantidadInput.dispatchEvent(new Event('input', { bubbles: true }));
                cantidadInput.focus();
            });
        });

        linea.querySelector('.salida-quitar').addEventListener('click', () => {
            linea.remove();
            renumerarLineas();
            actualizarContador();
        });

        if (datos.lote_id) {
            cargarLotes(linea, datos.lote_id);
        }

        if (datos.cantidad) {
            cantidadInput.value = datos.cantidad;
            refrescarLinea(linea);
        }

        actualizarContador();

        return linea;
    }

    function renumerarLineas() {
        lineasContenedor.querySelectorAll('.salida-linea').forEach((linea, indice) => {
            linea.querySelector('.salida-linea-badge').textContent = `Producto ${indice + 1}`;

            linea.querySelector('.producto-select').name = `items[${indice}][inventario_id]`;
            linea.querySelector('.lote-select').name = `items[${indice}][lote_id]`;
            linea.querySelector('.cantidad-input').name = `items[${indice}][cantidad]`;
        });
    }

    /* ---------- Eventos generales ---------- */
    almacenSelect.addEventListener('change', () => {
        ocultarError();
        lineasContenedor.querySelectorAll('.salida-linea').forEach((linea) => {
            cargarProductos(linea);
        });
        actualizarResumen();
    });

    tipoSelect.addEventListener('change', () => {
        ocultarError();
        actualizarResumen();
    });

    function actualizarContadorMotivo() {
        motivoContador.textContent = `${motivoInput.value.length} / ${motivoInput.maxLength}`;
    }

    motivoInput.addEventListener('input', actualizarContadorMotivo);

    agregarBoton.addEventListener('click', () => {
        const nueva = agregarLinea();
        nueva.querySelector('.producto-select').focus();
    });

    formulario.addEventListener('submit', (evento) => {
        const lineas = [...lineasContenedor.querySelectorAll('.salida-linea')];

        if (!almacenSelect.value) {
            evento.preventDefault();
            mostrarError('Selecciona el almacén de salida.', almacenSelect);
            return;
        }

        if (!tipoSelect.value) {
            evento.preventDefault();
            mostrarError('Selecciona el tipo de salida.', tipoSelect);
            return;
        }

        if (!motivoInput.value.trim()) {
            evento.preventDefault();
            mostrarError('Escribe el detalle o descripción de la salida.', motivoInput);
            return;
        }

        if (lineas.length === 0) {
            evento.preventDefault();
            mostrarError('Agrega al menos un producto.');
            return;
        }

        const lotesElegidos = [];

        for (const linea of lineas) {
            const productoSelect = linea.querySelector('.producto-select');
            const loteSelect = linea.querySelector('.lote-select');
            const cantidadInput = linea.querySelector('.cantidad-input');

            if (!productoSelect.value) {
                evento.preventDefault();
                mostrarError('Selecciona un producto en cada línea.', productoSelect, linea);
                return;
            }

            if (!loteSelect.value) {
                evento.preventDefault();
                mostrarError('Selecciona un lote para cada producto.', loteSelect, linea);
                return;
            }

            lotesElegidos.push(loteSelect.value);

            const cantidad = Number(cantidadInput.value || 0);

            if (!cantidadInput.value || cantidad <= 0) {
                evento.preventDefault();
                mostrarError('Ingresa una cantidad mayor a cero en cada producto.', cantidadInput, linea);
                return;
            }

            const stockLote = Number(loteSelect.selectedOptions[0]?.dataset.stock || 0);
            const stockProducto = Number(productoSelect.selectedOptions[0]?.dataset.stock || 0);

            if (cantidad > Math.min(stockLote, stockProducto)) {
                evento.preventDefault();
                mostrarError('La cantidad supera lo disponible en el lote seleccionado.', cantidadInput, linea);
                return;
            }
        }

        if (new Set(lotesElegidos).size !== lotesElegidos.length) {
            evento.preventDefault();
            mostrarError('No repitas el mismo lote en más de una línea.');
            return;
        }

        ocultarError();
        guardarBoton.disabled = true;
        guardarTexto.textContent = 'Registrando salida…';
    });

    /* ---------- Inicio ---------- */
    if (almacenAntiguo) {
        almacenSelect.value = String(almacenAntiguo);
    }

    if (itemsAntiguos.length) {
        itemsAntiguos.slice(0, MAX_LINEAS).forEach((item) => agregarLinea(item));
    } else {
        agregarLinea();
    }

    actualizarContadorMotivo();
    actualizarContador();
});
</script>
@endsection