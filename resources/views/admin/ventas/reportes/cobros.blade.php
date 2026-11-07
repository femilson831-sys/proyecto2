@extends('admin.layouts.app')

@section('title', 'Reporte de cobros')

@push('styles')
    <style>
        .cobros-page {
            --surface: #17181c;
            --surface-2: #1e2026;
            --border: #30333d;
            --text: #ececf0;
            --muted: #92949c;
            --red: #e31b23;
            color: var(--text);
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .cobros-header,
        .cobros-card {
            border: 1px solid var(--border);
            border-radius: 14px;
            background: var(--surface);
        }

        .cobros-header {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            padding: 22px;
            overflow: hidden;
        }

        .cobros-header::before {
            position: absolute;
            inset: 0 0 auto;
            height: 2px;
            background: linear-gradient(90deg, var(--red), transparent);
            content: "";
        }

        .cobros-eyebrow {
            color: #ff777d;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .cobros-header h1 {
            margin: 7px 0;
            color: #fff;
            font-size: 25px;
        }

        .cobros-header p {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
        }

        .cobros-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 14px;
            border: 1px solid #383b45;
            border-radius: 8px;
            color: #eee;
            background: #202126;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
        }

        .cobros-filtros {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr)) auto;
            align-items: end;
            gap: 12px;
            padding: 16px;
        }

        .cobros-campo label {
            display: block;
            margin-bottom: 7px;
            color: #c9cad0;
            font-size: 11px;
            font-weight: 700;
        }

        .cobros-campo input,
        .cobros-campo select {
            width: 100%;
            min-height: 40px;
            padding: 0 10px;
            border: 1px solid #383b45;
            border-radius: 8px;
            outline: none;
            color: #eee;
            background: #202126;
            font-size: 12px;
        }

        .cobros-campo input:focus,
        .cobros-campo select:focus {
            border-color: #e31b23;
            box-shadow: 0 0 0 3px rgba(227, 27, 35, .12);
        }

        .cobros-campo select option {
            color: #eee;
            background: #202126;
        }

        .cobros-button {
            min-height: 40px;
            padding: 0 16px;
            border: 1px solid #e31b23;
            border-radius: 8px;
            color: #fff;
            background: #c91820;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
        }

        .cobros-button:hover {
            background: #e31b23;
        }

        .cobros-resumen {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 13px;
        }

        .cobros-stat {
            position: relative;
            min-width: 0;
            padding: 17px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background:
                linear-gradient(145deg, rgba(255, 255, 255, .025), transparent 55%),
                var(--surface);
        }

        .cobros-stat span {
            display: block;
            margin-bottom: 9px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .cobros-stat strong {
            color: #fff;
            font-size: 23px;
            overflow-wrap: anywhere;
        }

        .cobros-stat small {
            margin-left: 4px;
            color: #b6b8c0;
            font-size: 11px;
        }

        .cobros-stat.efectivo {
            border-top: 2px solid #4ade80;
        }

        .cobros-stat.digital {
            border-top: 2px solid #60a5fa;
        }

        .cobros-stat.total {
            border-top: 2px solid #ff777d;
        }

        .cobros-card-header {
            padding: 16px 18px;
            border-bottom: 1px solid #2b2d35;
        }

        .cobros-card-header h2 {
            margin: 0;
            color: #fff;
            font-size: 15px;
        }

        .cobros-card-header p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 11px;
        }

        .cobros-table-wrap {
            overflow-x: auto;
        }

        .cobros-table {
            width: 100%;
            min-width: 720px;
            border-collapse: collapse;
        }

        .cobros-table th,
        .cobros-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #2b2d35;
            text-align: left;
            font-size: 12px;
        }

        .cobros-table th {
            color: var(--muted);
            background: var(--surface-2);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .cobros-table td {
            color: #e2e3e8;
        }

        .cobros-table tbody tr:hover {
            background: rgba(255, 255, 255, .018);
        }

        .cobros-table tfoot td {
            color: #fff;
            background: #1e2026;
            font-weight: 800;
        }

        .cobros-monto {
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        .cobros-empty {
            padding: 24px !important;
            color: var(--muted) !important;
            text-align: center !important;
        }

        .cobros-pagination {
            padding: 14px 16px;
        }

        .cobros-pagination nav {
            display: flex;
            justify-content: center;
        }

        .cobros-pagination nav p {
            color: var(--muted);
            font-size: 12px;
        }

        .cobros-pagination nav a,
        .cobros-pagination nav span[aria-current="page"] span {
            color: #eee;
        }

        .cobros-error {
            padding: 12px 14px;
            border: 1px solid rgba(227, 27, 35, .35);
            border-radius: 9px;
            color: #ff9297;
            background: rgba(227, 27, 35, .08);
            font-size: 12px;
        }

        @media (max-width: 900px) {
            .cobros-filtros {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .cobros-button {
                width: 100%;
            }
        }

        @media (max-width: 620px) {
            .cobros-resumen {
                grid-template-columns: 1fr;
            }

            .cobros-filtros {
                grid-template-columns: 1fr;
            }

            .cobros-header h1 {
                font-size: 21px;
            }

            .cobros-back {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="cobros-page">
        <header class="cobros-header">
            <div>
                <span class="cobros-eyebrow">Ventas · Administración</span>
                <h1>Reporte de cobros</h1>
                <p>Consulta cuánto cobró cada cajero por método de pago y fecha.</p>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="cobros-back">
                Volver al panel
            </a>
        </header>

        @if ($errors->any())
            <div class="cobros-error" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.ventas.reportes.cobros') }}" method="GET" class="cobros-card cobros-filtros">
            <div class="cobros-campo">
                <label for="desde">Desde</label>
                <input type="date" id="desde" name="desde" value="{{ $desde }}" required>
            </div>

            <div class="cobros-campo">
                <label for="hasta">Hasta</label>
                <input type="date" id="hasta" name="hasta" value="{{ $hasta }}" required>
            </div>

            <div class="cobros-campo">
                <label for="cajero_id">Cajero</label>
                <select name="cajero_id" id="cajero_id">
                    <option value="">Todos los cajeros</option>
                    @foreach ($cajeros as $cajero)
                        <option value="{{ $cajero->id }}" @selected((string) $cajeroId === (string) $cajero->id)>
                            {{ $cajero->username }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="cobros-campo">
                <label for="metodo">Método de pago</label>
                <select name="metodo" id="metodo">
                    <option value="todos" @selected($metodo === 'todos')>
                        Todos
                    </option>
                    <option value="efectivo" @selected($metodo === 'efectivo')>
                        Efectivo
                    </option>
                    <option value="qr_transferencia" @selected($metodo === 'qr_transferencia')>
                        QR / Transferencia
                    </option>
                </select>
            </div>

            <button type="submit" class="cobros-button">
                Aplicar filtros
            </button>
        </form>

        <section class="cobros-resumen" aria-label="Resumen de cobros">
            <article class="cobros-stat efectivo">
                <span>Total en efectivo</span>
                <strong>
                    Bs {{ number_format((float) $resumen->total_efectivo, 2, ',', '.') }}
                </strong>
            </article>

            <article class="cobros-stat digital">
                <span>Total QR / transferencia</span>
                <strong>
                    Bs {{ number_format((float) $resumen->total_qr_transferencia, 2, ',', '.') }}
                </strong>
            </article>

            <article class="cobros-stat total">
                <span>Total cobrado</span>
                <strong>
                    Bs {{ number_format((float) $resumen->total_general, 2, ',', '.') }}
                </strong>
            </article>
        </section>

        <section class="cobros-card">
            <div class="cobros-card-header">
                <h2>Totales por cajero</h2>
                <p>Montos cobrados y cantidad de ventas dentro del rango seleccionado.</p>
            </div>

            <div class="cobros-table-wrap">
                <table class="cobros-table">
                    <thead>
                        <tr>
                            <th>Cajero</th>
                            <th>Ventas</th>
                            <th>Efectivo</th>
                            <th>QR / transferencia</th>
                            <th>Total cobrado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reporteCajeros as $fila)
                            <tr>
                                <td>{{ $fila->cajero }}</td>
                                <td>{{ number_format((int) $fila->cantidad_ventas) }}</td>
                                <td class="cobros-monto">
                                    Bs {{ number_format((float) $fila->total_efectivo, 2, ',', '.') }}
                                </td>
                                <td class="cobros-monto">
                                    Bs {{ number_format((float) $fila->total_qr_transferencia, 2, ',', '.') }}
                                </td>
                                <td class="cobros-monto">
                                    Bs {{ number_format((float) $fila->total_cobrado, 2, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="cobros-empty">
                                    No hay cobros para los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="cobros-card">
            <div class="cobros-card-header">
                <h2>Detalle diario por cajero</h2>
                <p>El reporte usa la fecha del pago; si no existe, usa la fecha de la venta.</p>
            </div>

            <div class="cobros-table-wrap">
                <table class="cobros-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Cajero</th>
                            <th>Ventas</th>
                            <th>Efectivo</th>
                            <th>QR / transferencia</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($detalleDiario as $fila)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($fila->fecha)->format('d/m/Y') }}</td>
                                <td>{{ $fila->cajero }}</td>
                                <td>{{ number_format((int) $fila->cantidad_ventas) }}</td>
                                <td class="cobros-monto">
                                    Bs {{ number_format((float) $fila->total_efectivo, 2, ',', '.') }}
                                </td>
                                <td class="cobros-monto">
                                    Bs {{ number_format((float) $fila->total_qr_transferencia, 2, ',', '.') }}
                                </td>
                                <td class="cobros-monto">
                                    Bs {{ number_format((float) $fila->total_cobrado, 2, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="cobros-empty">
                                    No hay cobros diarios para los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($detalleDiario->hasPages())
                <div class="cobros-pagination">
                    {{ $detalleDiario->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
