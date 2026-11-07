@extends('admin.layouts.app')

@section('title', 'Pesajes')

@push('styles')
<style>
    .pesajes-page {
        --bg: #131419;
        --surface: #1a1c22;
        --surface-2: #21232a;
        --border: #2d3039;
        --border-hover: #4a4e5b;
        --text: #ececf0;
        --muted: #8e919b;
        --red: #e31b23;
        --red-soft: #ff777d;
        --green: #7be0a3;
        --amber: #f3d38a;
        --radius: 14px;

        color: var(--text);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .pesajes-page *,
    .pesajes-page *::before,
    .pesajes-page *::after { box-sizing: border-box; }

    .pesajes-page [hidden] { display: none !important; }

    /* ---------- Header ---------- */
    .pesajes-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding: 18px 22px;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background:
            linear-gradient(90deg, rgba(227, 27, 35, .10), transparent 45%),
            var(--bg);
    }

    .pesajes-eyebrow {
        color: var(--red-soft);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.2px;
        text-transform: uppercase;
    }
    .pesajes-header h1 { margin: 3px 0 2px; color: #fff; font-size: 22px; }
    .pesajes-header p { margin: 0; color: var(--muted); font-size: 12px; }

    .pesajes-button {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 16px;
        border: 1px solid var(--red);
        border-radius: 10px;
        color: #fff;
        background: var(--red);
        font: inherit;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: background .15s, transform .15s;
    }
    .pesajes-button svg { width: 15px; height: 15px; }
    .pesajes-button:hover { background: #f2262e; }
    .pesajes-button:active { transform: scale(.98); }
    .pesajes-button:focus-visible,
    .pesajes-chip:focus-visible,
    .pesajes-link:focus-visible { outline: 2px solid var(--red-soft); outline-offset: 2px; }

    /* ---------- Resumen ---------- */
    .pesajes-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }
    .pesajes-stat {
        padding: 14px 16px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: var(--surface);
    }
    .pesajes-stat span {
        display: block;
        margin-bottom: 6px;
        color: var(--muted);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .6px;
        text-transform: uppercase;
    }
    .pesajes-stat strong {
        color: #fff;
        font-size: 22px;
        font-variant-numeric: tabular-nums;
        overflow-wrap: anywhere;
    }
    .pesajes-stat.money { border-color: rgba(123, 224, 163, .25); background: rgba(123, 224, 163, .05); }
    .pesajes-stat.money strong { color: var(--green); }

    /* ---------- Card + toolbar ---------- */
    .pesajes-card {
        border: 1px solid var(--border);
        border-radius: var(--radius);
        background: var(--bg);
        overflow: hidden;
    }

    .pesajes-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
    }

    .pesajes-search-wrap { position: relative; width: min(100%, 320px); }
    .pesajes-search-wrap svg {
        position: absolute;
        left: 12px;
        top: 50%;
        width: 16px;
        height: 16px;
        transform: translateY(-50%);
        color: var(--muted);
        pointer-events: none;
    }
    .pesajes-search {
        width: 100%;
        min-height: 40px;
        padding: 0 12px 0 36px;
        border: 1px solid var(--border);
        border-radius: 10px;
        outline: none;
        color: #eee;
        background: var(--surface);
        font: inherit;
        font-size: 13px;
        transition: border-color .15s, box-shadow .15s;
    }
    .pesajes-search:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(227, 27, 35, .14); }

    .pesajes-chips { display: flex; gap: 8px; flex-wrap: wrap; }
    .pesajes-chip {
        padding: 7px 13px;
        border: 1px solid var(--border);
        border-radius: 999px;
        color: var(--muted);
        background: var(--surface);
        font: inherit;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s;
    }
    .pesajes-chip:hover { color: #fff; border-color: var(--border-hover); }
    .pesajes-chip[aria-pressed="true"] {
        color: #fff;
        border-color: var(--red);
        background: rgba(227, 27, 35, .16);
    }

    /* ---------- Tabla ---------- */
    .pesajes-table-wrap { overflow-x: auto; }

    .pesajes-table {
        width: 100%;
        min-width: 860px;
        border-collapse: collapse;
    }

    .pesajes-table th,
    .pesajes-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        text-align: left;
        font-size: 12px;
        vertical-align: middle;
    }
    .pesajes-table th {
        position: sticky;
        top: 0;
        color: var(--muted);
        background: var(--surface);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .pesajes-table td { color: #e2e3e8; }
    .pesajes-table tbody tr { transition: background .12s; }
    .pesajes-table tbody tr:hover { background: rgba(255, 255, 255, .025); }
    .pesajes-table tbody tr:last-child td { border-bottom: 0; }

    .pesajes-num,
    .pesajes-table .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .pesajes-table th.num { text-align: right; }

    .pesajes-code {
        color: var(--red-soft);
        font-family: ui-monospace, Menlo, Consolas, monospace;
        font-weight: 700;
        white-space: nowrap;
    }

    .pesajes-fecha { white-space: nowrap; }
    .pesajes-fecha small { display: block; margin-top: 2px; color: var(--muted); font-size: 10px; }

    .pesajes-prod strong { display: block; color: #fff; font-size: 12.5px; }
    .pesajes-prod small {
        display: block;
        margin-top: 2px;
        color: var(--muted);
        font-size: 10px;
        font-family: ui-monospace, Menlo, Consolas, monospace;
    }

    .pesajes-importe { color: var(--green); font-weight: 800; font-size: 13px; }

    /* Estado */
    .pesajes-estado {
        display: inline-block;
        padding: 3px 10px;
        border: 1px solid rgba(123, 224, 163, .3);
        border-radius: 999px;
        color: var(--green);
        background: rgba(123, 224, 163, .08);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .4px;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .pesajes-estado.anulado,
    .pesajes-estado.cancelado {
        color: #ff9ca0;
        border-color: rgba(227, 27, 35, .4);
        background: rgba(227, 27, 35, .08);
    }
    .pesajes-estado.impreso,
    .pesajes-estado.vendido {
        color: var(--amber);
        border-color: rgba(243, 211, 138, .3);
        background: rgba(243, 211, 138, .07);
    }

    /* Acciones */
    .pesajes-actions { display: flex; gap: 6px; justify-content: flex-end; }
    .pesajes-link {
        min-height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0 11px;
        border: 1px solid var(--border);
        border-radius: 8px;
        color: #eee;
        background: var(--surface-2);
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
        transition: background .15s, border-color .15s;
    }
    .pesajes-link svg { width: 14px; height: 14px; }
    .pesajes-link:hover { border-color: var(--border-hover); background: #2a2d35; }
    .pesajes-link.print:hover { border-color: var(--red); background: rgba(227, 27, 35, .12); }

    /* Vacío */
    .pesajes-empty {
        padding: 36px 20px !important;
        color: var(--muted) !important;
        text-align: center !important;
        font-size: 12px;
    }
    .pesajes-empty strong { display: block; margin-bottom: 6px; color: #fff; font-size: 14px; }

    .pesajes-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        padding: 12px 16px;
        border-top: 1px solid var(--border);
        color: var(--muted);
        font-size: 11px;
    }
    .pesajes-pagination { padding: 0; }

    /* ---------- Móvil: filas como tarjetas ---------- */
    @media (max-width: 760px) {
        .pesajes-stats { grid-template-columns: 1fr 1fr; }
        .pesajes-stat.money { grid-column: 1 / -1; }
        .pesajes-header .pesajes-button,
        .pesajes-search-wrap { width: 100%; }

        .pesajes-table { min-width: 0; }
        .pesajes-table thead { display: none; }
        .pesajes-table,
        .pesajes-table tbody,
        .pesajes-table tr,
        .pesajes-table td { display: block; width: 100%; }

        .pesajes-table tbody tr {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
        }
        .pesajes-table td {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 5px 0;
            border: 0;
            text-align: right;
        }
        .pesajes-table td::before {
            content: attr(data-label);
            flex: none;
            color: var(--muted);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
        }
        .pesajes-table td.pesajes-empty::before { content: none; }
        .pesajes-table td.pesajes-empty { display: block; }
        .pesajes-actions { width: 100%; }
        .pesajes-actions .pesajes-link { flex: 1; }
        .pesajes-table td.actions-cell::before { content: none; }
    }

    @media (prefers-reduced-motion: reduce) {
        .pesajes-page * { transition: none !important; }
    }
</style>
@endpush

@section('content')
@php
    $coleccion = $pesajes->getCollection();
    $totalImporte = $coleccion->sum(fn ($p) => (float) $p->importe);
    $estados = $coleccion
        ->map(fn ($p) => strtolower($p->estado ?? 'generado'))
        ->unique()->sort()->values();
@endphp

<div class="pesajes-page">
    <header class="pesajes-header">
        <div>
            <span class="pesajes-eyebrow">Operaciones · Pesaje</span>
            <h1>Historial de pesajes</h1>
            <p>Consulta los productos pesados y vuelve a imprimir sus etiquetas.</p>
        </div>

        <a href="{{ route('admin.pesajes.create') }}" class="pesajes-button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Registrar pesaje
        </a>
    </header>

    <div class="pesajes-stats">
        <div class="pesajes-stat">
            <span>Total de pesajes</span>
            <strong>{{ number_format($pesajes->total(), 0, ',', '.') }}</strong>
        </div>
        <div class="pesajes-stat">
            <span>En esta página</span>
            <strong>{{ number_format($coleccion->count(), 0, ',', '.') }}</strong>
        </div>
        <div class="pesajes-stat money">
            <span>Importe de esta página</span>
            <strong>Bs {{ number_format($totalImporte, 2, ',', '.') }}</strong>
        </div>
    </div>

    <section class="pesajes-card">
        <div class="pesajes-toolbar">
            <div class="pesajes-search-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                </svg>
                <input type="search" id="buscarPesaje" class="pesajes-search"
                       placeholder="Buscar número, producto o código"
                       autocomplete="off" aria-label="Buscar pesaje">
            </div>

            @if ($estados->count() > 1)
                <div class="pesajes-chips" id="chipsEstado" role="group" aria-label="Filtrar por estado">
                    <button type="button" class="pesajes-chip" data-estado="" aria-pressed="true">Todos</button>
                    @foreach ($estados as $est)
                        <button type="button" class="pesajes-chip" data-estado="{{ $est }}" aria-pressed="false">
                            {{ ucfirst($est) }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="pesajes-table-wrap">
            <table class="pesajes-table">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th class="num">Peso</th>
                        <th class="num">Precio</th>
                        <th class="num">Importe</th>
                        <th>Estado</th>
                        <th class="num">Etiqueta</th>
                    </tr>
                </thead>

                <tbody id="pesajesBody">
                    @forelse ($pesajes as $pesaje)
                        @php
                            $estado = strtolower($pesaje->estado ?? 'generado');
                            $fecha = $pesaje->fecha ? \Illuminate\Support\Carbon::parse($pesaje->fecha) : null;
                        @endphp
                        <tr data-estado="{{ $estado }}"
                            data-search="{{ mb_strtolower($pesaje->numero_pesaje . ' ' . $pesaje->codigo_interno . ' ' . $pesaje->producto, 'UTF-8') }}">
                            <td data-label="Número" class="pesajes-code">{{ $pesaje->numero_pesaje }}</td>

                            <td data-label="Fecha" class="pesajes-fecha">
                                @if ($fecha)
                                    <span>{{ $fecha->format('d/m/Y') }}</span>
                                    <small>{{ $fecha->format('H:i') }}</small>
                                @else
                                    —
                                @endif
                            </td>

                            <td data-label="Producto" class="pesajes-prod">
                                <span>
                                    <strong>{{ $pesaje->producto }}</strong>
                                    <small>{{ $pesaje->codigo_interno }}</small>
                                </span>
                            </td>

                            <td data-label="Peso" class="num">
                                {{ number_format((float) $pesaje->peso, 3, ',', '.') }} {{ $pesaje->unidad }}
                            </td>

                            <td data-label="Precio" class="num">
                                Bs {{ number_format((float) $pesaje->precio_por_unidad, 2, ',', '.') }}
                                / {{ $pesaje->unidad }}
                            </td>

                            <td data-label="Importe" class="num pesajes-importe">
                                Bs {{ number_format((float) $pesaje->importe, 2, ',', '.') }}
                            </td>

                            <td data-label="Estado">
                                <span class="pesajes-estado {{ $estado }}">{{ ucfirst($estado) }}</span>
                            </td>

                            <td class="num actions-cell">
                                <div class="pesajes-actions">
                                    <a href="{{ route('admin.pesajes.etiqueta', $pesaje->id) }}" class="pesajes-link">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        Ver
                                    </a>
                                    <a href="{{ route('admin.pesajes.etiqueta', $pesaje->id) }}?imprimir=1"
                                       class="pesajes-link print">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M6 9V3h12v6"/><rect x="6" y="14" width="12" height="7" rx="1"/>
                                            <path d="M6 18H4a1 1 0 0 1-1-1v-6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6a1 1 0 0 1-1 1h-2"/>
                                        </svg>
                                        Imprimir
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="pesajes-empty">
                                <strong>Todavía no hay pesajes</strong>
                                Registra el primero para generar su etiqueta.
                            </td>
                        </tr>
                    @endforelse

                    <tr id="sinResultados" hidden>
                        <td colspan="8" class="pesajes-empty">
                            <strong>Sin resultados</strong>
                            Ningún pesaje de esta página coincide con el filtro.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pesajes-footer">
            <span id="contador" aria-live="polite">
                Mostrando {{ $pesajes->firstItem() ?? 0 }}–{{ $pesajes->lastItem() ?? 0 }}
                de {{ $pesajes->total() }}
            </span>

            @if ($pesajes->hasPages())
                <div class="pesajes-pagination">
                    {{ $pesajes->links() }}
                </div>
            @endif
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const buscar = document.getElementById('buscarPesaje');
    const chips = document.getElementById('chipsEstado');
    const body = document.getElementById('pesajesBody');
    const sinResultados = document.getElementById('sinResultados');
    const contador = document.getElementById('contador');

    if (!buscar || !body) return;

    const filas = Array.from(body.querySelectorAll('tr[data-search]'));
    if (!filas.length) return;

    const textoOriginal = contador.textContent.trim();
    let estadoActivo = '';

    function filtrar() {
        const texto = buscar.value.trim().toLocaleLowerCase('es');
        let visibles = 0;

        filas.forEach(function (fila) {
            const ok = (!estadoActivo || fila.dataset.estado === estadoActivo)
                && (!texto || fila.dataset.search.includes(texto));
            fila.hidden = !ok;
            if (ok) visibles++;
        });

        sinResultados.hidden = visibles > 0;
        contador.textContent = (texto || estadoActivo)
            ? visibles + ' de ' + filas.length + ' en esta página'
            : textoOriginal;
    }

    let t;
    buscar.addEventListener('input', function () {
        clearTimeout(t);
        t = setTimeout(filtrar, 90);
    });

    buscar.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            buscar.value = '';
            filtrar();
        }
    });

    chips?.addEventListener('click', function (e) {
        const chip = e.target.closest('.pesajes-chip');
        if (!chip) return;

        estadoActivo = chip.dataset.estado;
        chips.querySelectorAll('.pesajes-chip').forEach(function (c) {
            c.setAttribute('aria-pressed', c === chip ? 'true' : 'false');
        });
        filtrar();
    });

    /* Atajo "/" para buscar */
    document.addEventListener('keydown', function (e) {
        const tag = (e.target.tagName || '').toLowerCase();
        if (e.key === '/' && !['input', 'textarea', 'select'].includes(tag)) {
            e.preventDefault();
            buscar.focus();
        }
    });
});
</script>
@endsection