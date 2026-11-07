@php
    $editando = isset($producto);

    $categoriaSeleccionada = old('categoria_id', $editando ? $producto->categoria_id : '');
    $subcategoriaSeleccionada = old('subcategoria_id', $editando ? $producto->subcategoria_id : '');
    $marcaSeleccionada = old('marca_id', $editando ? $producto->marca_id : '');
    $unidadSeleccionada = old('unidad_medida_id', $editando ? $producto->unidad_medida_id : '');
    $tipoSeleccionado = old('tipo_producto_id', $editando ? $producto->tipo_producto_id : '');

    $codigoBarrasActual = old(
        'codigo_barras',
        $editando ? optional($producto->codigoBarraPrincipal)->codigo : ''
    );

    $precioCompraActual = old(
        'precio_compra',
        $editando && $producto->precioActual ? $producto->precioActual->precio_compra : ''
    );

    $precioVentaActual = old(
        'precio_venta',
        $editando && $producto->precioActual ? $producto->precioActual->precio_venta : ''
    );

    $estadoActual = old('estado', $editando ? $producto->estado : 'activo');

    $proveedoresSeleccionados = collect(
        old(
            'proveedores',
            $editando && isset($producto)
                ? $producto->proveedores->pluck('id')->all()
                : []
        )
    )->map(fn ($id) => (string) $id)->all();
@endphp

