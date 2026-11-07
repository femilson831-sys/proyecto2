@extends('admin.layouts.app')

@section('title', 'Registrar entrada de inventario')

@push('styles')
<style>
    .entrada-page {
        --ep-bg: #13141a;
        --ep-panel: #1a1c23;
        --ep-bg-soft: #20232b;
        --ep-border: #2b2e38;
        --ep-border-soft: #23262e;
        --ep-text: #ececf0;
        --ep-muted: #9a9ca6;
        --ep-muted-2: #6c6f7d;
        --ep-red: #e31b23;
        --ep-red-light: #ff9aa0;
        --ep-red-soft: rgba(227, 27, 35, .12);
        --ep-green: #4ade80;
        --ep-amber: #f5b942;
        --ep-radius: 16px;
        --ep-radius-sm: 10px;

        display: flex;
        flex-direction: column;
        gap: 20px;
        color: var(--ep-text);
    }

    .entrada-page,
    .entrada-page *,
    .entrada-page *::before,
    .entrada-page *::after {
        box-sizing: border-box;
    }

    /* ---------- Header ---------- */
    .entrada-header {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 28px 26px;
        border: 1px solid var(--ep-border);
        border-radius: var(--ep-radius);
        background:
            radial-gradient(ellipse at 100% -30%, rgba(227, 27, 35, .22), transparent 55%),
            radial-gradient(ellipse at 0% 130%, rgba(96, 165, 250, .07), transparent 45%),
            var(--ep-bg);
    }

    .entrada-header::before {
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

    .entrada-header > * { position: relative; z-index: 1; }

    .entrada-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        padding: 5px 11px 5px 9px;
        border: 1px solid rgba(227, 27, 35, .28);
        border-radius: 999px;
        color: var(--ep-red-light);
        background: var(--ep-red-soft);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.3px;
        text-transform: uppercase;
    }

    .entrada-eyebrow::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--ep-red);
        box-shadow: 0 0 10px var(--ep-red);
    }

    .entrada-header h1 {
        margin: 0 0 8px;
        color: #fff;
        font-size: clamp(25px, 3vw, 31px);
        font-weight: 800;
        letter-spacing: -.7px;
        line-height: 1.05;
    }

    .entrada-header p {
        max-width: 560px;
        margin: 0;
        color: var(--ep-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    /* ---------- Botones ---------- */
    .entrada-link,
    .entrada-submit {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 17px;
        border: 1px solid var(--ep-border);
        border-radius: var(--ep-radius-sm);
        color: #dedee4;
        background: var(--ep-bg-soft);
        box-shadow: 0 1px 0 rgba(255,255,255,.04) inset;
        font: inherit;
        font-size: 12px;
        font-weight: 750;
        text-decoration: none;
        cursor: pointer;
        transition: transform .15s, background .15s, border-color .15s, color .15s, filter .15s, box-shadow .15s;
    }

    .entrada-link svg { width: 14px; height: 14px; transition: transform .15s; }

    .entrada-link:hover {
        border-color: #41454f;
        color: #fff;
        background: #2a2d37;
    }

    .entrada-link:hover svg { transform: translateX(-3px); }

    .entrada-link:focus-visible,
    .entrada-submit:focus-visible {
        outline: 2px solid var(--ep-red-light);
        outline-offset: 2px;
    }

    .entrada-submit {
        border-color: var(--ep-red);
        color: #fff;
        background: linear-gradient(180deg, #e6272f 0%, #c91820 100%);
        box-shadow: 0 1px 0 rgba(255,255,255,.14) inset, 0 8px 20px -9px rgba(227,27,35,.7);
        min-width: 170px;
    }

    .entrada-submit:hover {
        transform: translateY(-1px);
        color: #fff;
        filter: brightness(1.08);
        box-shadow: 0 1px 0 rgba(255,255,255,.2) inset, 0 12px 24px -9px rgba(227,27,35,.8);
    }

    .entrada-submit:active { transform: translateY(0); }

    .entrada-submit:disabled {
        opacity: .65;
        cursor: wait;
        transform: none;
        filter: none;
    }

    /* ---------- Alertas ---------- */
    .entrada-alerta {
        display: flex;
        gap: 12px;
        padding: 14px 16px;
        border: 1px solid;
        border-radius: var(--ep-radius-sm);
        font-size: 12.5px;
        line-height: 1.55;
    }

    .entrada-alerta::before {
        content: "";
        flex-shrink: 0;
        width: 4px;
        border-radius: 4px;
        background: currentColor;
        opacity: .8;
    }

    .entrada-alerta.error {
        border-color: rgba(227, 27, 35, .35);
        color: #ff9297;
        background: rgba(227, 27, 35, .07);
    }

    .entrada-alerta strong { color: #fff; }

    .entrada-alerta ul {
        margin: 7px 0 0;
        padding-left: 20px;
    }

    .entrada-alerta li + li { margin-top: 3px; }

    /* ---------- Card y secciones ---------- */
    .entrada-card {
        overflow: hidden;
        border: 1px solid var(--ep-border);
        border-radius: var(--ep-radius);
        background: var(--ep-bg);
    }

    .entrada-seccion { padding: 24px; }

    .entrada-seccion + .entrada-seccion {
        border-top: 1px solid var(--ep-border-soft);
    }

    .entrada-seccion-cabecera {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 20px;
    }

    .entrada-numero {
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        display: grid;
        place-items: center;
        border: 1px solid rgba(227, 27, 35, .3);
        border-radius: 10px;
        color: var(--ep-red-light);
        background: var(--ep-red-soft);
        font-size: 13px;
        font-weight: 800;
    }

    .entrada-seccion-cabecera h2 {
        margin: 0 0 4px;
        color: #f4f4f6;
        font-size: 16px;
        font-weight: 760;
        letter-spacing: -.2px;
    }

    .entrada-seccion-cabecera p {
        max-width: 620px;
        margin: 0;
        color: var(--ep-muted);
        font-size: 12px;
        line-height: 1.55;
    }

    /* ---------- Campos ---------- */
    .entrada-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 16px;
    }

    .entrada-campo {
        min-width: 0;
        grid-column: span 6;
    }

    .entrada-campo.col-4 { grid-column: span 4; }
    .entrada-campo.col-3 { grid-column: span 3; }
    .entrada-campo.col-12 { grid-column: 1 / -1; }

    .entrada-campo label {
        display: block;
        margin-bottom: 7px;
        color: #b9bbc4;
        font-size: 10.5px;
        font-weight: 750;
        letter-spacing: .5px;
        text-transform: uppercase;
    }

    .entrada-requerido { color: var(--ep-red-light); }

    .entrada-control {
        width: 100%;
        min-height: 44px;
        padding: 0 13px;
        border: 1px solid var(--ep-border);
        border-radius: var(--ep-radius-sm);
        outline: none;
        color: var(--ep-text);
        background: var(--ep-bg-soft);
        color-scheme: dark;
        font: inherit;
        font-size: 12.5px;
        transition: border-color .15s, box-shadow .15s, background .15s;
    }

    textarea.entrada-control {
        min-height: 100px;
        padding: 12px 13px;
        line-height: 1.55;
        resize: vertical;
    }

    .entrada-control::placeholder { color: #70737f; }

    .entrada-control:hover { border-color: #3d414c; }

    .entrada-control:focus {
        border-color: #c4252c;
        background: #24262e;
        box-shadow: 0 0 0 3px rgba(227, 27, 35, .14);
    }

    select.entrada-control {
        appearance: none;
        -webkit-appearance: none;
        padding-right: 36px;
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%239a9ca6' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
    }

    select.entrada-control option {
        color: var(--ep-text);
        background: var(--ep-bg-soft);
    }

    /* Buscador + select del producto */
    #producto_buscar {
        margin-bottom: 8px;
        padding-left: 38px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236c6f7d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='7'/%3E%3Cpath d='M21 21l-4.3-4.3'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 13px center;
    }

    /* Campos automáticos (solo lectura) */
    .entrada-control:read-only:not(select):not(textarea) {
        padding-right: 36px;
        border-style: dashed;
        color: #c4c6ce;
        background-color: #16171c;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%236c6f7d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='4' y='11' width='16' height='10' rx='2'/%3E%3Cpath d='M8 11V7a4 4 0 0 1 8 0v4'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 13px center;
        cursor: default;
    }

    .entrada-control:read-only:not(select):not(textarea):focus {
        border-color: var(--ep-border);
        box-shadow: none;
    }

    /* Ayudas y avisos */
    .entrada-ayuda,
    .entrada-aviso,
    .entrada-error-campo {
        display: block;
        margin-top: 7px;
        font-size: 11px;
        line-height: 1.5;
    }

    .entrada-ayuda { color: var(--ep-muted-2); }

    .entrada-aviso {
        padding: 6px 10px;
        border: 1px solid rgba(245, 185, 66, .25);
        border-radius: 8px;
        color: #f3c66e;
        background: rgba(245, 185, 66, .07);
    }

    .entrada-aviso[hidden] { display: none; }

    .entrada-error-campo { color: #ff858b; font-weight: 600; }

    /* ---------- Estado del producto ---------- */
    .entrada-estado-producto {
        min-height: 42px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 18px;
        padding: 10px 14px;
        border: 1px solid var(--ep-border);
        border-radius: var(--ep-radius-sm);
        color: var(--ep-muted);
        background: rgba(255, 255, 255, .02);
        font-size: 12px;
        line-height: 1.5;
    }

    .entrada-estado-producto::before {
        content: "i";
        width: 18px;
        height: 18px;
        flex: 0 0 18px;
        display: grid;
        place-items: center;
        border-radius: 50%;
        color: var(--ep-muted);
        background: rgba(255,255,255,.06);
        font-size: 10px;
        font-weight: 800;
        font-style: italic;
        font-family: Georgia, serif;
    }

    .entrada-estado-producto.ok {
        border-color: rgba(74, 222, 128, .25);
        color: #8be8a7;
        background: rgba(74, 222, 128, .06);
    }

    .entrada-estado-producto.ok::before {
        content: "✓";
        color: var(--ep-green);
        background: rgba(74, 222, 128, .14);
        font-style: normal;
        font-family: inherit;
    }

    .entrada-estado-producto.aviso {
        border-color: rgba(245, 185, 66, .28);
        color: #f3c66e;
        background: rgba(245, 185, 66, .06);
    }

    .entrada-estado-producto.aviso::before {
        content: "!";
        color: var(--ep-amber);
        background: rgba(245, 185, 66, .15);
        font-style: normal;
        font-family: inherit;
    }

    .entrada-estado-producto.cargando::before {
        content: '';
        width: 14px;
        height: 14px;
        flex: 0 0 14px;
        border: 2px solid #44464f;
        border-top-color: var(--ep-red-light);
        border-radius: 50%;
        background: transparent;
        animation: entrada-giro .7s linear infinite;
    }

    @keyframes entrada-giro {
        to { transform: rotate(360deg); }
    }

    /* ---------- Resumen ---------- */
    .entrada-resumen {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-top: 16px;
        padding: 18px 20px;
        border: 1px solid var(--ep-border);
        border-radius: var(--ep-radius);
        background:
            radial-gradient(ellipse at 100% 0%, rgba(74, 222, 128, .09), transparent 60%),
            linear-gradient(180deg, var(--ep-panel) 0%, var(--ep-bg) 100%);
    }

    .entrada-resumen span {
        color: var(--ep-muted);
        font-size: 12px;
        font-weight: 650;
    }

    .entrada-resumen small {
        display: block;
        margin-top: 4px;
        color: var(--ep-muted-2);
        font-size: 11px;
        font-variant-numeric: tabular-nums;
    }

    .entrada-resumen small:empty { display: none; }

    .entrada-resumen strong {
        color: #fff;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -.6px;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    /* ---------- Acciones ---------- */
    .entrada-acciones {
        position: sticky;
        bottom: 0;
        z-index: 5;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        padding: 16px 24px;
        border-top: 1px solid var(--ep-border);
        background: rgba(26, 28, 35, .92);
        -webkit-backdrop-filter: blur(8px);
        backdrop-filter: blur(8px);
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 760px) {
        .entrada-campo,
        .entrada-campo.col-4,
        .entrada-campo.col-3 { grid-column: span 6; }
    }

    @media (max-width: 560px) {
        .entrada-header {
            align-items: stretch;
            flex-direction: column;
            padding: 22px 18px;
        }

        .entrada-header .entrada-link { width: 100%; }

        .entrada-seccion { padding: 18px 16px; }

        .entrada-campo,
        .entrada-campo.col-4,
        .entrada-campo.col-3 { grid-column: 1 / -1; }

        .entrada-resumen { padding: 16px; }
        .entrada-resumen strong { font-size: 22px; }

        .entrada-acciones {
            flex-direction: column-reverse;
            padding: 14px 16px;
        }

        .entrada-acciones > * { width: 100%; }
    }

    @media (prefers-reduced-motion: reduce) {
        .entrada-page * {
            transition: none !important;
            animation: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="entrada-page">

    <header class="entrada-header">
        <div>
            <span class="entrada-eyebrow">Inventario · Recepción</span>
            <h1>Registrar entrada</h1>
            <p>Registra el producto recibido, su proveedor, costo y datos del lote.</p>
        </div>

        <a href="{{ route('admin.inventario.index') }}" class="entrada-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Volver al inventario
        </a>
    </header>

    @if (session('error'))
        <div class="entrada-alerta error" role="alert">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="entrada-alerta error" role="alert">
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

    <form
        id="form-entrada"
        action="{{ route('admin.inventario.store') }}"
        method="POST"
        autocomplete="off"
    >
        @csrf

        <div class="entrada-card">

            {{-- ========== 1. Recepción ========== --}}
            <section class="entrada-seccion">
                <div class="entrada-seccion-cabecera">
                    <span class="entrada-numero">1</span>
                    <div>
                        <h2>Datos de recepción</h2>
                        <p>Indica dónde y cuándo se recibió la mercadería.</p>
                    </div>
                </div>

                <div class="entrada-grid">
                    <div class="entrada-campo">
                        <label for="almacen_id">
                            Almacén de destino
                            <span class="entrada-requerido">*</span>
                        </label>
                        <select
                            name="almacen_id"
                            id="almacen_id"
                            class="entrada-control"
                            required
                        >
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
                        @error('almacen_id')
                            <small class="entrada-error-campo">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="entrada-campo">
                        <label for="fecha_recepcion">
                            Fecha y hora de recepción
                            <span class="entrada-requerido">*</span>
                        </label>
                        <input
                            type="datetime-local"
                            name="fecha_recepcion"
                            id="fecha_recepcion"
                            class="entrada-control"
                            value="{{ old('fecha_recepcion', now()->format('Y-m-d\TH:i')) }}"
                            required
                        >
                        @error('fecha_recepcion')
                            <small class="entrada-error-campo">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="entrada-campo col-12">
                        <label for="numero_factura">Número de factura o nota de entrega</label>
                        <input
                            type="text"
                            name="numero_factura"
                            id="numero_factura"
                            class="entrada-control"
                            maxlength="80"
                            value="{{ old('numero_factura') }}"
                            placeholder="Ej.: FACT-00125"
                        >
                        @error('numero_factura')
                            <small class="entrada-error-campo">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- ========== 2. Producto y cantidades ========== --}}
            <section class="entrada-seccion">
                <div class="entrada-seccion-cabecera">
                    <span class="entrada-numero">2</span>
                    <div>
                        <h2>Producto y cantidades</h2>
                        <p>
                            Busca el producto por código o nombre. Al seleccionarlo se
                            cargarán su proveedor, unidad y costo registrado.
                        </p>
                    </div>
                </div>

                <div class="entrada-grid">
                    <div class="entrada-campo col-12">
                        <label for="producto_buscar">
                            Producto
                            <span class="entrada-requerido">*</span>
                        </label>

                        <input
                            type="search"
                            id="producto_buscar"
                            class="entrada-control"
                            placeholder="Escribe el código o nombre del producto"
                            autocomplete="off"
                        >

                        <select
                            name="producto_id"
                            id="producto_id"
                            class="entrada-control"
                            required
                        >
                            <option value="">Selecciona un producto</option>
                            @foreach ($productos as $producto)
                                <option
                                    value="{{ $producto->id }}"
                                    @selected((string) old('producto_id') === (string) $producto->id)
                                >
                                    {{ $producto->codigo_interno }} — {{ $producto->nombre }}
                                </option>
                            @endforeach
                        </select>

                        <small class="entrada-ayuda" id="buscar-conteo"></small>

                        @error('producto_id')
                            <small class="entrada-error-campo">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="entrada-campo">
                        <label for="proveedor_nombre">Proveedor automático</label>
                        <input
                            type="text"
                            id="proveedor_nombre"
                            class="entrada-control"
                            readonly
                            placeholder="Selecciona un producto"
                        >
                        <input
                            type="hidden"
                            name="proveedor_id"
                            id="proveedor_id"
                            value="{{ old('proveedor_id') }}"
                        >
                        @error('proveedor_id')
                            <small class="entrada-error-campo">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="entrada-campo col-3">
                        <label for="unidad_medida">Unidad</label>
                        <input
                            type="text"
                            id="unidad_medida"
                            class="entrada-control"
                            readonly
                            placeholder="—"
                        >
                    </div>

                    <div class="entrada-campo col-3">
                        <label for="cantidad">
                            Cantidad recibida
                            <span class="entrada-requerido">*</span>
                        </label>
                        <input
                            type="number"
                            name="cantidad"
                            id="cantidad"
                            class="entrada-control"
                            min="0.001"
                            max="999999999.999"
                            step="0.001"
                            value="{{ old('cantidad') }}"
                            placeholder="Ej.: 80"
                            inputmode="decimal"
                            required
                        >
                        @error('cantidad')
                            <small class="entrada-error-campo">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="entrada-campo">
                        <label for="costo_registrado">Costo registrado (Bs)</label>
                        <input
                            type="text"
                            id="costo_registrado"
                            class="entrada-control"
                            readonly
                            placeholder="—"
                        >
                    </div>

                    <div class="entrada-campo">
                        <label for="costo_unitario">
                            Costo unitario de esta entrada (Bs)
                            <span class="entrada-requerido">*</span>
                        </label>
                        <input
                            type="number"
                            name="costo_unitario"
                            id="costo_unitario"
                            class="entrada-control"
                            min="0"
                            max="9999999999.99"
                            step="0.01"
                            value="{{ old('costo_unitario') }}"
                            placeholder="Se completa al elegir producto"
                            inputmode="decimal"
                            required
                        >
                        <small class="entrada-ayuda">
                            Puedes corregirlo según el precio de la factura.
                        </small>
                        <small
                            class="entrada-aviso"
                            id="costo-aviso"
                            role="status"
                            hidden
                        ></small>
                        @error('costo_unitario')
                            <small class="entrada-error-campo">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div
                    id="estado-producto"
                    class="entrada-estado-producto"
                    role="status"
                    aria-live="polite"
                >
                    Selecciona un producto para cargar los datos registrados.
                </div>

                <div class="entrada-resumen">
                    <div>
                        <span>Subtotal estimado de la recepción</span>
                        <small id="subtotal-detalle"></small>
                    </div>
                    <strong id="subtotal-preview">Bs 0,00</strong>
                </div>
            </section>

            {{-- ========== 3. Lote ========== --}}
            <section class="entrada-seccion">
                <div class="entrada-seccion-cabecera">
                    <span class="entrada-numero">3</span>
                    <div>
                        <h2>Datos del lote</h2>
                        <p>Registra el código del lote y las fechas impresas en el producto.</p>
                    </div>
                </div>

                <div class="entrada-grid">
                    <div class="entrada-campo col-4">
                        <label for="codigo_lote">
                            Código del lote
                            <span class="entrada-requerido">*</span>
                        </label>
                        <input
                            type="text"
                            name="codigo_lote"
                            id="codigo_lote"
                            class="entrada-control"
                            maxlength="100"
                            value="{{ old('codigo_lote') }}"
                            placeholder="Ej.: L-2026-015"
                            required
                        >
                        @error('codigo_lote')
                            <small class="entrada-error-campo">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="entrada-campo col-4">
                        <label for="fecha_fabricacion">Fecha de fabricación</label>
                        <input
                            type="date"
                            name="fecha_fabricacion"
                            id="fecha_fabricacion"
                            class="entrada-control"
                            value="{{ old('fecha_fabricacion') }}"
                        >
                        @error('fecha_fabricacion')
                            <small class="entrada-error-campo">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="entrada-campo col-4">
                        <label for="fecha_vencimiento">Fecha de vencimiento</label>
                        <input
                            type="date"
                            name="fecha_vencimiento"
                            id="fecha_vencimiento"
                            class="entrada-control"
                            value="{{ old('fecha_vencimiento') }}"
                        >
                        <small class="entrada-ayuda">
                            Si el producto no tiene vencimiento, deja el campo vacío.
                        </small>
                        <small
                            class="entrada-aviso"
                            id="vencimiento-aviso"
                            role="status"
                            hidden
                        ></small>
                        @error('fecha_vencimiento')
                            <small class="entrada-error-campo">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="entrada-campo col-12">
                        <label for="observaciones">Observaciones</label>
                        <textarea
                            name="observaciones"
                            id="observaciones"
                            class="entrada-control"
                            maxlength="5000"
                            placeholder="Estado de la mercadería u otro detalle de la recepción"
                        >{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                            <small class="entrada-error-campo">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </section>

            <div class="entrada-acciones">
                <a href="{{ route('admin.inventario.index') }}" class="entrada-link">
                    Cancelar
                </a>

                <button
                    type="submit"
                    id="btn-registrar"
                    class="entrada-submit"
                >
                    Registrar entrada
                </button>
            </div>
        </div>
    </form>
</div>

<script>
(() => {
    'use strict';

    const DIAS_AVISO_VENCIMIENTO = 30;
    const URL_PRODUCTO_DATOS = @json(
        route('admin.inventario.producto-datos', ['producto' => '__ID__'])
    );

    const $ = (id) => document.getElementById(id);

    const el = {
        form: $('form-entrada'),
        boton: $('btn-registrar'),
        buscar: $('producto_buscar'),
        conteo: $('buscar-conteo'),
        producto: $('producto_id'),
        proveedorNombre: $('proveedor_nombre'),
        proveedorId: $('proveedor_id'),
        unidad: $('unidad_medida'),
        cantidad: $('cantidad'),
        costoRegistrado: $('costo_registrado'),
        costo: $('costo_unitario'),
        costoAviso: $('costo-aviso'),
        estado: $('estado-producto'),
        subtotal: $('subtotal-preview'),
        subtotalDetalle: $('subtotal-detalle'),
        fabricacion: $('fecha_fabricacion'),
        vencimiento: $('fecha_vencimiento'),
        vencimientoAviso: $('vencimiento-aviso'),
    };

    const opcionesProductos = Array.from(el.producto.options)
        .filter((opcion) => opcion.value !== '')
        .map((opcion) => ({
            value: opcion.value,
            texto: opcion.textContent.trim(),
        }));

    let controladorPeticion = null;
    let costoRegistrado = null;
    let costoModificado = Boolean(el.producto.value && el.costo.value);
    let enviando = false;

    function normalizar(texto) {
        return String(texto || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }

    function dinero(valor) {
        return 'Bs ' + Number(valor || 0).toLocaleString('es-BO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    function mostrarEstado(texto, tipo = '') {
        el.estado.textContent = texto;
        el.estado.className = 'entrada-estado-producto';

        if (tipo) {
            el.estado.classList.add(tipo);
        }
    }

    function calcularSubtotal() {
        const cantidad = Number(el.cantidad.value || 0);
        const costo = Number(el.costo.value || 0);
        const total = cantidad * costo;

        el.subtotal.textContent = dinero(total);

        if (cantidad > 0 && el.unidad.value) {
            el.subtotalDetalle.textContent =
                `${cantidad.toLocaleString('es-BO')} ${el.unidad.value} × ${dinero(costo)}`;
        } else {
            el.subtotalDetalle.textContent = '';
        }
    }

    function revisarCosto() {
        el.costoAviso.hidden = true;
        el.costoAviso.textContent = '';

        if (!el.costo.value) {
            return;
        }

        const actual = Number(el.costo.value);

        if (actual === 0) {
            el.costoAviso.textContent =
                'El costo es Bs 0,00. Verifica el precio de la factura.';
            el.costoAviso.hidden = false;
            return;
        }

        if (costoRegistrado !== null && costoRegistrado > 0) {
            const diferencia =
                Math.abs(actual - costoRegistrado) / costoRegistrado * 100;

            if (diferencia >= 20) {
                el.costoAviso.textContent =
                    `El costo difiere ${diferencia.toFixed(1)} % del registrado (${dinero(costoRegistrado)}). Verifica la factura.`;
                el.costoAviso.hidden = false;
            }
        }
    }

    function fechaLocal(valor) {
        if (!valor) {
            return null;
        }

        const partes = valor.split('-').map(Number);

        if (partes.length !== 3 || partes.some(Number.isNaN)) {
            return null;
        }

        return new Date(partes[0], partes[1] - 1, partes[2], 12, 0, 0);
    }

    function revisarVencimiento() {
        const fecha = fechaLocal(el.vencimiento.value);

        el.vencimientoAviso.hidden = true;
        el.vencimientoAviso.textContent = '';

        if (!fecha) {
            return;
        }

        const hoy = new Date();
        hoy.setHours(12, 0, 0, 0);

        const dias = Math.ceil((fecha - hoy) / 86400000);

        if (dias < 0) {
            el.vencimientoAviso.textContent =
                `El lote venció hace ${Math.abs(dias)} día(s).`;
            el.vencimientoAviso.hidden = false;
        } else if (dias <= DIAS_AVISO_VENCIMIENTO) {
            el.vencimientoAviso.textContent =
                `Atención: este producto vence en ${dias} día(s).`;
            el.vencimientoAviso.hidden = false;
        }
    }

    function filtrarProductos() {
        const texto = normalizar(el.buscar.value);
        const valorActual = el.producto.value;

        const coincidencias = opcionesProductos.filter((opcion) => {
            return !texto || normalizar(opcion.texto).includes(texto);
        });

        el.producto.replaceChildren(
            new Option('Selecciona un producto', '')
        );

        coincidencias.forEach((opcion) => {
            el.producto.add(new Option(opcion.texto, opcion.value));
        });

        const sigueVisible = coincidencias.some(
            (opcion) => opcion.value === valorActual
        );

        if (sigueVisible) {
            el.producto.value = valorActual;
        } else if (valorActual) {
            el.producto.value = '';
            cargarDatosProducto(true);
        }

        el.conteo.textContent = texto
            ? `${coincidencias.length} producto(s) encontrado(s).`
            : `${opcionesProductos.length} producto(s) disponible(s).`;
    }

    async function cargarDatosProducto(limpiarCosto = true) {
        const productoId = el.producto.value;

        if (controladorPeticion) {
            controladorPeticion.abort();
            controladorPeticion = null;
        }

        el.proveedorNombre.value = '';
        el.proveedorId.value = '';
        el.unidad.value = '';
        el.costoRegistrado.value = '';
        costoRegistrado = null;

        if (limpiarCosto) {
            el.costo.value = '';
            costoModificado = false;
        }

        revisarCosto();
        calcularSubtotal();

        if (!productoId) {
            mostrarEstado(
                'Selecciona un producto para cargar los datos registrados.'
            );
            return;
        }

        controladorPeticion = new AbortController();
        mostrarEstado('Cargando datos del producto...', 'cargando');
        el.proveedorNombre.value = 'Cargando...';

        try {
            const url = URL_PRODUCTO_DATOS.replace(
                '__ID__',
                encodeURIComponent(productoId)
            );

            const respuesta = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: controladorPeticion.signal,
            });

            const datos = await respuesta.json();

            if (!respuesta.ok) {
                throw new Error(
                    datos.message || 'No se pudieron cargar los datos del producto.'
                );
            }

            // Ignora una respuesta atrasada si ya se eligió otro producto.
            if (el.producto.value !== productoId) {
                return;
            }

            el.proveedorNombre.value =
                datos.proveedor || 'Sin proveedor relacionado';
            el.proveedorId.value = datos.proveedor_id || '';
            el.unidad.value = datos.unidad || '';

            if (
                datos.costo_unitario !== null &&
                datos.costo_unitario !== undefined
            ) {
                costoRegistrado = Number(datos.costo_unitario);
                el.costoRegistrado.value = dinero(costoRegistrado);

                if (!costoModificado || !el.costo.value) {
                    el.costo.value = costoRegistrado.toFixed(2);
                }
            }

            if (!datos.proveedor_id) {
                mostrarEstado(
                    'Este producto no tiene proveedor relacionado. Relaciónalo antes de registrar la entrada.',
                    'aviso'
                );
            } else if (
                datos.costo_unitario === null ||
                datos.costo_unitario === undefined
            ) {
                mostrarEstado(
                    'Proveedor y unidad cargados. No hay costo registrado; ingresa el costo de la factura.',
                    'aviso'
                );
            } else {
                mostrarEstado(
                    'Proveedor, unidad y costo cargados. Puedes ajustar el costo según la factura.',
                    'ok'
                );
            }

            revisarCosto();
            calcularSubtotal();
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            el.proveedorNombre.value = '';
            el.proveedorId.value = '';
            mostrarEstado(
                error.message || 'No se pudieron cargar los datos del producto.',
                'aviso'
            );
        }
    }

    function validarFechas() {
        if (
            el.fabricacion.value &&
            el.vencimiento.value &&
            el.vencimiento.value < el.fabricacion.value
        ) {
            el.vencimiento.setCustomValidity(
                'El vencimiento no puede ser anterior a la fecha de fabricación.'
            );
            el.vencimiento.reportValidity();
            el.vencimiento.setCustomValidity('');
            return false;
        }

        return true;
    }

    let temporizadorBusqueda;

    el.buscar.addEventListener('input', () => {
        clearTimeout(temporizadorBusqueda);
        temporizadorBusqueda = setTimeout(filtrarProductos, 180);
    });

    el.buscar.addEventListener('keydown', (evento) => {
        if (evento.key !== 'Enter') {
            return;
        }

        const primera = Array.from(el.producto.options)
            .find((opcion) => opcion.value !== '');

        if (!primera) {
            return;
        }

        evento.preventDefault();
        el.producto.value = primera.value;
        el.buscar.value = primera.textContent.trim();
        cargarDatosProducto(true);
    });

    el.producto.addEventListener('change', () => {
        const opcion = el.producto.selectedOptions[0];

        if (opcion && opcion.value) {
            el.buscar.value = opcion.textContent.trim();
        }

        costoModificado = false;
        cargarDatosProducto(true);
    });

    el.cantidad.addEventListener('input', calcularSubtotal);

    el.costo.addEventListener('input', () => {
        costoModificado = true;
        revisarCosto();
        calcularSubtotal();
    });

    el.fabricacion.addEventListener('change', validarFechas);

    el.vencimiento.addEventListener('change', () => {
        validarFechas();
        revisarVencimiento();
    });

    el.form.addEventListener('submit', (evento) => {
        if (enviando) {
            evento.preventDefault();
            return;
        }

        if (!el.proveedorId.value) {
            evento.preventDefault();
            mostrarEstado(
                'No se puede registrar la entrada hasta relacionar el producto con un proveedor.',
                'aviso'
            );
            el.producto.focus();
            return;
        }

        if (!validarFechas()) {
            evento.preventDefault();
            return;
        }

        const fechaVencimiento = fechaLocal(el.vencimiento.value);

        if (fechaVencimiento) {
            const hoy = new Date();
            hoy.setHours(12, 0, 0, 0);

            if (
                fechaVencimiento < hoy &&
                !window.confirm(
                    'El lote ya está vencido. ¿Confirmas que deseas registrar esta entrada?'
                )
            ) {
                evento.preventDefault();
                return;
            }
        }

        enviando = true;
        el.boton.disabled = true;
        el.boton.textContent = 'Registrando entrada…';
    });

    // Recupera proveedor, unidad y costo cuando la validación del servidor devuelve el formulario.
    if (el.producto.value) {
        const opcion = el.producto.selectedOptions[0];

        if (opcion) {
            el.buscar.value = opcion.textContent.trim();
        }

        cargarDatosProducto(false);
    }

    filtrarProductos();
    revisarCosto();
    revisarVencimiento();
    calcularSubtotal();
})();
</script>
@endsection