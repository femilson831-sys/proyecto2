@extends('admin.layouts.app')

@section('title', 'Nuevo usuario')

@push('styles')
<style>
    /* =========================================================
       NUEVO USUARIO - NANOS
       ========================================================= */

    .user-create-page {
        width: 100%;
        max-width: 1180px;
        margin: 0 auto;
        padding-bottom: 35px;
    }

    /* HEADER */
    .user-create-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 25px;
        padding: 28px 30px;
        margin-bottom: 24px;
        border: 1px solid rgba(255, 255, 255, .07);
        border-radius: 18px;
        background:
            radial-gradient(
                circle at 90% 20%,
                rgba(227, 27, 35, .14),
                transparent 35%
            ),
            linear-gradient(
                135deg,
                rgba(255,255,255,.035),
                rgba(255,255,255,.015)
            );
        box-shadow: 0 14px 40px rgba(0, 0, 0, .18);
    }

    .user-create-hero::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: #e31b23;
    }

    .user-create-heading {
        display: flex;
        align-items: center;
        gap: 18px;
        position: relative;
        z-index: 2;
    }

    .user-create-icon {
        width: 58px;
        height: 58px;
        min-width: 58px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        color: #fff;
        background: rgba(227, 27, 35, .12);
        border: 1px solid rgba(227, 27, 35, .28);
        box-shadow:
            inset 0 0 20px rgba(227, 27, 35, .06),
            0 8px 20px rgba(0, 0, 0, .18);
    }

    .user-create-icon svg {
        width: 27px;
        height: 27px;
    }

    .user-create-eyebrow {
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

    .user-create-eyebrow::before {
        content: "";
        width: 17px;
        height: 2px;
        border-radius: 10px;
        background: #e31b23;
    }

    .user-create-title {
        margin: 0;
        color: #f5f5f5;
        font-size: clamp(24px, 3vw, 34px);
        font-weight: 800;
        letter-spacing: -.7px;
    }

    .user-create-description {
        max-width: 620px;
        margin: 7px 0 0;
        color: #858991;
        font-size: 13px;
        line-height: 1.6;
    }

    /* INDICADOR DE PASOS */
    .user-progress {
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 2;
    }

    .user-progress-item {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 8px 11px;
        border-radius: 9px;
        color: #6f737a;
        background: rgba(255,255,255,.025);
        border: 1px solid rgba(255,255,255,.05);
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .user-progress-item.active {
        color: #f4f4f4;
        border-color: rgba(227,27,35,.25);
        background: rgba(227,27,35,.08);
    }

    .user-progress-number {
        width: 21px;
        height: 21px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255,255,255,.06);
        font-size: 9px;
    }

    .user-progress-item.active .user-progress-number {
        background: #e31b23;
        color: #fff;
    }

    /* ALERTA */
    .user-error-box {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 17px 19px;
        margin-bottom: 22px;
        border-radius: 13px;
        border: 1px solid rgba(239, 68, 68, .25);
        background: rgba(239, 68, 68, .07);
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

    /* CONTENEDOR */
    .user-create-container {
        position: relative;
    }

    /* BARRA INFERIOR */
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
        line-height: 1.5;
    }

    .user-form-footer-info svg {
        width: 18px;
        height: 18px;
        min-width: 18px;
        color: #e31b23;
    }

    .user-form-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* CANCELAR */
    .user-btn-cancel {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 18px;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,.09);
        background: rgba(255,255,255,.025);
        color: #a4a7ad;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        transition: all .2s ease;
    }

    .user-btn-cancel:hover {
        color: #fff;
        border-color: rgba(255,255,255,.16);
        background: rgba(255,255,255,.055);
        transform: translateY(-1px);
    }

    /* CREAR */
    .user-btn-create {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 0 21px;
        border: 0;
        border-radius: 10px;
        background: linear-gradient(135deg, #e31b23, #b90f16);
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 8px 22px rgba(227,27,35,.18);
        transition: all .2s ease;
    }

    .user-btn-create:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 11px 28px rgba(227,27,35,.27);
    }

    .user-btn-create:active:not(:disabled) {
        transform: translateY(0);
    }

    .user-btn-create:disabled {
        cursor: not-allowed;
        opacity: .4;
        box-shadow: none;
    }

    .user-btn-create svg,
    .user-btn-cancel svg {
        width: 15px;
        height: 15px;
    }

    /* SEGURIDAD */
    .security-note {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 14px;
        padding: 11px 15px;
        border-radius: 10px;
        border: 1px solid rgba(34,197,94,.10);
        background: rgba(34,197,94,.035);
        color: #71757b;
        font-size: 10px;
    }

    .security-note svg {
        width: 15px;
        height: 15px;
        min-width: 15px;
        color: #22c55e;
    }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .user-create-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .user-progress {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 3px;
        }
    }

    @media (max-width: 650px) {
        .user-create-page {
            padding-bottom: 20px;
        }

        .user-create-hero {
            padding: 22px 18px;
            border-radius: 14px;
        }

        .user-create-heading {
            align-items: flex-start;
        }

        .user-create-icon {
            width: 48px;
            height: 48px;
            min-width: 48px;
            border-radius: 13px;
        }

        .user-create-title {
            font-size: 24px;
        }

        .user-form-footer {
            align-items: stretch;
            flex-direction: column;
        }

        .user-form-actions {
            width: 100%;
        }

        .user-btn-cancel,
        .user-btn-create {
            flex: 1;
        }
    }

    @media (max-width: 430px) {
        .user-create-heading {
            gap: 12px;
        }

        .user-progress-item {
            padding: 7px 9px;
        }

        .user-form-actions {
            flex-direction: column-reverse;
        }

        .user-btn-cancel,
        .user-btn-create {
            width: 100%;
        }
    }
