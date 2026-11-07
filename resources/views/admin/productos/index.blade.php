@extends('admin.layouts.app')

@section('title', 'Productos')

@push('styles')
<style>
    /* ============================================
       SISTEMA DE DISEÑO - VARIABLES
       ============================================ */
    :root {
        --color-bg-primary: #0f1014;
        --color-bg-secondary: #18191f;
        --color-bg-tertiary: #222329;
        --color-bg-elevated: #2a2b32;
        --color-border: #30333d;
        --color-border-light: #383a43;
        --color-border-hover: #656873;
        --color-text-primary: #f3f3f5;
        --color-text-secondary: #d1d3da;
        --color-text-muted: #a1a5b1;
        --color-text-dim: #9295a0;
        --color-accent: #e31b23;
        --color-accent-hover: #c9161e;
        --color-accent-light: #ff777d;
        --color-accent-soft: rgba(227, 27, 35, 0.1);
        --color-success: #38d478;
        --color-success-soft: rgba(56, 212, 120, 0.1);
        --color-warning: #ffb020;
        --color-warning-soft: rgba(255, 176, 32, 0.1);
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 16px;
        --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);
        --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.4);
        --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.5);
        --transition-fast: 150ms ease;
        --transition-normal: 250ms ease;
        --transition-slow: 350ms ease;
    }

    /* ============================================
       LAYOUT PRINCIPAL
       ============================================ */
    .products-page {
        display: flex;
        flex-direction: column;
        gap: 20px;
        color: var(--color-text-secondary);
        padding: 4px;
    }

    /* ============================================
       HEADER
       ============================================ */
    .products-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        flex-wrap: wrap;
        padding-bottom: 4px;
    }

    .products-header__content {
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .products-header__icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        border-radius: var(--radius-lg);
        background: var(--color-accent-soft);
        color: var(--color-accent-light);
    }

    .products-header__icon svg {
        width: 22px;
        height: 22px;
    }

    .products-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--color-accent-light);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.3px;
        text-transform: uppercase;
    }

    .products-eyebrow::before {
        content: '';
        display: block;
        width: 16px;
        height: 2px;
        border-radius: 2px;
        background: var(--color-accent);
    }

    .products-header h1 {
        margin: 6px 0 4px;
        color: var(--color-text-primary);
        font-size: 26px;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .products-subtitle {
        margin: 0;
        color: var(--color-text-muted);
        font-size: 12px;
        line-height: 1.5;
    }

    /* ============================================
       BOTONES
       ============================================ */
    .products-create,
    .filter-button,
    .camera-button,
    .clear-button,
    .product-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 38px;
        padding: 0 14px;
        border-radius: var(--radius-md);
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: all var(--transition-fast);
        white-space: nowrap;
    }

    .products-create,
    .filter-button {
        border: 1px solid var(--color-accent);
        color: #fff;
        background: var(--color-accent);
        box-shadow: 0 2px 8px rgba(227, 27, 35, 0.25);
    }

    .products-create:hover,
    .filter-button:hover {
        color: #fff;
        background: var(--color-accent-hover);
        border-color: var(--color-accent-hover);
        box-shadow: 0 4px 14px rgba(227, 27, 35, 0.35);
        transform: translateY(-1px);
    }

    .products-create:active,
    .filter-button:active {
        transform: translateY(0);
    }

    .camera-button {
        border: 1px solid rgba(227, 27, 35, 0.45);
        color: var(--color-accent-light);
        background: var(--color-accent-soft);
    }

    .camera-button:hover {
        color: #fff;
        background: var(--color-accent);
        border-color: var(--color-accent);
    }

    .clear-button,
    .product-action {
        border: 1px solid var(--color-border-light);
        color: var(--color-text-secondary);
        background: var(--color-bg-tertiary);
    }

    .clear-button:hover,
    .product-action:hover {
        color: var(--color-text-primary);
        border-color: var(--color-border-hover);
        background: var(--color-bg-elevated);
    }

    .products-create svg,
    .camera-button svg,
    .product-action svg {
        width: 15px;
        height: 15px;
        flex: 0 0 auto;
    }

    /* ============================================
       ALERTAS
       ============================================ */
    .products-alert {
        position: fixed;
        z-index: 1500;
        top: 50%;
        left: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        width: min(520px, calc(100vw - 32px));
        min-height: 64px;
        padding: 18px 22px;
        border-radius: var(--radius-xl);
        box-shadow: 0 18px 55px rgba(0, 0, 0, .58);
        font-size: 14px;
        font-weight: 600;
        line-height: 1.5;
        text-align: center;
        transform: translate(-50%, -50%);
        animation: centerAlertIn 220ms ease both;
    }

    .products-alert svg {
        width: 20px;
        height: 20px;
        flex: 0 0 auto;
    }

    .products-alert.is-hiding {
        opacity: 0;
        transition: opacity 220ms ease, transform 220ms ease;
        transform: translate(-50%, -46%);
    }

    @keyframes centerAlertIn {
        from { opacity: 0; transform: translate(-50%, calc(-50% - 10px)); }
        to { opacity: 1; transform: translate(-50%, -50%); }
    }

    .products-alert.success {
        border: 1px solid rgba(56, 212, 120, 0.25);
        color: #8de5b2;
        background: var(--color-success-soft);
    }

    .products-alert.error {
        border: 1px solid rgba(227, 27, 35, 0.25);
        color: #ff9297;
        background: rgba(227, 27, 35, 0.08);
    }

    /* ============================================
       TARJETAS DE RESUMEN
       ============================================ */
    .products-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .summary-card {
        position: relative;
        padding: 16px;
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        background: var(--color-bg-secondary);
        overflow: hidden;
        transition: border-color var(--transition-fast), transform var(--transition-fast);
    }

    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 3px;
        height: 100%;
        background: var(--color-accent);
        opacity: 0;
        transition: opacity var(--transition-fast);
    }

    .summary-card:hover {
        border-color: var(--color-border-hover);
        transform: translateY(-2px);
    }

    .summary-card:hover::before {
        opacity: 1;
    }

    .summary-card__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .summary-card span {
        display: block;
        color: var(--color-text-muted);
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .summary-card__icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: var(--radius-sm);
        background: var(--color-bg-tertiary);
        color: var(--color-text-muted);
    }

    .summary-card__icon svg {
        width: 14px;
        height: 14px;
    }

    .summary-card strong {
        display: block;
        color: var(--color-text-primary);
        font-size: 24px;
        font-weight: 700;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }

    .summary-card__hint {
        display: block;
        margin-top: 4px;
        color: var(--color-text-dim);
        font-size: 10px;
    }

    /* ============================================
       PANEL PRINCIPAL
       ============================================ */
    .products-panel {
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        background: var(--color-bg-secondary);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    /* ============================================
       FILTROS
       ============================================ */
    .products-filters {
        display: grid;
        grid-template-columns: minmax(220px, 1.6fr) auto minmax(160px, 0.8fr) minmax(140px, 0.6fr) auto;
        gap: 10px;
        padding: 16px;
        border-bottom: 1px solid var(--color-border);
        background: var(--color-bg-secondary);
    }

    .barcode-reader-help {
        grid-column: 1 / -1;
        margin: -4px 2px 0;
        color: var(--color-text-muted);
        font-size: 11px;
        line-height: 1.5;
    }

    .products-filters input,
    .products-filters select {
        width: 100%;
        min-height: 40px;
        padding: 0 12px;
        border: 1px solid var(--color-border-light);
        border-radius: var(--radius-md);
        outline: none;
        color: var(--color-text-secondary);
        background: var(--color-bg-tertiary);
        font-size: 12px;
        transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
    }

    .products-filters input::placeholder {
        color: var(--color-text-dim);
    }

    .products-filters input:focus,
    .products-filters select:focus {
        border-color: var(--color-accent);
        box-shadow: 0 0 0 3px rgba(227, 27, 35, 0.1);
    }

    .products-filters select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23a1a5b1' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 34px;
    }

    .filter-actions {
        display: flex;
        gap: 8px;
    }

    /* ============================================
       TABLA
       ============================================ */
    .products-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .products-table {
        width: 100%;
        min-width: 950px;
        border-collapse: collapse;
    }

    .products-table th {
        padding: 12px 14px;
        border-bottom: 1px solid var(--color-border);
        color: var(--color-text-muted);
        background: var(--color-bg-tertiary);
        font-size: 9px;
        font-weight: 700;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        white-space: nowrap;
    }

    .products-table td {
        padding: 12px 14px;
        border-bottom: 1px solid rgba(48, 51, 61, 0.5);
        color: var(--color-text-secondary);
        font-size: 11px;
        vertical-align: middle;
    }

    .products-table tbody tr {
        transition: background var(--transition-fast);
    }

    .products-table tbody tr:hover {
        background: var(--color-bg-tertiary);
    }

    .products-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ============================================
       CELDA DE PRODUCTO
       ============================================ */
    .product-cell {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 200px;
    }

    .product-thumb {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        overflow: hidden;
        border: 1px solid var(--color-border-light);
        border-radius: var(--radius-md);
        color: var(--color-text-muted);
        background: var(--color-bg-tertiary);
    }

    .product-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-name,
    .product-code {
        display: block;
    }

    .product-name {
        color: var(--color-text-primary);
        font-weight: 600;
        font-size: 12px;
    }

    .product-code {
        margin-top: 3px;
        color: var(--color-text-muted);
        font-size: 10px;
        font-family: ui-monospace, 'SF Mono', monospace;
    }

    /* ============================================
       BADGES DE ESTADO
       ============================================ */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge::before {
        content: '';
        display: block;
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: currentColor;
    }

    .status-badge.active {
        color: #8de5b2;
        background: var(--color-success-soft);
    }

    .status-badge.inactive {
        color: #ff9297;
        background: rgba(227, 27, 35, 0.1);
    }

    /* ============================================
       ACCIONES
       ============================================ */
    .product-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .product-actions form {
        margin: 0;
    }

    .product-action {
        min-height: 30px;
        padding: 0 10px;
        font-size: 10px;
    }

    .product-action.delete {
        border-color: rgba(227, 27, 35, 0.45);
        color: #ff9297;
        background: rgba(227, 27, 35, 0.08);
    }

    .product-action.delete:hover {
        color: #fff;
        background: var(--color-accent-hover);
        border-color: var(--color-accent-hover);
    }

    /* ============================================
       ESTADO VACÍO
       ============================================ */
    .empty-products {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 48px 24px;
        color: var(--color-text-muted);
        text-align: center;
    }

    .empty-products svg {
        width: 40px;
        height: 40px;
        color: var(--color-border-hover);
        margin-bottom: 4px;
    }

    .empty-products strong {
        display: block;
        color: var(--color-text-secondary);
        font-size: 14px;
    }

    .empty-products p {
        margin: 0;
        font-size: 12px;
        max-width: 320px;
        line-height: 1.5;
    }

    /* ============================================
       PAGINACIÓN
       ============================================ */
    .products-pagination {
        padding: 16px;
        border-top: 1px solid var(--color-border);
    }

    /* ============================================
       DIÁLOGOS
       ============================================ */
    .product-dialog {
        width: min(480px, calc(100% - 28px));
        max-height: calc(100dvh - 32px);
        margin: auto;
        padding: 0;
        overflow: auto;
        border: 1px solid var(--color-border-light);
        border-radius: var(--radius-xl);
        color: var(--color-text-secondary);
        background: var(--color-bg-secondary);
        box-shadow: var(--shadow-lg);
    }

    .product-dialog::backdrop {
        background: rgba(0, 0, 0, 0.76);
        backdrop-filter: blur(4px);
    }

    .product-dialog[open] {
        animation: dialogIn var(--transition-normal);
    }

    @keyframes dialogIn {
        from {
            opacity: 0;
            transform: scale(0.96) translateY(8px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .dialog-content {
        padding: 24px;
    }

    .dialog-content h2 {
        margin: 8px 0 10px;
        color: var(--color-text-primary);
        font-size: 19px;
        font-weight: 700;
        letter-spacing: -0.01em;
    }

    .dialog-content p {
        color: var(--color-text-muted);
        font-size: 12px;
        line-height: 1.6;
        margin: 0;
    }

    .dialog-warning {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-top: 14px;
        padding: 12px 14px;
        border: 1px solid rgba(227, 27, 35, 0.25);
        border-radius: var(--radius-md);
        color: #ffb4b8;
        background: rgba(227, 27, 35, 0.08);
        font-size: 11px;
        line-height: 1.5;
    }

    .dialog-warning svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .dialog-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 20px;
    }

    .dialog-actions button {
        min-height: 38px;
        padding: 0 16px;
        border: 1px solid var(--color-border-light);
        border-radius: var(--radius-md);
        color: var(--color-text-secondary);
        background: var(--color-bg-tertiary);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .dialog-actions button:hover {
        background: var(--color-bg-elevated);
        border-color: var(--color-border-hover);
    }

    .dialog-actions .confirm-delete,
    .dialog-actions .confirm-status {
        border-color: var(--color-accent);
        color: #fff;
        background: var(--color-accent);
    }

    .dialog-actions .confirm-delete:hover,
    .dialog-actions .confirm-status:hover {
        background: var(--color-accent-hover);
        border-color: var(--color-accent-hover);
    }

    /* ============================================
       CÁMARA + MARCO DE ESCANEO
       ============================================ */
    .camera-dialog {
        width: min(600px, calc(100% - 28px));
    }

    .camera-dialog .dialog-content {
        padding: 20px 24px 24px;
    }

    .scanner-stage {
        position: relative;
        width: 100%;
        margin: 6px 0 14px;
        border-radius: var(--radius-lg);
        overflow: hidden;
        background: #09090b;
        border: 1px solid var(--color-border-light);
        aspect-ratio: 4 / 3;
    }

    .scanner-stage video {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        background: #09090b;
    }

    /* Marco de escaneo superpuesto */
    .scanner-overlay {
        position: absolute;
        inset: 0;
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .scanner-frame {
        position: relative;
        width: 72%;
        max-width: 340px;
        aspect-ratio: 16 / 9;
        border-radius: 12px;
        box-shadow: 0 0 0 100vmax rgba(0, 0, 0, 0.45);
    }

    /* Esquinas del marco */
    .scanner-frame::before,
    .scanner-frame::after,
    .scanner-corner::before,
    .scanner-corner::after {
        content: '';
        position: absolute;
        width: 26px;
        height: 26px;
        border: 3px solid var(--color-accent-light);
        border-radius: 2px;
        filter: drop-shadow(0 0 6px rgba(227, 27, 35, 0.6));
    }

    .scanner-frame::before {
        top: -2px;
        left: -2px;
        border-right: none;
        border-bottom: none;
        border-top-left-radius: 12px;
    }

    .scanner-frame::after {
        top: -2px;
        right: -2px;
        border-left: none;
        border-bottom: none;
        border-top-right-radius: 12px;
    }

    .scanner-corner::before {
        bottom: -2px;
        left: -2px;
        border-right: none;
        border-top: none;
        border-bottom-left-radius: 12px;
    }

    .scanner-corner::after {
        bottom: -2px;
        right: -2px;
        border-left: none;
        border-top: none;
        border-bottom-right-radius: 12px;
    }

    /* Línea láser animada */
    .scanner-laser {
        position: absolute;
        left: 8%;
        right: 8%;
        height: 2px;
        background: linear-gradient(90deg,
            transparent 0%,
            rgba(255, 119, 125, 0.2) 10%,
            rgba(255, 119, 125, 0.95) 50%,
            rgba(255, 119, 125, 0.2) 90%,
            transparent 100%);
        box-shadow: 0 0 14px 2px rgba(227, 27, 35, 0.55);
        animation: scanLine 2.2s ease-in-out infinite;
        border-radius: 2px;
    }

    @keyframes scanLine {
        0%   { top: 12%; opacity: 0.2; }
        15%  { opacity: 1; }
        50%  { top: 86%; opacity: 1; }
        85%  { opacity: 1; }
        100% { top: 12%; opacity: 0.2; }
    }

    .scanner-hint {
        position: absolute;
        bottom: 12px;
        left: 50%;
        transform: translateX(-50%);
        padding: 5px 12px;
        border-radius: 20px;
        background: rgba(15, 16, 20, 0.75);
        backdrop-filter: blur(4px);
        color: var(--color-text-secondary);
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.4px;
        white-space: nowrap;
    }

    #barcode-camera-status {
        min-height: 20px;
        color: var(--color-text-muted);
        font-size: 11px;
        text-align: center;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 900px) {
        .products-filters {
            grid-template-columns: 1fr 1fr;
        }

        .products-summary {
            grid-template-columns: 1fr;
        }

        .products-header__icon {
            display: none;
        }
    }

    @media (max-width: 600px) {
        .products-page {
            gap: 16px;
        }

        .products-header h1 {
            font-size: 22px;
        }

        .products-filters {
            grid-template-columns: 1fr;
            padding: 12px;
        }

        .filter-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .products-create {
            width: 100%;
        }

        .dialog-content {
            padding: 20px;
        }

        .scanner-frame {
            width: 82%;
        }
    }
</style>
@endpush

@section('content')
<div class="products-page">
    {{-- HEADER --}}
    <header class="products-header">
        <div class="products-header__content">
            <div class="products-header__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    <path d="M3.27 6.96L12 12.01l8.73-5.05M12 22.08V12"/>
                </svg>
            </div>
            <div>
                <span class="products-eyebrow">Inventario · Catálogo</span>
                <h1>Productos</h1>
                <p class="products-subtitle">
                    Busca por nombre, código interno o código de barras.
                </p>
            </div>
        </div>
        <a href="{{ route('admin.productos.create') }}" class="products-create">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Nuevo producto
        </a>
    </header>

    {{-- ALERTAS --}}
    @if (session('success'))
        <div class="products-alert success" role="alert">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <path d="M22 4L12 14.01l-3-3"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="products-alert error" role="alert">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path d="M15 9l-6 6M9 9l6 6"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- RESUMEN --}}
    <section class="products-summary" aria-label="Resumen del catálogo">
        <article class="summary-card">
            <div class="summary-card__header">
                <span>Productos encontrados</span>
                <div class="summary-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        <path d="M3.27 6.96L12 12.01l8.73-5.05M12 22.08V12"/>
                    </svg>
                </div>
            </div>
            <strong>{{ $productos->total() }}</strong>
            <span class="summary-card__hint">en el catálogo actual</span>
        </article>

        <article class="summary-card">
            <div class="summary-card__header">
                <span>Categorías disponibles</span>
                <div class="summary-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
            </div>
            <strong>{{ $categorias->count() }}</strong>
            <span class="summary-card__hint">para clasificar productos</span>
        </article>

        <article class="summary-card">
            <div class="summary-card__header">
                <span>Página actual</span>
                <div class="summary-card__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <path d="M3 9h18M9 21V9"/>
                    </svg>
                </div>
            </div>
            <strong>{{ $productos->currentPage() }} <span style="font-size: 14px; color: var(--color-text-muted);">/ {{ max(1, $productos->lastPage()) }}</span></strong>
            <span class="summary-card__hint">páginas de resultados</span>
        </article>
    </section>

    {{-- PANEL PRINCIPAL --}}
    <section class="products-panel">
        {{-- FILTROS --}}
        <form
            action="{{ route('admin.productos.index') }}"
            method="GET"
            class="products-filters"
            id="product-search-form"
            role="search"
        >
            <input
                type="search"
                name="buscar"
                id="product-search"
                value="{{ $buscar ?? request('buscar') }}"
                placeholder="Buscar producto o enfocar aquí y escanear código..."
                autocomplete="off"
                autofocus
                aria-label="Buscar por nombre, código interno o escanear con lector USB o Bluetooth"
                aria-describedby="barcode-reader-help"
            >
            <button type="button" class="camera-button" id="open-barcode-camera" aria-label="Escanear código de barras con la cámara">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2"/>
                    <path d="M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2"/>
                    <path d="M7 12h10"/>
                </svg>
                Escanear
            </button>
            <select name="categoria_id" aria-label="Filtrar por categoría">
                <option value="">Todas las categorías</option>
                @foreach ($categorias as $categoria)
                    <option
                        value="{{ $categoria->id }}"
                        @selected((string) request('categoria_id') === (string) $categoria->id)
                    >
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>
            <select name="estado" aria-label="Filtrar por estado">
                <option value="">Todos los estados</option>
                <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
            </select>
            <div class="filter-actions">
                <button type="submit" class="filter-button">Buscar</button>
                @if (request()->filled('buscar') || request()->filled('categoria_id') || request()->filled('estado'))
                    <a href="{{ route('admin.productos.index') }}" class="clear-button">Limpiar</a>
                @endif
            </div>
            <p class="barcode-reader-help" id="barcode-reader-help">
                Lector USB o Bluetooth: haz clic en el campo y escanea. También puedes buscar escribiendo el código.
            </p>
        </form>

        {{-- TABLA --}}
        @if ($productos->count())
            <div class="products-table-wrap">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Código de barras</th>
                            <th>Categoría</th>
                            <th>Marca</th>
                            <th>Precio de venta</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $producto)
                            <tr>
                                <td>
                                    <div class="product-cell">
                                        <div class="product-thumb">
                                            @if ($producto->imagen_url)
                                                <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" loading="lazy">
                                            @else
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                                    <path d="M21 15l-5-5L5 21"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="product-name">{{ $producto->nombre }}</span>
                                            <span class="product-code">{{ $producto->codigo_interno }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($producto->codigoBarraPrincipal?->codigo)
                                        <code style="font-size: 10px; color: var(--color-text-muted);">{{ $producto->codigoBarraPrincipal->codigo }}</code>
                                    @else
                                        <span style="color: var(--color-text-dim);">—</span>
                                    @endif
                                </td>
                                <td>{{ $producto->categoria?->nombre ?? '—' }}</td>
                                <td>{{ $producto->marca?->nombre ?? '—' }}</td>
                                <td>
                                    @if ($producto->precioActual)
                                        <strong style="color: var(--color-text-primary); font-weight: 600;">
                                            Bs {{ number_format((float) $producto->precioActual->precio_venta, 2, '.', ',') }}
                                        </strong>
                                    @else
                                        <span style="color: var(--color-text-dim);">Sin precio</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge {{ $producto->estado === 'activo' ? 'active' : 'inactive' }}">
                                        {{ ucfirst($producto->estado) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="product-actions">
                                        <a
                                            href="{{ route('admin.productos.show', $producto) }}"
                                            class="product-action"
                                            title="Ver producto"
                                        >
                                            Ver
                                        </a>
                                        <a
                                            href="{{ route('admin.productos.edit', $producto) }}"
                                            class="product-action"
                                            title="Editar producto"
                                        >
                                            Editar
                                        </a>
                                        <form
                                            action="{{ route('admin.productos.estado', $producto) }}"
                                            method="POST"
                                            data-status-form
                                            data-product-name="{{ $producto->nombre }}"
                                            data-current-status="{{ $producto->estado }}"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="button"
                                                class="product-action"
                                                data-open-status
                                                title="{{ $producto->estado === 'activo' ? 'Desactivar producto' : 'Activar producto' }}"
                                            >
                                                {{ $producto->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                        <form
                                            action="{{ route('admin.productos.destroy', $producto) }}"
                                            method="POST"
                                            data-delete-form
                                            data-product-name="{{ $producto->nombre }}"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="button"
                                                class="product-action delete"
                                                data-open-delete
                                                title="Eliminar permanentemente"
                                            >
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($productos->hasPages())
                <div class="products-pagination">
                    {{ $productos->links() }}
                </div>
            @endif
        @else
            <div class="empty-products">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
                <strong>No se encontraron productos</strong>
                <p>Prueba con otro término de búsqueda o modifica los filtros aplicados.</p>
            </div>
        @endif
    </section>
</div>

{{-- DIÁLOGO: CAMBIO DE ESTADO --}}
<dialog class="product-dialog" id="status-dialog">
    <div class="dialog-content">
        <span class="products-eyebrow">Confirmar cambio</span>
        <h2 id="status-title">Cambiar estado del producto</h2>
        <p id="status-message"></p>
        <div class="dialog-actions">
            <button type="button" id="cancel-status">Cancelar</button>
            <button type="button" class="confirm-status" id="confirm-status">
                Confirmar
            </button>
        </div>
    </div>
</dialog>

{{-- DIÁLOGO: ELIMINACIÓN --}}
<dialog class="product-dialog" id="delete-dialog">
    <div class="dialog-content">
        <span class="products-eyebrow">Confirmar eliminación</span>
        <h2>¿Eliminar <span id="delete-product-name"></span>?</h2>
        <p>El producto se eliminará permanentemente. Esta acción no se puede deshacer.</p>
        <div class="dialog-warning">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <path d="M12 9v4M12 17h.01"/>
            </svg>
            <span>Si tiene inventario, lotes o movimientos registrados, el sistema impedirá eliminarlo.</span>
        </div>
        <div class="dialog-actions">
            <button type="button" id="cancel-delete">Cancelar</button>
            <button type="button" class="confirm-delete" id="confirm-delete">
                Sí, eliminar
            </button>
        </div>
    </div>
</dialog>

{{-- DIÁLOGO: CÁMARA --}}
<dialog class="product-dialog camera-dialog" id="barcode-camera-dialog">
    <div class="dialog-content">
        <span class="products-eyebrow">Lector de productos</span>
        <h2>Escanea el código de barras</h2>

        <div class="scanner-stage">
            <video id="barcode-camera-video" autoplay muted playsinline></video>
            <div class="scanner-overlay" aria-hidden="true">
                <div class="scanner-frame">
                    <div class="scanner-corner"></div>
                    <div class="scanner-laser"></div>
                </div>
                <span class="scanner-hint">Coloca el código dentro del marco</span>
            </div>
        </div>

        <p id="barcode-camera-status" aria-live="polite">La cámara todavía no está activa.</p>
        <div class="dialog-actions">
            <button type="button" id="close-barcode-camera">Cerrar cámara</button>
        </div>
    </div>
</dialog>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.products-alert[role="alert"]').forEach(function (alert) {
        window.setTimeout(function () {
            alert.classList.add('is-hiding');
            window.setTimeout(function () { alert.remove(); }, 240);
        }, 4200);
    });

    // ============================================
    // DIÁLOGO DE ESTADO
    // ============================================
    const statusDialog = document.getElementById('status-dialog');
    const statusTitle = document.getElementById('status-title');
    const statusMessage = document.getElementById('status-message');
    const cancelStatus = document.getElementById('cancel-status');
    const confirmStatus = document.getElementById('confirm-status');

    const deleteDialog = document.getElementById('delete-dialog');
    const deleteName = document.getElementById('delete-product-name');
    const cancelDelete = document.getElementById('cancel-delete');
    const confirmDelete = document.getElementById('confirm-delete');

    let pendingStatusForm = null;
    let pendingDeleteForm = null;

    // Abrir diálogo de estado
    document.querySelectorAll('[data-open-status]').forEach(function (button) {
        button.addEventListener('click', function () {
            pendingStatusForm = button.closest('[data-status-form]');
            if (!pendingStatusForm) return;

            const nombre = pendingStatusForm.dataset.productName || 'este producto';
            const estaActivo = pendingStatusForm.dataset.currentStatus === 'activo';

            statusTitle.textContent = estaActivo
                ? '¿Desactivar producto?'
                : '¿Activar producto?';

            statusMessage.textContent = estaActivo
                ? `Se desactivará "${nombre}". No estará disponible para nuevas operaciones, pero podrás activarlo nuevamente.`
                : `Se activará "${nombre}". Volverá a estar disponible para nuevas operaciones.`;

            confirmStatus.textContent = estaActivo ? 'Sí, desactivar' : 'Sí, activar';
            statusDialog.showModal();
        });
    });

    cancelStatus.addEventListener('click', function () {
        pendingStatusForm = null;
        statusDialog.close();
    });

    confirmStatus.addEventListener('click', function () {
        if (!pendingStatusForm) {
            statusDialog.close();
            return;
        }
        const form = pendingStatusForm;
        pendingStatusForm = null;
        statusDialog.close();
        form.requestSubmit();
    });

    // Abrir diálogo de eliminación
    document.querySelectorAll('[data-open-delete]').forEach(function (button) {
        button.addEventListener('click', function () {
            pendingDeleteForm = button.closest('[data-delete-form]');
            if (!pendingDeleteForm) return;
            deleteName.textContent = pendingDeleteForm.dataset.productName || 'este producto';
            deleteDialog.showModal();
        });
    });

    cancelDelete.addEventListener('click', function () {
        pendingDeleteForm = null;
        deleteDialog.close();
    });

    confirmDelete.addEventListener('click', function () {
        if (!pendingDeleteForm) {
            deleteDialog.close();
            return;
        }
        const form = pendingDeleteForm;
        pendingDeleteForm = null;
        deleteDialog.close();
        form.requestSubmit();
    });

    // ============================================
    // CÁMARA + LECTOR FÍSICO USB / BLUETOOTH (HID)
    // ============================================
    const searchForm = document.getElementById('product-search-form');
    const searchInput = document.getElementById('product-search');
    const openCamera = document.getElementById('open-barcode-camera');
    const cameraDialog = document.getElementById('barcode-camera-dialog');
    const closeCamera = document.getElementById('close-barcode-camera');
    const video = document.getElementById('barcode-camera-video');
    const cameraStatus = document.getElementById('barcode-camera-status');

    let cameraStream = null;
    let scanActive = false;
    let barcodeDetector = null;
    let zxingReader = null;
    let zxingControls = null;
    let lastKeyAt = 0;
    let scannerKeyCount = 0;
    let scannerStartedAt = 0;
    let scannerSubmitTimer = null;

    function submitBarcodeSearch() {
        if (!searchInput.value.trim()) return;
        window.clearTimeout(scannerSubmitTimer);
        scannerKeyCount = 0;
        scannerStartedAt = 0;
        searchForm.requestSubmit();
    }

    // Los lectores USB/Bluetooth en modo teclado escriben en el campo enfocado.
    // Acepta Enter como terminador y también detecta escaneos rápidos sin sufijo.
    searchInput.addEventListener('keydown', function (event) {
        const now = performance.now();
        if (event.key.length === 1 && !event.ctrlKey && !event.altKey && !event.metaKey) {
            if (!lastKeyAt || now - lastKeyAt > 100) {
                scannerStartedAt = now;
                scannerKeyCount = 0;
            }
            scannerKeyCount += 1;
            lastKeyAt = now;
            window.clearTimeout(scannerSubmitTimer);
            scannerSubmitTimer = window.setTimeout(function () {
                const duration = lastKeyAt - scannerStartedAt;
                if (scannerKeyCount >= 8 && duration <= 900) submitBarcodeSearch();
            }, 180);
            return;
        }

        if (event.key === 'Enter' && !event.isComposing) {
            event.preventDefault();
            submitBarcodeSearch();
        }
    });

    document.addEventListener('keydown', function (event) {
        const target = event.target;
        const isTyping = target instanceof HTMLElement && (
            target.isContentEditable || /^(INPUT|TEXTAREA|SELECT)$/.test(target.tagName)
        );
        if (event.key === '/' && !isTyping && !event.ctrlKey && !event.metaKey && !event.altKey) {
            event.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
    });

    function abrirDialogoCamara() {
        if (!cameraDialog.open) cameraDialog.showModal();
    }

    function detenerCamara() {
        scanActive = false;

        if (zxingControls) {
            try { zxingControls.stop(); } catch (error) {}
            zxingControls = null;
        }
        if (zxingReader) {
            try { zxingReader.reset(); } catch (error) {}
            zxingReader = null;
        }
        if (cameraStream) {
            cameraStream.getTracks().forEach(function (track) { track.stop(); });
            cameraStream = null;
        }
        if (video.srcObject) {
            const tracks = video.srcObject.getTracks ? video.srcObject.getTracks() : [];
            tracks.forEach(function (track) { track.stop(); });
            video.srcObject = null;
        }
        if (cameraDialog.open) cameraDialog.close();
    }

    function codigoDetectado(valor) {
        const codigo = String(valor || '').trim();
        if (!codigo) return;
        searchInput.value = codigo;
        detenerCamara();
        submitBarcodeSearch();
    }

    async function cargarZXing() {
        if (window.ZXingBrowser) return true;

        return new Promise(function (resolve) {
            let script = document.querySelector('script[data-zxing-nanos]');
            if (script) {
                if (window.ZXingBrowser) return resolve(true);
                script.addEventListener('load', function () {
                    resolve(Boolean(window.ZXingBrowser));
                }, { once: true });
                script.addEventListener('error', function () { resolve(false); }, { once: true });
                return;
            }

            script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/@zxing/browser@0.1.5/umd/zxing-browser.min.js';
            script.async = true;
            script.dataset.zxingNanos = '1';
            script.onload = function () { resolve(Boolean(window.ZXingBrowser)); };
            script.onerror = function () { resolve(false); };
            document.head.appendChild(script);
        });
    }

    async function iniciarCamaraZXing() {
        cameraStatus.textContent = 'Preparando el lector de cámara...';
        const cargado = await cargarZXing();

        if (!cargado || !window.ZXingBrowser) {
            throw new Error('No se pudo cargar el lector alternativo. Verifica la conexión a internet.');
        }

        scanActive = true;
        zxingReader = new window.ZXingBrowser.BrowserMultiFormatReader();
        cameraStatus.textContent = 'Solicitando permiso para usar la cámara...';

        const dispositivos = await window.ZXingBrowser.BrowserCodeReader.listVideoInputDevices();
        const trasera = dispositivos.find(function (dispositivo) {
            return /back|rear|environment|trasera/i.test(dispositivo.label);
        });
        const dispositivoId = trasera
            ? trasera.deviceId
            : (dispositivos.length ? dispositivos[dispositivos.length - 1].deviceId : undefined);

        zxingControls = await zxingReader.decodeFromVideoDevice(
            dispositivoId,
            video,
            function (resultado) {
                if (resultado) codigoDetectado(resultado.getText());
            }
        );

        cameraStatus.textContent = 'Apunta la cámara al código de barras.';
    }

    async function detectarCuadro() {
        if (!scanActive || !barcodeDetector) return;

        try {
            if (video.readyState >= HTMLMediaElement.HAVE_CURRENT_DATA) {
                const resultados = await barcodeDetector.detect(video);
                if (resultados.length && resultados[0].rawValue) {
                    codigoDetectado(resultados[0].rawValue);
                    return;
                }
            }
        } catch (error) {
            // Se vuelve a intentar en el siguiente cuadro.
        }

        if (scanActive) window.setTimeout(detectarCuadro, 100);
    }

    async function iniciarCamaraNativa() {
        const formatosDisponibles = await window.BarcodeDetector.getSupportedFormats();
        const formatosDeseados = ['ean_13', 'ean_8', 'upc_a', 'upc_e', 'code_128', 'code_39', 'itf'];
        const formatos = formatosDeseados.filter(function (formato) {
            return formatosDisponibles.includes(formato);
        });

        barcodeDetector = formatos.length
            ? new window.BarcodeDetector({ formats: formatos })
            : new window.BarcodeDetector();

        cameraStatus.textContent = 'Solicitando permiso para usar la cámara...';
        cameraStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: { ideal: 'environment' } },
            audio: false
        });
        video.srcObject = cameraStream;
        await video.play();

        scanActive = true;
        cameraStatus.textContent = 'Apunta la cámara al código de barras.';
        detectarCuadro();
    }

    openCamera.addEventListener('click', async function () {
        abrirDialogoCamara();

        if (!window.isSecureContext || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            cameraStatus.textContent = 'La cámara requiere HTTPS o abrir el sistema desde localhost. Permite el acceso a la cámara en el navegador.';
            return;
        }

        cameraStatus.textContent = 'Iniciando el lector de cámara...';

        try {
            // BarcodeDetector no existe en todos los navegadores: ZXing sirve de alternativa.
            if ('BarcodeDetector' in window) {
                try {
                    await iniciarCamaraNativa();
                } catch (errorNativo) {
                    detenerCamara();
                    abrirDialogoCamara();
                    await iniciarCamaraZXing();
                }
            } else {
                await iniciarCamaraZXing();
            }
        } catch (error) {
            console.error(error);
            detenerCamara();
            abrirDialogoCamara();

            cameraStatus.textContent = error && error.name === 'NotAllowedError'
                ? 'No se concedió permiso para usar la cámara. Habilítalo en el navegador e inténtalo de nuevo.'
                : (error && error.name === 'NotFoundException'
                    ? 'No se encontró una cámara disponible en este dispositivo.'
                    : 'No se pudo iniciar la cámara. Comprueba el permiso, la conexión y vuelve a intentar.');
        }
    });

    closeCamera.addEventListener('click', function () {
        detenerCamara();
        searchInput.focus();
    });

    cameraDialog.addEventListener('click', function (event) {
        if (event.target === cameraDialog) {
            detenerCamara();
            searchInput.focus();
        }
    });

    cameraDialog.addEventListener('close', function () {
        scanActive = false;
        if (cameraStream) {
            cameraStream.getTracks().forEach(function (track) { track.stop(); });
            cameraStream = null;
        }
        video.srcObject = null;
    });
});
</script>
@endpush