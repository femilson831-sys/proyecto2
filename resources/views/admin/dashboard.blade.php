@extends('admin.layouts.app')


@section(
    'title',
    'Dashboard'
)


@section('content')

@php

    $usuario =
        auth()->user();

    $empleado =
        $usuario?->empleado;

    $nombre =
        $empleado
            ? $empleado->nombres
            : $usuario?->username;

@endphp


<div class="page-header">

    <div class="page-title">

        <span class="eyebrow">
            Administración
        </span>

        <h1>
            Bienvenido, {{ $nombre ?? 'Administrador' }}
        </h1>

        <p>
            Resumen general de las operaciones
            del Minisúper NANOS.
        </p>

    </div>


    <div class="date-box">

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
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
            <path d="M3 10h18"/>
        </svg>

        {{ now()->format('d/m/Y') }}

    </div>

</div>


{{-- ============================
     TARJETAS
============================ --}}

<div class="stats-grid">

    <div class="stat-card">

        <div class="stat-top">

            <div class="stat-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle
                        cx="9"
                        cy="20"
                        r="1"
                    />

                    <circle
                        cx="19"
                        cy="20"
                        r="1"
                    />

                    <path
                        d="M3 4h2l2.5 11h11l2-7H7"
                    />
                </svg>

            </div>

            <span class="stat-status">
                Hoy
            </span>

        </div>

        <div class="stat-label">
            Ventas del día
        </div>

        <div class="stat-value">
            Bs 0.00
        </div>

        <div class="stat-description">
            Pendiente de conectar con ventas
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-top">

            <div class="stat-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"
                    />

                    <path
                        d="m3.3 7 8.7 5 8.7-5"
                    />

                    <path
                        d="M12 22V12"
                    />
                </svg>

            </div>

            <span class="stat-status">
                Inventario
            </span>

        </div>

        <div class="stat-label">
            Productos
        </div>

        <div class="stat-value">
            0
        </div>

        <div class="stat-description">
            Productos registrados
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-top">

            <div class="stat-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M12 2v20"/>

                    <path
                        d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"
                    />
                </svg>

            </div>

            <span class="stat-status">
                Caja
            </span>

        </div>

        <div class="stat-label">
            Ingresos de hoy
        </div>

        <div class="stat-value">
            Bs 0.00
        </div>

        <div class="stat-description">
            Total registrado en cajas
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-top">

            <div class="stat-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        d="M12 2a6 6 0 0 0-6 6c0 7-3 7-3 9h18c0-2-3-2-3-9a6 6 0 0 0-6-6"
                    />

                    <path d="M10 21h4"/>
                </svg>

            </div>

            <span class="stat-status">
                Sistema
            </span>

        </div>

        <div class="stat-label">
            Alertas pendientes
        </div>

        <div class="stat-value">
            0
        </div>

        <div class="stat-description">
            Requieren revisión
        </div>

    </div>

</div>


{{-- ============================
     CONTENIDO PRINCIPAL
============================ --}}

<div class="dashboard-grid">

    <section class="panel">

        <div class="panel-header">

            <div class="panel-title">

                <strong>
                    Actividad reciente
                </strong>

                <span>
                    Últimas operaciones del sistema
                </span>

            </div>

            <button
                type="button"
                class="panel-action"
            >
                Ver todo
            </button>

        </div>


        <div class="activity-list">

            <div class="activity-item">

                <div class="activity-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                        />

                        <path d="M12 8v4"/>
                        <path d="M12 16h.01"/>
                    </svg>

                </div>

                <div class="activity-info">

                    <strong>
                        Sin operaciones recientes
                    </strong>

                    <span>
                        Las actividades aparecerán
                        cuando conectemos los módulos.
                    </span>

                </div>

            </div>


            <div class="activity-item">

                <div class="activity-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M3 21V9l9-6 9 6v12"/>
                        <path d="M9 21v-8h6v8"/>
                    </svg>

                </div>

                <div class="activity-info">

                    <strong>
                        Control de almacenes
                    </strong>

                    <span>
                        Preparado para movimientos
                        y ubicaciones.
                    </span>

                </div>

                <div class="activity-time">
                    NANOS
                </div>

            </div>


            <div class="activity-item">

                <div class="activity-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M4 20V10"/>
                        <path d="M9 20V4"/>
                        <path d="M14 20v-7"/>
                        <path d="M19 20V7"/>
                    </svg>

                </div>

                <div class="activity-info">

                    <strong>
                        Control de inventario
                    </strong>

                    <span>
                        Preparado para registrar
                        existencias y movimientos.
                    </span>

                </div>

                <div class="activity-time">
                    NANOS
                </div>

            </div>

        </div>

    </section>


    <section class="panel">

        <div class="panel-header">

            <div class="panel-title">

                <strong>
                    Alertas
                </strong>

                <span>
                    Estado del sistema
                </span>

            </div>

            <button
                type="button"
                class="panel-action"
            >
                Revisar
            </button>

        </div>


        <div class="alert-list">

            <div class="alert-item">

                <span
                    class="alert-indicator green"
                ></span>

                <div class="alert-content">

                    <strong>
                        Sistema operativo
                    </strong>

                    <p>
                        El panel administrativo
                        se encuentra disponible.
                    </p>

                </div>

            </div>


            <div class="alert-item">

                <span
                    class="alert-indicator yellow"
                ></span>

                <div class="alert-content">

                    <strong>
                        Inventario
                    </strong>

                    <p>
                        Los datos reales se mostrarán
                        al conectar el módulo.
                    </p>

                </div>

            </div>


            <div class="alert-item">

                <span
                    class="alert-indicator red"
                ></span>

                <div class="alert-content">

                    <strong>
                        Stock bajo
                    </strong>

                    <p>
                        Aún no existen alertas
                        calculadas.
                    </p>

                </div>

            </div>

        </div>

    </section>

</div>


{{-- ============================
     ACCESOS RÁPIDOS
============================ --}}

<div class="quick-section">

    <div class="quick-title">
        Accesos rápidos
    </div>


    <div class="quick-grid">

        <a
            href="#"
            class="quick-card"
        >

            <div class="quick-card-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle
                        cx="12"
                        cy="8"
                        r="4"
                    />

                    <path
                        d="M4 21a8 8 0 0 1 16 0"
                    />

                    <path d="M19 8h4"/>
                    <path d="M21 6v4"/>
                </svg>

            </div>

            <div>

                <strong>
                    Nuevo empleado
                </strong>

                <span>
                    Registrar personal
                </span>

            </div>

        </a>


        <a
            href="#"
            class="quick-card"
        >

            <div class="quick-card-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"
                    />

                    <path d="M12 22V12"/>
                </svg>

            </div>

            <div>

                <strong>
                    Nuevo producto
                </strong>

                <span>
                    Registrar producto
                </span>

            </div>

        </a>


        <a
            href="#"
            class="quick-card"
        >

            <div class="quick-card-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M3 21V9l9-6 9 6v12"/>
                    <path d="M9 21v-8h6v8"/>
                </svg>

            </div>

            <div>

                <strong>
                    Recepción
                </strong>

                <span>
                    Registrar mercadería
                </span>

            </div>

        </a>


        <a
            href="#"
            class="quick-card"
        >

            <div class="quick-card-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M4 19V9"/>
                    <path d="M10 19V5"/>
                    <path d="M16 19v-7"/>
                    <path d="M22 19V3"/>
                </svg>

            </div>

            <div>

                <strong>
                    Reportes
                </strong>

                <span>
                    Consultar información
                </span>

            </div>

        </a>

    </div>

</div>

@endsection