@extends('admin.layouts.app')

@section('title', 'Papelera de empleados')

@push('styles')

<style>

    .history-page {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* ==========================================================
       CABECERA
    ========================================================== */

    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
    }

    .history-title .eyebrow {
        display: block;
        margin-bottom: 7px;

        color: #e31b23;

        font-size: 11px;
        font-weight: 800;

        text-transform: uppercase;
        letter-spacing: .8px;
    }

    .history-title h1 {
        margin: 0 0 7px;

        color: #fff;

        font-size: 25px;
        font-weight: 800;
    }

    .history-title p {
        margin: 0;

        color: #777d85;

        font-size: 13px;
    }

    /* ==========================================================
       BOTONES SUPERIORES
    ========================================================== */

    .history-header-actions {
        display: flex;
        gap: 9px;
        flex-wrap: wrap;
    }

    .history-btn {
        min-height: 41px;
        padding: 0 14px;

        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;

        border: 1px solid rgba(255,255,255,.08);
        border-radius: 10px;

        background: rgba(255,255,255,.035);

        color: #c8ccd1;

        text-decoration: none;

        font-size: 11px;
        font-weight: 800;

        cursor: pointer;

        transition: .2s ease;
    }

    .history-btn:hover {
        color: #fff;

        background: rgba(255,255,255,.07);

        transform: translateY(-1px);
    }

    .history-btn.excel {
        color: #4ade80;

        border-color: rgba(34,197,94,.18);

        background: rgba(34,197,94,.06);
    }

    .history-btn.excel:hover {
        color: #fff;
        background: #168044;
    }

    .history-btn.pdf {
        color: #f87171;

        border-color: rgba(239,68,68,.18);

        background: rgba(239,68,68,.06);
    }

    .history-btn.pdf:hover {
        color: #fff;
        background: #c51d25;
    }

    /* ==========================================================
       ALERTAS
    ========================================================== */

    .history-alert {
        padding: 14px 17px;

        border-radius: 12px;

        font-size: 12px;
        font-weight: 700;
    }

    .history-alert.success {
        border: 1px solid rgba(34,197,94,.18);

        background: rgba(34,197,94,.07);

        color: #86efac;
    }

    .history-alert.error {
        border: 1px solid rgba(239,68,68,.18);

        background: rgba(239,68,68,.07);

        color: #fca5a5;
    }

    /* ==========================================================
       INFORMACIÓN
    ========================================================== */

    .history-info {
        display: flex;
        align-items: center;
        gap: 10px;

        padding: 12px 14px;

        border: 1px solid rgba(59,130,246,.14);
        border-radius: 12px;

        background: rgba(59,130,246,.045);

        color: #9298a1;

        font-size: 11px;
    }

    .history-info-icon {
        width: 28px;
        height: 28px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 8px;

        background: rgba(59,130,246,.09);

        color: #60a5fa;

        font-weight: 900;
    }

    .history-info strong {
        color: #cdd0d5;
    }

    /* ==========================================================
       BUSCADOR
    ========================================================== */

    .history-search-panel {
        padding: 14px;

        border: 1px solid rgba(255,255,255,.07);
        border-radius: 14px;

        background: rgba(255,255,255,.025);
    }

    .history-search {
        display: flex;
        gap: 9px;
    }

    .history-search input {
        flex: 1;

        height: 42px;

        padding: 0 14px;

        border: 1px solid rgba(255,255,255,.08);
        border-radius: 10px;

        outline: none;

        background: rgba(255,255,255,.035);

        color: #fff;
    }

    .history-search input::placeholder {
        color: #60656d;
    }

    .history-search input:focus {
        border-color: rgba(227,27,35,.55);

        box-shadow:
            0 0 0 3px
            rgba(227,27,35,.08);
    }

    .history-search button {
        padding: 0 18px;

        border: none;
        border-radius: 10px;

        background: #e31b23;

        color: #fff;

        font-size: 11px;
        font-weight: 800;

        cursor: pointer;
    }

    .history-search button:hover {
        background: #c5171e;
    }

    .clear-search {
        min-height: 42px;
        padding: 0 15px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        border: 1px solid rgba(255,255,255,.08);
        border-radius: 10px;

        color: #a4a8ae;

        text-decoration: none;

        font-size: 11px;
        font-weight: 700;
    }

    .clear-search:hover {
        color: #fff;

        background: rgba(255,255,255,.05);
    }

    /* ==========================================================
       TABLA
    ========================================================== */

    .history-panel {
        overflow: hidden;

        border: 1px solid rgba(255,255,255,.07);
        border-radius: 15px;

        background: #15171a;
    }

    .history-panel-header {
        padding: 14px 16px;

        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;

        border-bottom: 1px solid rgba(255,255,255,.055);
    }

    .history-panel-header h2 {
        margin: 0;

        color: #e7e8ea;

        font-size: 13px;
        font-weight: 800;
    }

    .history-panel-header span {
        color: #6f747c;

        font-size: 10px;
    }

    .history-total {
        padding: 5px 9px;

        border: 1px solid rgba(255,255,255,.07);
        border-radius: 999px;

        background: rgba(255,255,255,.025);

        color: #a8acb2 !important;

        font-weight: 800;
    }

    .history-responsive {
        overflow-x: auto;
    }

    .history-table {
        width: 100%;

        border-collapse: collapse;
    }

    .history-table th {
        padding: 13px 15px;

        border-bottom:
            1px solid
            rgba(255,255,255,.06);

        background:
            rgba(255,255,255,.025);

        color: #727880;

        text-align: left;

        font-size: 10px;
        font-weight: 800;

        text-transform: uppercase;

        white-space: nowrap;
    }

    .history-table td {
        padding: 14px 15px;

        border-bottom:
            1px solid
            rgba(255,255,255,.045);

        color: #afb4bb;

        font-size: 12px;

        vertical-align: middle;
    }

    .history-table tbody tr:last-child td {
        border-bottom: none;
    }

    .history-table tr:hover td {
        background:
            rgba(255,255,255,.015);
    }

    .history-name {
        display: block;

        color: #fff;

        font-weight: 800;

        white-space: nowrap;
    }

    .history-code {
        color: #ddd;

        font-weight: 700;
    }

    .history-muted {
        color: #747981;
    }

    /* ==========================================================
       RESPONSABLE
    ========================================================== */

    .responsible {
        display: flex;
        flex-direction: column;
        gap: 2px;

        min-width: 155px;
    }

    .responsible strong {
        color: #ddd;

        font-size: 11px;
    }

    .responsible span {
        color: #777d85;

        font-size: 10px;
    }

    /* ==========================================================
       BADGE
    ========================================================== */

    .deleted-badge {
        display: inline-flex;

        padding: 5px 8px;

        border-radius: 999px;

        background: rgba(239,68,68,.08);

        color: #f87171;

        font-size: 9px;
        font-weight: 800;

        text-transform: uppercase;
    }

    /* ==========================================================
       ACCIONES
    ========================================================== */

    .history-actions {
        display: flex;
        align-items: center;
        gap: 6px;

        white-space: nowrap;
    }

    .history-action {
        min-height: 32px;

        padding: 0 10px;

        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;

        border-radius: 8px;

        text-decoration: none;

        font-size: 10px;
        font-weight: 800;

        cursor: pointer;

        transition: .18s ease;
    }

    /* VER */

    .history-action.view {
        border:
            1px solid
            rgba(255,255,255,.08);

        background:
            rgba(255,255,255,.035);

        color: #bbbfc5;
    }

    .history-action.view:hover {
        background: rgba(255,255,255,.08);

        color: #fff;
    }

    /* HISTORIAL INDIVIDUAL */

    .history-action.individual {
        border:
            1px solid
            rgba(59,130,246,.20);

        background:
            rgba(59,130,246,.07);

        color: #60a5fa;
    }

    .history-action.individual:hover {
        border-color: #2563eb;

        background: #2563eb;

        color: #fff;
    }

    /* RESTAURAR */

    .history-action.restore {
        border:
            1px solid
            rgba(34,197,94,.18);

        background:
            rgba(34,197,94,.06);

        color: #4ade80;
    }

    .history-action.restore:hover {
        background: #16a34a;

        color: #fff;
    }

    /* ==========================================================
       VACÍO
    ========================================================== */

    .history-empty {
        padding: 50px 20px !important;

        color: #6f757d !important;

        text-align: center;
    }

    .history-empty strong {
        display: block;

        margin-bottom: 6px;

        color: #aeb2b8;

        font-size: 13px;
    }

    /* ==========================================================
       PAGINACIÓN
    ========================================================== */

    .history-pagination {
        padding: 15px;

        border-top: 1px solid rgba(255,255,255,.05);
    }

    /* ==========================================================
       RESPONSIVE
    ========================================================== */

    @media(max-width: 900px) {

        .history-header {
            flex-direction: column;
        }

        .history-header-actions {
            width: 100%;
        }

    }

    @media(max-width: 760px) {

        .history-search {
            flex-direction: column;
        }

        .history-search button,
        .clear-search {
            height: 42px;
        }

        .history-info {
            align-items: flex-start;
        }

    }

