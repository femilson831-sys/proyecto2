@extends('admin.layouts.app')

@section('title', 'Áreas y Cargos')

@push('styles')
<style>
    /* =========================================================
       ÁREAS Y CARGOS - NANOS
       ========================================================= */

    .ac-page {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        padding-bottom: 40px;
    }

    /* =========================================================
       CABECERA
       ========================================================= */

    .ac-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 25px;
        padding: 28px 30px;
        margin-bottom: 22px;

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

    .ac-hero::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 5px;
        height: 100%;
        background: #e31b23;
    }

    .ac-hero-left {
        display: flex;
        align-items: center;
        gap: 18px;
        position: relative;
        z-index: 2;
    }

    .ac-hero-icon {
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
    }

    .ac-hero-icon svg {
        width: 27px;
        height: 27px;
    }

    .ac-eyebrow {
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

    .ac-eyebrow::before {
        content: "";
        width: 17px;
        height: 2px;
        border-radius: 10px;
        background: #e31b23;
    }

    .ac-title {
        margin: 0;

        color: #f5f5f5;

        font-size: clamp(25px,3vw,34px);
        font-weight: 800;
        letter-spacing: -.7px;
    }

    .ac-description {
        max-width: 650px;
        margin: 7px 0 0;

        color: #858991;

        font-size: 13px;
        line-height: 1.6;
    }

    /* =========================================================
       ESTADÍSTICAS
       ========================================================= */

    .ac-stats {
        display: flex;
        align-items: center;
        gap: 10px;

        position: relative;
        z-index: 2;
    }

    .ac-stat {
        min-width: 105px;
        padding: 12px 15px;

        border-radius: 12px;

        border: 1px solid rgba(255,255,255,.06);
        background: rgba(0,0,0,.16);
    }

    .ac-stat span {
        display: block;

        margin-bottom: 4px;

        color: #6f7278;

        font-size: 9px;
        font-weight: 800;
        letter-spacing: .8px;
        text-transform: uppercase;
    }

    .ac-stat strong {
        color: #f1f1f1;
        font-size: 22px;
        font-weight: 800;
    }

    /* =========================================================
       MENSAJES
       ========================================================= */

    .ac-alert {
        display: flex;
        align-items: center;
        gap: 11px;

        padding: 14px 17px;
        margin-bottom: 20px;

        border-radius: 12px;

        font-size: 12px;
    }

    .ac-alert-success {
        color: #86efac;
        border: 1px solid rgba(34,197,94,.20);
        background: rgba(34,197,94,.07);
    }

    .ac-alert-error {
        color: #fca5a5;
        border: 1px solid rgba(239,68,68,.22);
        background: rgba(239,68,68,.07);
    }

    /* =========================================================
       COLUMNAS
       ========================================================= */

    .ac-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        align-items: start;
    }

    .ac-panel {
        overflow: hidden;

        border: 1px solid rgba(255,255,255,.065);
        border-radius: 16px;

        background: rgba(255,255,255,.018);

        box-shadow: 0 10px 35px rgba(0,0,0,.12);
    }

    .ac-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;

        padding: 20px 21px;

        border-bottom: 1px solid rgba(255,255,255,.055);
    }

    .ac-panel-heading {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ac-panel-icon {
        width: 39px;
        height: 39px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 11px;

        background: rgba(227,27,35,.09);
        color: #e31b23;
    }

    .ac-panel-icon svg {
        width: 19px;
        height: 19px;
    }

    .ac-panel-title {
        margin: 0 0 3px;

        color: #ededed;

        font-size: 15px;
        font-weight: 800;
    }

    .ac-panel-subtitle {
        color: #676b72;
        font-size: 10px;
    }

    /* =========================================================
       BOTONES
       ========================================================= */

    .ac-add-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;

        min-height: 36px;
        padding: 0 13px;

        border: 0;
        border-radius: 9px;

        color: #fff;
        background: linear-gradient(
            135deg,
            #e31b23,
            #b90f16
        );

        font-size: 10px;
        font-weight: 800;

        cursor: pointer;

        box-shadow: 0 7px 18px rgba(227,27,35,.15);

        transition: .2s ease;
    }

    .ac-add-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 9px 22px rgba(227,27,35,.24);
    }

    .ac-add-btn:focus-visible {
        outline: 2px solid #e31b23;
        outline-offset: 2px;
    }

    .ac-add-btn svg {
        width: 14px;
        height: 14px;
    }

    /* =========================================================
       LISTA
       ========================================================= */

    .ac-list {
        padding: 8px;
    }

    .ac-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;

        padding: 15px 13px;

        border-bottom: 1px solid rgba(255,255,255,.045);

        transition: background .2s ease;
    }

    .ac-item:last-child {
        border-bottom: 0;
    }

    .ac-item:hover {
        background: rgba(255,255,255,.018);
    }

    .ac-item-main {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ac-item-number {
        width: 34px;
        height: 34px;
        min-width: 34px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 9px;

        background: rgba(255,255,255,.035);
        border: 1px solid rgba(255,255,255,.055);

        color: #888c92;

        font-size: 10px;
        font-weight: 800;
    }

    .ac-item-info {
        min-width: 0;
    }

    .ac-item-name {
        display: block;

        margin-bottom: 4px;

        color: #e5e7eb;

        font-size: 12px;
        font-weight: 750;
    }

    .ac-item-description {
        display: block;

        max-width: 390px;

        color: #656970;

        font-size: 10px;
        line-height: 1.45;

        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ac-item-meta {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 7px;

        flex-wrap: wrap;
    }

    /* =========================================================
       BADGES
       ========================================================= */

    .ac-count {
        padding: 5px 8px;

        border-radius: 7px;

        color: #8b8f96;
        background: rgba(255,255,255,.035);

        font-size: 9px;
        font-weight: 700;
        white-space: nowrap;
    }

    .ac-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;

        padding: 5px 8px;

        border-radius: 7px;

        font-size: 9px;
        font-weight: 800;
        white-space: nowrap;
    }

    .ac-status::before {
        content: "";
        width: 5px;
        height: 5px;
        border-radius: 50%;
    }

    .ac-status.active {
        color: #86efac;
        background: rgba(34,197,94,.08);
    }

    .ac-status.active::before {
        background: #22c55e;
    }

    .ac-status.inactive {
        color: #fca5a5;
        background: rgba(239,68,68,.08);
    }

    .ac-status.inactive::before {
        background: #ef4444;
    }

    /* =========================================================
       ACCIONES
       ========================================================= */

    .ac-action {
        width: 30px;
        height: 30px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        padding: 0;

        border-radius: 8px;
        border: 1px solid rgba(255,255,255,.07);

        color: #8d9197;
        background: rgba(255,255,255,.025);

        cursor: pointer;

        transition: .2s ease;
    }

    .ac-action:hover {
        color: #fff;
        border-color: rgba(227,27,35,.25);
        background: rgba(227,27,35,.08);
    }

    .ac-action:focus-visible {
        outline: 2px solid #e31b23;
        outline-offset: 1px;
    }

    .ac-action svg {
        width: 14px;
        height: 14px;
    }

    .ac-action.on:hover {
        color: #86efac;
        border-color: rgba(34,197,94,.2);
        background: rgba(34,197,94,.07);
    }

    /* =========================================================
       MODAL
       ========================================================= */

    .ac-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;

        display: none;
        align-items: center;
        justify-content: center;

        padding: 20px;

        background: rgba(0,0,0,.72);
        backdrop-filter: blur(5px);

        opacity: 0;
        transition: opacity .2s ease;
    }

    .ac-modal.show {
        display: flex;
        opacity: 1;
    }

    .ac-modal-box {
        width: 100%;
        max-width: 520px;

        overflow: hidden;

        border: 1px solid rgba(255,255,255,.08);
        border-radius: 17px;

        background: #151618;

        box-shadow: 0 30px 90px rgba(0,0,0,.55);

        transform: translateY(10px) scale(.98);
        transition: transform .25s cubic-bezier(.16,1,.3,1);
    }

    .ac-modal.show .ac-modal-box {
        transform: translateY(0) scale(1);
    }

    .ac-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;

        padding: 19px 21px;

        border-bottom: 1px solid rgba(255,255,255,.06);
    }

    .ac-modal-header h3 {
        margin: 0;

        color: #f3f4f6;

        font-size: 15px;
    }

    .ac-modal-close {
        width: 31px;
        height: 31px;

        display: flex;
        align-items: center;
        justify-content: center;

        border: 0;
        border-radius: 8px;

        color: #777b81;
        background: rgba(255,255,255,.035);

        cursor: pointer;

        font-size: 18px;

        transition: .2s ease;
    }

    .ac-modal-close:hover {
        color: #fff;
        background: rgba(227,27,35,.10);
    }

    .ac-modal-close:focus-visible {
        outline: 2px solid #e31b23;
        outline-offset: 1px;
    }

    .ac-modal-body {
        padding: 21px;
    }

    .ac-form-group {
        margin-bottom: 17px;
    }

    .ac-form-group:last-child {
        margin-bottom: 0;
    }

    .ac-form-label {
        display: block;

        margin-bottom: 7px;

        color: #b8bbc0;

        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    .ac-required {
        color: #e31b23;
    }

    .ac-input,
    .ac-textarea {
        width: 100%;

        box-sizing: border-box;

        border: 1px solid rgba(255,255,255,.08);
        border-radius: 10px;

        outline: none;

        color: #e5e7eb;
        background: rgba(255,255,255,.025);

        font-family: inherit;
        font-size: 12px;

        transition: .2s ease;
    }

    .ac-input {
        min-height: 43px;
        padding: 0 13px;
    }

    .ac-textarea {
        min-height: 105px;
        padding: 12px 13px;

        resize: vertical;
    }

    .ac-input:focus,
    .ac-textarea:focus {
        border-color: rgba(227,27,35,.55);
        background: rgba(227,27,35,.025);
        box-shadow: 0 0 0 3px rgba(227,27,35,.06);
    }

    .ac-character-count {
        display: block;
        margin-top: 6px;

        color: #5f6268;

        text-align: right;
        font-size: 9px;
    }

    .ac-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 9px;

        padding: 16px 21px;

        border-top: 1px solid rgba(255,255,255,.06);
    }

    .ac-btn-secondary,
    .ac-btn-save {
        min-height: 39px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        padding: 0 17px;

        border-radius: 9px;

        font-size: 10px;
        font-weight: 800;

        cursor: pointer;

        transition: .2s ease;
    }

    .ac-btn-secondary {
        border: 1px solid rgba(255,255,255,.08);

        color: #999da3;
        background: rgba(255,255,255,.025);
    }

    .ac-btn-secondary:hover {
        color: #fff;
        background: rgba(255,255,255,.05);
    }

    .ac-btn-save {
        border: 0;

        color: #fff;
        background: linear-gradient(
            135deg,
            #e31b23,
            #b90f16
        );
    }

    .ac-btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 9px 22px rgba(227,27,35,.24);
    }

    .ac-btn-save:disabled {
        opacity: .6;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .ac-btn-secondary:focus-visible,
    .ac-btn-save:focus-visible {
        outline: 2px solid #e31b23;
        outline-offset: 2px;
    }

    /* =========================================================
       RESPONSIVE
       ========================================================= */

    @media (max-width: 1000px) {
        .ac-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 750px) {
        .ac-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .ac-stats {
            width: 100%;
        }

        .ac-stat {
            flex: 1;
        }
    }

    @media (max-width: 600px) {
        .ac-hero {
            padding: 22px 18px;
        }

        .ac-item {
            align-items: flex-start;
            flex-direction: column;
        }

        .ac-item-meta {
            width: 100%;
            justify-content: flex-start;
            padding-left: 46px;
        }

        .ac-item-description {
            white-space: normal;
        }
    }

    /* =========================================================
       MOVIMIENTO REDUCIDO
       ========================================================= */

    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: .01ms !important;
        }

        .ac-modal-box {
            transform: none !important;
        }
    }
