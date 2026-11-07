@extends('admin.layouts.app')

@section('title', 'Editar usuario')

@push('styles')
<style>
    .user-edit-page {
        width: 100%;
        max-width: 1180px;
        margin: 0 auto;
        padding-bottom: 35px;
    }

    .user-edit-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 25px;
        padding: 28px 30px;
        margin-bottom: 24px;
        border: 1px solid rgba(255,255,255,.07);
        border-radius: 18px;
        background:
            radial-gradient(
                circle at 90% 20%,
                rgba(227,27,35,.14),
                transparent 35%
            ),
            linear-gradient(
                135deg,
                rgba(255,255,255,.035),
                rgba(255,255,255,.015)
            );
        box-shadow: 0 14px 40px rgba(0,0,0,.18);
    }

    .user-edit-hero::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: #e31b23;
    }

    .user-edit-heading {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .user-edit-icon {
        width: 58px;
        height: 58px;
        min-width: 58px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        color: #fff;
        background: rgba(227,27,35,.12);
        border: 1px solid rgba(227,27,35,.28);
        box-shadow: 0 8px 20px rgba(0,0,0,.18);
    }

    .user-edit-icon svg {
        width: 27px;
        height: 27px;
    }

    .user-edit-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 7px;
        color: #e31b23;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.7px;
        text-transform: uppercase;
    }

    .user-edit-eyebrow::before {
        content: "";
        width: 17px;
        height: 2px;
        border-radius: 10px;
        background: #e31b23;
    }

    .user-edit-title {
        margin: 0;
        color: #f5f5f5;
        font-size: clamp(24px,3vw,34px);
        font-weight: 800;
        letter-spacing: -.7px;
    }

    .user-edit-description {
        max-width: 620px;
        margin: 7px 0 0;
        color: #858991;
        font-size: 13px;
        line-height: 1.6;
    }

    .user-account-badge {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 11px 15px;
        border: 1px solid rgba(255,255,255,.07);
        border-radius: 12px;
        background: rgba(0,0,0,.16);
    }

    .user-account-avatar {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: rgba(227,27,35,.13);
        color: #fff;
        font-size: 13px;
        font-weight: 800;
    }

    .user-account-data span {
        display: block;
        margin-bottom: 2px;
        color: #696c73;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .8px;
    }

    .user-account-data strong {
        display: block;
        color: #eee;
        font-size: 12px;
    }

    .user-error-box {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 17px 19px;
        margin-bottom: 22px;
        border-radius: 13px;
        border: 1px solid rgba(239,68,68,.25);
        background: rgba(239,68,68,.07);
    }

    .user-error-icon {
        width: 36px;
        height: 36px;
        min-width: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: rgba(239,68,68,.12);
        color: #f87171;
    }

    .user-error-icon svg {
        width: 19px;
        height: 19px;
    }

    .user-error-content strong {
        display: block;
        margin-bottom: 6px;
        color: #fca5a5;
        font-size: 13px;
    }

    .user-error-content ul {
        margin: 0;
        padding-left: 18px;
        color: #9da0a6;
        font-size: 12px;
        line-height: 1.7;
    }

    .user-edit-note {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        margin-top: 14px;
        padding: 13px 15px;
        border-radius: 11px;
        border: 1px solid rgba(245,185,66,.12);
        background: rgba(245,185,66,.04);
        color: #777a80;
        font-size: 10px;
        line-height: 1.6;
    }

    .user-edit-note svg {
        width: 17px;
        height: 17px;
        min-width: 17px;
        color: #f5b942;
    }

    .user-form-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-top: 22px;
        padding: 18px 20px;
        border-radius: 14px;
        border: 1px solid rgba(255,255,255,.06);
        background: rgba(255,255,255,.02);
    }

    .user-form-footer-info {
        display: flex;
        align-items: center;
        gap: 11px;
        color: #74777d;
        font-size: 11px;
    }

    .user-form-footer-info svg {
        width: 18px;
        height: 18px;
        color: #e31b23;
    }

    .user-form-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-btn-cancel,
    .user-btn-view {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 17px;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,.09);
        background: rgba(255,255,255,.025);
        color: #a4a7ad;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        transition: .2s ease;
    }

    .user-btn-cancel:hover,
    .user-btn-view:hover {
        color: #fff;
        background: rgba(255,255,255,.055);
        border-color: rgba(255,255,255,.16);
        transform: translateY(-1px);
    }

    .user-btn-save {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 0 21px;
        border: 0;
        border-radius: 10px;
        background: linear-gradient(135deg,#e31b23,#b90f16);
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 8px 22px rgba(227,27,35,.18);
        transition: .2s ease;
    }

    .user-btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 11px 28px rgba(227,27,35,.27);
    }

    .user-btn-save svg,
    .user-btn-cancel svg,
    .user-btn-view svg {
        width: 15px;
        height: 15px;
    }

    @media (max-width: 850px) {
        .user-edit-hero {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    @media (max-width: 650px) {
        .user-edit-hero {
            padding: 22px 18px;
        }

        .user-edit-icon {
            width: 48px;
            height: 48px;
            min-width: 48px;
        }

        .user-form-footer {
            align-items: stretch;
            flex-direction: column;
        }

        .user-form-actions {
            width: 100%;
        }

        .user-btn-cancel,
        .user-btn-view,
        .user-btn-save {
            flex: 1;
        }
    }

    @media (max-width: 470px) {
        .user-form-actions {
            flex-direction: column;
        }

        .user-btn-cancel,
        .user-btn-view,
        .user-btn-save {
            width: 100%;
        }
    }
</style>
@endpush


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

    $inicial = strtoupper(
        substr(
            $empleado?->nombres
                ?? $usuario->username,
            0,
            1
        )
    );
@endphp


<div class="user-edit-page">

    {{-- CABECERA --}}
    <div class="user-edit-hero">

        <div class="user-edit-heading">

            <div class="user-edit-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M12 20h9"/>
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                </svg>

            </div>

            <div>

                <span class="user-edit-eyebrow">
                    Administración de usuarios
                </span>

                <h1 class="user-edit-title">
                    Editar usuario
                </h1>

                <p class="user-edit-description">
                    Actualiza los datos de acceso, el rol,
                    el estado o la contraseña de esta cuenta.
                </p>

            </div>

        </div>


        {{-- USUARIO ACTUAL --}}
        <div class="user-account-badge">

            <div class="user-account-avatar">
                {{ $inicial }}
            </div>

            <div class="user-account-data">

                <span>
                    Cuenta seleccionada
                </span>

                <strong>
                    {{ $usuario->username }}
                </strong>

            </div>

        </div>

    </div>


    {{-- ERRORES --}}
    @if($errors->any())

        <div class="user-error-box">

            <div class="user-error-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>

            </div>

            <div class="user-error-content">

                <strong>
                    No se pudieron guardar los cambios
                </strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>

        </div>

    @endif


    {{-- FORMULARIO --}}
    <form
        method="POST"
        action="{{ route('admin.usuarios.update', $usuario) }}"
        autocomplete="off"
    >

        @csrf
        @method('PUT')


        @include('admin.usuarios._form')


        {{-- AVISO --}}
        <div class="user-edit-note">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>

            <span>
                Si no deseas cambiar la contraseña,
                deja los campos de contraseña vacíos.
                La contraseña actual continuará funcionando.
            </span>

        </div>


        {{-- ACCIONES --}}
        <div class="user-form-footer">

            <div class="user-form-footer-info">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>

                <span>
                    Usuario asociado a
                    <strong style="color:#d1d5db;">
                        {{ $nombreCompleto }}
                    </strong>
                </span>

            </div>


            <div class="user-form-actions">

                <a
                    href="{{ route('admin.usuarios.index') }}"
                    class="user-btn-cancel"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m15 18-6-6 6-6"/>
                    </svg>

                    Cancelar
                </a>


                <a
                    href="{{ route('admin.usuarios.show', $usuario) }}"
                    class="user-btn-view"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>

                    Ver
                </a>


                <button
                    type="submit"
                    class="user-btn-save"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>

                    Guardar cambios
                </button>

            </div>

        </div>

    </form>

</div>

@endsection