</style>

@endpush


@section('content')

<div class="history-page">

    {{-- =========================================================
         CABECERA
    ========================================================== --}}

    <div class="history-header">

        <div class="history-title">

            <span class="eyebrow">
                Personal
            </span>

            <h1>
                Papelera de empleados
            </h1>

            <p>
                Empleados eliminados del sistema NANOS.
                Puedes consultar su historial individual
                o restaurarlos.
            </p>

        </div>


        <div class="history-header-actions">

            {{-- VOLVER --}}

            <a
                href="{{ route('admin.empleados.index') }}"
                class="history-btn"
            >
                ← Empleados
            </a>


            {{-- EXCEL GENERAL --}}

            <a
                href="{{ route(
                    'admin.empleados.historial.excel',
                    ['buscar' => $buscar]
                ) }}"
                class="history-btn excel"
            >
                Excel
            </a>


            {{-- PDF GENERAL --}}

            <a
                href="{{ route(
                    'admin.empleados.historial.pdf',
                    ['buscar' => $buscar]
                ) }}"
                target="_blank"
                class="history-btn pdf"
            >
                PDF
            </a>

        </div>

    </div>


    {{-- =========================================================
         MENSAJES
    ========================================================== --}}

    @if(session('success'))

        <div class="history-alert success">

            {{ session('success') }}

        </div>

    @endif


    @if(session('error'))

        <div class="history-alert error">

            {{ session('error') }}

        </div>

    @endif


    {{-- =========================================================
         EXPLICACIÓN
    ========================================================== --}}

    <div class="history-info">

        <div class="history-info-icon">
            i
        </div>

        <div>

            <strong>
                Papelera e historial son diferentes.
            </strong>

            Aquí aparecen los empleados eliminados.

            El botón
            <strong>Historial</strong>
            permite consultar todas las acciones registradas
            para un empleado específico.

        </div>

    </div>


    {{-- =========================================================
         BUSCADOR
    ========================================================== --}}

    <div class="history-search-panel">

        <form
            method="GET"
            action="{{ route(
                'admin.empleados.historial'
            ) }}"
            class="history-search"
        >

            <input
                type="search"
                name="buscar"
                value="{{ $buscar }}"
                placeholder="Buscar por código, nombre, apellido o CI..."
                autocomplete="off"
            >


            <button type="submit">
                Buscar
            </button>


            @if(!empty($buscar))

                <a
                    href="{{ route(
                        'admin.empleados.historial'
                    ) }}"
                    class="clear-search"
                >
                    Limpiar
                </a>

            @endif

        </form>

    </div>


    {{-- =========================================================
         TABLA
    ========================================================== --}}

    <div class="history-panel">

        <div class="history-panel-header">

            <div>

                <h2>
                    Empleados eliminados
                </h2>

                <span>
                    Registros disponibles en la papelera
                </span>

            </div>


            <span class="history-total">

                {{ $empleados->total() }}

                {{
                    $empleados->total() === 1
                        ? 'registro'
                        : 'registros'
                }}

            </span>

        </div>


        <div class="history-responsive">

            <table class="history-table">

                <thead>

                    <tr>

                        <th>
                            Código
                        </th>

                        <th>
                            Empleado
                        </th>

                        <th>
                            CI
                        </th>

                        <th>
                            Salida
                        </th>

                        <th>
                            Eliminado
                        </th>

                        <th>
                            Eliminado por
                        </th>

                        <th>
                            Estado
                        </th>

                        <th>
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody>

                @forelse(
                    $empleados
                    as $empleado
                )

                    @php

                        /*
                         * Usuario que realizó
                         * la eliminación.
                         */

                        $responsable =
                            $empleado->eliminadoPor;


                        /*
                         * Nombre predeterminado.
                         */

                        $nombreResponsable =
                            'Sin registro';


                        /*
                         * IMPORTANTE:
                         *
                         * Usamos empleadoConEliminados
                         * para que el nombre del responsable
                         * siga disponible aunque su empleado
                         * tenga deleted_at.
                         */

                        if ($responsable) {

                            $empleadoResponsable =
                                $responsable
                                    ->empleadoConEliminados;


                            if ($empleadoResponsable) {

                                $nombreResponsable =
                                    trim(
                                        $empleadoResponsable
                                            ->nombres
                                        .
                                        ' '
                                        .
                                        $empleadoResponsable
                                            ->apellidos
                                    );

                            } else {

                                $nombreResponsable =
                                    $responsable
                                        ->username;

                            }

                        }

                    @endphp


                    <tr>

                        {{-- CÓDIGO --}}

                        <td>

                            <span class="history-code">

                                {{
                                    $empleado
                                        ->codigo_empleado
                                }}

                            </span>

                        </td>


                        {{-- EMPLEADO --}}

                        <td>

                            <span class="history-name">

                                {{ $empleado->nombres }}

                                {{ $empleado->apellidos }}

                            </span>

                        </td>


                        {{-- CI --}}

                        <td>

                            <span class="history-muted">

                                {{
                                    $empleado->ci
                                        ?: '—'
                                }}

                            </span>

                        </td>


                        {{-- FECHA SALIDA --}}

                        <td>

                            {{
                                $empleado->fecha_salida
                                    ? $empleado
                                        ->fecha_salida
                                        ->format('d/m/Y')
                                    : '—'
                            }}

                        </td>


                        {{-- FECHA ELIMINACIÓN --}}

                        <td>

                            {{
                                $empleado->deleted_at
                                    ? $empleado
                                        ->deleted_at
                                        ->format(
                                            'd/m/Y H:i'
                                        )
                                    : '—'
                            }}

                        </td>


                        {{-- RESPONSABLE --}}

                        <td>

                            <div class="responsible">

                                <strong>

                                    {{
                                        $nombreResponsable
                                    }}

                                </strong>


                                @if($responsable)

                                    <span>

                                        {{
                                            '@' .
                                            $responsable
                                                ->username
                                        }}

                                    </span>

                                @endif

                            </div>

                        </td>


                        {{-- ESTADO --}}

                        <td>

                            <span class="deleted-badge">
                                Eliminado
                            </span>

                        </td>


                        {{-- =================================================
                             ACCIONES
                        ================================================== --}}

                        <td>

                            <div class="history-actions">


                                {{-- VER REGISTRO ELIMINADO --}}

                                <a
                                    href="{{ route(
                                        'admin.empleados.historial.ver',
                                        $empleado->id
                                    ) }}"
                                    class="history-action view"
                                    title="Ver datos del empleado"
                                >
                                    Ver
                                </a>


                                {{-- =========================================
                                     HISTORIAL INDIVIDUAL
                                ========================================== --}}

                                <a
                                    href="{{ route(
                                        'admin.empleados.historial.individual',
                                        $empleado->id
                                    ) }}"
                                    class="history-action individual"
                                    title="Ver historial individual del empleado"
                                >
                                    Historial
                                </a>


                                {{-- RESTAURAR --}}

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.empleados.restaurar',
                                        $empleado->id
                                    ) }}"
                                    style="margin:0;"
                                    onsubmit="
                                        return confirm(
                                            '¿Deseas restaurar al empleado {{ addslashes($empleado->nombres . ' ' . $empleado->apellidos) }}?'
                                        );
                                    "
                                >

                                    @csrf

                                    @method('PATCH')


                                    <button
                                        type="submit"
                                        class="history-action restore"
                                    >
                                        Restaurar
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="history-empty"
                        >

                            <strong>
                                Papelera vacía
                            </strong>

                            No existen empleados
                            eliminados actualmente.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        {{-- =====================================================
             PAGINACIÓN
        ====================================================== --}}

        @if($empleados->hasPages())

            <div class="history-pagination">

                {{ $empleados->links() }}

            </div>

        @endif

    </div>

</div>

@endsection