</style>
@endpush


@section('content')

<div class="ac-page">

    {{-- =====================================================
         CABECERA
         ===================================================== --}}

    <div class="ac-hero">

        <div class="ac-hero-left">

            <div class="ac-hero-icon" aria-hidden="true">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                </svg>
            </div>

            <div>

                <span class="ac-eyebrow">
                    Organización
                </span>

                <h1 class="ac-title">
                    Áreas y Cargos
                </h1>

                <p class="ac-description">
                    Administra la estructura organizacional del
                    Minisúper NANOS y los cargos asignables a los empleados.
                </p>

            </div>

        </div>


        <div class="ac-stats" role="group" aria-label="Estadísticas">

            <div class="ac-stat">
                <span>Áreas</span>
                <strong>{{ $areas->count() }}</strong>
            </div>

            <div class="ac-stat">
                <span>Cargos</span>
                <strong>{{ $cargos->count() }}</strong>
            </div>

        </div>

    </div>


    {{-- =====================================================
         MENSAJES
         ===================================================== --}}

    @if(session('success'))

        <div class="ac-alert ac-alert-success" role="alert">
            ✓ {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="ac-alert ac-alert-error" role="alert">
            ⚠ {{ session('error') }}
        </div>

    @endif


    @if($errors->any())

        <div class="ac-alert ac-alert-error" role="alert">

            <div>
                <strong>
                    Revisa los siguientes datos:
                </strong>

                <ul style="margin:7px 0 0;padding-left:18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

        </div>

    @endif


    {{-- =====================================================
         ÁREAS / CARGOS
         ===================================================== --}}

    <div class="ac-grid">

        {{-- ==========================
             ÁREAS
             ========================== --}}

        <section class="ac-panel" aria-labelledby="tituloAreas">

            <div class="ac-panel-header">

                <div class="ac-panel-heading">

                    <div class="ac-panel-icon" aria-hidden="true">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M3 21h18"/>
                            <path d="M6 21V7l6-4 6 4v14"/>
                            <path d="M9 9h1"/>
                            <path d="M14 9h1"/>
                            <path d="M9 13h1"/>
                            <path d="M14 13h1"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="ac-panel-title" id="tituloAreas">
                            Áreas
                        </h2>

                        <span class="ac-panel-subtitle">
                            {{ $areas->count() }}
                            áreas registradas
                        </span>
                    </div>

                </div>


                <button
                    type="button"
                    class="ac-add-btn"
                    onclick="abrirNuevaArea()"
                    aria-label="Crear nueva área"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        aria-hidden="true"
                    >
                        <path d="M12 5v14"/>
                        <path d="M5 12h14"/>
                    </svg>

                    Nueva área
                </button>

            </div>


            <div class="ac-list">

                @forelse($areas as $area)

                    <div class="ac-item">

                        <div class="ac-item-main">

                            <div class="ac-item-number" aria-hidden="true">
                                {{ str_pad($area->id, 2, '0', STR_PAD_LEFT) }}
                            </div>

                            <div class="ac-item-info">

                                <span class="ac-item-name">
                                    {{ $area->nombre }}
                                </span>

                                <span
                                    class="ac-item-description"
                                    title="{{ $area->descripcion }}"
                                >
                                    {{
                                        $area->descripcion
                                        ?: 'Sin descripción registrada.'
                                    }}
                                </span>

                            </div>

                        </div>


                        <div class="ac-item-meta">

                            <span class="ac-count">
                                {{ $area->empleados_count }}
                                empleado{{ $area->empleados_count == 1 ? '' : 's' }}
                            </span>


                            <span
                                class="ac-status {{ $area->estado ? 'active' : 'inactive' }}"
                            >
                                {{ $area->estado ? 'Activa' : 'Inactiva' }}
                            </span>


                            {{-- EDITAR --}}
                            <button
                                type="button"
                                class="ac-action"
                                title="Editar área"
                                aria-label="Editar área {{ $area->nombre }}"
                                onclick='editarArea(@json([
                                    "id" => $area->id,
                                    "nombre" => $area->nombre,
                                    "descripcion" => $area->descripcion
                                ]))'
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    aria-hidden="true"
                                >
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                </svg>
                            </button>


                            {{-- ESTADO --}}
                            <form
                                method="POST"
                                action="{{ route('admin.areas.estado', $area) }}"
                                style="display:inline;"
                                onsubmit="return confirm(
                                    '{{ $area->estado
                                        ? '¿Deseas desactivar esta área?'
                                        : '¿Deseas activar esta área?' }}'
                                );"
                            >
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="ac-action {{ !$area->estado ? 'on' : '' }}"
                                    title="{{ $area->estado ? 'Desactivar' : 'Activar' }}"
                                    aria-label="{{ $area->estado ? 'Desactivar' : 'Activar' }} área {{ $area->nombre }}"
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        aria-hidden="true"
                                    >
                                        <path d="M18.36 6.64a9 9 0 1 1-12.73 0"/>
                                        <line x1="12" y1="2" x2="12" y2="12"/>
                                    </svg>
                                </button>

                            </form>

                        </div>

                    </div>

                @empty

                    <div
                        style="
                            padding:30px;
                            text-align:center;
                            color:#6f7379;
                            font-size:12px;
                        "
                    >
                        No existen áreas registradas.
                    </div>

                @endforelse

            </div>

        </section>


        {{-- ==========================
             CARGOS
             ========================== --}}

        <section class="ac-panel" aria-labelledby="tituloCargos">

            <div class="ac-panel-header">

                <div class="ac-panel-heading">

                    <div class="ac-panel-icon" aria-hidden="true">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="ac-panel-title" id="tituloCargos">
                            Cargos
                        </h2>

                        <span class="ac-panel-subtitle">
                            {{ $cargos->count() }}
                            cargos registrados
                        </span>
                    </div>

                </div>


                <button
                    type="button"
                    class="ac-add-btn"
                    onclick="abrirNuevoCargo()"
                    aria-label="Crear nuevo cargo"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        aria-hidden="true"
                    >
                        <path d="M12 5v14"/>
                        <path d="M5 12h14"/>
                    </svg>

                    Nuevo cargo
                </button>

            </div>


            <div class="ac-list">

                @forelse($cargos as $cargo)

                    <div class="ac-item">

                        <div class="ac-item-main">

                            <div class="ac-item-number" aria-hidden="true">
                                {{ str_pad($cargo->id, 2, '0', STR_PAD_LEFT) }}
                            </div>

                            <div class="ac-item-info">

                                <span class="ac-item-name">
                                    {{ $cargo->nombre }}
                                </span>

                                <span
                                    class="ac-item-description"
                                    title="{{ $cargo->descripcion }}"
                                >
                                    {{
                                        $cargo->descripcion
                                        ?: 'Sin descripción registrada.'
                                    }}
                                </span>

                            </div>

                        </div>


                        <div class="ac-item-meta">

                            <span class="ac-count">
                                {{ $cargo->empleados_count }}
                                empleado{{ $cargo->empleados_count == 1 ? '' : 's' }}
                            </span>


                            <span
                                class="ac-status {{ $cargo->estado ? 'active' : 'inactive' }}"
                            >
                                {{ $cargo->estado ? 'Activo' : 'Inactivo' }}
                            </span>


                            {{-- EDITAR --}}
                            <button
                                type="button"
                                class="ac-action"
                                title="Editar cargo"
                                aria-label="Editar cargo {{ $cargo->nombre }}"
                                onclick='editarCargo(@json([
                                    "id" => $cargo->id,
                                    "nombre" => $cargo->nombre,
                                    "descripcion" => $cargo->descripcion
                                ]))'
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    aria-hidden="true"
                                >
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                </svg>
                            </button>


                            {{-- ESTADO --}}
                            <form
                                method="POST"
                                action="{{ route('admin.cargos.estado', $cargo) }}"
                                style="display:inline;"
                                onsubmit="return confirm(
                                    '{{ $cargo->estado
                                        ? '¿Deseas desactivar este cargo?'
                                        : '¿Deseas activar este cargo?' }}'
                                );"
                            >
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="ac-action {{ !$cargo->estado ? 'on' : '' }}"
                                    title="{{ $cargo->estado ? 'Desactivar' : 'Activar' }}"
                                    aria-label="{{ $cargo->estado ? 'Desactivar' : 'Activar' }} cargo {{ $cargo->nombre }}"
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        aria-hidden="true"
                                    >
                                        <path d="M18.36 6.64a9 9 0 1 1-12.73 0"/>
                                        <line x1="12" y1="2" x2="12" y2="12"/>
                                    </svg>
                                </button>

                            </form>

                        </div>

                    </div>

                @empty

                    <div
                        style="
                            padding:30px;
                            text-align:center;
                            color:#6f7379;
                            font-size:12px;
                        "
                    >
                        No existen cargos registrados.
                    </div>

                @endforelse

            </div>

        </section>

    </div>

