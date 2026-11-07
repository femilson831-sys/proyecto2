@extends('admin.layouts.app')

@section('title', 'Detalle del empleado')

@push('styles')
<style>
    /* =========================================================
       DETALLE DE EMPLEADO
    ========================================================= */

    .employee-profile {
        display: grid;
        grid-template-columns: 320px minmax(0, 1fr);
        gap: 22px;
        align-items: start;
    }

    /* =========================================================
       TARJETA PRINCIPAL
    ========================================================= */

    .employee-profile-card {
        position: sticky;
        top: 96px;
        background:
            linear-gradient(
                145deg,
                rgba(227, 27, 35, .10),
                transparent 42%
            ),
            var(--panel, #15171a);
        border: 1px solid rgba(255,255,255,.07);
        border-radius: 22px;
        padding: 28px 24px;
        text-align: center;
        overflow: hidden;
    }

    .employee-profile-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--red, #e31b23);
    }

    .employee-photo {
        width: 112px;
        height: 112px;
        margin: 5px auto 18px;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background:
            linear-gradient(
                145deg,
                var(--red, #e31b23),
                #8d0c12
            );
        border: 5px solid rgba(255,255,255,.06);
        box-shadow:
            0 15px 35px rgba(0,0,0,.28),
            0 0 0 1px rgba(227,27,35,.18);
        color: #fff;
        font-size: 36px;
        font-weight: 800;
        letter-spacing: 1px;
    }

    .employee-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .employee-profile-name {
        margin: 0;
        color: #fff;
        font-size: 22px;
        font-weight: 800;
        line-height: 1.25;
    }

    .employee-profile-code {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-top: 10px;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(255,255,255,.07);
        color: #aeb3ba;
        font-size: 12px;
        font-weight: 700;
    }

    .employee-profile-code svg {
        width: 15px;
        height: 15px;
        color: var(--red, #e31b23);
    }

    .employee-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        margin-top: 16px;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .employee-status::before {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    .employee-status.active {
        color: #4ade80;
        background: rgba(34,197,94,.10);
        border: 1px solid rgba(34,197,94,.20);
    }

    .employee-status.inactive {
        color: #f87171;
        background: rgba(239,68,68,.10);
        border: 1px solid rgba(239,68,68,.20);
    }

    .employee-profile-divider {
        height: 1px;
        background: rgba(255,255,255,.07);
        margin: 24px 0;
    }

    .profile-summary {
        display: grid;
        gap: 15px;
        text-align: left;
    }

    .profile-summary-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .profile-summary-icon {
        flex: 0 0 38px;
        width: 38px;
        height: 38px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(227,27,35,.09);
        color: var(--red-light, #ff4148);
    }

    .profile-summary-icon svg {
        width: 18px;
        height: 18px;
    }

    .profile-summary-text {
        min-width: 0;
    }

    .profile-summary-text span {
        display: block;
        margin-bottom: 2px;
        color: #747a82;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .profile-summary-text strong {
        display: block;
        color: #e9eaec;
        font-size: 13px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    /* =========================================================
       INFORMACIÓN
    ========================================================= */

    .employee-information {
        display: grid;
        gap: 20px;
    }

    .employee-section {
        background: var(--panel, #15171a);
        border: 1px solid rgba(255,255,255,.07);
        border-radius: 20px;
        overflow: hidden;
    }

    .employee-section-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px 22px;
        border-bottom: 1px solid rgba(255,255,255,.06);
    }

    .employee-section-icon {
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: rgba(227,27,35,.09);
        color: var(--red-light, #ff4148);
    }

    .employee-section-icon svg {
        width: 20px;
        height: 20px;
    }

    .employee-section-heading {
        min-width: 0;
    }

    .employee-section-heading h2 {
        margin: 0 0 4px;
        color: #f4f4f5;
        font-size: 15px;
        font-weight: 800;
    }

    .employee-section-heading p {
        margin: 0;
        color: #747a82;
        font-size: 12px;
    }

    .employee-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        padding: 5px 22px 20px;
    }

    .employee-detail {
        min-width: 0;
        padding: 17px 8px;
        border-bottom: 1px solid rgba(255,255,255,.05);
    }

    .employee-detail:nth-last-child(-n + 2) {
        border-bottom: none;
    }

    .employee-detail span {
        display: block;
        margin-bottom: 7px;
        color: #737981;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .employee-detail strong {
        display: block;
        color: #e7e8ea;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .employee-detail.full {
        grid-column: 1 / -1;
    }

    .employee-empty {
        color: #696f77 !important;
        font-weight: 500 !important;
        font-style: italic;
    }

    /* =========================================================
       ACCIONES
    ========================================================= */

    .employee-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .employee-action-btn {
        min-height: 43px;
        padding: 0 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 11px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 800;
        transition:
            transform .18s ease,
            background .18s ease,
            border-color .18s ease;
    }

    .employee-action-btn svg {
        width: 17px;
        height: 17px;
    }

    .employee-action-btn:hover {
        transform: translateY(-1px);
    }

    .employee-action-btn.back {
        color: #c4c7cc;
        background: rgba(255,255,255,.035);
        border: 1px solid rgba(255,255,255,.08);
    }

    .employee-action-btn.back:hover {
        background: rgba(255,255,255,.07);
        border-color: rgba(255,255,255,.13);
    }

    .employee-action-btn.edit {
        color: #fff;
        background: var(--red, #e31b23);
        border: 1px solid var(--red, #e31b23);
        box-shadow: 0 8px 20px rgba(227,27,35,.17);
    }

    .employee-action-btn.edit:hover {
        background: var(--red-dark, #b90f16);
        border-color: var(--red-dark, #b90f16);
    }

    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 1050px) {
        .employee-profile {
            grid-template-columns: 280px minmax(0, 1fr);
        }
    }

    @media (max-width: 850px) {
        .employee-profile {
            grid-template-columns: 1fr;
        }

        .employee-profile-card {
            position: static;
        }

        .profile-summary {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 650px) {
        .employee-header-actions {
            width: 100%;
        }

        .employee-action-btn {
            flex: 1;
        }

        .employee-detail-grid {
            grid-template-columns: 1fr;
            padding-left: 16px;
            padding-right: 16px;
        }

        .employee-detail:nth-last-child(-n + 2) {
            border-bottom:
                1px solid rgba(255,255,255,.05);
        }

        .employee-detail:last-child {
            border-bottom: none;
        }

        .employee-detail.full {
            grid-column: auto;
        }

        .profile-summary {
            grid-template-columns: 1fr;
        }

        .employee-section-header {
            padding-left: 18px;
            padding-right: 18px;
        }
    }
</style>
@endpush


@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | DATOS PARA LA VISTA
    |--------------------------------------------------------------------------
    */

    $nombreCompleto = trim(
        $empleado->nombres . ' ' .
        $empleado->apellidos
    );

    $iniciales =
        strtoupper(
            substr($empleado->nombres ?? 'E', 0, 1) .
            substr($empleado->apellidos ?? '', 0, 1)
        );

@endphp


{{-- =========================================================
     ENCABEZADO
========================================================= --}}

<div class="page-header">

    <div class="page-title">

        <span class="eyebrow">
            Personal / Empleados
        </span>

        <h1>
            Detalle del empleado
        </h1>

        <p>
            Información personal, laboral y de contacto registrada
            en el sistema.
        </p>

    </div>


    <div class="employee-header-actions">

        <a
            href="{{ route('admin.empleados.index') }}"
            class="employee-action-btn back"
        >
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path d="m15 18-6-6 6-6"/>
            </svg>

            Volver
        </a>


        <a
            href="{{ route('admin.empleados.edit', $empleado) }}"
            class="employee-action-btn edit"
        >
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    d="M12 20h9"
                />

                <path
                    d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"
                />
            </svg>

            Editar empleado
        </a>

    </div>

</div>


{{-- =========================================================
     PERFIL
========================================================= --}}

<div class="employee-profile">


    {{-- =====================================================
         COLUMNA IZQUIERDA
    ====================================================== --}}

    <div class="employee-profile-card">


        {{-- FOTO / INICIALES --}}

        <div class="employee-photo">

            @if($empleado->foto)

                <img
                    src="{{ asset(ltrim($empleado->foto, '/')) }}"
                    alt="{{ $nombreCompleto }}"
                >

            @else

                {{ $iniciales }}

            @endif

        </div>


        <h2 class="employee-profile-name">
            {{ $nombreCompleto }}
        </h2>


        <div class="employee-profile-code">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <rect
                    x="3"
                    y="5"
                    width="18"
                    height="14"
                    rx="2"
                />

                <path d="M7 9h4"/>
                <path d="M7 13h7"/>
            </svg>

            {{ $empleado->codigo_empleado }}

        </div>


        <div
            class="employee-status
            {{ $empleado->estado === 'activo'
                ? 'active'
                : 'inactive' }}"
        >

            {{ ucfirst($empleado->estado) }}

        </div>


        <div class="employee-profile-divider"></div>


        {{-- RESUMEN --}}

        <div class="profile-summary">


            {{-- CI --}}

            <div class="profile-summary-item">

                <div class="profile-summary-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <rect
                            x="3"
                            y="5"
                            width="18"
                            height="14"
                            rx="2"
                        />

                        <circle
                            cx="8"
                            cy="11"
                            r="2"
                        />

                        <path d="M13 10h5"/>
                        <path d="M13 14h4"/>
                    </svg>

                </div>

                <div class="profile-summary-text">

                    <span>
                        Cédula de identidad
                    </span>

                    <strong>
                        {{ $empleado->ci ?: 'No registrado' }}
                    </strong>

                </div>

            </div>


            {{-- CELULAR --}}

            <div class="profile-summary-item">

                <div class="profile-summary-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <rect
                            x="6"
                            y="2"
                            width="12"
                            height="20"
                            rx="2"
                        />

                        <path d="M10 18h4"/>
                    </svg>

                </div>

                <div class="profile-summary-text">

                    <span>
                        Celular
                    </span>

                    <strong>
                        {{ $empleado->celular ?: 'No registrado' }}
                    </strong>

                </div>

            </div>


            {{-- CORREO --}}

            <div class="profile-summary-item">

                <div class="profile-summary-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <rect
                            x="3"
                            y="5"
                            width="18"
                            height="14"
                            rx="2"
                        />

                        <path d="m3 7 9 6 9-6"/>
                    </svg>

                </div>

                <div class="profile-summary-text">

                    <span>
                        Correo
                    </span>

                    <strong>
                        {{ $empleado->correo ?: 'No registrado' }}
                    </strong>

                </div>

            </div>


            {{-- FECHA INGRESO --}}

            <div class="profile-summary-item">

                <div class="profile-summary-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <rect
                            x="3"
                            y="5"
                            width="18"
                            height="16"
                            rx="2"
                        />

                        <path d="M16 3v4"/>
                        <path d="M8 3v4"/>
                        <path d="M3 11h18"/>
                    </svg>

                </div>

                <div class="profile-summary-text">

                    <span>
                        Ingreso
                    </span>

                    <strong>
                        {{
                            $empleado->fecha_ingreso
                                ? $empleado->fecha_ingreso->format('d/m/Y')
                                : 'No registrado'
                        }}
                    </strong>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         COLUMNA DERECHA
    ====================================================== --}}

    <div class="employee-information">


        {{-- =================================================
             INFORMACIÓN PERSONAL
        ================================================== --}}

        <section class="employee-section">

            <div class="employee-section-header">

                <div class="employee-section-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle
                            cx="12"
                            cy="8"
                            r="4"
                        />

                        <path
                            d="M4 21a8 8 0 0 1 16 0"
                        />
                    </svg>

                </div>

                <div class="employee-section-heading">

                    <h2>
                        Información personal
                    </h2>

                    <p>
                        Datos generales de identificación del empleado.
                    </p>

                </div>

            </div>


            <div class="employee-detail-grid">

                <div class="employee-detail">

                    <span>
                        Nombres
                    </span>

                    <strong>
                        {{ $empleado->nombres }}
                    </strong>

                </div>


                <div class="employee-detail">

                    <span>
                        Apellidos
                    </span>

                    <strong>
                        {{ $empleado->apellidos }}
                    </strong>

                </div>


                <div class="employee-detail">

                    <span>
                        Cédula de identidad
                    </span>

                    <strong
                        class="{{ !$empleado->ci ? 'employee-empty' : '' }}"
                    >
                        {{ $empleado->ci ?: 'No registrado' }}
                    </strong>

                </div>


                <div class="employee-detail">

                    <span>
                        Género
                    </span>

                    <strong
                        class="{{ !$empleado->genero ? 'employee-empty' : '' }}"
                    >
                        {{ $empleado->genero ?: 'No registrado' }}
                    </strong>

                </div>


                <div class="employee-detail">

                    <span>
                        Fecha de nacimiento
                    </span>

                    <strong
                        class="{{ !$empleado->fecha_nacimiento ? 'employee-empty' : '' }}"
                    >
                        {{
                            $empleado->fecha_nacimiento
                                ? $empleado->fecha_nacimiento->format('d/m/Y')
                                : 'No registrada'
                        }}
                    </strong>

                </div>


                <div class="employee-detail">

                    <span>
                        Ciudad
                    </span>

                    <strong
                        class="{{ !$empleado->ciudad ? 'employee-empty' : '' }}"
                    >
                        {{ $empleado->ciudad ?: 'No registrada' }}
                    </strong>

                </div>

            </div>

        </section>


        {{-- =================================================
             CONTACTO
        ================================================== --}}

        <section class="employee-section">

            <div class="employee-section-header">

                <div class="employee-section-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            d="M22 16.92v3a2 2 0 0 1-2.18 2
                            19.79 19.79 0 0 1-8.63-3.07
                            19.5 19.5 0 0 1-6-6
                            19.79 19.79 0 0 1-3.07-8.67
                            A2 2 0 0 1 4.11 2h3
                            a2 2 0 0 1 2 1.72
                            12.84 12.84 0 0 0 .7 2.81
                            2 2 0 0 1-.45 2.11L8.09 9.91
                            a16 16 0 0 0 6 6l1.27-1.27
                            a2 2 0 0 1 2.11-.45
                            12.84 12.84 0 0 0 2.81.7
                            A2 2 0 0 1 22 16.92Z"
                        />
                    </svg>

                </div>

                <div class="employee-section-heading">

                    <h2>
                        Información de contacto
                    </h2>

                    <p>
                        Datos para comunicación y ubicación.
                    </p>

                </div>

            </div>


            <div class="employee-detail-grid">

                <div class="employee-detail">

                    <span>
                        Celular
                    </span>

                    <strong
                        class="{{ !$empleado->celular ? 'employee-empty' : '' }}"
                    >
                        {{ $empleado->celular ?: 'No registrado' }}
                    </strong>

                </div>


                <div class="employee-detail">

                    <span>
                        Teléfono
                    </span>

                    <strong
                        class="{{ !$empleado->telefono ? 'employee-empty' : '' }}"
                    >
                        {{ $empleado->telefono ?: 'No registrado' }}
                    </strong>

                </div>


                <div class="employee-detail">

                    <span>
                        Correo electrónico
                    </span>

                    <strong
                        class="{{ !$empleado->correo ? 'employee-empty' : '' }}"
                    >
                        {{ $empleado->correo ?: 'No registrado' }}
                    </strong>

                </div>


                <div class="employee-detail">

                    <span>
                        Ciudad
                    </span>

                    <strong
                        class="{{ !$empleado->ciudad ? 'employee-empty' : '' }}"
                    >
                        {{ $empleado->ciudad ?: 'No registrada' }}
                    </strong>

                </div>


                <div class="employee-detail full">

                    <span>
                        Dirección
                    </span>

                    <strong
                        class="{{ !$empleado->direccion ? 'employee-empty' : '' }}"
                    >
                        {{ $empleado->direccion ?: 'No registrada' }}
                    </strong>

                </div>

            </div>

        </section>


        {{-- =================================================
             INFORMACIÓN LABORAL
        ================================================== --}}

        <section class="employee-section">

            <div class="employee-section-header">

                <div class="employee-section-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <rect
                            x="3"
                            y="7"
                            width="18"
                            height="13"
                            rx="2"
                        />

                        <path
                            d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                        />

                        <path d="M3 12h18"/>
                    </svg>

                </div>

                <div class="employee-section-heading">

                    <h2>
                        Información laboral
                    </h2>

                    <p>
                        Datos relacionados con su registro en NANOS.
                    </p>

                </div>

            </div>


            <div class="employee-detail-grid">

                <div class="employee-detail">

                    <span>
                        Código de empleado
                    </span>

                    <strong>
                        {{ $empleado->codigo_empleado }}
                    </strong>

                </div>


                <div class="employee-detail">

                    <span>
                        Estado
                    </span>

                    <strong>
                        {{ ucfirst($empleado->estado) }}
                    </strong>

                </div>


                <div class="employee-detail">

                    <span>
                        Fecha de ingreso
                    </span>

                    <strong
                        class="{{ !$empleado->fecha_ingreso ? 'employee-empty' : '' }}"
                    >
                        {{
                            $empleado->fecha_ingreso
                                ? $empleado->fecha_ingreso->format('d/m/Y')
                                : 'No registrada'
                        }}
                    </strong>

                </div>


                <div class="employee-detail">

                    <span>
                        Fecha de salida
                    </span>

                    <strong
                        class="{{ !$empleado->fecha_salida ? 'employee-empty' : '' }}"
                    >
                        {{
                            $empleado->fecha_salida
                                ? $empleado->fecha_salida->format('d/m/Y')
                                : 'No registrada'
                        }}
                    </strong>

                </div>


                <div class="employee-detail">

                    <span>
                        Tipo de contrato
                    </span>

                    <strong
                        class="{{ !$empleado->tipo_contrato ? 'employee-empty' : '' }}"
                    >
                        {{
                            $empleado->tipo_contrato
                                ?: 'No registrado'
                        }}
                    </strong>

                </div>


                <div class="employee-detail">

                    <span>
                        Área
                    </span>

                    <strong
                        class="{{ !$empleado->area_id ? 'employee-empty' : '' }}"
                    >
                        {{
                            $empleado->area_id
                                ?: 'No asignada'
                        }}
                    </strong>

                </div>


                <div class="employee-detail">

                    <span>
                        Cargo
                    </span>

                    <strong
                        class="{{ !$empleado->cargo_id ? 'employee-empty' : '' }}"
                    >
                        {{
                            $empleado->cargo_id
                                ?: 'No asignado'
                        }}
                    </strong>

                </div>

            </div>

        </section>


        {{-- =================================================
             OBSERVACIONES
        ================================================== --}}

        <section class="employee-section">

            <div class="employee-section-header">

                <div class="employee-section-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"
                        />
                    </svg>

                </div>

                <div class="employee-section-heading">

                    <h2>
                        Observaciones
                    </h2>

                    <p>
                        Información adicional registrada.
                    </p>

                </div>

            </div>


            <div class="employee-detail-grid">

                <div class="employee-detail full">

                    <strong
                        class="{{ !$empleado->observaciones ? 'employee-empty' : '' }}"
                    >
                        {{
                            $empleado->observaciones
                                ?: 'No existen observaciones registradas para este empleado.'
                        }}
                    </strong>

                </div>

            </div>

        </section>

    </div>

</div>

@endsection