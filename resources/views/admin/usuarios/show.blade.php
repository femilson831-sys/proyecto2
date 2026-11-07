@extends('admin.layouts.app')

@section('title', 'Detalle del usuario')

@section('content')

@php
    $empleado = $usuario->empleado;

    $nombreCompleto = $empleado
        ? trim(
            $empleado->nombres
            . ' '
            . $empleado->apellidos
        )
        : 'Empleado no disponible';
@endphp


<div class="page-header">

    <div class="page-title">

        <span class="eyebrow">
            Usuarios
        </span>

        <h1>
            {{ $usuario->username }}
        </h1>

        <p>
            Información de la cuenta y acceso al sistema.
        </p>

    </div>


    <a
        href="{{ route(
            'admin.usuarios.edit',
            $usuario
        ) }}"
        class="btn-primary"
    >
        Editar usuario
    </a>

</div>


<div class="form-panel-admin">

    <div class="form-section">

        <div class="form-section-title">
            <strong>Información del usuario</strong>
            <span>Datos generales de la cuenta.</span>
        </div>


        <div class="detail-grid">

            <div class="detail-item">
                <span>Empleado</span>

                <strong>
                    {{ $nombreCompleto }}
                </strong>
            </div>


            <div class="detail-item">
                <span>Código empleado</span>

                <strong>
                    {{ $empleado?->codigo_empleado ?? '—' }}
                </strong>
            </div>


            <div class="detail-item">
                <span>Usuario</span>

                <strong>
                    {{ $usuario->username }}
                </strong>
            </div>


            <div class="detail-item">
                <span>Correo</span>

                <strong>
                    {{ $usuario->email ?: '—' }}
                </strong>
            </div>


            <div class="detail-item">
                <span>Rol</span>

                <strong>
                    {{ ucfirst(
                        str_replace(
                            '_',
                            ' ',
                            $usuario->rol?->nombre
                                ?? 'Sin rol'
                        )
                    ) }}
                </strong>
            </div>


            <div class="detail-item">
                <span>Estado</span>

                <strong>
                    <span
                        class="status-badge {{
                            $usuario->estado === 'activo'
                                ? 'status-active'
                                : 'status-inactive'
                        }}"
                    >
                        {{ ucfirst($usuario->estado) }}
                    </span>
                </strong>
            </div>


            <div class="detail-item">
                <span>Último inicio de sesión</span>

                <strong>
                    {{ $usuario->ultimo_login
                        ? $usuario->ultimo_login->format(
                            'd/m/Y H:i:s'
                        )
                        : 'Nunca ha iniciado sesión' }}
                </strong>
            </div>


            <div class="detail-item">
                <span>Cuenta creada</span>

                <strong>
                    {{ $usuario->created_at
                        ? $usuario->created_at->format(
                            'd/m/Y H:i:s'
                        )
                        : '—' }}
                </strong>
            </div>


            <div class="detail-item">
                <span>Última actualización</span>

                <strong>
                    {{ $usuario->updated_at
                        ? $usuario->updated_at->format(
                            'd/m/Y H:i:s'
                        )
                        : '—' }}
                </strong>
            </div>

        </div>

    </div>


    <div class="form-actions">

        <a
            href="{{ route(
                'admin.usuarios.index'
            ) }}"
            class="btn-secondary"
        >
            Volver
        </a>

    </div>

</div>

@endsection