</style>
@endpush


@section('content')

<div class="user-create-page">

    {{-- =====================================================
         CABECERA
         ===================================================== --}}
    <div class="user-create-hero">

        <div class="user-create-heading">

            <div class="user-create-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <line x1="19" y1="8" x2="19" y2="14"/>
                    <line x1="22" y1="11" x2="16" y2="11"/>
                </svg>

            </div>

            <div>

                <span class="user-create-eyebrow">
                    Administración de usuarios
                </span>

                <h1 class="user-create-title">
                    Nuevo usuario
                </h1>

                <p class="user-create-description">
                    Crea una cuenta de acceso para un empleado
                    del Minisúper NANOS y asigna su rol dentro
                    del sistema.
                </p>

            </div>

        </div>


        <div class="user-progress">

            <div class="user-progress-item active">
                <span class="user-progress-number">1</span>
                Empleado
            </div>

            <div class="user-progress-item active">
                <span class="user-progress-number">2</span>
                Acceso
            </div>

            <div class="user-progress-item active">
                <span class="user-progress-number">3</span>
                Seguridad
            </div>

        </div>

    </div>


    {{-- =====================================================
         ERRORES
         ===================================================== --}}
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
                    No se pudo crear el usuario
                </strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>

        </div>

    @endif


    {{-- =====================================================
         FORMULARIO
         ===================================================== --}}
    <form
        method="POST"
        action="{{ route('admin.usuarios.store') }}"
        id="formCrearUsuario"
        autocomplete="off"
    >

        @csrf


        <div class="user-create-container">

            @include('admin.usuarios._form')

        </div>


        {{-- INFORMACIÓN DE SEGURIDAD --}}
        <div class="security-note">

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
                La contraseña se almacenará protegida.
                El sistema nunca mostrará la contraseña guardada
                después de crear la cuenta.
            </span>

        </div>


        {{-- =================================================
             ACCIONES
             ================================================= --}}
        <div class="user-form-footer">

            <div class="user-form-footer-info">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="16" x2="12" y2="12"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>

                <span>
                    Los campos marcados con
                    <strong style="color:#e31b23;">*</strong>
                    son obligatorios.
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


                <button
                    type="submit"
                    class="user-btn-create"
                    @disabled($empleados->isEmpty())
                >

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M19 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="10" cy="7" r="4"/>
                        <line x1="19" y1="8" x2="19" y2="14"/>
                        <line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>

                    Crear usuario

                </button>

            </div>

        </div>

    </form>

</div>

@endsection