@extends('admin.layouts.app')

@section('title', 'Usuarios')

@section('content')

<style>
    /* =========================================================
       FOTO DEL EMPLEADO EN USUARIOS
    ========================================================= */

    .employee-cell {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 210px;
    }

    .employee-avatar {
        width: 46px;
        height: 46px;
        min-width: 46px;

        display: flex;
        align-items: center;
        justify-content: center;

        overflow: hidden;

        border-radius: 50%;
        border: 2px solid rgba(227, 27, 35, .35);

        background: rgba(227, 27, 35, .10);
        color: #ff555b;

        font-size: 15px;
        font-weight: 800;

        box-shadow: 0 0 0 3px rgba(227, 27, 35, .05);
    }

    .employee-avatar img {
        width: 100%;
        height: 100%;

        display: block;

        object-fit: cover;
        object-position: center;

        border-radius: 50%;
    }

    .employee-name {
        min-width: 0;
    }

    .employee-name strong {
        display: block;

        color: #eeeeee;

        font-size: 12px;
        font-weight: 700;

        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .employee-name span {
        display: block;

        margin-top: 4px;

        color: #777d85;

        font-size: 10px;
        font-weight: 600;
    }

    .current-account-label {
        margin-top: 4px;

        color: #e31b23;

        font-size: 9px;
        font-weight: 800;

        text-transform: uppercase;
        letter-spacing: .4px;
    }

    @media (max-width: 768px) {
        .employee-avatar {
            width: 40px;
            height: 40px;
            min-width: 40px;
        }

        .employee-cell {
            min-width: 180px;
        }
    }
</style>


{{-- =========================================================
     ENCABEZADO
========================================================= --}}

<div class="page-header">

    <div class="page-title">

        <span class="eyebrow">
            Administración
        </span>

        <h1>
            Usuarios
        </h1>

        <p>
            Gestiona las cuentas de acceso del personal de
            Minisúper NANOS.
        </p>

    </div>


    <a
        href="{{ route('admin.usuarios.create') }}"
        class="btn-primary"
    >
        + Nuevo usuario
    </a>

</div>


{{-- =========================================================
     MENSAJES
========================================================= --}}

@if(session('success'))

    <div class="success-message">
        {{ session('success') }}
    </div>

@endif


@if(session('error'))

    <div class="error-message">
        {{ session('error') }}
    </div>

@endif


{{-- =========================================================
     ESTADÍSTICAS
========================================================= --}}

<div class="stats-grid">

    <div class="stat-card">

        <div class="stat-label">
            Total de usuarios
        </div>

        <div class="stat-value">
            {{ $totalUsuarios }}
        </div>

        <div class="stat-description">
            Cuentas registradas
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-label">
            Usuarios activos
        </div>

        <div class="stat-value">
            {{ $usuariosActivos }}
        </div>

        <div class="stat-description">
            Con acceso al sistema
        </div>

    </div>


    <div class="stat-card">

        <div class="stat-label">
            Usuarios inactivos
        </div>

        <div class="stat-value">
            {{ $usuariosInactivos }}
        </div>

        <div class="stat-description">
            Sin acceso al sistema
        </div>

    </div>

</div>


{{-- =========================================================
     BUSCADOR
========================================================= --}}

<div class="crud-toolbar">

    <form
        method="GET"
        action="{{ route('admin.usuarios.index') }}"
        class="search-form"
    >

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >
            <circle
                cx="11"
                cy="11"
                r="8"
            />

            <path
                d="m21 21-4.35-4.35"
            />
        </svg>


        <input
            type="text"
            name="buscar"
            value="{{ $buscar }}"
            class="search-input"
            placeholder="Buscar por empleado, usuario, correo o rol..."
        >

    </form>

</div>


{{-- =========================================================
     TABLA DE USUARIOS
========================================================= --}}

<div class="table-panel">

    <div class="table-responsive">

        <table class="admin-table">

            <thead>

                <tr>

                    <th>
                        Empleado
                    </th>

                    <th>
                        Usuario
                    </th>

                    <th>
                        Rol
                    </th>

                    <th>
                        Correo
                    </th>

                    <th>
                        Estado
                    </th>

                    <th>
                        Último acceso
                    </th>

                    <th>
                        Acciones
                    </th>

                </tr>

            </thead>


            <tbody>

            @forelse($usuarios as $usuario)

                @php

                    /*
                    |--------------------------------------------------------------------------
                    | EMPLEADO RELACIONADO
                    |--------------------------------------------------------------------------
                    */

                    $empleado = $usuario->empleado;


                    /*
                    |--------------------------------------------------------------------------
                    | NOMBRE COMPLETO
                    |--------------------------------------------------------------------------
                    */

                    $nombreCompleto = $empleado
                        ? trim(
                            $empleado->nombres
                            . ' '
                            . $empleado->apellidos
                        )
                        : 'Empleado no disponible';


                    /*
                    |--------------------------------------------------------------------------
                    | INICIAL
                    |--------------------------------------------------------------------------
                    */

                    $nombreParaInicial = $empleado?->nombres
                        ?? $usuario->username;

                    $inicial = mb_strtoupper(
                        mb_substr(
                            $nombreParaInicial,
                            0,
                            1
                        )
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | FOTO
                    |--------------------------------------------------------------------------
                    */

                    $fotoEmpleado = null;

                    if (
                        $empleado
                        && !empty($empleado->foto)
                    ) {

                        $fotoEmpleado =
                            asset(
                                'storage/'
                                . ltrim(
                                    $empleado->foto,
                                    '/'
                                )
                            );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | USUARIO ACTUAL
                    |--------------------------------------------------------------------------
                    */

                    $esUsuarioActual =
                        auth()->id()
                        === $usuario->id;

                @endphp


                <tr>

                    {{-- =================================================
                         EMPLEADO + FOTO
                    ================================================== --}}

                    <td>

                        <div class="employee-cell">

                            <div class="employee-avatar">

                                @if($fotoEmpleado)

                                    <img
                                        src="{{ $fotoEmpleado }}"
                                        alt="Foto de {{ $nombreCompleto }}"
                                        loading="lazy"

                                        onerror="
                                            this.style.display='none';
                                            this.nextElementSibling.style.display='flex';
                                        "
                                    >

                                    <span
                                        style="
                                            display:none;
                                            width:100%;
                                            height:100%;
                                            align-items:center;
                                            justify-content:center;
                                        "
                                    >
                                        {{ $inicial }}
                                    </span>

                                @else

                                    <span>
                                        {{ $inicial }}
                                    </span>

                                @endif

                            </div>


                            <div class="employee-name">

                                <strong>
                                    {{ $nombreCompleto }}
                                </strong>

                                <span>
                                    {{
                                        $empleado?->codigo_empleado
                                        ?? 'Sin empleado'
                                    }}
                                </span>

                            </div>

                        </div>

                    </td>


                    {{-- =================================================
                         USUARIO
                    ================================================== --}}

                    <td>

                        <strong style="color:#eee;">
                            {{ $usuario->username }}
                        </strong>


                        @if($esUsuarioActual)

                            <div class="current-account-label">
                                TU CUENTA
                            </div>

                        @endif

                    </td>


                    {{-- =================================================
                         ROL
                    ================================================== --}}

                    <td>

                        {{
                            ucfirst(
                                str_replace(
                                    '_',
                                    ' ',
                                    $usuario->rol?->nombre
                                    ?? 'Sin rol'
                                )
                            )
                        }}

                    </td>


                    {{-- =================================================
                         CORREO
                    ================================================== --}}

                    <td>

                        {{
                            $usuario->email
                            ?: '—'
                        }}

                    </td>


                    {{-- =================================================
                         ESTADO
                    ================================================== --}}

                    <td>

                        <span
                            class="
                                status-badge
                                {{
                                    $usuario->estado === 'activo'
                                    ? 'status-active'
                                    : 'status-inactive'
                                }}
                            "
                        >

                            {{ ucfirst($usuario->estado) }}

                        </span>

                    </td>


                    {{-- =================================================
                         ÚLTIMO ACCESO
                    ================================================== --}}

                    <td>

                        @if($usuario->ultimo_login)

                            {{
                                $usuario
                                    ->ultimo_login
                                    ->format('d/m/Y H:i')
                            }}

                        @else

                            Nunca

                        @endif

                    </td>


                    {{-- =================================================
                         ACCIONES
                    ================================================== --}}

                    <td>

                        <div class="actions">


                            {{-- VER --}}

                            <a
                                href="{{
                                    route(
                                        'admin.usuarios.show',
                                        $usuario
                                    )
                                }}"
                                class="action-btn"
                                title="Ver usuario"
                            >
                                👁
                            </a>


                            {{-- EDITAR --}}

                            <a
                                href="{{
                                    route(
                                        'admin.usuarios.edit',
                                        $usuario
                                    )
                                }}"
                                class="action-btn edit"
                                title="Editar usuario"
                            >
                                ✎
                            </a>


                            {{-- ACTIVAR / DESACTIVAR --}}

                            @if(!$esUsuarioActual)

                                <form
                                    method="POST"
                                    action="{{
                                        route(
                                            'admin.usuarios.estado',
                                            $usuario
                                        )
                                    }}"
                                    onsubmit="
                                        return confirm(
                                            '{{ $usuario->estado === 'activo'
                                                ? '¿Deseas desactivar este usuario?'
                                                : '¿Deseas activar este usuario?' }}'
                                        );
                                    "
                                >

                                    @csrf

                                    @method('PATCH')


                                    <button
                                        type="submit"
                                        class="action-btn"
                                        title="{{
                                            $usuario->estado === 'activo'
                                            ? 'Desactivar'
                                            : 'Activar'
                                        }}"
                                    >

                                        {{
                                            $usuario->estado === 'activo'
                                            ? '⊘'
                                            : '✓'
                                        }}

                                    </button>

                                </form>

                            @endif

                        </div>

                    </td>

                </tr>


            @empty

                <tr>

                    <td colspan="7">

                        <div class="empty-state">

                            <h3>
                                No se encontraron usuarios
                            </h3>

                            <p>
                                Todavía no existen cuentas que
                                coincidan con la búsqueda.
                            </p>

                        </div>

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>


{{-- =========================================================
     PAGINACIÓN
========================================================= --}}

@if($usuarios->hasPages())

    <div style="margin-top:20px;">

        {{ $usuarios->links() }}

    </div>

@endif


@endsection