</div>


{{-- =========================================================
     MODAL ÁREA
     ========================================================= --}}

<div
    class="ac-modal"
    id="modalArea"
    role="dialog"
    aria-modal="true"
    aria-labelledby="tituloModalArea"
    onclick="cerrarModalFondo(event, 'modalArea')"
>

    <div class="ac-modal-box">

        <form
            method="POST"
            id="formArea"
            action="{{ route('admin.areas.store') }}"
        >

            @csrf

            <div id="areaMethod"></div>


            <div class="ac-modal-header">

                <h3 id="tituloModalArea">
                    Nueva área
                </h3>

                <button
                    type="button"
                    class="ac-modal-close"
                    onclick="cerrarModal('modalArea')"
                    aria-label="Cerrar modal"
                >
                    ×
                </button>

            </div>


            <div class="ac-modal-body">

                <div class="ac-form-group">

                    <label
                        class="ac-form-label"
                        for="areaNombre"
                    >
                        Nombre
                        <span class="ac-required">*</span>
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        id="areaNombre"
                        class="ac-input"
                        maxlength="100"
                        required
                        autocomplete="off"
                        placeholder="Ej. Administración"
                    >

                </div>


                <div class="ac-form-group">

                    <label
                        class="ac-form-label"
                        for="areaDescripcion"
                    >
                        Descripción
                    </label>

                    <textarea
                        name="descripcion"
                        id="areaDescripcion"
                        class="ac-textarea"
                        maxlength="255"
                        placeholder="Describe la función del área..."
                        oninput="actualizarContador(this, 'areaContador')"
                    ></textarea>

                    <span
                        class="ac-character-count"
                        id="areaContador"
                    >
                        0 / 255
                    </span>

                </div>

            </div>


            <div class="ac-modal-footer">

                <button
                    type="button"
                    class="ac-btn-secondary"
                    onclick="cerrarModal('modalArea')"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="ac-btn-save"
                    id="btnGuardarArea"
                >
                    Guardar área
                </button>

            </div>

        </form>

    </div>

