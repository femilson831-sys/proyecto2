@extends('admin.layouts.app')

@section('title', 'Empleados')


@push('styles')
<style>

    /* =========================================================
       CABECERA EMPLEADOS
    ========================================================= */

    .employees-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }


    /* =========================================================
       MENSAJE DE ÉXITO
    ========================================================= */

    .employee-alert {
        position: relative;
        display: flex;
        align-items: center;
        gap: 13px;

        margin-bottom: 20px;
        padding: 15px 18px;

        background: rgba(34, 197, 94, .08);
        border: 1px solid rgba(34, 197, 94, .20);
        border-radius: 14px;

        color: #d8ffe5;

        overflow: hidden;

        animation: alertIn .30s ease;
    }

    .employee-alert::before {
        content: "";

        position: absolute;

        left: 0;
        top: 0;
        bottom: 0;

        width: 4px;

        background: #22c55e;
    }

    .employee-alert-icon {
        width: 36px;
        height: 36px;

        flex: 0 0 36px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 50%;

        background: rgba(34, 197, 94, .13);

        color: #4ade80;
    }

    .employee-alert-icon svg {
        width: 18px;
        height: 18px;
    }

    .employee-alert-content {
        flex: 1;
        min-width: 0;
    }

    .employee-alert-content strong {
        display: block;

        margin-bottom: 2px;

        color: #f4fff7;

        font-size: 13px;
    }

    .employee-alert-content span {
        color: #8ba995;
        font-size: 12px;
    }

    .employee-alert-close {
        width: 30px;
        height: 30px;

        border: none;
        border-radius: 8px;

        background: transparent;

        color: #75907e;

        display: flex;
        align-items: center;
        justify-content: center;

        cursor: pointer;
    }

    .employee-alert-close:hover {
        color: #fff;
        background: rgba(255,255,255,.05);
    }

    .employee-alert-close svg {
        width: 16px;
        height: 16px;
    }

    @keyframes alertIn {

        from {
            opacity: 0;
            transform: translateY(-8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }

    }


    /* =========================================================
       BUSCADOR
    ========================================================= */

    .employee-search-wrapper {
        position: relative;

        flex: 1;

        max-width: 520px;
    }

    .employee-search-wrapper svg {
        position: absolute;

        left: 15px;
        top: 50%;

        transform: translateY(-50%);

        width: 18px;
        height: 18px;

        color: #6d737b;

        pointer-events: none;
    }

    .employee-search {
        width: 100%;
        height: 45px;

        padding:
            0
            42px
            0
            44px;

        border:
            1px solid
            rgba(255,255,255,.08);

        border-radius: 12px;

        outline: none;

        background: rgba(255,255,255,.035);

        color: #fff;

        font-size: 13px;

        transition:
            border-color .2s ease,
            background .2s ease,
            box-shadow .2s ease;
    }

    .employee-search::placeholder {
        color: #686e76;
    }

    .employee-search:focus {
        border-color: rgba(227,27,35,.55);

        background: rgba(255,255,255,.05);

        box-shadow:
            0 0 0 3px
            rgba(227,27,35,.08);
    }

    .search-clear {
        position: absolute;

        right: 10px;
        top: 50%;

        transform: translateY(-50%);

        width: 28px;
        height: 28px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 7px;

        color: #777d85;

        text-decoration: none;
    }

    .search-clear:hover {
        color: #fff;
        background: rgba(255,255,255,.06);
    }


    /* =========================================================
       EMPLEADO
    ========================================================= */

    .employee-avatar-photo {
        width: 42px;
        height: 42px;

        flex: 0 0 42px;

        overflow: hidden;

        border-radius: 12px;

        display: flex;
        align-items: center;
        justify-content: center;

        background:
            linear-gradient(
                145deg,
                rgba(227,27,35,.95),
                rgba(145,12,18,.95)
            );

        color: #fff;

        font-size: 14px;
        font-weight: 800;

        border:
            1px solid
            rgba(255,255,255,.06);
    }

    .employee-avatar-photo img {
        width: 100%;
        height: 100%;

        object-fit: cover;
    }


    /* =========================================================
       INTERRUPTOR DE ESTADO
    ========================================================= */

    .status-control {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .status-switch-form {
        margin: 0;
    }

    .status-switch {
        position: relative;

        width: 42px;
        height: 23px;

        padding: 0;

        border: none;
        border-radius: 999px;

        cursor: pointer;

        transition:
            background .25s ease,
            box-shadow .25s ease;
    }

    .status-switch.active {
        background: #22c55e;

        box-shadow:
            0 0 0 3px
            rgba(34,197,94,.08);
    }

    .status-switch.inactive {
        background: #40444a;
    }

    .status-switch::after {
        content: "";

        position: absolute;

        top: 3px;

        width: 17px;
        height: 17px;

        border-radius: 50%;

        background: #fff;

        box-shadow:
            0 2px 5px
            rgba(0,0,0,.25);

        transition:
            left .25s ease;
    }

    .status-switch.active::after {
        left: 22px;
    }

    .status-switch.inactive::after {
        left: 3px;
    }

    .status-text {
        min-width: 57px;

        font-size: 11px;
        font-weight: 800;

        text-transform: uppercase;

        letter-spacing: .4px;
    }

    .status-text.active {
        color: #4ade80;
    }

    .status-text.inactive {
        color: #8a9098;
    }


    /* =========================================================
       BOTÓN HISTORIAL INDIVIDUAL
    ========================================================= */

    .action-btn.history {
        color: #60a5fa;
        background: rgba(59, 130, 246, .08);
        border-color: rgba(59, 130, 246, .18);
    }

    .action-btn.history:hover {
        color: #fff;
        background: #2563eb;
        border-color: #2563eb;
    }

    .action-btn.history svg {
        width: 17px;
        height: 17px;
    }


    /* =========================================================
       MENSAJE DE ERROR
    ========================================================= */

    .employee-alert.error {
        background: rgba(239, 68, 68, .08);
        border-color: rgba(239, 68, 68, .20);
        color: #fee2e2;
    }

    .employee-alert.error::before {
        background: #ef4444;
    }

    .employee-alert.error .employee-alert-icon {
        background: rgba(239, 68, 68, .13);
        color: #f87171;
    }

    .employee-alert.error .employee-alert-content strong {
        color: #fff;
    }

    .employee-alert.error .employee-alert-content span {
        color: #fca5a5;
    }


    /* =========================================================
       USUARIO ACTUAL
    ========================================================= */

    .current-user-badge {
        display: inline-flex;
        align-items: center;

        margin-top: 5px;
        padding: 3px 7px;

        border-radius: 999px;

        background: rgba(227, 27, 35, .10);
        border: 1px solid rgba(227, 27, 35, .18);

        color: #ff7479;

        font-size: 9px;
        font-weight: 800;

        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .status-switch.disabled {
        cursor: not-allowed;
        opacity: .45;
    }


    /* =========================================================
       BOTÓN ELIMINAR EMPLEADO
    ========================================================= */

    .btn-delete-employee {
        min-height: 34px;
        padding: 0 10px;

        border: 1px solid rgba(239,68,68,.20);
        border-radius: 8px;

        background: rgba(239,68,68,.07);

        color: #f87171;

        font-size: 10px;
        font-weight: 800;

        cursor: pointer;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        transition:
            background .2s ease,
            border-color .2s ease,
            color .2s ease;
    }

    .btn-delete-employee:hover {
        background: #ef4444;
        border-color: #ef4444;
        color: white;
    }


    /* =========================================================
       MODAL
    ========================================================= */

    .employee-modal {
        position: fixed;

        inset: 0;

        z-index: 99999;

        display: none;
        align-items: center;
        justify-content: center;

        padding: 20px;

        background: rgba(0,0,0,.70);

        backdrop-filter: blur(6px);
    }

    .employee-modal.show {
        display: flex;
    }

    .employee-modal-card {
        width: 100%;
        max-width: 430px;

        background: #17191c;

        border:
            1px solid
            rgba(255,255,255,.08);

        border-radius: 20px;

        padding: 26px;

        box-shadow:
            0 25px 70px
            rgba(0,0,0,.45);

        animation: modalIn .20s ease;
    }

    @keyframes modalIn {

        from {
            opacity: 0;
            transform:
                scale(.96)
                translateY(10px);
        }

        to {
            opacity: 1;
            transform:
                scale(1)
                translateY(0);
        }

    }

    .modal-icon {
        width: 54px;
        height: 54px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-bottom: 18px;

        border-radius: 15px;

        background: rgba(227,27,35,.10);

        color: #ff4148;
    }

    .modal-icon.activate {
        background: rgba(34,197,94,.10);
        color: #4ade80;
    }

    .modal-icon svg {
        width: 25px;
        height: 25px;
    }

    .employee-modal-card h3 {
        margin:
            0
            0
            8px;

        color: #fff;

        font-size: 19px;
        font-weight: 800;
    }

    .employee-modal-card p {
        margin: 0;

        color: #8d939b;

        font-size: 13px;
        line-height: 1.65;
    }

    .modal-employee-name {
        color: #fff;
        font-weight: 700;
    }

    .employee-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;

        margin-top: 24px;
    }

    .modal-cancel,
    .modal-confirm {
        min-height: 41px;

        padding:
            0
            16px;

        border-radius: 10px;

        font-size: 12px;
        font-weight: 800;

        cursor: pointer;
    }

    .modal-cancel {
        border:
            1px solid
            rgba(255,255,255,.09);

        background:
            rgba(255,255,255,.04);

        color: #c3c7cc;
    }

    .modal-cancel:hover {
        background:
            rgba(255,255,255,.07);
    }

    .modal-confirm {
        border: none;

        background: #e31b23;

        color: #fff;
    }

    .modal-confirm.activate {
        background: #16a34a;
    }

    .modal-confirm:hover {
        filter: brightness(1.08);
    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 700px) {

        .crud-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .employee-search-wrapper {
            max-width: none;
        }

        .crud-toolbar .btn-primary,
        .crud-toolbar .btn-secondary {
            width: 100%;
            justify-content: center;
        }

        .employee-modal-actions {
            flex-direction: column-reverse;
        }

        .modal-cancel,
        .modal-confirm {
            width: 100%;
        }

    }

</style>
@endpush


@section('content')


{{-- =========================================================
     CABECERA
========================================================= --}}

<div class="page-header">

    <div class="page-title">

        <span class="eyebrow">
            Personal
        </span>

        <h1>
            Gestión de empleados
        </h1>

        <p>
            Administra el personal registrado
            en el Minisúper NANOS.
        </p>

    </div>

</div>


{{-- =========================================================
     MENSAJE DE ÉXITO
========================================================= --}}

@if(session('success'))

    <div
        class="employee-alert"
        id="successAlert"
    >

        <div class="employee-alert-icon">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.2"
            >
                <path d="m5 12 4 4L19 6"/>
            </svg>

        </div>


        <div class="employee-alert-content">

            <strong>
                Operación realizada
            </strong>

            <span>
                {{ session('success') }}
            </span>

        </div>


        <button
            type="button"
            class="employee-alert-close"
            onclick="cerrarAlerta()"
            title="Cerrar"
        >
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path d="M18 6 6 18"/>
                <path d="m6 6 12 12"/>
            </svg>
        </button>

    </div>

@endif


{{-- =========================================================
     MENSAJE DE ERROR
========================================================= --}}

@if(session('error'))

    <div
        class="employee-alert error"
        id="errorAlert"
    >

        <div class="employee-alert-icon">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.2"
            >
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 8v5"/>
                <path d="M12 16h.01"/>
            </svg>

        </div>

        <div class="employee-alert-content">

            <strong>
                No se pudo realizar la operación
            </strong>

            <span>
                {{ session('error') }}
            </span>

        </div>

        <button
            type="button"
            class="employee-alert-close"
            onclick="cerrarAlertaError()"
            title="Cerrar"
        >
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path d="M18 6 6 18"/>
                <path d="m6 6 12 12"/>
            </svg>
        </button>

    </div>

@endif


{{-- =========================================================
     HERRAMIENTAS
========================================================= --}}

<div class="crud-toolbar">

    <form
        method="GET"
        action="{{ route('admin.empleados.index') }}"
        class="employee-search-wrapper"
    >

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
        >
            <circle
                cx="11"
                cy="11"
                r="7"
            />

            <path
                d="m20 20-4-4"
            />
        </svg>


        <input
            type="text"
            name="buscar"
            class="employee-search"
            value="{{ $buscar }}"
            placeholder="Buscar por nombre, CI, código, correo..."
        >


        @if($buscar)

            <a
                href="{{ route('admin.empleados.index') }}"
                class="search-clear"
                title="Limpiar búsqueda"
            >
                ×
            </a>

        @endif

    </form>


    <a
        href="{{ route('admin.empleados.create') }}"
        class="btn-primary"
    >

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >
            <path d="M12 5v14"/>
            <path d="M5 12h14"/>
        </svg>

        Nuevo empleado

    </a>


    <a
        href="{{ route('admin.empleados.historial') }}"
        class="btn-secondary"
    >
        Historial de empleados
    </a>

</div>


{{-- =========================================================
     TABLA
========================================================= --}}

<div class="table-panel">

    @if($empleados->count())

        <div class="table-responsive">

            <table class="admin-table">

                <thead>

                    <tr>
                        <th>Empleado</th>
                        <th>Código</th>
                        <th>CI</th>
                        <th>Celular</th>
                        <th>Ingreso</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>

                </thead>


                <tbody>

                    @foreach($empleados as $empleado)

                        @php

                            $nombreCompleto =
                                trim(
                                    $empleado->nombres .
                                    ' ' .
                                    $empleado->apellidos
                                );

                            $iniciales =
                                strtoupper(
                                    substr(
                                        $empleado->nombres ?? 'E',
                                        0,
                                        1
                                    ) .
                                    substr(
                                        $empleado->apellidos ?? '',
                                        0,
                                        1
                                    )
                                );

                            $estaActivo =
                                $empleado->estado === 'activo';

                            $esEmpleadoActual =
                                auth()->user()?->empleado_id === $empleado->id;

                        @endphp


                        <tr>

                            {{-- EMPLEADO --}}
                            <td>

                                <div class="employee-cell">

                                    <div class="employee-avatar-photo">

                                        @if($empleado->foto)

                                            <img
                                                src="{{ asset(ltrim($empleado->foto, '/')) }}"
                                                alt="{{ $nombreCompleto }}"
                                            >

                                        @else

                                            {{ $iniciales }}

                                        @endif

                                    </div>


                                    <div class="employee-name">

                                        <strong>
                                            {{ $nombreCompleto }}
                                        </strong>

                                        <span>
                                            {{
                                                $empleado->correo
                                                    ?: 'Sin correo registrado'
                                            }}
                                        </span>

                                        @if($esEmpleadoActual)

                                            <span class="current-user-badge">
                                                Tu usuario
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </td>


                            {{-- CÓDIGO --}}
                            <td>

                                <strong>
                                    {{ $empleado->codigo_empleado }}
                                </strong>

                            </td>


                            {{-- CI --}}
                            <td>

                                {{
                                    $empleado->ci
                                        ?: '—'
                                }}

                            </td>


                            {{-- CELULAR --}}
                            <td>

                                {{
                                    $empleado->celular
                                        ?: '—'
                                }}

                            </td>


                            {{-- INGRESO --}}
                            <td>

                                {{
                                    $empleado->fecha_ingreso
                                        ? $empleado
                                            ->fecha_ingreso
                                            ->format('d/m/Y')
                                        : '—'
                                }}

                            </td>


                            {{-- ESTADO --}}
                            <td>

                                <div class="status-control">

                                    <form
                                        method="POST"
                                        action="{{ route('admin.empleados.estado', $empleado) }}"
                                        class="status-switch-form"
                                        id="estadoForm{{ $empleado->id }}"
                                    >

                                        @csrf
                                        @method('PATCH')


                                        <button
                                            type="button"
                                            class="status-switch
                                                {{ $estaActivo ? 'active' : 'inactive' }}
                                                {{ $esEmpleadoActual ? 'disabled' : '' }}"
                                            @if(!$esEmpleadoActual)
                                                onclick="confirmarEstado(
                                                    {{ $empleado->id }},
                                                    @js($nombreCompleto),
                                                    @js($empleado->estado)
                                                )"
                                            @endif
                                            title="{{
                                                $esEmpleadoActual
                                                    ? 'No puedes cambiar el estado de tu propio usuario'
                                                    : (
                                                        $estaActivo
                                                            ? 'Desactivar empleado'
                                                            : 'Activar empleado'
                                                    )
                                            }}"
                                            aria-label="{{
                                                $estaActivo
                                                    ? 'Desactivar empleado'
                                                    : 'Activar empleado'
                                            }}"
                                        >
                                        </button>

                                    </form>


                                    <span
                                        class="status-text {{ $estaActivo ? 'active' : 'inactive' }}"
                                    >
                                        {{
                                            $estaActivo
                                                ? 'Activo'
                                                : 'Inactivo'
                                        }}
                                    </span>

                                </div>

                            </td>


                            {{-- ACCIONES --}}
                            <td>

                                <div class="actions">

                                    {{-- ============================================
                                         VER
                                    ============================================= --}}
                                    <a
                                        href="{{ route(
                                            'admin.empleados.show',
                                            $empleado
                                        ) }}"
                                        class="action-btn"
                                        title="Ver empleado"
                                    >

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <path
                                                d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"
                                            />

                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="3"
                                            />
                                        </svg>

                                    </a>


                                    {{-- ============================================
                                         EDITAR
                                    ============================================= --}}
                                    <a
                                        href="{{ route(
                                            'admin.empleados.edit',
                                            $empleado
                                        ) }}"
                                        class="action-btn edit"
                                        title="Editar empleado"
                                    >

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <path d="M12 20h9"/>

                                            <path
                                                d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"
                                            />
                                        </svg>

                                    </a>


                                    {{-- ============================================
                                         HISTORIAL INDIVIDUAL
                                    ============================================= --}}
                                    <a
                                        href="{{ route(
                                            'admin.empleados.historial.individual',
                                            $empleado->id
                                        ) }}"
                                        class="action-btn history"
                                        title="Ver historial del empleado"
                                    >

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <path
                                                d="M3 12a9 9 0 1 0 3-6.7"
                                            />

                                            <path
                                                d="M3 4v6h6"
                                            />

                                            <path
                                                d="M12 7v5l3 2"
                                            />
                                        </svg>

                                    </a>


                                    {{-- ============================================
                                         ELIMINAR
                                    ============================================= --}}

                                    @if(!$esEmpleadoActual)

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'admin.empleados.destroy',
                                                $empleado
                                            ) }}"
                                            onsubmit="
                                                return confirm(
                                                    '¿Seguro que deseas eliminar a ' +
                                                    @js($nombreCompleto) +
                                                    '? El empleado será enviado a la papelera.'
                                                );
                                            "
                                            style="display:inline-block;"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn-delete-employee"
                                                title="Eliminar empleado"
                                            >
                                                Eliminar
                                            </button>

                                        </form>

                                    @else

                                        <button
                                            type="button"
                                            class="btn-delete-employee"
                                            disabled
                                            title="No puedes eliminar tu propio registro"
                                            style="
                                                opacity:.35;
                                                cursor:not-allowed;
                                            "
                                        >
                                            Eliminar
                                        </button>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        {{-- PAGINACIÓN --}}
        <div style="padding: 16px 20px;">

            {{ $empleados->links() }}

        </div>

    @else

        <div class="empty-state">

            <h3>
                No se encontraron empleados
            </h3>

            <p>
                Registra un empleado o modifica
                los criterios de búsqueda.
            </p>

        </div>

    @endif

