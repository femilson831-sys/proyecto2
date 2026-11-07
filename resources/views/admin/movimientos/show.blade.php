@extends('admin.layouts.app')

@section('title', 'Detalle del movimiento')

@push('styles')
<style>
    .mov-detalle-page {
        --surface: #17181c;
        --surface-soft: #1d1e24;
        --border: #30333d;
        --text: #ececf0;
        --muted: #92949c;
        --red: #e31b23;

        display: flex;
        flex-direction: column;
        gap: 18px;
        color: var(--text);
    }

    .mov-detalle-header,
    .mov-detalle-card {
        border: 1px solid var(--border);
        border-radius: 14px;
        background: var(--surface);
    }

    .mov-detalle-header {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding: 20px 22px;
        overflow: hidden;
    }

    .mov-detalle-header::before {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, var(--red), transparent);
        content: "";
    }

    .mov-detalle-header h1 {
        margin: 6px 0;
        color: #fff;
        font-size: 24px;
        overflow-wrap: anywhere;
    }

    .mov-detalle-header p {
        margin: 0;
        color: var(--muted);
        font-size: 12px;
    }

    .mov-detalle-eyebrow {
        color: #ff777d;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .mov-detalle-button {
        min-height: 39px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 13px;
        border: 1px solid #383b45;
        border-radius: 8px;
        color: #eee;
        background: #202126;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: background .2s ease, border-color .2s ease;
    }

    .mov-detalle-button:hover {
        border-color: #555966;
        color: #fff;
        background: #292a30;
    }

    .mov-detalle-info {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        padding: 16px;
    }

    .mov-detalle-dato {
        min-width: 0;
        padding: 13px;
        border: 1px solid #2d3038;
        border-radius: 9px;
        background: var(--surface-soft);
    }

    .mov-detalle-dato span {
        display: block;
        margin-bottom: 6px;
        color: var(--muted);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .3px;
        text-transform: uppercase;
    }

    .mov-detalle-dato strong {
        color: #fff;
        font-size: 13px;
        overflow-wrap: anywhere;
    }

    .mov-detalle-dato.motivo {
        grid-column: 1 / -1;
    }

    .mov-detalle-table-wrap {
        overflow-x: auto;
        border-radius: 14px;
    }

    .mov-detalle-table {
        width: 100%;
        min-width: 750px;
        border-collapse: collapse;
    }

    .mov-detalle-table th,
    .mov-detalle-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #2b2d35;
        text-align: left;
        vertical-align: middle;
        font-size: 12px;
    }

    .mov-detalle-table th {
        color: var(--muted);
        background: var(--surface-soft);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .4px;
        text-transform: uppercase;
    }

    .mov-detalle-table td {
        color: #e2e3e8;
    }

    .mov-detalle-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .mov-detalle-empty {
        padding: 25px !important;
        color: var(--muted) !important;
        text-align: center !important;
    }

    @media (max-width: 700px) {
        .mov-detalle-info {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 480px) {
        .mov-detalle-info {
            grid-template-columns: 1fr;
        }

        .mov-detalle-header {
            align-items: stretch;
        }

        .mov-detalle-header h1 {
            font-size: 21px;
        }

        .mov-detalle-button {
            width: 100%;
        }

        .mov-detalle-dato.motivo {
            grid-column: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="mov-detalle-page">

    @if (!isset($registro))
        <section class="mov-detalle-card mov-detalle-empty" role="alert">
            No se encontraron los datos de este movimiento.
            <br><br>

            <a href="{{ route('admin.movimientos.index') }}" class="mov-detalle-button">
                Volver a movimientos
            </a>
        </section>
    @else
        <header class="mov-detalle-header">
            <div>
                <span class="mov-detalle-eyebrow">Detalle del movimiento</span>
                <h1>{{ $registro->numero_movimiento }}</h1>
                <p>{{ $registro->tipo_nombre ?? 'Tipo no especificado' }}</p>
            </div>

            <a href="{{ route('admin.movimientos.index') }}" class="mov-detalle-button">
                ← Volver a movimientos
            </a>
        </header>

        <section class="mov-detalle-card mov-detalle-info">
            <div class="mov-detalle-dato">
                <span>Fecha</span>
                <strong>
                    {{ $registro->fecha_movimiento
                        ? \Illuminate\Support\Carbon::parse($registro->fecha_movimiento)->format('d/m/Y H:i')
                        : '—' }}
                </strong>
            </div>

            <div class="mov-detalle-dato">
                <span>Tipo</span>
                <strong>{{ $registro->tipo_nombre ?: '—' }}</strong>
            </div>

            <div class="mov-detalle-dato">
                <span>Estado</span>
                <strong>{{ ucfirst($registro->estado ?? 'sin estado') }}</strong>
            </div>

            <div class="mov-detalle-dato">
                <span>Usuario</span>
                <strong>{{ $registro->usuario ?: 'No registrado' }}</strong>
            </div>

            <div class="mov-detalle-dato">
                <span>Almacén de origen</span>
                <strong>{{ $registro->almacen_origen ?: '—' }}</strong>
            </div>

            <div class="mov-detalle-dato">
                <span>Ubicación de origen</span>
                <strong>{{ $registro->ubicacion_origen ?: '—' }}</strong>
            </div>

            <div class="mov-detalle-dato">
                <span>Almacén de destino</span>
                <strong>{{ $registro->almacen_destino ?: '—' }}</strong>
            </div>

            <div class="mov-detalle-dato">
                <span>Ubicación de destino</span>
                <strong>{{ $registro->ubicacion_destino ?: '—' }}</strong>
            </div>

            <div class="mov-detalle-dato motivo">
                <span>Motivo</span>
                <strong>{{ $registro->motivo ?: 'Sin motivo registrado' }}</strong>
            </div>
        </section>

        <section class="mov-detalle-card">
            <div class="mov-detalle-table-wrap">
                <table class="mov-detalle-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Lote</th>
                            <th>Vencimiento</th>
                            <th>Cantidad</th>
                            <th>Costo unitario</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse (($detalles ?? collect()) as $detalle)
                            <tr>
                                <td>{{ $detalle->codigo_interno ?: '—' }}</td>
                                <td>{{ $detalle->producto ?: 'Producto sin nombre' }}</td>
                                <td>{{ $detalle->codigo_lote ?: '—' }}</td>
                                <td>
                                    {{ $detalle->fecha_vencimiento
                                        ? \Illuminate\Support\Carbon::parse($detalle->fecha_vencimiento)->format('d/m/Y')
                                        : '—' }}
                                </td>
                                <td>
                                    {{ rtrim(rtrim(number_format((float) $detalle->cantidad, 3, ',', '.'), '0'), ',') }}
                                    {{ $detalle->unidad ?: '' }}
                                </td>
                                <td>
                                    {{ $detalle->costo_unitario !== null
                                        ? 'Bs ' . number_format((float) $detalle->costo_unitario, 2, ',', '.')
                                        : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="mov-detalle-empty">
                                    Este movimiento no tiene productos registrados en el detalle.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>
@endsection