@push('styles')
<style>
    /* ============================================================
       FORMULARIO DE PRODUCTO · NANOS
       ============================================================ */
    .product-form-panel {
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
        overflow: hidden;
        border: 1px solid var(--color-border);
        border-radius: var(--radius-lg);
        background: var(--color-bg-secondary);
        box-shadow: var(--shadow-sm);
    }

    .product-form-panel * {
        box-sizing: border-box;
    }

    /* ===================== SECCIONES ===================== */
    .product-form-section {
        position: relative;
        padding: 26px 26px 24px;
        border-bottom: 1px solid var(--color-border);
    }

    .product-form-section:last-of-type {
        border-bottom: none;
    }

    .product-section-heading {
        display: flex;
        align-items: center;
        gap: 13px;
        margin-bottom: 22px;
    }

    .product-section-icon {
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        display: grid;
        place-items: center;
        border: 1px solid rgba(227, 27, 35, .2);
        border-radius: var(--radius-md);
        color: var(--color-accent-light);
        background: var(--color-accent-soft);
        font-size: 13px;
        font-weight: 800;
        letter-spacing: .2px;
    }

    .product-section-icon svg {
        width: 18px;
        height: 18px;
    }

    .product-section-heading strong {
        display: block;
        color: var(--color-text-primary);
        font-size: 13.5px;
        font-weight: 750;
        letter-spacing: -.1px;
    }

    .product-section-heading span {
        display: block;
        margin-top: 3px;
        color: var(--color-text-dim);
        font-size: 10.5px;
        line-height: 1.55;
    }

    /* ===================== HELPERS ===================== */
    .product-code-box {
        min-height: 47px;
        display: flex;
        align-items: center;
        padding: 0 14px;
        border: 1px solid var(--color-border);
        border-radius: var(--radius-sm);
        color: var(--color-text-secondary);
        background: var(--color-bg-primary);
        font-family: ui-monospace, "SFMono-Regular", Menlo, Consolas, monospace;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: .6px;
    }

    .product-help {
        display: block;
        margin-top: 7px;
        color: var(--color-text-dim);
        font-size: 10px;
        line-height: 1.55;
    }

    /* ===================== CANTIDAD INICIAL ===================== */
    .stock-inicial-control {
        display: flex;
        align-items: stretch;
        gap: 8px;
    }

    .stock-inicial-control .form-control {
        flex: 1;
        min-width: 0;
    }

    .stock-unidad {
        min-width: 90px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 12px;
        border: 1px solid var(--color-border);
        border-radius: var(--radius-sm);
        color: var(--color-text-secondary);
        background: var(--color-bg-primary);
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    /* ===================== INPUT CON PREFIJO ===================== */
    .product-input-group {
        display: flex;
        align-items: stretch;
    }

    .product-input-prefix {
        min-width: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 12px;
        border: 1px solid var(--color-border);
        border-right: 0;
        border-radius: var(--radius-sm) 0 0 var(--radius-sm);
        color: var(--color-text-muted);
        background: var(--color-bg-primary);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .3px;
    }

    .product-input-group .form-control {
        border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    }

    /* ===================== CÓDIGO DE BARRAS ===================== */
    .barcode-entry-row {
        display: flex;
        align-items: stretch;
        gap: 10px;
    }

    .barcode-entry-row input {
        flex: 1 1 auto;
        min-width: 0;
    }

    .barcode-entry-row button {
        flex: 0 0 auto;
        white-space: nowrap;
    }

    .barcode-scan-button {
        min-height: 45px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 16px;
        border: 1px solid rgba(227, 27, 35, .25);
        border-radius: var(--radius-sm);
        color: var(--color-accent-light);
        background: var(--color-accent-soft);
        font: inherit;
        font-size: 11px;
        font-weight: 750;
        letter-spacing: .2px;
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .barcode-scan-button:hover {
        color: #fff;
        border-color: var(--color-accent);
        background: var(--color-accent);
        box-shadow: 0 6px 18px rgba(227, 27, 35, .35);
        transform: translateY(-1px);
    }

    .barcode-scan-button:focus-visible {
        outline: 2px solid var(--color-accent-light);
        outline-offset: 2px;
    }

    .barcode-scan-button svg {
        width: 16px;
        height: 16px;
    }

    /* ===================== PESAJE ===================== */
    .product-weighing-info {
        display: none;
        margin-top: 10px;
        padding: 11px 13px;
        border: 1px solid rgba(255, 176, 32, .2);
        border-radius: var(--radius-sm);
        color: #e0b964;
        background: var(--color-warning-soft);
        font-size: 10.5px;
        line-height: 1.6;
    }

    .product-weighing-info.show {
        display: block;
    }

    /* ===================== IMAGEN ===================== */
    .product-image-container {
        display: grid;
        grid-template-columns: 170px 1fr;
        gap: 24px;
        align-items: center;
    }

    .product-image-preview {
        position: relative;
        width: 170px;
        height: 170px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 1px dashed rgba(255, 255, 255, .16);
        border-radius: var(--radius-lg);
        color: var(--color-text-muted);
        background: var(--color-bg-primary);
    }

    .product-image-preview img {
        width: 100%;
        height: 100%;
        display: none;
        object-fit: cover;
    }

    .product-image-preview img.show {
        display: block;
    }

    .product-image-empty {
        padding: 16px;
        text-align: center;
    }

    .product-image-empty svg {
        width: 34px;
        height: 34px;
        margin-bottom: 8px;
        color: var(--color-border-hover);
    }

    .product-image-empty strong {
        display: block;
        color: var(--color-text-muted);
        font-size: 11px;
        font-weight: 700;
    }

    .product-image-empty span {
        display: block;
        margin-top: 4px;
        color: var(--color-text-dim);
        font-size: 9.5px;
    }

    .product-file-button {
        min-height: 43px;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin: 0 !important;
        padding: 0 16px;
        border: 1px solid var(--color-border-light);
        border-radius: var(--radius-sm);
        color: var(--color-text-secondary) !important;
        background: var(--color-bg-tertiary);
        font-size: 11px !important;
        font-weight: 700 !important;
        letter-spacing: .2px;
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .product-file-button:hover {
        color: #fff !important;
        border-color: rgba(227, 27, 35, .35);
        background: var(--color-accent-soft);
    }

    .product-file-button:focus-within {
        outline: 2px solid var(--color-accent-light);
        outline-offset: 2px;
    }

    .product-file-button svg {
        width: 16px;
        height: 16px;
    }

    .product-file-input {
        display: none;
    }

    .product-file-name {
        display: block;
        margin-top: 9px;
        color: var(--color-text-muted);
        font-size: 10px;
    }

    /* ===================== ESTADO ===================== */
    .product-status-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .product-status-option {
        cursor: pointer;
    }

    .product-status-option input {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        pointer-events: none;
    }

    .product-status-card {
        min-height: 82px;
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 15px 16px;
        border: 1px solid var(--color-border);
        border-radius: var(--radius-md);
        background: rgba(255, 255, 255, .018);
        transition: border-color var(--transition-fast), background var(--transition-fast), box-shadow var(--transition-fast);
    }

    .product-status-option:hover .product-status-card {
        border-color: var(--color-border-light);
        background: rgba(255, 255, 255, .03);
    }

    .product-status-dot {
        width: 10px;
        height: 10px;
        flex-shrink: 0;
        border-radius: 50%;
    }

    .product-status-dot.active {
        background: var(--color-success);
        box-shadow: 0 0 10px rgba(56, 212, 120, .35);
    }

    .product-status-dot.inactive {
        background: var(--color-accent);
        box-shadow: 0 0 10px rgba(227, 27, 35, .35);
    }

    .product-status-card strong {
        display: block;
        color: var(--color-text-primary);
        font-size: 11.5px;
        font-weight: 750;
    }

    .product-status-card span {
        display: block;
        margin-top: 4px;
        color: var(--color-text-muted);
        font-size: 10px;
        line-height: 1.55;
    }

    .product-status-option input:checked + .product-status-card {
        border-color: rgba(227, 27, 35, .4);
        background: var(--color-accent-soft);
        box-shadow: 0 0 0 2px rgba(227, 27, 35, .08);
    }

    .product-status-option input:focus-visible + .product-status-card {
        outline: 2px solid var(--color-accent-light);
        outline-offset: 2px;
    }

    /* ===================== FOOTER ===================== */
    .product-form-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        padding: 20px 26px;
        border-top: 1px solid var(--color-border);
        background: rgba(0, 0, 0, .12);
    }

    .product-save-button {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 20px;
        border: none;
        border-radius: var(--radius-sm);
        color: #fff;
        background: var(--color-accent);
        font: inherit;
        font-size: 11.5px;
        font-weight: 750;
        letter-spacing: .2px;
        cursor: pointer;
        box-shadow: 0 6px 18px rgba(227, 27, 35, .35);
        transition: transform var(--transition-fast), box-shadow var(--transition-fast), background var(--transition-fast);
    }

    .product-save-button:hover {
        background: var(--color-accent-hover);
        transform: translateY(-1px);
        box-shadow: 0 10px 26px rgba(227, 27, 35, .45);
    }

    .product-save-button:active {
        transform: translateY(0);
    }

    .product-save-button:focus-visible {
        outline: 2px solid var(--color-accent-light);
        outline-offset: 2px;
    }

    .product-save-button svg {
        width: 16px;
        height: 16px;
    }

    .product-cancel-button {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 18px;
        border: 1px solid var(--color-border-light);
        border-radius: var(--radius-sm);
        color: var(--color-text-secondary);
        background: var(--color-bg-tertiary);
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .2px;
        text-decoration: none;
        transition: all var(--transition-fast);
    }

    .product-cancel-button:hover {
        color: #fff;
        border-color: var(--color-border-hover);
        background: var(--color-bg-elevated);
    }

    .product-cancel-button:focus-visible {
        outline: 2px solid var(--color-accent-light);
        outline-offset: 2px;
    }

    /* ===================== MODAL CÁMARA ===================== */
    .barcode-modal {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(0, 0, 0, .8);
        backdrop-filter: blur(5px);
    }

    .barcode-modal.show {
        display: flex;
    }

    .barcode-modal-content {
        width: min(560px, 100%);
        max-height: calc(100dvh - 40px);
        overflow: auto;
        border: 1px solid var(--color-border-light);
        border-radius: var(--radius-xl);
        background: var(--color-bg-secondary);
        box-shadow: var(--shadow-lg);
        animation: dialog-pop .22s ease-out;
    }

    @keyframes dialog-pop {
        from { opacity: 0; transform: translateY(8px) scale(.98); }
        to   { opacity: 1; transform: translateY(0)   scale(1); }
    }

    .barcode-modal-header {
        min-height: 62px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 0 18px;
        border-bottom: 1px solid var(--color-border);
    }

    .barcode-modal-header strong {
        color: var(--color-text-primary);
        font-size: 12.5px;
        font-weight: 750;
    }

    .barcode-modal-close {
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: var(--radius-sm);
        color: var(--color-text-muted);
        background: var(--color-bg-tertiary);
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .barcode-modal-close:hover {
        color: #fff;
        background: var(--color-accent);
    }

    .barcode-camera {
        position: relative;
        aspect-ratio: 4 / 3;
        max-height: min(55dvh, 440px);
        background: #000;
        overflow: hidden;
    }

    .barcode-camera video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Marco de escaneo con esquinas y láser */
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
        width: 74%;
        max-width: 340px;
        aspect-ratio: 16 / 9;
        border-radius: 12px;
        box-shadow: 0 0 0 100vmax rgba(0, 0, 0, 0.45);
    }

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
        top: -2px; left: -2px;
        border-right: none; border-bottom: none;
        border-top-left-radius: 12px;
    }

    .scanner-frame::after {
        top: -2px; right: -2px;
        border-left: none; border-bottom: none;
        border-top-right-radius: 12px;
    }

    .scanner-corner::before {
        bottom: -2px; left: -2px;
        border-right: none; border-top: none;
        border-bottom-left-radius: 12px;
    }

    .scanner-corner::after {
        bottom: -2px; right: -2px;
        border-left: none; border-top: none;
        border-bottom-right-radius: 12px;
    }

    .scanner-laser {
        position: absolute;
        left: 8%; right: 8%;
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

    .barcode-modal-status {
        padding: 14px 18px;
        color: var(--color-text-muted);
        font-size: 10.5px;
        text-align: center;
    }

    /* ===================== PRECIOS ===================== */
    .price-mode-row {
        margin-bottom: 18px;
    }

    .price-mode-row label,
    .price-field label {
        display: block;
        margin-bottom: 8px;
        color: var(--color-text-secondary);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .1px;
    }

    .price-mode-row select,
    .price-field input {
        width: 100%;
        min-height: 45px;
    }

    .price-calculator-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 15px;
    }

    .price-field-help {
        display: block;
        margin-top: 7px;
        color: var(--color-text-muted);
        font-size: 9.5px;
        line-height: 1.55;
    }

    .price-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
    }

    .price-summary-card {
        padding: 15px 16px;
        border: 1px solid var(--color-border);
        border-radius: var(--radius-md);
        background: var(--color-bg-primary);
    }

    .price-summary-card span {
        display: block;
        margin-bottom: 7px;
        color: var(--color-text-muted);
        font-size: 9.5px;
        font-weight: 600;
        letter-spacing: .4px;
        text-transform: uppercase;
    }

    .price-summary-card strong {
        color: var(--color-text-primary);
        font-size: 17px;
        font-weight: 800;
        letter-spacing: -.3px;
    }

    .price-summary-card.profit {
        border-color: rgba(56, 212, 120, .22);
        background: var(--color-success-soft);
    }

    .price-summary-card.profit strong {
        color: var(--color-success);
    }

    .price-warning {
        margin-top: 13px;
        padding: 11px 13px;
        border: 1px solid rgba(227, 27, 35, .25);
        border-radius: var(--radius-sm);
        color: #ff9297;
        background: rgba(227, 27, 35, .07);
        font-size: 10.5px;
        line-height: 1.55;
    }

    /* ===================== PROVEEDORES ===================== */
    .supplier-search {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 14px;
        padding: 0 14px;
        min-height: 46px;
        border: 1px solid var(--color-border-light);
        border-radius: var(--radius-sm);
        background: var(--color-bg-primary);
        transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
    }

    .supplier-search:focus-within {
        border-color: var(--color-accent);
        box-shadow: 0 0 0 3px rgba(227, 27, 35, .12);
    }

    .supplier-search svg {
        width: 17px;
        height: 17px;
        flex: 0 0 auto;
        color: var(--color-text-muted);
    }

    .supplier-search input {
        width: 100%;
        min-width: 0;
        min-height: 44px;
        border: 0;
        outline: 0;
        color: var(--color-text-secondary);
        background: transparent;
        font: inherit;
        font-size: 12px;
    }

    .supplier-search input::placeholder {
        color: var(--color-text-dim);
    }

    .supplier-search-count {
        margin: 0 0 12px;
        color: var(--color-text-muted);
        font-size: 11px;
    }

    .supplier-no-match {
        display: none;
        padding: 16px;
        color: var(--color-text-muted);
        border: 1px dashed rgba(255, 255, 255, .18);
        border-radius: var(--radius-sm);
        text-align: center;
        font-size: 11px;
    }

    .supplier-selection-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 11px;
    }

    .supplier-option[hidden] {
        display: none !important;
    }

    .supplier-option {
        position: relative;
        display: flex;
        min-height: 74px;
        align-items: center;
        gap: 13px;
        padding: 13px 15px;
        border: 1px solid var(--color-border);
        border-radius: var(--radius-md);
        background: var(--color-bg-primary);
        color: var(--color-text-primary);
        cursor: pointer;
        transition: border-color var(--transition-fast), background var(--transition-fast), transform var(--transition-fast);
    }

    .supplier-option:hover {
        transform: translateY(-1px);
        border-color: rgba(227, 27, 35, .42);
        background: var(--color-bg-tertiary);
    }

    .supplier-option input {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
    }

    .supplier-option-check {
        display: inline-flex;
        width: 20px;
        height: 20px;
        flex: 0 0 20px;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--color-border-hover);
        border-radius: 5px;
        background: var(--color-bg-tertiary);
        transition: all var(--transition-fast);
    }

    .supplier-option input:checked + .supplier-option-check {
        border-color: var(--color-accent);
        background: var(--color-accent);
        box-shadow: 0 0 0 3px var(--color-accent-soft);
    }

    .supplier-option input:checked + .supplier-option-check::after {
        width: 8px;
        height: 4px;
        border-bottom: 2px solid #fff1f2;
        border-left: 2px solid #fff1f2;
        content: "";
        transform: rotate(-45deg) translateY(-1px);
    }

    .supplier-option input:focus-visible + .supplier-option-check {
        outline: 3px solid rgba(227, 27, 35, .3);
        outline-offset: 2px;
    }

    .supplier-option-details strong,
    .supplier-option-details small {
        display: block;
    }

    .supplier-option-details strong {
        color: var(--color-text-primary);
        font-size: 11.5px;
        font-weight: 700;
        line-height: 1.5;
    }

    .supplier-option-details small {
        margin-top: 4px;
        color: var(--color-text-muted);
        font-size: 10px;
        line-height: 1.4;
    }

    .supplier-empty-state {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        padding: 14px 15px;
        border: 1px solid var(--color-border);
        border-radius: var(--radius-sm);
        background: var(--color-bg-primary);
        color: var(--color-text-secondary);
        font-size: 11px;
    }

    .supplier-empty-state a {
        color: var(--color-accent-light);
        font-weight: 700;
        text-decoration: none;
    }

    .supplier-empty-state a:hover {
        text-decoration: underline;
    }

    /* ===================== ERRORES ===================== */
    .error-message[role="alert"] {
        position: fixed;
        z-index: 200000;
        top: 50%;
        left: 50%;
        width: min(560px, calc(100vw - 32px));
        max-height: calc(100dvh - 40px);
        overflow-y: auto;
        padding: 20px 22px;
        border: 1px solid rgba(227, 27, 35, .4);
        border-radius: var(--radius-lg);
        color: #ffb4b8;
        background: #211416;
        box-shadow: 0 20px 70px rgba(0, 0, 0, .7);
        font-size: 13px;
        line-height: 1.6;
        transform: translate(-50%, -50%);
    }

    .error-message[role="alert"] strong {
        display: block;
        margin-bottom: 8px;
        color: #fff;
    }

    .error-message[role="alert"] ul {
        margin: 0;
        padding-left: 20px;
    }

    .field-error {
        display: block;
        margin-top: 6px;
        color: #ff858a;
        font-size: 10.5px;
        line-height: 1.5;
    }

    .required {
        color: var(--color-accent-light);
    }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 900px) {
        .price-calculator-grid,
        .price-summary {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 650px) {
        .product-form-section {
            padding: 20px 17px;
        }

        .product-image-container {
            grid-template-columns: 1fr;
        }

        .product-image-preview {
            width: 150px;
            height: 150px;
        }

        .product-status-grid,
        .price-calculator-grid,
        .price-summary,
        .supplier-selection-grid {
            grid-template-columns: 1fr;
        }

        .product-form-footer {
            padding: 17px;
            flex-direction: column-reverse;
        }

        .product-form-footer a,
        .product-form-footer button {
            width: 100%;
        }

        .barcode-entry-row {
            flex-direction: column;
        }

        .barcode-entry-row button {
            width: 100%;
        }

        .scanner-frame {
            width: 84%;
        }
    }

    @media (max-width: 620px) {
        .supplier-selection-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@if ($errors->any())
    <div class="error-message" role="alert">
        <strong>Corrige los siguientes datos:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-panel-admin product-form-panel">

    {{-- ========================================================= --}}
    {{-- DATOS GENERALES --}}
    {{-- ========================================================= --}}
    <div class="product-form-section">
        <div class="product-section-heading">
            <div class="product-section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    <polyline points="3.29 7 12 12 20.71 7"/>
                    <line x1="12" y1="22" x2="12" y2="12"/>
                </svg>
            </div>
            <div>
                <strong>Información general</strong>
                <span>Datos principales para identificar el producto.</span>
            </div>
        </div>

        <div class="form-grid">
            @if ($editando)
                <div class="form-group">
                    <label>Código interno</label>
                    <div class="product-code-box">{{ $producto->codigo_interno }}</div>
                    <span class="product-help">Generado automáticamente por NANOS.</span>
                </div>
            @endif

            <div class="form-group {{ !$editando ? 'full' : '' }}">
                <label for="nombre">
                    Nombre del producto <span class="required">*</span>
                </label>
                <input
                    type="text"
                    name="nombre"
                    id="nombre"
                    class="form-control"
                    maxlength="150"
                    required
                    autocomplete="off"
                    placeholder="Ej.: Coca-Cola 2 litros"
                    value="{{ old('nombre', $editando ? $producto->nombre : '') }}"
                >
                @error('nombre')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group full">
                <label for="descripcion">Descripción</label>
                <textarea
                    name="descripcion"
                    id="descripcion"
                    class="form-control"
                    placeholder="Descripción o características del producto..."
                >{{ old('descripcion', $editando ? $producto->descripcion : '') }}</textarea>
                @error('descripcion')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- CLASIFICACIÓN --}}
    {{-- ========================================================= --}}
    <div class="product-form-section">
        <div class="product-section-heading">
            <div class="product-section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M4 4h6v6H4z"/>
                    <path d="M14 4h6v6h-6z"/>
                    <path d="M4 14h6v6H4z"/>
                    <path d="M14 14h6v6h-6z"/>
                </svg>
            </div>
            <div>
                <strong>Clasificación</strong>
                <span>Categoría, subcategoría, marca y unidad de medida.</span>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="categoria_id">
                    Categoría <span class="required">*</span>
                </label>
                <select name="categoria_id" id="categoria_id" class="form-control" required>
                    <option value="">Seleccione</option>
                    @foreach ($categorias as $categoria)
                        <option
                            value="{{ $categoria->id }}"
                            @selected((string) $categoriaSeleccionada === (string) $categoria->id)
                        >
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('categoria_id')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="subcategoria_id">Subcategoría</label>
                <select name="subcategoria_id" id="subcategoria_id" class="form-control">
                    <option value="">Sin subcategoría</option>
                    @foreach ($subcategorias as $subcategoria)
                        <option
                            value="{{ $subcategoria->id }}"
                            data-categoria="{{ $subcategoria->categoria_id }}"
                            @selected((string) $subcategoriaSeleccionada === (string) $subcategoria->id)
                        >
                            {{ $subcategoria->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('subcategoria_id')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="marca_id">Marca</label>
                <select name="marca_id" id="marca_id" class="form-control">
                    <option value="">Sin marca</option>
                    @foreach ($marcas as $marca)
                        <option
                            value="{{ $marca->id }}"
                            @selected((string) $marcaSeleccionada === (string) $marca->id)
                        >
                            {{ $marca->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('marca_id')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="unidad_medida_id">
                    Unidad de medida <span class="required">*</span>
                </label>
                <select name="unidad_medida_id" id="unidad_medida_id" class="form-control" required>
                    <option value="">Seleccione</option>
                    @foreach ($unidadesMedida as $unidad)
                        <option
                            value="{{ $unidad->id }}"
                            data-abreviatura="{{ $unidad->abreviatura }}"
                            @selected((string) $unidadSeleccionada === (string) $unidad->id)
                        >
                            {{ $unidad->nombre }} ({{ $unidad->abreviatura }})
                        </option>
                    @endforeach
                </select>
                @error('unidad_medida_id')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group full">
                <label for="tipo_producto_id">
                    Tipo de producto <span class="required">*</span>
                </label>
                <select name="tipo_producto_id" id="tipo_producto_id" class="form-control" required>
                    <option value="">Seleccione</option>
                    @foreach ($tiposProducto as $tipo)
                        <option
                            value="{{ $tipo->id }}"
                            data-pesaje="{{ $tipo->permite_pesaje ? '1' : '0' }}"
                            @selected((string) $tipoSeleccionado === (string) $tipo->id)
                        >
                            {{ $tipo->nombre }}
                        </option>
                    @endforeach
                </select>
                <div class="product-weighing-info" id="pesajeInfo">
                    Este producto permite pesaje y podrá integrarse posteriormente con balanzas y etiquetas de peso.
                </div>
                @error('tipo_producto_id')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- CÓDIGO DE BARRAS NANOS --}}
    {{-- ========================================================= --}}
    <div class="product-form-section">
        <div class="product-section-heading">
            <div class="product-section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M3 5v14"/>
                    <path d="M7 5v14"/>
                    <path d="M10 5v14"/>
                    <path d="M14 5v14"/>
                    <path d="M18 5v14"/>
                    <path d="M21 5v14"/>
                </svg>
            </div>
            <div>
                <strong>Código de barras NANOS</strong>
                <span>Identificador interno generado automáticamente por el sistema.</span>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group full">
                <label for="codigo_barras">Código de barras del producto</label>
                <div class="barcode-entry-row">
                    <input
                        type="text"
                        id="codigo_barras"
                        name="codigo_barras"
                        class="form-control"
                        value="{{ $codigoBarrasActual }}"
                        maxlength="80"
                        autocomplete="off"
                        autocapitalize="off"
                        spellcheck="false"
                        placeholder="Haz clic aquí y escanea con lector USB o Bluetooth"
                        aria-describedby="ayuda-codigo-barras"
                    >
                    <button type="button" class="barcode-scan-button" id="abrirScanner">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M3 7V5a2 2 0 0 1 2-2h2"/>
                            <path d="M17 3h2a2 2 0 0 1 2 2v2"/>
                            <path d="M21 17v2a2 2 0 0 1-2 2h-2"/>
                            <path d="M7 21H5a2 2 0 0 1-2-2v-2"/>
                            <line x1="7" y1="12" x2="17" y2="12"/>
                        </svg>
                        Escanear con cámara
                    </button>
                </div>
                <span class="product-help" id="ayuda-codigo-barras">
                    Un lector USB o Bluetooth configurado como teclado escribirá el código en este campo. Haz clic aquí antes de escanear. También puedes usar la cámara. Si lo dejas vacío, NANOS generará un código interno.
                </span>
                @error('codigo_barras')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- PROVEEDORES --}}
    {{-- ========================================================= --}}
    <div class="product-form-section">
        <div class="product-section-heading">
            <div class="product-section-icon" aria-hidden="true">P</div>
            <div>
                <strong>Proveedores del producto</strong>
                <span>Selecciona uno o varios proveedores para este producto.</span>
            </div>
        </div>

        @if ($proveedores->isNotEmpty())
            <label class="supplier-search" for="buscar-proveedor">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-4-4"/>
                </svg>
                <input
                    id="buscar-proveedor"
                    type="search"
                    placeholder="Buscar por nombre, código, NIT, teléfono o celular..."
                    autocomplete="off"
                    data-proveedor-busqueda
                >
            </label>

            <p class="supplier-search-count" data-proveedor-conteo>
                {{ $proveedores->count() }} proveedor(es) disponibles · puedes seleccionar varios
            </p>

            <div class="supplier-selection-grid" data-proveedor-lista>
                @foreach ($proveedores as $proveedor)
                    <label
                        class="supplier-option"
                        data-proveedor-card
                        data-proveedor-texto="{{ strtolower(implode(' ', array_filter([
                            $proveedor->codigo,
                            $proveedor->razon_social,
                            $proveedor->nombre_comercial,
                            $proveedor->nit,
                            $proveedor->ci,
                            $proveedor->telefono,
                            $proveedor->celular,
                            $proveedor->correo,
                        ]))) }}"
                    >
                        <input
                            type="checkbox"
                            name="proveedores[]"
                            value="{{ $proveedor->id }}"
                            @checked(in_array((string) $proveedor->id, $proveedoresSeleccionados, true))
                        >
                        <span class="supplier-option-check" aria-hidden="true"></span>
                        <span class="supplier-option-details">
                            <strong>{{ $proveedor->razon_social }}</strong>
                            <small>
                                {{ $proveedor->nombre_comercial ?: ($proveedor->nit ?: 'Proveedor activo') }}
                            </small>
                        </span>
                    </label>
                @endforeach
            </div>

            <div class="supplier-no-match" data-proveedor-sin-resultados>
                No hay proveedores que coincidan con tu búsqueda.
            </div>
        @else
            <div class="supplier-empty-state">
                <span>No hay proveedores activos para seleccionar.</span>
                <a href="{{ route('admin.proveedores.create') }}">Registrar proveedor</a>
            </div>
        @endif

        @error('proveedores')
            <span class="field-error">{{ $message }}</span>
        @enderror
        @error('proveedores.*')
            <span class="field-error">{{ $message }}</span>
        @enderror
    </div>

    {{-- ========================================================= --}}
    {{-- PRECIOS Y GANANCIA --}}
    {{-- ========================================================= --}}
    <div class="product-form-section">
        <div class="product-section-heading">
            <div class="product-section-icon" aria-hidden="true">Bs</div>
            <div>
                <strong>Costos y precio de venta</strong>
                <span>Calcula cuánto cobrar y revisa la ganancia bruta por unidad.</span>
            </div>
        </div>

        <div class="price-mode-row">
            <label for="modo_precio">Forma de calcular el precio de venta</label>
            <select id="modo_precio" class="form-control" data-precio-modo>
                <option value="automatico" @selected(old('modo_precio', 'automatico') === 'automatico')>
                    Calcular desde porcentaje de ganancia
                </option>
                <option value="manual" @selected(old('modo_precio', 'automatico') === 'manual')>
                    Ingresar precio de venta manualmente
                </option>
            </select>
        </div>

        <div class="price-calculator-grid">
            <div class="form-group price-field">
                <label for="precio_compra">
                    Costo de compra por unidad <span class="required">*</span>
                </label>
                <div class="product-input-group">
                    <span class="product-input-prefix">Bs</span>
                    <input
                        type="number"
                        name="precio_compra"
                        id="precio_compra"
                        class="form-control"
                        min="0"
                        max="9999999999.99"
                        step="0.01"
                        required
                        inputmode="decimal"
                        value="{{ $precioCompraActual }}"
                        placeholder="Ej.: 10.00"
                        data-precio-compra
                    >
                </div>
                <small class="price-field-help">
                    Ingresa el costo de una unidad de venta, por ejemplo una unidad, kilo o litro.
                </small>
                @error('precio_compra')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group price-field">
                <label for="ganancia_deseada">Ganancia deseada sobre el costo (%)</label>
                <input
                    type="number"
                    name="ganancia_deseada"
                    id="ganancia_deseada"
                    class="form-control"
                    min="0"
                    max="999999"
                    step="0.01"
                    inputmode="decimal"
                    value="{{ old('ganancia_deseada', '15') }}"
                    placeholder="Ej.: 15"
                    data-ganancia-deseada
                >
                <small class="price-field-help">
                    Se usa cuando eliges el cálculo automático. El 15 % se calcula sobre el costo.
                </small>
            </div>

            <div class="form-group price-field">
                <label for="precio_venta">
                    Precio de venta <span class="required">*</span>
                </label>
                <div class="product-input-group">
                    <span class="product-input-prefix">Bs</span>
                    <input
                        type="number"
                        name="precio_venta"
                        id="precio_venta"
                        class="form-control"
                        min="0"
                        max="9999999999.99"
                        step="0.01"
                        required
                        inputmode="decimal"
                        value="{{ $precioVentaActual }}"
                        placeholder="Ej.: 11.50"
                        data-precio-venta
                    >
                </div>
                <small class="price-field-help" data-precio-ayuda>
                    Escribe el precio final que pagará el cliente.
                </small>
                @error('precio_venta')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="price-summary" aria-live="polite">
            <div class="price-summary-card">
                <span>Precio de venta</span>
                <strong data-resumen-venta>—</strong>
            </div>
            <div class="price-summary-card profit">
                <span>Ganancia bruta por unidad</span>
                <strong data-resumen-ganancia>—</strong>
            </div>
            <div class="price-summary-card">
                <span>Ganancia real sobre el costo</span>
                <strong data-resumen-porcentaje>—</strong>
            </div>
        </div>

        <div class="price-warning" data-precio-aviso role="alert" hidden></div>

        <small class="price-field-help" style="margin-top: 13px;">
            Ejemplo: costo Bs 10,00 con 15 % de ganancia = precio sugerido Bs 11,50.
            La ganancia mostrada es bruta y no incluye otros gastos.
        </small>
    </div>

    {{-- ========================================================= --}}
    {{-- INVENTARIO --}}
    {{-- ========================================================= --}}
    <div class="product-form-section">
        <div class="product-section-heading">
            <div class="product-section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M4 19V9"/>
                    <path d="M10 19V5"/>
                    <path d="M16 19v-7"/>
                    <path d="M22 19H2"/>
                </svg>
            </div>
            <div>
                <strong>Control de inventario</strong>
                <span>Límites para alertas y futura reposición inteligente.</span>
            </div>
        </div>

        <div class="form-grid">
            @unless($editando)
                <div class="form-group">
                    <label for="stock_inicial">Cantidad inicial</label>
                    <div class="stock-inicial-control">
                        <input
                            type="number"
                            name="stock_inicial"
                            id="stock_inicial"
                            class="form-control"
                            min="0"
                            step="0.001"
                            value="{{ old('stock_inicial', '0') }}"
                            placeholder="Ej.: 80"
                            inputmode="decimal"
                        >
                        <span id="unidadStockTexto" class="stock-unidad">Seleccione una unidad</span>
                    </div>
                    <span class="product-help">
                        Ejemplo: selecciona Kilogramo (kg) y escribe 80 para registrar 80 kg.
                    </span>
                    @error('stock_inicial')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            @endunless

            <div class="form-group">
                <label for="stock_minimo">Stock mínimo</label>
                <input
                    type="number"
                    name="stock_minimo"
                    id="stock_minimo"
                    class="form-control"
                    min="0"
                    step="0.001"
                    value="{{ old('stock_minimo', $editando ? $producto->stock_minimo : '0.000') }}"
                    placeholder="0.000"
                >
                <span class="product-help">Nivel para generar alertas de stock bajo.</span>
                @error('stock_minimo')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="stock_maximo">Stock máximo</label>
                <input
                    type="number"
                    name="stock_maximo"
                    id="stock_maximo"
                    class="form-control"
                    min="0"
                    step="0.001"
                    value="{{ old('stock_maximo', $editando ? $producto->stock_maximo : '') }}"
                    placeholder="Ej.: 100.000"
                >
                <span class="product-help">Nivel máximo recomendado para almacenamiento.</span>
                @error('stock_maximo')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- IMAGEN --}}
    {{-- ========================================================= --}}
    <div class="product-form-section">
        <div class="product-section-heading">
            <div class="product-section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <path d="m21 15-5-5L5 21"/>
                </svg>
            </div>
            <div>
                <strong>Imagen del producto</strong>
                <span>JPG, PNG o WEBP. Máximo 4 MB.</span>
            </div>
        </div>

        <div class="product-image-container">
            <div class="product-image-preview">
                <img
                    id="imagenPreview"
                    alt="Producto"
                    @if ($editando && $producto->imagen)
                        src="{{ asset('storage/' . $producto->imagen) }}"
                        class="show"
                    @endif
                >
                <div
                    class="product-image-empty"
                    id="imagenEmpty"
                    @if ($editando && $producto->imagen) style="display:none;" @endif
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <path d="m21 15-5-5L5 21"/>
                    </svg>
                    <strong>Sin imagen</strong>
                    <span>Vista previa</span>
                </div>
            </div>

            <div>
                <label for="imagen" class="product-file-button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M12 3v12"/>
                        <path d="m7 8 5-5 5 5"/>
                        <path d="M5 21h14"/>
                    </svg>
                    Seleccionar imagen
                </label>
                <input
                    type="file"
                    name="imagen"
                    id="imagen"
                    class="product-file-input"
                    accept="image/jpeg,image/png,image/webp"
                >
                <span class="product-file-name" id="nombreArchivo">
                    @if ($editando && $producto->imagen)
                        Imagen actual registrada
                    @else
                        Ningún archivo seleccionado
                    @endif
                </span>
                @if ($editando && $producto->imagen)
                    <span class="product-help">Si no seleccionas otra imagen, se conservará la actual.</span>
                @endif
                @error('imagen')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- ESTADO --}}
    {{-- ========================================================= --}}
    <div class="product-form-section">
        <div class="product-section-heading">
            <div class="product-section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="m5 12 4 4L19 6"/>
                </svg>
            </div>
            <div>
                <strong>Estado del producto</strong>
                <span>Define la disponibilidad operativa.</span>
            </div>
        </div>

        <div class="product-status-grid">
            <label class="product-status-option">
                <input type="radio" name="estado" value="activo" @checked($estadoActual === 'activo')>
                <div class="product-status-card">
                    <span class="product-status-dot active"></span>
                    <div>
                        <strong>Activo</strong>
                        <span>Disponible para las operaciones del sistema.</span>
                    </div>
                </div>
            </label>

            <label class="product-status-option">
                <input type="radio" name="estado" value="inactivo" @checked($estadoActual === 'inactivo')>
                <div class="product-status-card">
                    <span class="product-status-dot inactive"></span>
                    <div>
                        <strong>Inactivo</strong>
                        <span>Conserva su historial, pero no estará disponible normalmente.</span>
                    </div>
                </div>
            </label>
        </div>

        @error('estado')
            <span class="field-error">{{ $message }}</span>
        @enderror
    </div>

    {{-- ========================================================= --}}
    {{-- ACCIONES --}}
    {{-- ========================================================= --}}
    <div class="product-form-footer">
        <a
            href="{{ $editando
                ? route('admin.productos.show', $producto)
                : route('admin.productos.index') }}"
            class="product-cancel-button"
        >
            Cancelar
        </a>

        <button type="submit" class="product-save-button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg>
            {{ $editando ? 'Guardar cambios' : 'Registrar producto' }}
        </button>
    </div>
