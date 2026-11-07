@extends('admin.layouts.app')

@section('title', 'Detalle del producto')

@push('styles')
<style>
    .product-show-actions { display:flex; gap:9px; flex-wrap:wrap; }
    .product-show-grid { display:grid; grid-template-columns:330px minmax(0,1fr); gap:18px; align-items:start; }
    .product-card { overflow:hidden; border:1px solid var(--border); border-radius:14px; background:rgba(255,255,255,.015); }
    .product-card-header { display:flex; align-items:center; gap:11px; padding:16px 18px; border-bottom:1px solid var(--border); background:rgba(0,0,0,.08); }
    .product-card-icon { width:34px; height:34px; display:flex; align-items:center; justify-content:center; flex-shrink:0; border-radius:9px; color:var(--red-light); background:rgba(227,27,35,.08); border:1px solid rgba(227,27,35,.12); }
    .product-card-icon svg { width:16px; height:16px; }
    .product-card-header strong { display:block; color:#dedfe2; font-size:11px; }
    .product-card-header span { display:block; margin-top:2px; color:#5e6168; font-size:8px; }
    .product-card-body { padding:18px; }
    .product-main-image { width:100%; aspect-ratio:1/1; display:flex; align-items:center; justify-content:center; overflow:hidden; border:1px solid var(--border); border-radius:13px; background:#17181c; }
    .product-main-image img { width:100%; height:100%; object-fit:cover; }
    .product-no-image { text-align:center; color:#4e5158; }
    .product-no-image svg { width:55px; height:55px; margin-bottom:10px; }
    .product-no-image strong { display:block; color:#696c73; font-size:10px; }
    .product-name-show { margin:17px 0 0; color:#e7e7e9; font-size:18px; font-weight:750; line-height:1.3; }
    .product-code-show { display:inline-flex; margin-top:8px; padding:5px 8px; border:1px solid var(--border); border-radius:7px; color:#777a82; background:#191a1f; font-family:monospace; font-size:9px; }
    .product-state-show { display:inline-flex; align-items:center; gap:6px; margin-top:12px; padding:6px 9px; border-radius:20px; font-size:8px; font-weight:700; }
    .product-state-show::before { content:""; width:6px; height:6px; border-radius:50%; }
    .product-state-show.active { color:#6bdd99; background:rgba(56,212,120,.07); border:1px solid rgba(56,212,120,.12); }
    .product-state-show.active::before { background:var(--green); }
    .product-state-show.inactive { color:#ef777c; background:rgba(227,27,35,.07); border:1px solid rgba(227,27,35,.12); }
    .product-state-show.inactive::before { background:var(--red); }
    .product-description-show { margin-top:15px; padding-top:15px; border-top:1px solid var(--border); color:#777a81; font-size:9px; line-height:1.7; }
    .product-detail-grid, .product-price-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
    .product-stock-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; }
    .product-detail-item, .product-price-card, .product-stock-card { padding:14px; border:1px solid var(--border); border-radius:10px; background:rgba(255,255,255,.012); }
    .product-detail-item { min-height:72px; }
    .product-detail-item span, .product-price-card span, .product-stock-card span { display:block; color:#5f6269; font-size:8px; font-weight:650; text-transform:uppercase; letter-spacing:.6px; }
    .product-detail-item strong { display:block; margin-top:7px; color:#c8c9cd; font-size:10px; font-weight:650; }
    .product-detail-item small { display:block; margin-top:4px; color:#60636a; font-size:8px; }
    .product-price-card strong { display:block; margin-top:7px; color:#e2e3e5; font-size:20px; font-weight:750; }
    .product-price-card strong small { color:#74777e; font-size:10px; }
    .product-stock-card strong { display:block; margin-top:7px; color:#dadbde; font-size:17px; }
    .product-stock-card.total { border-color:rgba(227,27,35,.16); background:rgba(227,27,35,.035); }
    .product-stock-card.total strong { color:var(--red-light); }
    .product-table-wrapper { overflow-x:auto; }
    .product-show-table { width:100%; min-width:650px; border-collapse:collapse; }
    .product-show-table th { padding:11px 14px; border-bottom:1px solid var(--border); color:#5e6168; background:rgba(0,0,0,.08); font-size:8px; text-align:left; text-transform:uppercase; letter-spacing:.6px; }
    .product-show-table td { padding:12px 14px; border-bottom:1px solid rgba(255,255,255,.04); color:#9799a0; font-size:9px; }
    .product-show-table tbody tr:last-child td { border-bottom:0; }
    .product-current-price, .product-old-price { display:inline-flex; padding:4px 7px; border-radius:20px; font-size:7px; }
    .product-current-price { color:#6bdd99; background:rgba(56,212,120,.07); border:1px solid rgba(56,212,120,.10); font-weight:700; }
    .product-old-price { color:#777a81; background:rgba(255,255,255,.03); border:1px solid var(--border); }
    .product-barcode-list { display:flex; flex-direction:column; gap:8px; }
    .product-barcode-item { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:11px 13px; border:1px solid var(--border); border-radius:9px; background:rgba(255,255,255,.012); }
    .product-barcode-item code { color:#c5c7cb; font-size:10px; }
    .product-barcode-item span { color:#60636a; font-size:8px; }
    .product-primary-barcode { color:var(--red-light)!important; }
    .product-empty-data { padding:25px; color:#5e6168; font-size:9px; text-align:center; }
    .product-section-space { margin-top:18px; }
    .product-note { margin-top:14px; color:#5f6269; font-size:8px; line-height:1.6; }
    @media(max-width:1000px){ .product-show-grid{grid-template-columns:280px minmax(0,1fr);} }
    @media(max-width:800px){ .product-show-grid{grid-template-columns:1fr;} .product-main-image{max-width:300px;} }
    @media(max-width:600px){
        .product-detail-grid,.product-price-grid,.product-stock-grid{grid-template-columns:1fr;}
        .product-show-actions,.product-show-actions a,.product-show-actions form,.product-show-actions button{width:100%;}
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-title">
        <span class="eyebrow">{{ $producto->codigo_interno }}</span>
        <h1>Detalle del producto</h1>
        <p>Información general, precios e inventario del producto.</p>
    </div>

    <div class="product-show-actions">
        <a href="{{ route('admin.productos.index') }}" class="btn-secondary">← Productos</a>
        <a href="{{ route('admin.productos.edit', $producto) }}" class="btn-primary">Editar producto</a>
    </div>
</div>

@if (session('success'))
    <div class="success-message">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="error-message">{{ session('error') }}</div>
@endif

<div class="product-show-grid">
    <div>
        <div class="product-card">
            <div class="product-card-body">
                <div class="product-main-image">
                    @if ($producto->imagen)
                        <img src="{{ asset(ltrim($producto->imagen, '/')) }}" alt="{{ $producto->nombre }}">
                    @else
                        <div class="product-no-image">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 极简风格，注重实用性，追求极致的用户体验。
                                <line x1="12" y1="22" x2="12" y2="12"/>
                            </svg>
                            <strong>Sin imagen</strong>
                        </div>
                    @endif
                </div>

                <h2 class="product-name-show">{{ $producto->nombre }}</h2>
                <span class="product-code-show">{{ $producto->codigo_interno }}</span>

                <div class="product-state-show {{ $producto->estado === 'activo' ? 'active' : 'inactive' }}">
                    {{ $producto->estado === 'activo' ? 'Activo' : 'Inactivo' }}
                </div>

                <div class="product-description-show">
                    {{ $producto->descripcion ?: 'Sin descripción registrada.' }}
                </div>
            </div>
        </div>

        <div class="product-card product-section-space">
            <div class="product-card-header">
                <div class="product-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M3 5v14"/><path d="M7 5v14"/><path d="M10 5v14"/>
                        <path d="M14 5v14"/><path d="M18 5v14"/><path d="M21 5v14"/>
                    </svg>
                </div>
                <div><strong>Códigos de barras</strong><span>Identificadores registrados</span></div>
            </div>

            <div class="product-card-body">
                <div class="product-barcode-list">
                    @forelse ($producto->codigosBarras as $codigo)
                        <div class="product-barcode-item">
                            <code>{{ $codigo->codigo }}</code>
                            @if ($codigo->es_principal)
                                <span class="product-primary-barcode">Principal</span>
                            @else
                                <span>{{ $codigo->tipo ?? 'Producto' }}</span>
                            @endif
                        </div>
                    @empty
                        <div class="product-empty-data">No tiene código de barras registrado.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="product-card">
            <div class="product-card-header">
                <div class="product-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 4h6v6H4z"/><path d="M14 4h6v6h-6z"/>
                        <path d="M4 14h6v6H4z"/><path d="M14 14h6v6h-6z"/>
                    </svg>
                </div>
                <div><strong>Clasificación</strong><span>Información del catálogo</span></div>
            </div>

            <div class="product-card-body">
                <div class="product-detail-grid">
                    <div class="product-detail-item"><span>Categoría</span><strong>{{ $producto->categoria?->nombre ?? '—' }}</strong></div>
                    <div class="product-detail-item"><span>Subcategoría</span><strong>{{ $producto->subcategoria?->nombre ?? '—' }}</strong></div>
                    <div class="product-detail-item"><span>Marca</span><strong>{{ $producto->marca?->nombre ?? '—' }}</strong></div>
                    <div class="product-detail-item">
                        <span>Unidad de medida</span>
                        <strong>{{ $producto->unidadMedida?->nombre ?? '—' }}</strong>
                        @if ($producto->unidadMedida)
                            <small>{{ $producto->unidadMedida->abreviatura }}</small>
                        @endif
                    </div>
                    <div class="product-detail-item"><span>Tipo de producto</span><strong>{{ $producto->tipoProducto?->nombre ?? '—' }}</strong></div>
                    <div class="product-detail-item"><span>Permite pesaje</span><strong>{{ $producto->tipoProducto?->permite_pesaje ? 'Sí' : 'No' }}</strong></div>
                </div>
            </div>
        </div>

        <div class="product-card product-section-space">
            <div class="product-card-header">
                <div class="product-card-icon"><strong style="color:inherit;font-size:10px;">Bs</strong></div>
                <div><strong>Precio actual</strong><span>Compra y venta</span></div>
            </div>

            <div class="product-card-body">
                @if ($producto->precioActual)
                    <div class="product-price-grid">
                        <div class="product-price-card">
                            <span>Precio de compra</span>
                            <strong><small>Bs</small> {{ number_format((float) $producto->precioActual->precio_compra, 2) }}</strong>
                        </div>
                        <div class="product-price-card">
                            <span>Precio de venta</span>
                            <strong><small>Bs</small> {{ number_format((float) $producto->precioActual->precio_venta, 2) }}</strong>
                        </div>
                    </div>
                @else
                    <div class="product-empty-data">Este producto no tiene un precio actual registrado.</div>
                @endif
            </div>
        </div>

        <div class="product-card product-section-space">
            <div class="product-card-header">
                <div class="product-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 19V9"/><path d="M10 19V5"/><path d="M16 19v-7"/><path d="M22 19H2"/>
                    </svg>
                </div>
                <div><strong>Inventario</strong><span>Niveles y existencia total</span></div>
            </div>

            <div class="product-card-body">
                <div class="product-stock-grid">
                    <div class="product-stock-card total">
                        <span>Stock total</span>
                        <strong>{{ number_format((float) $producto->inventarios->sum('cantidad'), 3) }}</strong>
                    </div>
                    <div class="product-stock-card">
                        <span>Stock mínimo</span>
                        <strong>{{ number_format((float) $producto->stock_minimo, 3) }}</strong>
                    </div>
                    <div class="product-stock-card">
                        <span>Stock máximo</span>
                        <strong>{{ $producto->stock_maximo !== null ? number_format((float) $producto->stock_maximo, 3) : '—' }}</strong>
                    </div>
                </div>
                <div class="product-note">
                    El stock físico se administra por almacén. Los niveles mínimo y máximo se utilizarán posteriormente para alertas y reposición inteligente.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="product-card product-section-space">
    <div class="product-card-header">
        <div class="product-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M3 21V8l9-5 9 5v13"/><path d="M7 21v-8h10v8"/>
            </svg>
        </div>
        <div><strong>Existencias por almacén</strong><span>Distribución física del producto</span></div>
    </div>

    @if ($producto->inventarios->count())
        <div class="product-table-wrapper">
            <table class="product-show-table">
                <thead><tr><th>Almacén</th><th>Cantidad</th></tr></thead>
                <tbody>
                    @foreach ($producto->inventarios as $inventario)
                        <tr>
                            <td>Almacén #{{ $inventario->almacen_id }}</td>
                            <td>{{ number_format((float) $inventario->cantidad, 3) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="product-empty-data">Este producto todavía no tiene existencias registradas en ningún almacén.</div>
    @endif
</div>

<div class="product-card product-section-space">
    <div class="product-card-header">
        <div class="product-card-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
            </svg>
        </div>
        <div><strong>Historial de precios</strong><span>Cambios realizados al producto</span></div>
    </div>

    @if ($producto->precios->count())
        <div class="product-table-wrapper">
            <table class="product-show-table">
                <thead>
                    <tr><th>Compra</th><th>Venta</th><th>Fecha inicio</th><th>Fecha fin</th><th>Usuario</th><th>Estado</th></tr>
                </thead>
                <tbody>
                    @foreach ($producto->precios as $precio)
                        <tr>
                            <td>Bs {{ number_format((float) $precio->precio_compra, 2) }}</td>
                            <td>Bs {{ number_format((float) $precio->precio_venta, 2) }}</td>
                            <td>{{ $precio->fecha_inicio ? $precio->fecha_inicio->format('d/m/Y H:i') : '—' }}</td>
                            <td>{{ $precio->fecha_fin ? $precio->fecha_fin->format('d/m/Y H:i') : '—' }}</td>
                            <td>{{ $precio->usuario?->username ?? '—' }}</td>
                            <td>
                                @if ($precio->estado)
                                    <span class="product-current-price">Actual</span>
                                @else
                                    <span class="product-old-price">Histórico</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="product-empty-data">No existe historial de precios.</div>
    @endif
</div>
@endsection