</div>


{{-- =========================================================
     MODAL CARGO
     ========================================================= --}}

<div
    class="ac-modal"
    id="modalCargo"
    role="dialog"
    aria-modal="true"
    aria-labelledby="tituloModalCargo"
    onclick="cerrarModalFondo(event, 'modalCargo')"
>

    <div class="ac-modal-box">

        <form
            method="POST"
            id="formCargo"
            action="{{ route('admin.cargos.store') }}"
        >

            @csrf

            <div id="cargoMethod"></div>


            <div class="ac-modal-header">

                <h3 id="tituloModalCargo">
                    Nuevo cargo
                </h3>

                <button
                    type="button"
                    class="ac-modal-close"
                    onclick="cerrarModal('modalCargo')"
                    aria-label="Cerrar modal"
                >
                    ×
                </button>

            </div>


            <div class="ac-modal-body">

                <div class="ac-form-group">

                    <label
                        class="ac-form-label"
                        for="cargoNombre"
                    >
                        Nombre
                        <span class="ac-required">*</span>
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        id="cargoNombre"
                        class="ac-input"
                        maxlength="100"
                        required
                        autocomplete="off"
                        placeholder="Ej. Supervisor de caja"
                    >

                </div>


                <div class="ac-form-group">

                    <label
                        class="ac-form-label"
                        for="cargoDescripcion"
                    >
                        Descripción
                    </label>

                    <textarea
                        name="descripcion"
                        id="cargoDescripcion"
                        class="ac-textarea"
                        maxlength="255"
                        placeholder="Describe las funciones del cargo..."
                        oninput="actualizarContador(this, 'cargoContador')"
                    ></textarea>

                    <span
                        class="ac-character-count"
                        id="cargoContador"
                    >
                        0 / 255
                    </span>

                </div>

            </div>


            <div class="ac-modal-footer">

                <button
                    type="button"
                    class="ac-btn-secondary"
                    onclick="cerrarModal('modalCargo')"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="ac-btn-save"
                    id="btnGuardarCargo"
                >
                    Guardar cargo
                </button>

            </div>

        </form>

    </div>

