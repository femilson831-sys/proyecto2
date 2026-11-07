@extends('admin.layouts.app')

@section('title', 'Reportes')

@php
    $formatear = static function ($valor, string $formato): string {
        if ($valor === null || $valor === '') {
            return '—';
        }

        return match ($formato) {
            'moneda' => 'Bs ' . number_format((float) $valor, 2, ',', '.'),
            'entero' => number_format((float) $valor, 0, ',', '.'),
            'cantidad' => rtrim(
                rtrim(number_format((float) $valor, 3, ',', '.'), '0'),
                ','
            ),
            'fecha' => \Illuminate\Support\Carbon::parse($valor)
                ->format('d/m/Y'),
            'fecha_hora' => \Illuminate\Support\Carbon::parse($valor)
                ->format('d/m/Y H:i:s'),
            default => (string) $valor,
        };
    };

    $maxGrafico = max(
        1,
        (float) ($grafico['datos']->max('valor') ?? 0)
    );

    $usaFiltro = static fn (string $nombre): bool =>
        in_array($nombre, $reporte['filtros'], true);

    $usuarioFiltrado = $filtros['usuario_id']
        ? $usuarios->firstWhere('id', $filtros['usuario_id'])
        : null;

    $almacenFiltrado = $filtros['almacen_id']
        ? $almacenes->firstWhere('id', $filtros['almacen_id'])
        : null;

    $productoFiltrado = $filtros['producto_id']
        ? $productos->firstWhere('id', $filtros['producto_id'])
        : null;

    $movimientoFiltrado = $filtros['tipo_movimiento_id']
        ? $tiposMovimiento->firstWhere(
            'id',
            $filtros['tipo_movimiento_id']
        )
        : null;
@endphp

