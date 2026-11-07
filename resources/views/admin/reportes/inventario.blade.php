@extends('admin.layouts.app')

@section('title', 'Reporte de inventario')

@push('styles')
    @include('admin.reportes._styles')
@endpush

@php
    $cantidad = static function ($valor) {
        return rtrim(
            rtrim(
                number_format((float) ($valor ?? 0), 3, ',', '.'),
                '0'
            ),
            ','
        );
    };

    $almacenActual = $filtros['almacen_id'] !== null
        ? $almacenes->firstWhere('id', $filtros['almacen_id'])
        : null;

    $ambito = $almacenActual
        ? $almacenActual->codigo . ' · ' . $almacenActual->nombre
        : 'Todos los almacenes';
@endphp

@section('content')
<div class="rep-page">
    <header class="rep-header">
        <div>
            <span class="rep-eyebrow">Reportes · Inventario</span>

            <h1>Inventario actual</h1>

            <p class="rep-help">
                Existencias registradas de los productos activos.
            </p>
        </div>

        <div class="rep-actions">
            <a
                href="{{ route('admin.reportes.index') }}"
                class="rep-button"
            >
                ← Reportes
            </a>

            <a
                href="{{ route('admin.reportes.inventario.exportar', $filtros) }}"
                class="rep-button primary"
            >
                Descargar CSV
            </a>
        </div>
    </header>

    @if ($errors->any())
        <div class="rep-errors" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <section class="rep-card">
        <form
            action="{{ route('admin.reportes.inventario') }}"
            method="GET"
            class="rep-filters"
        >
            <div class="rep-field">
                <label for="almacen_id">Almacén</label>

                <select id="almacen_id" name="almacen_id">
                    <option value="">Todos los almacenes</option>

                    @foreach ($almacenes as $almacen)
                        <option
                            value="{{ $almacen->id }}"
                            @selected($filtros['almacen_id'] == $almacen->id)
                        >
                            {{ $almacen->codigo }} · {{ $almacen->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="rep-field">
                <label for="buscar">Producto</label>

                <input
                    id="buscar"
                    name="buscar"
                    type="search"
                    maxlength="100"
                    value="{{ $filtros['buscar'] }}"
                    placeholder="Nombre o código"
                >
            </div>

            <div class="rep-field">
                <label for="estado_stock">Estado del stock</label>

                <select id="estado_stock" name="estado_stock">
                    <option
                        value="todos"
                        @selected($filtros['estado_stock'] === 'todos')
                    >
                        Todos
                    </option>

                    <option
                        value="agotado"
                        @selected($filtros['estado_stock'] === 'agotado')
                    >
                        Agotado
                    </option>

                    <option
                        value="bajo"
                        @selected($filtros['estado_stock'] === 'bajo')
                    >
                        Stock bajo
                    </option>

                    <option
                        value="normal"
                        @selected($filtros['estado_stock'] === 'normal')
                    >
                        Normal
                    </option>
                </select>
            </div>

            <div class="rep-actions">
                <button type="submit" class="rep-button primary">
                    Consultar
                </button>

                <a
                    href="{{ route('admin.reportes.inventario') }}"
                    class="rep-button"
                >
                    Limpiar
                </a>
            </div>
        </form>
    </section>

    <section class="rep-stats" aria-label="Resumen de productos">
        <article class="rep-stat">
            <span>Productos activos</span>
            <strong>{{ $resumen['total'] }}</strong>
        </article>

        <article class="rep-stat">
            <span>Agotados</span>

            <strong style="color:#ff9ca4;">
                {{ $resumen['agotados'] }}
            </strong>
        </article>

        <article class="rep-stat">
            <span>Stock bajo</span>

            <strong style="color:#f4cf7d;">
                {{ $resumen['bajos'] }}
            </strong>
        </article>

        <article class="rep-stat">
            <span>Stock normal</span>

            <strong style="color:#8be5ae;">
                {{ $resumen['normales'] }}
            </strong>
        </article>
    </section>

    <p class="rep-help">
        Los indicadores consideran el almacén y la búsqueda.
        El filtro de estado se aplica a la tabla y a la descarga.
    </p>

    <section class="rep-card rep-table-card">
        <div class="rep-table-heading">
            <div>
                <h2>Existencias por producto</h2>

                <p class="rep-help">
                    {{ $ambito }}
                    · {{ $productos->total() }} resultados
                </p>
            </div>

            <p class="rep-help">
                Consulta: {{ now()->format('d/m/Y H:i') }}
            </p>
        </div>

        <div class="rep-table-wrap">
            <table class="rep-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th class="number">Stock actual</th>
                        <th>Unidad</th>
                        <th class="number">Mínimo</th>
                        <th class="number">Máximo</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($productos as $producto)
                        @php
                            $stock = (float) $producto->stock_actual;
                            $minimo = (float) ($producto->stock_minimo ?? 0);

                            if ($stock <= 0) {
                                $clase = 'agotado';
                                $estado = 'Agotado';
                            } elseif ($stock <= $minimo) {
                                $clase = 'bajo';
                                $estado = 'Stock bajo';
                            } else {
                                $clase = 'normal';
                                $estado = 'Normal';
                            }
                        @endphp

                        <tr>
                            <td>{{ $producto->codigo_interno }}</td>

                            <td>
                                <strong>{{ $producto->nombre }}</strong>
                            </td>

                            <td class="number">
                                {{ $cantidad($producto->stock_actual) }}
                            </td>

                            <td>{{ $producto->unidad ?? '—' }}</td>

                            <td class="number">
                                {{ $cantidad($producto->stock_minimo) }}
                            </td>

                            <td class="number">
                                {{ $producto->stock_maximo !== null
                                    ? $cantidad($producto->stock_maximo)
                                    : '—' }}
                            </td>

                            <td>
                                <span class="rep-badge {{ $clase }}">
                                    {{ $estado }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="rep-empty">
                                No hay productos que coincidan con los filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($productos->hasPages())
            <div class="rep-pagination">
                {{ $productos->links() }}
            </div>
        @endif
    </section>

    <p class="rep-help">
        Cada cantidad conserva su unidad de medida.
        Los mínimos y máximos corresponden a la configuración general del producto.
        Un producto sin existencias registradas en el ámbito seleccionado aparece con cero.
    </p>
</div>
@endsection