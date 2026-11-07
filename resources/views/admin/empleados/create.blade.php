@extends('admin.layouts.app')

@section('title', 'Nuevo empleado')

@push('styles')
<style>
    /* =========================================================
       NUEVO EMPLEADO
    ========================================================= */

    .employee-create-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 22px;
    }

    .employee-create-header .page-title {
        margin: 0;
    }

    .employee-back-btn {
        min-height: 42px;
        padding: 0 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 11px;
        border: 1px solid rgba(255,255,255,.08);
        background: rgba(255,255,255,.035);
        color: #b9bec5;
        text-decoration: none;
        font-size: 12px;
        font-weight: 800;
        transition:
            background .2s ease,
            border-color .2s ease,
            color .2s ease,
            transform .2s ease;
    }

    .employee-back-btn:hover {
        background: rgba(255,255,255,.07);
        border-color: rgba(255,255,255,.13);
        color: #fff;
        transform: translateY(-1px);
    }

    .employee-back-btn svg {
        width: 17px;
        height: 17px;
    }


    /* =========================================================
       BARRA INFORMATIVA
    ========================================================= */

    .create-info-bar {
        position: relative;
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
        padding: 15px 18px;
        border: 1px solid rgba(227,27,35,.14);
        border-radius: 14px;
        background:
            linear-gradient(
                90deg,
                rgba(227,27,35,.08),
                rgba(227,27,35,.015)
            );
        overflow: hidden;
    }

    .create-info-bar::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--red, #e31b23);
    }

    .create-info-icon {
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: rgba(227,27,35,.11);
        color: #ff4148;
    }

    .create-info-icon svg {
        width: 19px;
        height: 19px;
    }

    .create-info-text strong {
        display: block;
        margin-bottom: 3px;
        color: #f0f1f2;
        font-size: 13px;
        font-weight: 800;
    }

    .create-info-text span {
        display: block;
        color: #7e848c;
        font-size: 12px;
        line-height: 1.5;
    }


    /* =========================================================
       ERRORES
    ========================================================= */

    .employee-error-box {
        position: relative;
        display: flex;
        gap: 14px;
        margin-bottom: 20px;
        padding: 17px 18px;
        border-radius: 14px;
        border: 1px solid rgba(239,68,68,.20);
        background: rgba(239,68,68,.07);
        overflow: hidden;
        animation: errorIn .25s ease;
    }

    .employee-error-box::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 4px;
        background: #ef4444;
    }

    .employee-error-icon {
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: rgba(239,68,68,.12);
        color: #f87171;
    }

    .employee-error-icon svg {
        width: 19px;
        height: 19px;
    }

    .employee-error-content {
        flex: 1;
        min-width: 0;
    }

    .employee-error-content strong {
        display: block;
        margin-bottom: 7px;
        color: #fecaca;
        font-size: 13px;
        font-weight: 800;
    }

    .employee-error-content ul {
        margin: 0;
        padding-left: 18px;
        color: #d99a9a;
        font-size: 12px;
        line-height: 1.7;
    }

    @keyframes errorIn {
        from {
            opacity: 0;
            transform: translateY(-6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }


    /* =========================================================
       PANEL
    ========================================================= */

    .employee-create-panel {
        position: relative;
        background: var(--panel, #15171a);
        border: 1px solid rgba(255,255,255,.07);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0,0,0,.12);
    }

    .employee-create-panel::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background:
            linear-gradient(
                90deg,
                var(--red, #e31b23),
                rgba(227,27,35,.15)
            );
    }

    .employee-create-content {
        padding: 8px 24px 0;
    }


    /* =========================================================
       ACCIONES
    ========================================================= */

    .employee-create-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-top: 8px;
        padding: 20px 24px;
        border-top: 1px solid rgba(255,255,255,.06);
        background: rgba(0,0,0,.08);
    }

    .employee-create-actions-info {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #696f77;
        font-size: 11px;
    }

    .employee-create-actions-info svg {
        width: 15px;
        height: 15px;
        color: #8a9098;
    }

    .employee-create-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .employee-cancel-btn,
    .employee-save-btn {
        min-height: 43px;
        padding: 0 17px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 11px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition:
            background .2s ease,
            border-color .2s ease,
            transform .2s ease,
            opacity .2s ease;
    }

    .employee-cancel-btn {
        border: 1px solid rgba(255,255,255,.09);
        background: rgba(255,255,255,.035);
        color: #b9bdc3;
        text-decoration: none;
    }

    .employee-cancel-btn:hover {
        background: rgba(255,255,255,.07);
        border-color: rgba(255,255,255,.14);
        color: #fff;
        transform: translateY(-1px);
    }

    .employee-save-btn {
        border: 1px solid var(--red, #e31b23);
        background: var(--red, #e31b23);
        color: #fff;
        box-shadow: 0 8px 22px rgba(227,27,35,.18);
    }

    .employee-save-btn:hover {
        background: var(--red-dark, #bd1118);
        border-color: var(--red-dark, #bd1118);
        transform: translateY(-1px);
    }

    .employee-save-btn:disabled {
        opacity: .65;
        cursor: not-allowed;
        transform: none;
    }

    .employee-cancel-btn svg,
    .employee-save-btn svg {
        width: 17px;
        height: 17px;
    }


    /* =========================================================
       SPINNER
    ========================================================= */

    .save-spinner {
        display: none;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,.30);
        border-top-color: #fff;
        border-radius: 50%;
        animation: employeeSpin .7s linear infinite;
    }

    .employee-save-btn.loading .save-spinner {
        display: block;
    }

    .employee-save-btn.loading .save-icon {
        display: none;
    }

    @keyframes employeeSpin {
        to {
            transform: rotate(360deg);
        }
    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 700px) {

        .employee-create-header {
            flex-direction: column;
        }

        .employee-back-btn {
            width: 100%;
        }

        .employee-create-content {
            padding-left: 16px;
            padding-right: 16px;
        }

        .employee-create-actions {
            padding: 17px 16px;
            flex-direction: column;
            align-items: stretch;
        }

        .employee-create-actions-info {
            justify-content: center;
            text-align: center;
        }

        .employee-create-buttons {
            width: 100%;
        }

        .employee-cancel-btn,
        .employee-save-btn {
            flex: 1;
        }
    }

    @media (max-width: 470px) {

        .employee-create-buttons {
            flex-direction: column-reverse;
        }

        .employee-cancel-btn,
        .employee-save-btn {
            width: 100%;
        }

        .create-info-bar {
            align-items: flex-start;
        }
    }
</style>
@endpush


@section('content')

{{-- =========================================================
     CABECERA
========================================================= --}}

<div class="employee-create-header">

    <div class="page-title">

        <span class="eyebrow">
            Personal / Empleados
        </span>

        <h1>
            Nuevo empleado
        </h1>

        <p>
            Registra un nuevo miembro del personal
            del Minisúper NANOS.
        </p>

    </div>


    <a
        href="{{ route('admin.empleados.index') }}"
        class="employee-back-btn"
    >
        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >
            <path d="m15 18-6-6 6-6"/>
        </svg>

        Volver a empleados
    </a>

</div>


{{-- =========================================================
     INFORMACIÓN
========================================================= --}}

<div class="create-info-bar">

    <div class="create-info-icon">

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 11v5"/>
            <path d="M12 8h.01"/>
        </svg>

    </div>

    <div class="create-info-text">

        <strong>
            Registro de personal
        </strong>

        <span>
            Completa los datos personales, fotografía,
            área y cargo del empleado.
        </span>

    </div>

</div>


{{-- =========================================================
     ERRORES
========================================================= --}}

@if($errors->any())

    <div class="employee-error-box">

        <div class="employee-error-icon">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v6"/>
                <path d="M12 17h.01"/>
            </svg>

        </div>

        <div class="employee-error-content">

            <strong>
                No se pudo registrar el empleado
            </strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>

        </div>

    </div>

@endif


{{-- =========================================================
     FORMULARIO
========================================================= --}}

<form
    method="POST"
    action="{{ route('admin.empleados.store') }}"
    class="employee-create-panel"
    id="employeeCreateForm"
    enctype="multipart/form-data"
    autocomplete="off"
>

    @csrf

    <div class="employee-create-content">

        @include('admin.empleados._form')

    </div>


    {{-- =====================================================
         ACCIONES
    ====================================================== --}}

    <div class="employee-create-actions">

        <div class="employee-create-actions-info">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    d="M12 22a10 10 0 1 0 0-20
                       10 10 0 0 0 0 20Z"
                />

                <path d="M12 8v4"/>
                <path d="M12 16h.01"/>
            </svg>

            Verifica la información antes de guardar.

        </div>


        <div class="employee-create-buttons">

            <a
                href="{{ route('admin.empleados.index') }}"
                class="employee-cancel-btn"
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

                Cancelar
            </a>


            <button
                type="submit"
                class="employee-save-btn"
                id="employeeSaveButton"
            >

                <svg
                    class="save-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        d="M19 21H5a2 2 0 0 1-2-2V5
                           a2 2 0 0 1 2-2h11l5 5v11
                           a2 2 0 0 1-2 2Z"
                    />

                    <path d="M17 21v-8H7v8"/>
                    <path d="M7 3v5h8"/>
                </svg>

                <span class="save-spinner"></span>

                <span id="employeeSaveText">
                    Guardar empleado
                </span>

            </button>

        </div>

    </div>

</form>

@endsection


@push('scripts')
<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const formulario =
            document.getElementById(
                'employeeCreateForm'
            );

        const boton =
            document.getElementById(
                'employeeSaveButton'
            );

        const texto =
            document.getElementById(
                'employeeSaveText'
            );

        if (
            !formulario ||
            !boton ||
            !texto
        ) {
            return;
        }

        formulario.addEventListener(
            'submit',
            function () {

                boton.disabled = true;

                boton.classList.add(
                    'loading'
                );

                texto.textContent =
                    'Guardando...';
            }
        );
    }
);
</script>
@endpush