@push('styles')
<style>
    .rp-page {
        --rp-bg: #17181c;
        --rp-soft: #202127;
        --rp-border: #343741;
        --rp-muted: #a7adba;
        display: grid;
        gap: 20px;
        color: #edf0f5;
    }

    .rp-page * { box-sizing: border-box; }
    .rp-page [hidden] { display: none !important; }

    .rp-header, .rp-card, .rp-stat {
        border: 1px solid var(--rp-border);
        border-radius: 15px;
        background: var(--rp-bg);
    }

    .rp-header {
        padding: 24px;
        background:
            radial-gradient(circle at right top, #e31b231b, transparent 65%),
            var(--rp-bg);
    }

    .rp-eyebrow {
        color: #ff858e;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .rp-header h1 { margin: 8px 0; font-size: 27px; }

    .rp-help {
        margin: 0;
        color: var(--rp-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .rp-card { min-width: 0; padding: 22px; }
    .rp-card h2 { margin: 0 0 16px; font-size: 17px; }

    .rp-info {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 22px;
        margin-top: 15px;
        color: var(--rp-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .rp-info strong { color: #edf0f5; }

    .rp-filters {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .rp-wide { grid-column: 1 / -1; }

    .rp-field label {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
    }

    .rp-field input, .rp-field select {
        width: 100%;
        min-height: 44px;
        padding: 10px 12px;
        border: 1px solid #444854;
        border-radius: 9px;
        color: #edf0f5;
        background: var(--rp-soft);
        font-family: inherit;
        font-size: 13px;
    }

    .rp-field input:focus, .rp-field select:focus {
        outline: 2px solid #ff858e;
        outline-offset: 2px;
    }

    .rp-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .rp-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 10px 15px;
        border: 1px solid #444854;
        border-radius: 9px;
        color: #fff;
        background: #272930;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
    }

    .rp-button.primary { border-color: #ce1b25; background: #ce1b25; }

    .rp-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(165px, 1fr));
        gap: 14px;
    }

    .rp-stat { padding: 20px; }
    .rp-stat span { color: var(--rp-muted); font-size: 12px; }

    .rp-stat strong {
        display: block;
        margin-top: 10px;
        font-size: 25px;
        overflow-wrap: anywhere;
    }

    .rp-chart { display: grid; gap: 16px; }

    .rp-bar-heading {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 7px;
        font-size: 12px;
    }

    .rp-track {
        height: 12px;
        overflow: hidden;
        border-radius: 999px;
        background: #2a2d36;
    }

    .rp-fill {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #b81923, #ff737c);
    }

    .rp-table-card { padding: 0; overflow: hidden; }

    .rp-table-heading {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
        padding: 20px;
    }

    .rp-table-heading h2 { margin: 0 0 6px; }
    .rp-table-wrap { overflow-x: auto; }

    .rp-table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
    }

    .rp-table th, .rp-table td {
        padding: 13px 15px;
        border-bottom: 1px solid var(--rp-border);
        text-align: left;
        vertical-align: top;
        font-size: 12px;
    }

    .rp-table th {
        color: var(--rp-muted);
        background: var(--rp-soft);
        font-size: 10px;
        text-transform: uppercase;
    }

    .rp-table .number {
        text-align: right;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    .rp-empty {
        padding: 30px !important;
        color: var(--rp-muted);
        text-align: center !important;
    }

    .rp-pagination { padding: 18px; }

    .rp-errors {
        padding: 16px;
        border: 1px solid #e31b2355;
        border-radius: 10px;
        color: #ffb1b8;
        background: #e31b2310;
        font-size: 13px;
        line-height: 1.8;
    }

    @media (max-width: 900px) {
        .rp-filters { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 600px) {
        .rp-filters { grid-template-columns: 1fr; }
        .rp-wide { grid-column: auto; }
        .rp-header h1 { font-size: 23px; }
    }

    @page {
        size: A4 landscape;
        margin: 12mm;
    }

    @media print {
        .sidebar, .navbar, .rp-no-print { display: none !important; }

        html, body {
            height: auto !important;
            overflow: visible !important;
            background: white !important;
        }

        .admin-wrapper {
            display: block !important;
            height: auto !important;
            overflow: visible !important;
        }

        main, .main-content, .content, .content-wrapper {
            width: 100% !important;
            height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
        }

        .rp-page { display: block; color: #111; }

        .rp-header, .rp-card, .rp-stat {
            margin-bottom: 12px;
            border-color: #ccc;
            border-radius: 0;
            color: #111;
            background: white !important;
        }

        .rp-help, .rp-eyebrow, .rp-info,
        .rp-info strong, .rp-stat span {
            color: #333 !important;
        }

        .rp-stats { display: flex; flex-wrap: wrap; gap: 10px; }
        .rp-stat { flex: 1; padding: 10px; }
        .rp-stat strong { font-size: 18px; }

        .rp-table-card, .rp-table-wrap { overflow: visible; }
        .rp-table { min-width: 0; }

        .rp-table th, .rp-table td {
            padding: 7px;
            border-color: #ccc;
            color: #111;
            font-size: 9px;
            overflow-wrap: anywhere;
        }

        .rp-table th { background: #eee !important; }
        .rp-table thead { display: table-header-group; }
        .rp-table tr { break-inside: avoid; }
    }
</style>
@endpush

@section('content')
<div class="rp-page">
    <header class="rp-header">
        <span class="rp-eyebrow">
            Minisúper NANOS · Reportes
        </span>

        <h1>{{ $reporte['titulo'] }}</h1>

        <p class="rp-help">{{ $reporte['descripcion'] }}</p>

        <div class="rp-info">
            <span>
                <strong>Generado:</strong>
                {{ $generadoEn->format('d/m/Y H:i:s') }}
            </span>

            <span>
                <strong>Generado por:</strong>
                {{ $generadoPor }}
            </span>

            @if ($reporte['fechas'])
                <span>
                    <strong>Período:</strong>
                    {{ $formatear($filtros['desde'], 'fecha') }}
                    al
                    {{ $formatear($filtros['hasta'], 'fecha') }}
                </span>
            @else
                <span>
                    <strong>Referencia:</strong>
                    existencias actuales
                </span>
            @endif

            @if ($usaFiltro('usuario'))
                <span>
                    <strong>Usuario consultado:</strong>
                    {{ $usuarioFiltrado?->username ?? 'Todos' }}
                </span>
            @endif

            @if ($usaFiltro('almacen'))
                <span>
                    <strong>Almacén:</strong>
                    {{ $almacenFiltrado?->nombre ?? 'Todos' }}
                </span>
            @endif

            @if ($usaFiltro('producto'))
                <span>
                    <strong>Producto:</strong>
                    {{ $productoFiltrado?->nombre ?? 'Todos' }}
                </span>
            @endif

            @if ($usaFiltro('movimiento'))
                <span>
                    <strong>Movimiento:</strong>
                    {{ $movimientoFiltrado?->nombre ?? 'Todos' }}
                </span>
            @endif

            @if ($usaFiltro('stock'))
                <span>
                    <strong>Stock:</strong>
                    {{ $filtros['estado_stock'] }}
                </span>
            @endif

            @if ($usaFiltro('vencimiento'))
                <span>
                    <strong>Vencimiento:</strong>
                    {{ $filtros['vencimiento'] }}
                </span>
            @endif

            @if ($usaFiltro('pago'))
                <span>
                    <strong>Pago:</strong>
                    {{ match ($filtros['metodo_pago']) {
                        'efectivo' => 'Efectivo',
                        'qr' => 'QR / Transferencia',
                        default => 'Todos',
                    } }}
                </span>
            @endif
        </div>
    </header>

    @if ($errors->any())
        <div class="rp-errors rp-no-print" role="alert">
            <strong>Revisa los filtros:</strong>

            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <section class="rp-card rp-no-print">
        <h2>Seleccionar reporte y filtros</h2>

        <form action="{{ route('admin.reportes.index') }}" method="GET">
            <div class="rp-filters">
                <div class="rp-field rp-wide">
                    <label for="tipo">Tipo de reporte</label>

                    <select id="tipo" name="tipo">
                        @foreach ($definiciones as $clave => $definicion)
                            <option
                                value="{{ $clave }}"
                                @selected($filtros['tipo'] === $clave)
                            >
                                {{ $definicion['titulo'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="rp-field" data-filter="fechas">
                    <label for="desde">Desde</label>

                    <input
                        id="desde"
                        name="desde"
                        type="date"
                        value="{{ $filtros['desde'] }}"
                    >
                </div>

                <div class="rp-field" data-filter="fechas">
                    <label for="hasta">Hasta</label>

                    <input
                        id="hasta"
                        name="hasta"
                        type="date"
                        value="{{ $filtros['hasta'] }}"
                    >
                </div>

                <div class="rp-field" data-filter="usuario">
                    <label for="usuario_id" id="usuarioLabel">Usuario</label>

                    <select id="usuario_id" name="usuario_id">
                        <option value="">Todos los usuarios</option>

                        @foreach ($usuarios as $usuario)
                            <option
                                value="{{ $usuario->id }}"
                                @selected($filtros['usuario_id'] == $usuario->id)
                            >
                                {{ $usuario->username }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="rp-field" data-filter="almacen">
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

                <div class="rp-field" data-filter="producto">
                    <label for="producto_id">Producto</label>

                    <select id="producto_id" name="producto_id">
                        <option value="">Todos los productos</option>

                        @foreach ($productos as $producto)
                            <option
                                value="{{ $producto->id }}"
                                @selected($filtros['producto_id'] == $producto->id)
                            >
                                {{ $producto->codigo_interno }} · {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="rp-field" data-filter="stock">
                    <label for="estado_stock">Estado del stock</label>

                    <select id="estado_stock" name="estado_stock">
                        @foreach ([
                            'todos' => 'Todos',
                            'Agotado' => 'Agotado',
                            'Bajo' => 'Stock bajo',
                            'Normal' => 'Normal',
                        ] as $valor => $etiqueta)
                            <option
                                value="{{ $valor }}"
                                @selected($filtros['estado_stock'] === $valor)
                            >
                                {{ $etiqueta }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="rp-field" data-filter="pago">
                    <label for="metodo_pago">Método de pago</label>

                    <select id="metodo_pago" name="metodo_pago">
                        @foreach ([
                            'todos' => 'Todos',
                            'efectivo' => 'Efectivo',
                            'qr' => 'QR / Transferencia',
                        ] as $valor => $etiqueta)
                            <option
                                value="{{ $valor }}"
                                @selected($filtros['metodo_pago'] === $valor)
                            >
                                {{ $etiqueta }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="rp-field" data-filter="movimiento">
                    <label for="tipo_movimiento_id">Tipo de movimiento</label>

                    <select id="tipo_movimiento_id" name="tipo_movimiento_id">
                        <option value="">Todos los tipos</option>

                        @foreach ($tiposMovimiento as $tipoMovimiento)
                            <option
                                value="{{ $tipoMovimiento->id }}"
                                @selected($filtros['tipo_movimiento_id'] == $tipoMovimiento->id)
                            >
                                {{ $tipoMovimiento->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="rp-field" data-filter="vencimiento">
                    <label for="vencimiento">Condición del lote</label>

                    <select id="vencimiento" name="vencimiento">
                        @foreach ([
                            'todos' => 'Todos',
                            'Vencido' => 'Vencidos',
                            'Próximo a vencer' => 'Vencen en los próximos 30 días',
                            'Vigente' => 'Vigentes a más de 30 días',
                            'Sin fecha' => 'Sin fecha de vencimiento',
                        ] as $valor => $etiqueta)
                            <option
                                value="{{ $valor }}"
                                @selected($filtros['vencimiento'] === $valor)
                            >
                                {{ $etiqueta }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="rp-actions">
                <button type="submit" class="rp-button primary">
                    Consultar reporte
                </button>

                <a
                    href="{{ route('admin.reportes.index', [
                        'tipo' => $filtros['tipo'],
                    ]) }}"
                    class="rp-button"
                >
                    Limpiar filtros
                </a>

                <a
                    href="{{ route('admin.reportes.exportar', $filtros) }}"
                    class="rp-button"
                >
                    Descargar CSV
                </a>

                <a
                    href="{{ route('admin.reportes.index', array_merge(
                        $filtros,
                        ['imprimir' => 1]
                    )) }}"
                    class="rp-button"
                >
                    Imprimir / guardar PDF
                </a>

                @if ($imprimir)
                    <a
                        href="{{ route('admin.reportes.index', $filtros) }}"
                        class="rp-button"
                    >
                        Volver a vista paginada
                    </a>
                @endif
            </div>

            <p class="rp-help" style="margin-top:12px;">
                Pulsa “Consultar reporte” después de modificar los filtros.
                La descarga y la impresión utilizan los filtros consultados.
            </p>
        </form>
    </section>

    <section class="rp-stats">
        @foreach ($resumen as $indicador)
            <article class="rp-stat">
                <span>{{ $indicador['titulo'] }}</span>

                <strong>
                    {{ $formatear(
                        $indicador['valor'],
                        $indicador['formato']
                    ) }}
                </strong>
            </article>
        @endforeach
    </section>

    <section class="rp-card rp-no-print">
        <h2>{{ $grafico['titulo'] }}</h2>

        <div class="rp-chart">
            @forelse ($grafico['datos'] as $barra)
                @php
                    $ancho = max(
                        0,
                        min(100, ((float) $barra->valor / $maxGrafico) * 100)
                    );
                @endphp

                <div>
                    <div class="rp-bar-heading">
                        <span>{{ $barra->etiqueta ?? 'Sin especificar' }}</span>

                        <strong>
                            {{ $formatear(
                                $barra->valor,
                                $grafico['moneda'] ? 'moneda' : 'entero'
                            ) }}
                        </strong>
                    </div>

                    <div class="rp-track" aria-hidden="true">
                        <div
                            class="rp-fill"
                            style="width: {{ $ancho }}%;"
                        ></div>
                    </div>
                </div>
            @empty
                <p class="rp-help">No hay datos para mostrar el gráfico.</p>
            @endforelse
        </div>
    </section>

    <section class="rp-card rp-table-card">
        <div class="rp-table-heading">
            <div>
                <h2>Detalle del reporte</h2>

                <p class="rp-help">
                    {{ number_format($totalRegistros, 0, ',', '.') }}
                    registros encontrados.
                </p>
            </div>

            @if (!$imprimir)
                <p class="rp-help">
                    Página {{ $registros->currentPage() }}
                    de {{ $registros->lastPage() }}
                </p>
            @endif
        </div>

        <div class="rp-table-wrap">
            <table class="rp-table">
                <thead>
                    <tr>
                        @foreach ($reporte['columnas'] as $columna)
                            <th
                                @class([
                                    'number' => in_array(
                                        $columna['formato'],
                                        ['cantidad', 'moneda', 'entero'],
                                        true
                                    ),
                                ])
                            >
                                {{ $columna['titulo'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @forelse ($registros as $registro)
                        <tr>
                            @foreach ($reporte['columnas'] as $columna)
                                <td
                                    @class([
                                        'number' => in_array(
                                            $columna['formato'],
                                            ['cantidad', 'moneda', 'entero'],
                                            true
                                        ),
                                    ])
                                >
                                    {{ $formatear(
                                        $registro->{$columna['campo']} ?? null,
                                        $columna['formato']
                                    ) }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="{{ count($reporte['columnas']) }}"
                                class="rp-empty"
                            >
                                No hay registros que coincidan con los filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (!$imprimir && $registros->hasPages())
            <div class="rp-pagination rp-no-print">
                {{ $registros->links() }}
            </div>
        @endif
    </section>

    @if ($filtros['tipo'] === 'accesos')
        <p class="rp-help">
            El cierre se confirma cuando el usuario pulsa “Cerrar sesión”.
            Cerrar el navegador no proporciona una hora exacta de salida.
            “Con actividad reciente” no confirma que continúe conectado.
        </p>
    @else
        <p class="rp-help">
            Los indicadores consideran todos los resultados filtrados.
            Las cantidades conservan su unidad de medida.
        </p>
    @endif

    <footer class="rp-help">
        Minisúper NANOS · {{ $reporte['titulo'] }}
        · Generado por {{ $generadoPor }}
        · {{ $generadoEn->format('d/m/Y H:i:s') }}.

        @if ($imprimir)
            Activa los encabezados y pies del navegador para incluir
            la numeración de páginas.
        @endif
    </footer>
</div>

<script>
(() => {
    const definiciones = {{ \Illuminate\Support\Js::from($definiciones) }};
    const selector = document.getElementById('tipo');
    const usuarioLabel = document.getElementById('usuarioLabel');

    function actualizarFiltros() {
        const tipo = selector.value;
        const configuracion = definiciones[tipo];

        document.querySelectorAll('[data-filter]').forEach(grupo => {
            const nombre = grupo.dataset.filter;

            const visible = nombre === 'fechas'
                ? configuracion.fechas
                : configuracion.filtros.includes(nombre);

            grupo.hidden = !visible;

            grupo.querySelectorAll('input, select').forEach(campo => {
                campo.disabled = !visible;
            });
        });

        usuarioLabel.textContent = (
            tipo === 'cobros' || tipo === 'vendidos'
        )
            ? 'Cajero'
            : tipo === 'tareas'
                ? 'Usuario asignado'
                : 'Usuario';
    }

    selector.addEventListener('change', actualizarFiltros);
    actualizarFiltros();

    @if ($imprimir)
        window.addEventListener('load', () => {
            window.print();
        });
    @endif
})();
</script>
@endsection