</div>


{{-- =========================================================
     MODAL CONFIRMACIÓN
========================================================= --}}

<div
    class="employee-modal"
    id="estadoModal"
>

    <div class="employee-modal-card">

        <div
            class="modal-icon"
            id="estadoModalIcon"
        >

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    d="M18 8A6 6 0 1 1 6 8"
                />

                <path
                    d="M12 2v10"
                />
            </svg>

        </div>


        <h3 id="estadoModalTitulo">
            Cambiar estado
        </h3>


        <p id="estadoModalTexto">
        </p>


        <div class="employee-modal-actions">

            <button
                type="button"
                class="modal-cancel"
                onclick="cerrarModalEstado()"
            >
                Cancelar
            </button>


            <button
                type="button"
                class="modal-confirm"
                id="estadoModalConfirmar"
                onclick="ejecutarCambioEstado()"
            >
                Confirmar
            </button>

        </div>

    </div>

</div>

@endsection


@push('scripts')
<script>

    let formularioEstadoActual = null;


    /*
    |--------------------------------------------------------------------------
    | ABRIR CONFIRMACIÓN
    |--------------------------------------------------------------------------
    */

    function confirmarEstado(
        empleadoId,
        empleadoNombre,
        estadoActual
    ) {

        formularioEstadoActual =
            document.getElementById(
                'estadoForm' + empleadoId
            );


        const modal =
            document.getElementById(
                'estadoModal'
            );

        const titulo =
            document.getElementById(
                'estadoModalTitulo'
            );

        const texto =
            document.getElementById(
                'estadoModalTexto'
            );

        const boton =
            document.getElementById(
                'estadoModalConfirmar'
            );

        const icono =
            document.getElementById(
                'estadoModalIcon'
            );


        const estaActivo =
            estadoActual === 'activo';


        if (estaActivo) {

            titulo.textContent =
                'Desactivar empleado';

            texto.innerHTML =
                '¿Estás seguro de que deseas desactivar a ' +
                '<span class="modal-employee-name">' +
                escaparHtml(empleadoNombre) +
                '</span>? ' +
                'El empleado quedará inactivo y, si tiene acceso al sistema, su usuario también será desactivado.';

            boton.textContent =
                'Sí, desactivar';

            boton.classList.remove(
                'activate'
            );

            icono.classList.remove(
                'activate'
            );

        } else {

            titulo.textContent =
                'Activar empleado';

            texto.innerHTML =
                '¿Deseas activar nuevamente a ' +
                '<span class="modal-employee-name">' +
                escaparHtml(empleadoNombre) +
                '</span>? ' +
                'El empleado volverá a estar activo en el sistema.';

            boton.textContent =
                'Sí, activar';

            boton.classList.add(
                'activate'
            );

            icono.classList.add(
                'activate'
            );

        }


        modal.classList.add('show');

        document.body.style.overflow =
            'hidden';

    }


    /*
    |--------------------------------------------------------------------------
    | CERRAR MODAL
    |--------------------------------------------------------------------------
    */

    function cerrarModalEstado() {

        const modal =
            document.getElementById(
                'estadoModal'
            );

        modal.classList.remove('show');

        document.body.style.overflow = '';

        formularioEstadoActual = null;

    }


    /*
    |--------------------------------------------------------------------------
    | EJECUTAR
    |--------------------------------------------------------------------------
    */

    function ejecutarCambioEstado() {

        if (!formularioEstadoActual) {
            return;
        }


        const boton =
            document.getElementById(
                'estadoModalConfirmar'
            );


        boton.disabled = true;

        boton.textContent =
            'Procesando...';


        formularioEstadoActual.submit();

    }


    /*
    |--------------------------------------------------------------------------
    | CERRAR ALERTA
    |--------------------------------------------------------------------------
    */

    function cerrarAlerta() {

        const alerta =
            document.getElementById(
                'successAlert'
            );

        if (alerta) {
            alerta.remove();
        }

    }


    /*
    |--------------------------------------------------------------------------
    | CERRAR ALERTA DE ERROR
    |--------------------------------------------------------------------------
    */

    function cerrarAlertaError() {

        const alerta =
            document.getElementById(
                'errorAlert'
            );

        if (alerta) {
            alerta.remove();
        }

    }


    /*
    |--------------------------------------------------------------------------
    | ESCAPAR HTML
    |--------------------------------------------------------------------------
    */

    function escaparHtml(texto) {

        const elemento =
            document.createElement('div');

        elemento.textContent =
            texto ?? '';

        return elemento.innerHTML;

    }


    /*
    |--------------------------------------------------------------------------
    | EVENTOS
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function(event) {

            if (event.key === 'Escape') {
                cerrarModalEstado();
            }

        }
    );


    document.getElementById('estadoModal')
        ?.addEventListener(
            'click',
            function(event) {

                if (event.target === this) {
                    cerrarModalEstado();
                }

            }
        );


    /*
    |--------------------------------------------------------------------------
    | OCULTAR MENSAJES AUTOMÁTICAMENTE
    |--------------------------------------------------------------------------
    */

    setTimeout(
        function() {

            const alertas = [
                document.getElementById(
                    'successAlert'
                ),
                document.getElementById(
                    'errorAlert'
                )
            ];

            alertas.forEach(
                function(alerta) {

                    if (!alerta) {
                        return;
                    }

                    alerta.style.opacity = '0';

                    alerta.style.transform =
                        'translateY(-6px)';

                    alerta.style.transition =
                        'all .25s ease';

                    setTimeout(
                        function() {
                            alerta.remove();
                        },
                        250
                    );
                }
            );

        },
        5000
    );

</script>
@endpush