</div>

{{-- ============================================================= --}}
{{-- MODAL CÁMARA --}}
{{-- ============================================================= --}}
<div class="barcode-modal" id="barcodeModal" role="dialog" aria-modal="true" aria-labelledby="barcode-modal-title">
    <div class="barcode-modal-content">
        <div class="barcode-modal-header">
            <strong id="barcode-modal-title">Escanear código de barras</strong>
            <button type="button" class="barcode-modal-close" id="cerrarScanner" aria-label="Cerrar escáner">
                ×
            </button>
        </div>
        <div class="barcode-camera">
            <video id="barcodeVideo" autoplay muted playsinline></video>
            <div class="scanner-overlay" aria-hidden="true">
                <div class="scanner-frame">
                    <div class="scanner-corner"></div>
                    <div class="scanner-laser"></div>
                </div>
                <span class="scanner-hint">Coloca el código dentro del marco</span>
            </div>
        </div>
        <div class="barcode-modal-status" id="scannerStatus">
            Coloca el código dentro del recuadro.
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // Mantiene el aviso de validación centrado respecto a toda la pantalla.
    const avisoErrores = document.querySelector('.error-message[role="alert"]');
    if (avisoErrores) document.body.appendChild(avisoErrores);

    /* ============================================================
     * SUBCATEGORÍAS
     * ============================================================ */
    const categoria = document.getElementById('categoria_id');
    const subcategoria = document.getElementById('subcategoria_id');

    function filtrarSubcategorias() {
        if (!categoria || !subcategoria) return;

        const categoriaId = String(categoria.value || '');
        const seleccionado = String(subcategoria.value || '');
        let existeSeleccionado = false;

        Array.from(subcategoria.options).forEach(function (option, index) {
            if (index === 0) {
                option.hidden = false;
                option.disabled = false;
                return;
            }

            const coincide = categoriaId !== '' && String(option.dataset.categoria) === categoriaId;
            option.hidden = !coincide;
            option.disabled = !coincide;

            if (coincide && String(option.value) === seleccionado) {
                existeSeleccionado = true;
            }
        });

        if (!existeSeleccionado) {
            subcategoria.value = '';
        }
    }

    if (categoria) {
        categoria.addEventListener('change', filtrarSubcategorias);
        filtrarSubcategorias();
    }

    /* ============================================================
     * PRODUCTO PESABLE
     * ============================================================ */
    const tipoProducto = document.getElementById('tipo_producto_id');
    const pesajeInfo = document.getElementById('pesajeInfo');

    function actualizarPesaje() {
        if (!tipoProducto || !pesajeInfo) return;

        const option = tipoProducto.options[tipoProducto.selectedIndex];
        const permite = option && option.dataset.pesaje === '1';

        pesajeInfo.classList.toggle('show', permite);
    }

    if (tipoProducto) {
        tipoProducto.addEventListener('change', actualizarPesaje);
        actualizarPesaje();
    }

    /* ============================================================
     * IMAGEN
     * ============================================================ */
    const imagen = document.getElementById('imagen');
    const preview = document.getElementById('imagenPreview');
    const empty = document.getElementById('imagenEmpty');
    const nombreArchivo = document.getElementById('nombreArchivo');

    if (imagen) {
        imagen.addEventListener('change', function () {
            const archivo = this.files?.[0];
            if (!archivo) return;

            const permitidos = ['image/jpeg', 'image/png', 'image/webp'];

            if (!permitidos.includes(archivo.type)) {
                alert('La imagen debe ser JPG, PNG o WEBP.');
                this.value = '';
                return;
            }

            if (archivo.size > 4 * 1024 * 1024) {
                alert('La imagen no puede superar los 4 MB.');
                this.value = '';
                return;
            }

            if (nombreArchivo) nombreArchivo.textContent = archivo.name;

            const reader = new FileReader();
            reader.onload = function (event) {
                preview.src = event.target.result;
                preview.classList.add('show');
                if (empty) empty.style.display = 'none';
            };
            reader.readAsDataURL(archivo);
        });
    }

    /* ============================================================
     * ESCÁNER DE CÓDIGO DE BARRAS
     * ============================================================ */
    const abrirScanner = document.getElementById('abrirScanner');
    const cerrarScannerBtn = document.getElementById('cerrarScanner');
    const modal = document.getElementById('barcodeModal');
    const video = document.getElementById('barcodeVideo');
    const status = document.getElementById('scannerStatus');
    const codigo = document.getElementById('codigo_barras');

    if (codigo) {
        codigo.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') event.preventDefault();
        });
    }

    let stream = null;
    let activo = false;

    async function cargarZXing() {
        if (window.ZXingBrowser) return true;

        return new Promise((resolve) => {
            const existente = document.querySelector('script[data-zxing-nanos]');
            if (existente) {
                existente.addEventListener('load', () => resolve(true), { once: true });
                existente.addEventListener('error', () => resolve(false), { once: true });
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/@zxing/browser@0.1.5/umd/zxing-browser.min.js';
            script.async = true;
            script.dataset.zxingNanos = '1';
            script.onload = () => resolve(true);
            script.onerror = () => resolve(false);
            document.head.appendChild(script);
        });
    }

    function guardarCodigoDetectado(valor) {
        valor = String(valor || '').trim();
        if (!valor) return false;

        if (codigo) {
            codigo.value = valor;
            codigo.dispatchEvent(new Event('input', { bubbles: true }));
            codigo.focus();
        }

        detenerScanner();
        return true;
    }

    async function iniciarScannerNativo() {
        const soportados = await BarcodeDetector.getSupportedFormats();
        const deseados = ['ean_13', 'ean_8', 'upc_a', 'upc_e', 'code_128', 'code_39'];
        const disponibles = deseados.filter((formato) => soportados.includes(formato));

        const detector = disponibles.length
            ? new BarcodeDetector({ formats: disponibles })
            : new BarcodeDetector();

        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } },
            audio: false,
        });

        video.srcObject = stream;
        modal.classList.add('show');
        activo = true;
        status.textContent = 'Apunta al código de barras del producto.';

        await video.play();
        detectarNativo(detector);
    }

    async function detectarNativo(detector) {
        if (!activo) return;

        try {
            if (video.readyState >= HTMLMediaElement.HAVE_CURRENT_DATA) {
                const encontrados = await detector.detect(video);
                if (encontrados.length > 0 && guardarCodigoDetectado(encontrados[0].rawValue)) return;
            }
        } catch (error) {
            console.error(error);
        }

        if (activo) requestAnimationFrame(() => detectarNativo(detector));
    }

    async function iniciarScannerZXing() {
        const cargado = await cargarZXing();
        if (!cargado || !window.ZXingBrowser) throw new Error('No se pudo cargar ZXing.');

        modal.classList.add('show');
        activo = true;
        status.textContent = 'Apunta al código de barras del producto.';

        const lector = new ZXingBrowser.BrowserMultiFormatReader();
        window.nanosZXingReader = lector;

        const dispositivos = await ZXingBrowser.BrowserCodeReader.listVideoInputDevices();
        let dispositivoId = null;

        if (dispositivos.length) {
            const trasera = dispositivos.find((d) => /back|rear|environment|trasera/i.test(d.label));
            dispositivoId = (trasera || dispositivos[dispositivos.length - 1]).deviceId;
        }

        window.nanosZXingControls = await lector.decodeFromVideoDevice(
            dispositivoId,
            video,
            (resultado, error) => {
                if (resultado) guardarCodigoDetectado(resultado.getText());
                if (error && !['NotFoundException', 'ChecksumException', 'FormatException'].includes(error.name)) {
                    console.debug(error);
                }
            }
        );
    }

    async function iniciarScanner() {
        if (!window.isSecureContext) {
            alert('La cámara está bloqueada porque NANOS se abrió por HTTP. En el celular debes abrir el sistema mediante HTTPS. En la laptop puedes usar localhost o 127.0.0.1.');
            return;
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert('Este navegador no permite utilizar la cámara.');
            return;
        }

        try {
            if ('BarcodeDetector' in window) await iniciarScannerNativo();
            else await iniciarScannerZXing();
        } catch (error) {
            console.error(error);
            detenerScanner();
            alert('No se pudo iniciar el lector. Revisa el permiso de cámara y asegúrate de abrir NANOS mediante HTTPS en el celular.');
        }
    }

    function detenerScanner() {
        activo = false;

        if (window.nanosZXingControls) {
            try { window.nanosZXingControls.stop(); } catch (e) {}
            window.nanosZXingControls = null;
        }

        if (window.nanosZXingReader) {
            try { window.nanosZXingReader.reset(); } catch (e) {}
            window.nanosZXingReader = null;
        }

        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
            stream = null;
        }

        if (video && video.srcObject) {
            const tracks = video.srcObject.getTracks ? video.srcObject.getTracks() : [];
            tracks.forEach((track) => track.stop());
            video.srcObject = null;
        }

        if (modal) modal.classList.remove('show');
    }

    if (abrirScanner) {
        abrirScanner.setAttribute('type', 'button');
        abrirScanner.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            iniciarScanner();
        });
    }

    if (cerrarScannerBtn) {
        cerrarScannerBtn.addEventListener('click', detenerScanner);
    }

    if (modal) {
        modal.addEventListener('click', function (event) {
            if (event.target === modal) detenerScanner();
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') detenerScanner();
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const modo = document.querySelector('[data-precio-modo]');
    const compra = document.querySelector('[data-precio-compra]');
    const tasa = document.querySelector('[data-ganancia-deseada]');
    const venta = document.querySelector('[data-precio-venta]');
    const ayuda = document.querySelector('[data-precio-ayuda]');
    const resumenVenta = document.querySelector('[data-resumen-venta]');
    const resumenGanancia = document.querySelector('[data-resumen-ganancia]');
    const resumenPorcentaje = document.querySelector('[data-resumen-porcentaje]');
    const aviso = document.querySelector('[data-precio-aviso]');

    if (!modo || !compra || !tasa || !venta) return;

    const formato = new Intl.NumberFormat('es-BO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    const leer = (campo) => (campo.value.trim() === '' ? NaN : campo.valueAsNumber);
    const bolivianos = (valor) => `Bs ${formato.format(valor)}`;

    function actualizarPrecio() {
        const automatico = modo.value === 'automatico';
        const costo = leer(compra);
        const porcentaje = leer(tasa);

        venta.readOnly = automatico;
        tasa.disabled = !automatico;
        tasa.required = automatico;
        venta.setCustomValidity('');
        tasa.setCustomValidity('');
        aviso.hidden = true;
        aviso.textContent = '';

        ayuda.textContent = automatico
            ? 'Precio calculado automáticamente y redondeado a dos decimales.'
            : 'Escribe el precio final que pagará el cliente.';

        if (automatico) {
            if (Number.isFinite(costo) && costo >= 0 && Number.isFinite(porcentaje) && porcentaje >= 0) {
                const costoCentavos = Math.round(costo * 100);
                const ventaCalculada = Math.round(costoCentavos * (1 + porcentaje / 100)) / 100;

                if (Number.isFinite(ventaCalculada) && ventaCalculada <= Number(venta.max)) {
                    venta.value = ventaCalculada.toFixed(2);
                } else {
                    venta.value = '';
                    tasa.setCustomValidity('El precio calculado supera el máximo permitido.');
                    aviso.textContent = 'Reduce el costo o el porcentaje: el precio calculado supera el máximo permitido.';
                    aviso.hidden = false;
                }
            } else {
                venta.value = '';
            }
        }

        const precio = leer(venta);
        resumenVenta.textContent = Number.isFinite(precio) && precio >= 0 ? bolivianos(precio) : '—';
        resumenGanancia.textContent = '—';
        resumenPorcentaje.textContent = '—';

        if (!Number.isFinite(costo) || !Number.isFinite(precio) || costo < 0 || precio < 0) return;

        const costoCentavos = Math.round(costo * 100);
        const diferenciaCentavos = Math.round(precio * 100) - costoCentavos;

        resumenGanancia.textContent = bolivianos(diferenciaCentavos / 100);
        resumenPorcentaje.textContent = costoCentavos > 0
            ? `${formato.format((diferenciaCentavos / costoCentavos) * 100)} %`
            : 'No calculable';

        if (precio < costo) {
            aviso.textContent = 'El precio de venta es menor al costo. Corrígelo antes de guardar el producto.';
            aviso.hidden = false;
            venta.setCustomValidity('El precio de venta debe ser mayor o igual al precio de compra.');
        }
    }

    modo.addEventListener('change', actualizarPrecio);
    compra.addEventListener('input', actualizarPrecio);
    tasa.addEventListener('input', actualizarPrecio);
    venta.addEventListener('input', actualizarPrecio);
    actualizarPrecio();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const buscador = document.querySelector('[data-proveedor-busqueda]');
    const tarjetas = Array.from(document.querySelectorAll('[data-proveedor-card]'));
    const mensajeVacio = document.querySelector('[data-proveedor-sin-resultados]');
    const conteo = document.querySelector('[data-proveedor-conteo]');

    if (!buscador || tarjetas.length === 0) return;

    const normalizar = (texto) => (texto || '')
        .toLocaleLowerCase('es')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .trim();

    function filtrarProveedores() {
        const termino = normalizar(buscador.value);
        let visibles = 0;

        tarjetas.forEach((tarjeta) => {
            const coincide = normalizar(tarjeta.dataset.proveedorTexto).includes(termino);
            tarjeta.hidden = !coincide;
            if (coincide) visibles++;
        });

        if (mensajeVacio) mensajeVacio.style.display = visibles === 0 ? 'block' : 'none';

        if (conteo) {
            conteo.textContent = termino
                ? `${visibles} resultado(s) · puedes seleccionar varios proveedores`
                : `${tarjetas.length} proveedor(es) disponibles · puedes seleccionar varios`;
        }
    }

    buscador.addEventListener('input', filtrarProveedores);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const selectorUnidad = document.getElementById('unidad_medida_id');
    const textoUnidadStock = document.getElementById('unidadStockTexto');

    if (!selectorUnidad || !textoUnidadStock) return;

    function actualizarUnidadStock() {
        const opcion = selectorUnidad.options[selectorUnidad.selectedIndex];
        const abreviatura = opcion ? opcion.dataset.abreviatura : '';
        textoUnidadStock.textContent = abreviatura || 'Seleccione una unidad';
    }

    selectorUnidad.addEventListener('change', actualizarUnidadStock);
    actualizarUnidadStock();
});
</script>
@endpush