</div>


<script>
    /*
    |--------------------------------------------------------------------------
    | RUTAS BASE
    |--------------------------------------------------------------------------
    */

    const areaStoreUrl  = @json(route('admin.areas.store'));
    const cargoStoreUrl = @json(route('admin.cargos.store'));
    const areaUpdateBase  = @json(url('/admin/areas'));
    const cargoUpdateBase = @json(url('/admin/cargos'));


    /*
    |--------------------------------------------------------------------------
    | UTILIDADES
    |--------------------------------------------------------------------------
    */

    function actualizarContador(campo, id) {
        const contador = document.getElementById(id);
        if (contador) {
            contador.textContent = campo.value.length + ' / 255';
        }
    }


    function setContador(valor, id) {
        const contador = document.getElementById(id);
        if (contador) {
            contador.textContent = (valor?.length ?? 0) + ' / 255';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | MODALES
    |--------------------------------------------------------------------------
    */

    let ultimoFoco = null;

    function abrirModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;

        // Guardamos el elemento que tenía el foco para devolverlo al cerrar
        ultimoFoco = document.activeElement;

        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        // Foco al primer campo interactivo
        requestAnimationFrame(() => {
            const primerCampo = modal.querySelector(
                'input:not([type="hidden"]), textarea, select'
            );
            if (primerCampo) {
                primerCampo.focus();
            } else {
                const cerrar = modal.querySelector('.ac-modal-close');
                if (cerrar) cerrar.focus();
            }
        });
    }


    function cerrarModal(id) {
        const modal = document.getElementById(id);
        if (!modal || !modal.classList.contains('show')) return;

        modal.classList.remove('show');

        // Solo restauramos el scroll si ya no hay otro modal abierto
        const algunModalAbierto = document.querySelector('.ac-modal.show');
        if (!algunModalAbierto) {
            document.body.style.overflow = '';
        }

        // Devolvemos el foco al elemento anterior
        if (ultimoFoco && typeof ultimoFoco.focus === 'function') {
            ultimoFoco.focus();
        }
        ultimoFoco = null;
    }


    function cerrarModalFondo(event, id) {
        if (event.target.id === id) {
            cerrarModal(id);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | NUEVA ÁREA
    |--------------------------------------------------------------------------
    */

    function abrirNuevaArea() {
        const form = document.getElementById('formArea');
        form.action = areaStoreUrl;

        document.getElementById('areaMethod').innerHTML = '';
        document.getElementById('tituloModalArea').textContent = 'Nueva área';

        const nombre = document.getElementById('areaNombre');
        const descripcion = document.getElementById('areaDescripcion');

        nombre.value = '';
        descripcion.value = '';
        setContador('', 'areaContador');

        abrirModal('modalArea');
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR ÁREA
    |--------------------------------------------------------------------------
    */

    function editarArea(area) {
        const form = document.getElementById('formArea');
        form.action = areaUpdateBase + '/' + area.id;

        document.getElementById('areaMethod').innerHTML =
            '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('tituloModalArea').textContent = 'Editar área';

        document.getElementById('areaNombre').value = area.nombre ?? '';
        document.getElementById('areaDescripcion').value = area.descripcion ?? '';

        setContador(area.descripcion, 'areaContador');

        abrirModal('modalArea');
    }


    /*
    |--------------------------------------------------------------------------
    | NUEVO CARGO
    |--------------------------------------------------------------------------
    */

    function abrirNuevoCargo() {
        const form = document.getElementById('formCargo');
        form.action = cargoStoreUrl;

        document.getElementById('cargoMethod').innerHTML = '';
        document.getElementById('tituloModalCargo').textContent = 'Nuevo cargo';

        const nombre = document.getElementById('cargoNombre');
        const descripcion = document.getElementById('cargoDescripcion');

        nombre.value = '';
        descripcion.value = '';
        setContador('', 'cargoContador');

        abrirModal('modalCargo');
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR CARGO
    |--------------------------------------------------------------------------
    */

    function editarCargo(cargo) {
        const form = document.getElementById('formCargo');
        form.action = cargoUpdateBase + '/' + cargo.id;

        document.getElementById('cargoMethod').innerHTML =
            '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('tituloModalCargo').textContent = 'Editar cargo';

        document.getElementById('cargoNombre').value = cargo.nombre ?? '';
        document.getElementById('cargoDescripcion').value = cargo.descripcion ?? '';

        setContador(cargo.descripcion, 'cargoContador');

        abrirModal('modalCargo');
    }


    /*
    |--------------------------------------------------------------------------
    | PREVENIR DOBLE SUBMIT
    |--------------------------------------------------------------------------
    */

    document.getElementById('formArea')?.addEventListener('submit', function () {
        const btn = document.getElementById('btnGuardarArea');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Guardando...';
        }
    });

    document.getElementById('formCargo')?.addEventListener('submit', function () {
        const btn = document.getElementById('btnGuardarCargo');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Guardando...';
        }
    });


    /*
    |--------------------------------------------------------------------------
    | ESC PARA CERRAR (solo el modal visible)
    |--------------------------------------------------------------------------
    */

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;

        const modalAbierto = document.querySelector('.ac-modal.show');
        if (modalAbierto) {
            cerrarModal(modalAbierto.id);
        }
    });


    /*
    |--------------------------------------------------------------------------
    | FOCUS TRAP BÁSICO (Tab dentro del modal)
    |--------------------------------------------------------------------------
    */

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Tab') return;

        const modalAbierto = document.querySelector('.ac-modal.show');
        if (!modalAbierto) return;

        const focusables = modalAbierto.querySelectorAll(
            'button, [href], input:not([type="hidden"]), select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        if (!focusables.length) return;

        const primero = focusables[0];
        const ultimo  = focusables[focusables.length - 1];

        if (event.shiftKey && document.activeElement === primero) {
            event.preventDefault();
            ultimo.focus();
        } else if (!event.shiftKey && document.activeElement === ultimo) {
            event.preventDefault();
            primero.focus();
        }
    });
</script>

@endsection