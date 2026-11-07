@extends('admin.layouts.app')

@section('title', 'Promociones')

@push('styles')
<style>
    .promociones-page {
        --surface: #191a20;
        --surface-soft: #202127;
        --border: #30323c;
        --text: #ececf0;
        --muted: #92949c;
        --red: #e31b23;
        --green: #4ade80;
        --amber: #f5b942;

        display: flex;
        flex-direction: column;
        gap: 18px;
        color: var(--text);
    }

    .promociones-page *,
    .promociones-page *::before,
    .promociones-page *::after {
        box-sizing: border-box;
    }

    .promociones-header,
    .promociones-card {
        border: 1px solid var(--border);
        border-radius: 15px;
        background: var(--surface);
    }

    .promociones-header {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        padding: 21px 23px;
        overflow: hidden;
    }

    .promociones-header::before {
        position: absolute;
        inset: 0 0 auto;
        height: 2px;
        background: linear-gradient(90deg, var(--red), #801118 45%, transparent);
        content: "";
    }

    .promociones-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 8px;
        color: #ff777d;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .promociones-eyebrow::before {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #ff777d;
        content: "";
    }

    .promociones-header h1 {
        margin: 0 0 6px;
        color: #fff;
        font-size: 25px;
        font-weight: 800;
    }

    .promociones-header p {
        margin: 0;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.5;
    }

    .promociones-button {
        min-height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 0 12px;
        border: 1px solid #393c46;
        border-radius: 8px;
        color: #e9e9ed;
        background: #22242a;
        font-size: 10px;
        font-weight: 750;
        text-decoration: none;
        cursor: pointer;
        transition: .18s ease;
    }

    .promociones-button:hover {
        border-color: #575b67;
        color: #fff;
        background: #292a31;
    }

    .promociones-button.primary {
        border-color: #bd171e;
        color: #fff;
        background: #c91820;
    }

    .promociones-button.primary:hover {
        border-color: var(--red);
        background: var(--red);
    }

    .promociones-button.warning {
        color: #f3d38a;
        border-color: rgba(245, 185, 66, .28);
        background: rgba(245, 185, 66, .07);
    }

    .promociones-button.danger {
        color: #ff9a9e;
        border-color: rgba(227, 27, 35, .28);
        background: rgba(227, 27, 35, .07);
    }

    .promociones-message {
        padding: 12px 15px;
        border: 1px solid;
        border-radius: 9px;
        font-size: 12px;
        line-height: 1.5;
    }

    .promociones-message.success {
        border-color: rgba(74, 222, 128, .28);
        color: #8ee5ad;
        background: rgba(74, 222, 128, .07);
    }

    .promociones-message.error {
        border-color: rgba(227, 27, 35, .32);
        color: #ff9297;
        background: rgba(227, 27, 35, .08);
    }

    .promociones-message ul {
        margin: 7px 0 0;
        padding-left: 19px;
    }

    .promociones-card {
        overflow: hidden;
    }

    .promociones-card-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 15px 17px;
        border-bottom: 1px solid var(--border);
    }

    .promociones-card-heading h2 {
        margin: 0;
        color: #f5f5f7;
        font-size: 13px;
    }

    .promociones-card-heading span {
        color: var(--muted);
        font-size: 10px;
    }

    .promociones-table-wrap {
        overflow-x: auto;
    }

    .promociones-table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
    }

    .promociones-table th,
    .promociones-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #2b2d35;
        text-align: left;
        vertical-align: middle;
        font-size: 11px;
    }

    .promociones-table th {
        color: var(--muted);
        background: var(--surface-soft);
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .45px;
        text-transform: uppercase;
    }

    .promociones-table tbody tr:hover {
        background: rgba(255, 255, 255, .015);
    }

    .promociones-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .promocion-identidad {
        min-width: 170px;
    }

    .promocion-codigo {
        display: block;
        color: #ff9297;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .3px;
    }

    .promocion-nombre {
        display: block;
        margin-top: 4px;
        color: #fff;
        font-size: 11px;
        font-weight: 750;
    }

    .promocion-descripcion {
        display: block;
        max-width: 260px;
        margin-top: 4px;
        overflow: hidden;
        color: var(--muted);
        font-size: 9px;
        line-height: 1.4;
        text-overflow: ellipsis;
    }

    .promocion-tipo {
        display: inline-flex;
        align-items: center;
        padding: 5px 8px;
        border: 1px solid #3a3d47;
        border-radius: 999px;
        color: #dfe0e5;
        background: #22242a;
        font-size: 9px;
        font-weight: 750;
        white-space: nowrap;
    }

    .promocion-tipo.combo {
        border-color: rgba(245, 185, 66, .28);
        color: #f3d38a;
        background: rgba(245, 185, 66, .07);
    }

    .promocion-tipo.descuento {
        border-color: rgba(120, 165, 255, .28);
        color: #a8c4ff;
        background: rgba(120, 165, 255, .08);
    }

    .promocion-valor {
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .promocion-productos-total {
        display: inline-grid;
        min-width: 28px;
        height: 26px;
        place-items: center;
        padding: 0 7px;
        border: 1px solid #383b45;
        border-radius: 8px;
        color: #eee;
        background: #22242a;
        font-size: 10px;
        font-weight: 800;
    }

    .promocion-vigencia {
        color: #e4e4e8;
        font-size: 10px;
        line-height: 1.6;
        white-space: nowrap;
    }

    .promocion-vigencia small {
        display: block;
        color: var(--muted);
        font-size: 9px;
    }

    .promocion-estado {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 8px;
        border: 1px solid;
        border-radius: 999px;
        font-size: 9px;
        font-weight: 800;
        white-space: nowrap;
    }

    .promocion-estado::before {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: currentColor;
        content: "";
    }

    .promocion-estado.activa {
        border-color: rgba(74, 222, 128, .25);
        color: #8ee5ad;
        background: rgba(74, 222, 128, .07);
    }

    .promocion-estado.inactiva {
        border-color: rgba(245, 185, 66, .25);
        color: #f3d38a;
        background: rgba(245, 185, 66, .07);
    }

    .promocion-acciones {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .promocion-acciones form {
        display: inline-flex;
        margin: 0;
    }

    .promocion-acciones .promociones-button {
        min-height: 31px;
        padding: 0 9px;
        font-size: 9px;
    }

    .promociones-empty {
        padding: 34px 18px !important;
        color: var(--muted);
        text-align: center !important;
    }

    .promociones-empty strong {
        display: block;
        margin-bottom: 5px;
        color: #e8e8ec;
        font-size: 12px;
    }

    .promociones-pagination {
        padding: 13px 16px;
        border-top: 1px solid var(--border);
    }

    @media (max-width: 640px) {
        .promociones-header {
            align-items: stretch;
            padding: 18px;
        }

        .promociones-header .promociones-button {
            width: 100%;
        }

        .promociones-card-heading {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
<div class="promociones-page">
    <header class="promociones-header">
        <div>
            <span class="promociones-eyebrow">Ventas · Ofertas</span>
            <h1>Promociones</h1>
            <p>
                Crea combos de productos o aplica descuentos porcentuales
                durante un periodo determinado.
            </p>
        </div>

        <a
            href="{{ route('admin.promociones.create') }}"
            class="promociones-button primary"
        >
            <span aria-hidden="true">＋</span>
            Crear promoción
        </a>
    </header>

    @if (session('success'))
        <div class="promociones-message success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="promociones-message error" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="promociones-message error" role="alert">
            <strong>Revisa los siguientes datos:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="promociones-card">
        <div class="promociones-card-heading">
            <h2>Promociones registradas</h2>
            <span>
                {{ $promociones->total() }}
                {{ $promociones->total() === 1 ? 'promoción' : 'promociones' }}
            </span>
        </div>

        <div class="promociones-table-wrap">
            <table class="promociones-table">
                <thead>
                    <tr>
                        <th>Promoción</th>
                        <th>Tipo</th>
                        <th>Beneficio</th>
                        <th>Productos</th>
                        <th>Vigencia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($promociones as $promocion)
                        @php
                            $tipoPromocion = strtolower((string) $promocion->tipo);
                            $estadoPromocion = strtolower((string) $promocion->estado);
                        @endphp

                        <tr>
                            <td class="promocion-identidad">
                                <span class="promocion-codigo">
                                    {{ $promocion->codigo ?: 'Sin código' }}
                                </span>

                                <span class="promocion-nombre">
                                    {{ $promocion->nombre }}
                                </span>

                                @if ($promocion->descripcion)
                                    <small class="promocion-descripcion">
                                        {{ $promocion->descripcion }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                @if ($tipoPromocion === 'combo')
                                    <span class="promocion-tipo combo">Combo</span>
                                @elseif ($tipoPromocion === 'descuento_porcentaje')
                                    <span class="promocion-tipo descuento">
                                        Descuento porcentual
                                    </span>
                                @else
                                    <span class="promocion-tipo">
                                        {{ ucfirst(str_replace('_', ' ', $tipoPromocion)) }}
                                    </span>
                                @endif
                            </td>

                            <td class="promocion-valor">
                                @if ($tipoPromocion === 'combo')
                                    Bs {{ number_format((float) $promocion->precio_promocional, 2, ',', '.') }}
                                @elseif ($tipoPromocion === 'descuento_porcentaje')
                                    {{ number_format((float) $promocion->valor_descuento, 2, ',', '.') }}%
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                <span class="promocion-productos-total">
                                    {{ (int) ($promocion->productos_total ?? 0) }}
                                </span>
                            </td>

                            <td class="promocion-vigencia">
                                {{ $promocion->fecha_inicio
                                    ? \Illuminate\Support\Carbon::parse($promocion->fecha_inicio)->format('d/m/Y H:i')
                                    : '—' }}

                                <small>
                                    @if ($promocion->fecha_fin)
                                        Hasta
                                        {{ \Illuminate\Support\Carbon::parse($promocion->fecha_fin)->format('d/m/Y H:i') }}
                                    @else
                                        Sin fecha final
                                    @endif
                                </small>
                            </td>

                            <td>
                                <span class="promocion-estado {{ $estadoPromocion === 'activa' ? 'activa' : 'inactiva' }}">
                                    {{ $estadoPromocion === 'activa' ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>

                            <td>
                                <div class="promocion-acciones">
                                    <a
                                        href="{{ route('admin.promociones.edit', ['promocion' => $promocion->id]) }}"
                                        class="promociones-button"
                                    >
                                        Editar
                                    </a>

                                    <form
                                        action="{{ route('admin.promociones.estado', ['promocion' => $promocion->id]) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="promociones-button warning"
                                        >
                                            {{ $estadoPromocion === 'activa' ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>

                                    <form
                                        action="{{ route('admin.promociones.destroy', ['promocion' => $promocion->id]) }}"
                                        method="POST"
                                        onsubmit="return confirm('¿Eliminar esta promoción y los productos asociados?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="promociones-button danger"
                                        >
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="promociones-empty">
                                <strong>Aún no hay promociones</strong>
                                Crea un combo o un descuento porcentual para que aparezca aquí.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($promociones->hasPages())
            <div class="promociones-pagination">
                {{ $promociones->links() }}
            </div>
        @endif
    </section>
</div>
@endsection