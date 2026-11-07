@extends('admin.layouts.app')

@section('title', 'Unidades de medida')

@php
    $unidadEditar = $unidadEditar ?? null;
    $editando = $unidadEditar !== null;
    $buscar = $buscar ?? request('buscar', '');
@endphp

@push('styles')
<style>
    .um-page {
        --um-bg: #111216;
        --um-card: #191a20;
        --um-card-soft: #202127;
        --um-border: #30323b;
        --um-text: #f1f2f5;
        --um-muted: #999da8;
        --um-red: #e31b23;
        --um-red-light: #ff777d;
        --um-green: #39d98a;
        --um-yellow: #f5bd4f;
        --um-radius: 14px;

        display: grid;
        gap: 20px;
        color: var(--um-text);
    }

    .um-page *,
    .um-page *::before,
    .um-page *::after {
        box-sizing: border-box;
    }

    .um-header {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 24px 26px;
        overflow: hidden;
        border: 1px solid var(--um-border);
        border-radius: var(--um-radius);
        background:
            radial-gradient(circle at 100% 0%, rgba(227, 27, 35, .13), transparent 42%),
            linear-gradient(145deg, #1c1d23, #15161a);
    }

    .um-header::before {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        content: "";
        background: linear-gradient(90deg, var(--um-red), #8f1118 45%, transparent);
    }

    .um-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        color: var(--um-red-light);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.3px;
        text-transform: uppercase;
    }

    .um-eyebrow::before {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        content: "";
        background: var(--um-red);
        box-shadow: 0 0 10px rgba(227, 27, 35, .7);
    }

    .um-header h1 {
        margin: 0;
        color: #fff;
        font-size: clamp(24px, 3vw, 32px);
        font-weight: 800;
        letter-spacing: -.5px;
    }

    .um-header p {
        max-width: 650px;
        margin: 8px 0 0;
        color: var(--um-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .um-header-icon {
        width: 54px;
        height: 54px;
        flex: 0 0 54px;
        display: grid;
        place-items: center;
        border: 1px solid rgba(227, 27, 35, .25);
        border-radius: 14px;
        color: var(--um-red-light);
        background: rgba(227, 27, 35, .1);
    }

    .um-header-icon svg {
        width: 25px;
        height: 25px;
    }

    .um-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 13px 16px;
        border: 1px solid;
        border-radius: 10px;
        font-size: 12px;
        line-height: 1.6;
    }

    .um-alert strong {
        display: block;
        margin-bottom: 4px;
    }

    .um-alert ul {
        margin: 6px 0 0;
        padding-left: 19px;
    }

    .um-success {
        border-color: rgba(57, 217, 138, .25);
        color: #a6edc9;
        background: rgba(57, 217, 138, .08);
    }

    .um-error {
        border-color: rgba(227, 27, 35, .3);
        color: #ffb0b4;
        background: rgba(227, 27, 35, .08);
    }

    .um-alert-icon {
        width: 18px;
        height: 18px;
        flex: 0 0 18px;
        margin-top: 1px;
    }

    .um-card {
        min-width: 0;
        overflow: hidden;
        border: 1px solid var(--um-border);
        border-radius: var(--um-radius);
        background: var(--um-card);
        box-shadow: 0 8px 24px rgba(0, 0, 0, .15);
    }

    .um-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 20px 22px;
        border-bottom: 1px solid var(--um-border);
        background: linear-gradient(180deg, rgba(255, 255, 255, .018), transparent);
    }

    .um-card-heading {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .um-card-icon {
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        display: grid;
        place-items: center;
        border: 1px solid rgba(227, 27, 35, .2);
        border-radius: 10px;
        color: var(--um-red-light);
        background: rgba(227, 27, 35, .09);
    }

    .um-card-icon svg {
        width: 18px;
        height: 18px;
    }

    .um-card-header h2 {
        margin: 0;
        color: var(--um-text);
        font-size: 15px;
        font-weight: 750;
    }

    .um-card-header p {
        margin: 4px 0 0;
        color: var(--um-muted);
        font-size: 11px;
    }

    .um-card-body {
        padding: 22px;
    }

    .um-form {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        align-items: start;
        gap: 16px;
    }

    .um-field {
        min-width: 0;
    }

    .um-field label {
        display: block;
        margin-bottom: 7px;
        color: #d8d9df;
        font-size: 11px;
        font-weight: 700;
    }

    .um-required {
        color: var(--um-red-light);
    }

    .um-field input {
        width: 100%;
        min-height: 43px;
        padding: 10px 12px;
        border: 1px solid #383a44;
        border-radius: 8px;
        outline: none;
        color: var(--um-text);
        background: #121318;
        font: inherit;
        font-size: 12px;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .um-field input::placeholder {
        color: #6f727d;
    }

    .um-field input:focus {
        border-color: var(--um-red);
        box-shadow: 0 0 0 3px rgba(227, 27, 35, .12);
    }

    .um-field-error {
        display: block;
        margin-top: 6px;
        color: #ff9297;
        font-size: 10px;
    }

    .um-form-actions {
        display: flex;
        align-items: center;
        gap: 9px;
        grid-column: 1 / -1;
        padding-top: 2px;
    }

    .um-button {
        min-height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 8px 13px;
        border: 1px solid transparent;
        border-radius: 8px;
        cursor: pointer;
        font: inherit;
        font-size: 11px;
        font-weight: 750;
        text-decoration: none;
        white-space: nowrap;
        transition: background .18s ease, border-color .18s ease,
                    color .18s ease, transform .18s ease;
    }

    .um-button:hover {
        transform: translateY(-1px);
    }

    .um-button svg {
        width: 15px;
        height: 15px;
    }

    .um-primary {
        border-color: var(--um-red);
        color: #fff;
        background: var(--um-red);
        box-shadow: 0 5px 14px rgba(227, 27, 35, .2);
    }

    .um-primary:hover {
        border-color: #c9161e;
        background: #c9161e;
    }

    .um-secondary {
        border-color: #3a3c46;
        color: #d7d9df;
        background: #222329;
    }

    .um-secondary:hover {
        border-color: #555864;
        color: #fff;
        background: #2a2b32;
    }

    .um-danger {
        border-color: rgba(227, 27, 35, .35);
        color: #ff9da2;
        background: rgba(227, 27, 35, .1);
    }

    .um-danger:hover {
        border-color: var(--um-red);
        color: #fff;
        background: var(--um-red);
    }

    .um-list-tools {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 18px 22px;
        border-bottom: 1px solid var(--um-border);
    }

    .um-count {
        color: var(--um-muted);
        font-size: 11px;
        white-space: nowrap;
    }

    .um-count strong {
        color: var(--um-text);
    }

    .um-search {
        width: min(100%, 500px);
        display: flex;
        gap: 8px;
    }

    .um-search input {
        width: 100%;
        min-width: 0;
        min-height: 39px;
        padding: 9px 12px;
        border: 1px solid #383a44;
        border-radius: 8px;
        outline: none;
        color: var(--um-text);
        background: #121318;
        font: inherit;
        font-size: 11px;
    }

    .um-search input:focus {
        border-color: var(--um-red);
        box-shadow: 0 0 0 3px rgba(227, 27, 35, .1);
    }

    .um-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .um-table {
        width: 100%;
        min-width: 680px;
        border-collapse: collapse;
    }

    .um-table th,
    .um-table td {
        padding: 13px 16px;
        border-bottom: 1px solid #292b33;
        text-align: left;
        vertical-align: middle;
    }

    .um-table th {
        color: #9296a2;
        background: #17181d;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .8px;
        text-transform: uppercase;
    }

    .um-table td {
        color: #d7d9df;
        font-size: 11px;
    }

    .um-table tbody tr {
        transition: background .15s ease;
    }

    .um-table tbody tr:hover {
        background: rgba(255, 255, 255, .018);
    }

    .um-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .um-name {
        color: #f1f2f5;
        font-weight: 700;
    }

    .um-abbreviation {
        display: inline-flex;
        min-width: 38px;
        justify-content: center;
        padding: 5px 9px;
        border: 1px solid rgba(227, 27, 35, .2);
        border-radius: 7px;
        color: var(--um-red-light);
        background: rgba(227, 27, 35, .08);
        font-weight: 800;
    }

    .um-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 750;
    }

    .um-status::before {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        content: "";
    }

    .um-status.active {
        color: #8be3b7;
        background: rgba(57, 217, 138, .1);
    }

    .um-status.active::before {
        background: var(--um-green);
    }

    .um-status.inactive {
        color: #f1c775;
        background: rgba(245, 189, 79, .1);
    }

    .um-status.inactive::before {
        background: var(--um-yellow);
    }

    .um-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
    }

    .um-actions form {
        margin: 0;
    }

    .um-actions .um-button {
        min-height: 31px;
        padding: 6px 9px;
        font-size: 10px;
    }

    .um-empty {
        padding: 34px 18px !important;
        color: var(--um-muted) !important;
        text-align: center !important;
    }

    .um-empty strong {
        display: block;
        margin-bottom: 5px;
        color: #e5e6ea;
        font-size: 12px;
    }

    @media (max-width: 900px) {
        .um-form {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .um-list-tools {
            align-items: flex-start;
            flex-direction: column;
        }

        .um-search {
            width: 100%;
        }
    }

    @media (max-width: 600px) {
        .um-header {
            padding: 20px;
        }

        .um-header-icon {
            display: none;
        }

        .um-card-body {
            padding: 17px;
        }

        .um-card-header,
        .um-list-tools {
            padding: 16px 17px;
        }

        .um-form {
            grid-template-columns: 1fr;
        }

        .um-form-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .um-form-actions .um-button {
            width: 100%;
        }

        .um-search {
            flex-wrap: wrap;
        }

        .um-search .um-button {
            flex: 1;
        }
    }
</style>
@endpush

@section('content')
<div class="um-page">
    <header class="um-header">
        <div>
            <span class="um-eyebrow">Configuración de inventario</span>
            <h1>Unidades de medida</h1>
            <p>
                Registra y administra unidades como kilogramo, litro, mililitro
                y unidad para utilizarlas en tus productos.
            </p>
        </div>

        <div class="um-header-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 20 20 4"/>
                <path d="m7 17 2 2"/>
                <path d="m11 13 2 2"/>
                <path d="m15 9 2 2"/>
                <path d="M4 14v6h6"/>
                <path d="M14 4h6v6"/>
            </svg>
        </div>
    </header>

    @if (session('success'))
        <div class="um-alert um-success" role="status">
            <svg class="um-alert-icon" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="m5 12 4 4L19 6"/>
                <circle cx="12" cy="12" r="10"/>
            </svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="um-alert um-error" role="alert">
            <svg class="um-alert-icon" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 8v4"/>
                <path d="M12 16h.01"/>
            </svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="um-alert um-error" role="alert">
            <svg class="um-alert-icon" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 8v4"/>
                <path d="M12 16h.01"/>
            </svg>

            <div>
                <strong>Revisa los datos ingresados:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <section class="um-card">
        <div class="um-card-header">
            <div class="um-card-heading">
                <div class="um-card-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 5v14"/>
                        <path d="M5 12h14"/>
                    </svg>
                </div>

                <div>
                    <h2>{{ $editando ? 'Editar unidad' : 'Registrar unidad' }}</h2>
                    <p>Completa el nombre, la abreviatura y el tipo de unidad.</p>
                </div>
            </div>

            @if ($editando)
                <a
                    href="{{ route('admin.unidades-medida.index') }}"
                    class="um-button um-secondary"
                >
                    Cancelar edición
                </a>
            @endif
        </div>

        <div class="um-card-body">
            <form
                method="POST"
                action="{{ $editando
                    ? route('admin.unidades-medida.update', $unidadEditar)
                    : route('admin.unidades-medida.store') }}"
                class="um-form"
            >
                @csrf

                @if ($editando)
                    @method('PUT')
                @endif

                <div class="um-field">
                    <label for="nombre">
                        Nombre <span class="um-required">*</span>
                    </label>

                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        maxlength="80"
                        value="{{ old('nombre', $unidadEditar?->nombre ?? '') }}"
                        placeholder="Ejemplo: Kilogramo"
                        autocomplete="off"
                        required
                    >

                    @error('nombre')
                        <span class="um-field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="um-field">
                    <label for="abreviatura">
                        Abreviatura <span class="um-required">*</span>
                    </label>

                    <input
                        type="text"
                        id="abreviatura"
                        name="abreviatura"
                        maxlength="20"
                        value="{{ old('abreviatura', $unidadEditar?->abreviatura ?? '') }}"
                        placeholder="Ejemplo: kg"
                        autocomplete="off"
                        required
                    >

                    @error('abreviatura')
                        <span class="um-field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="um-field">
                    <label for="tipo">Tipo de unidad</label>

                    <input
                        type="text"
                        id="tipo"
                        name="tipo"
                        maxlength="40"
                        value="{{ old('tipo', $unidadEditar?->tipo ?? 'unidad') }}"
                        placeholder="Ejemplo: peso, volumen o unidad"
                        autocomplete="off"
                    >

                    @error('tipo')
                        <span class="um-field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="um-form-actions">
                    <button type="submit" class="um-button um-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             aria-hidden="true">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/>
                            <path d="M17 21v-8H7v8"/>
                            <path d="M7 3v5h8"/>
                        </svg>

                        {{ $editando ? 'Guardar cambios' : 'Crear unidad' }}
                    </button>

                    @if ($editando)
                        <a
                            href="{{ route('admin.unidades-medida.index') }}"
                            class="um-button um-secondary"
                        >
                            Cancelar
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <section class="um-card">
        <div class="um-card-header">
            <div class="um-card-heading">
                <div class="um-card-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 6h13"/>
                        <path d="M8 12h13"/>
                        <path d="M8 18h13"/>
                        <path d="M3 6h.01"/>
                        <path d="M3 12h.01"/>
                        <path d="M3 18h.01"/>
                    </svg>
                </div>

                <div>
                    <h2>Unidades registradas</h2>
                    <p>Consulta, edita o cambia el estado de cada unidad.</p>
                </div>
            </div>

            <span class="um-count">
                Total: <strong>{{ $unidades->count() }}</strong>
            </span>
        </div>

        <div class="um-list-tools">
            <form
                method="GET"
                action="{{ route('admin.unidades-medida.index') }}"
                class="um-search"
            >
                <input
                    type="search"
                    name="buscar"
                    value="{{ $buscar }}"
                    placeholder="Buscar por nombre, abreviatura o tipo..."
                    aria-label="Buscar unidades de medida"
                >

                <button type="submit" class="um-button um-primary">
                    Buscar
                </button>

                @if ($buscar)
                    <a
                        href="{{ route('admin.unidades-medida.index') }}"
                        class="um-button um-secondary"
                    >
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        <div class="um-table-wrap">
            <table class="um-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Abreviatura</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($unidades as $unidad)
                        <tr>
                            <td>
                                <span class="um-name">{{ $unidad->nombre }}</span>
                            </td>

                            <td>
                                <span class="um-abbreviation">
                                    {{ $unidad->abreviatura }}
                                </span>
                            </td>

                            <td>{{ $unidad->tipo ?: 'unidad' }}</td>

                            <td>
                                <span class="um-status {{ $unidad->estado ? 'active' : 'inactive' }}">
                                    {{ $unidad->estado ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>

                            <td>
                                <div class="um-actions">
                                    <a
                                        href="{{ route('admin.unidades-medida.index', ['editar' => $unidad->id]) }}"
                                        class="um-button um-secondary"
                                    >
                                        Editar
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.unidades-medida.estado', $unidad) }}"
                                        data-confirm="{{ $unidad->estado
                                            ? '¿Desactivar esta unidad? Los productos existentes conservarán esta unidad.'
                                            : '¿Activar nuevamente esta unidad?' }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit" class="um-button um-secondary">
                                            {{ $unidad->estado ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.unidades-medida.destroy', $unidad) }}"
                                        data-confirm="¿Eliminar permanentemente la unidad {{ $unidad->nombre }}? Si está asignada a productos, el sistema bloqueará la eliminación."
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="um-button um-danger">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="um-empty">
                                <strong>No se encontraron unidades</strong>
                                Prueba con otro término o registra una nueva unidad de medida.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const mensaje = form.dataset.confirm;

            if (mensaje && !window.confirm(mensaje)) {
                event.preventDefault();
            }
        });
    });
});
</script>
@endpush
@endsection