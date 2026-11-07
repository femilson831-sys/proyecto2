@extends('admin.layouts.app')

@php
    $editando = $almacen !== null;
@endphp

@section('title', $editando ? 'Editar almacén' : 'Registrar almacén')

@push('styles')
<style>
    .almacen-form-page {
        --f-bg: #17181c;
        --f-border: #30333d;
        --f-text: #e8e8eb;
        --f-muted: #92949c;
        display: flex;
        flex-direction: column;
        gap: 18px;
        max-width: 900px;
        margin: 0 auto;
        color: var(--f-text);
    }

    .almacen-form-header,
    .almacen-form-panel {
        border: 1px solid var(--f-border);
        border-radius: 14px;
        background: var(--f-bg);
    }

    .almacen-form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 20px 22px;
    }

    .almacen-form-header span {
        color: #ff858b;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .almacen-form-header h1 {
        margin: 7px 0;
        color: #fff;
        font-size: 24px;
    }

    .almacen-form-header p {
        margin: 0;
        color: var(--f-muted);
        font-size: 12px;
        line-height: 1.6;
    }

    .almacen-form-panel {
        padding: 22px;
    }

    .almacen-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 17px;
    }

    .almacen-campo.completo {
        grid-column: 1 / -1;
    }

    .almacen-campo label {
        display: block;
        margin-bottom: 7px;
        color: #e3e3e7;
        font-size: 12px;
        font-weight: 700;
    }

    .almacen-campo label em {
        color: #ff777d;
        font-style: normal;
    }

    .almacen-campo input,
    .almacen-campo textarea,
    .almacen-campo select {
        width: 100%;
        min-height: 42px;
        padding: 10px 11px;
        border: 1px solid #383b45;
        border-radius: 8px;
        outline: none;
        color: var(--f-text);
        background: #202126;
        font: inherit;
        font-size: 12px;
    }

    .almacen-campo textarea {
        min-height: 90px;
        resize: vertical;
    }

    .almacen-campo input:focus,
    .almacen-campo textarea:focus,
    .almacen-campo select:focus {
        border-color: #d83a42;
        box-shadow: 0 0 0 3px rgba(227, 27, 35, .12);
    }

    .almacen-ayuda {
        display: block;
        margin-top: 6px;
        color: var(--f-muted);
        font-size: 10px;
    }

    .almacen-error {
        margin-top: 5px;
        color: #ff9297;
        font-size: 11px;
    }

    .almacen-error-general {
        margin-bottom: 16px;
        padding: 12px 15px;
        border: 1px solid rgba(227, 27, 35, .35);
        border-radius: 9px;
        color: #ff9297;
        background: rgba(227, 27, 35, .08);
        font-size: 12px;
    }

    .almacen-error-general ul {
        margin: 7px 0 0;
        padding-left: 18px;
    }

    .almacen-form-acciones {
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        margin-top: 20px;
        padding-top: 17px;
        border-top: 1px solid #2b2d35;
    }

    .almacen-form-boton {
        display: inline-flex;
        min-height: 41px;
        align-items: center;
        justify-content: center;
        padding: 0 15px;
        border: 1px solid #3a3d47;
        border-radius: 8px;
        color: #e1e1e5;
        background: #25262c;
        font-size: 11px;
        font-weight: 750;
        text-decoration: none;
        cursor: pointer;
    }

    .almacen-form-boton.guardar {
        border-color: #e31b23;
        color: #fff;
        background: #c91820;
    }

    @media (max-width: 600px) {
        .almacen-form-header {
            align-items: stretch;
            flex-direction: column;
        }

        .almacen-form-header .almacen-form-boton {
            width: 100%;
        }

        .almacen-form-grid {
            grid-template-columns: 1fr;
        }

        .almacen-campo.completo {
            grid-column: auto;
        }

        .almacen-form-acciones {
            flex-direction: column-reverse;
        }

        .almacen-form-acciones .almacen-form-boton {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="almacen-form-page">
    <header class="almacen-form-header">
        <div>
            <span>Administración de inventario</span>
            <h1>{{ $editando ? 'Editar almacén' : 'Registrar almacén' }}</h1>
            <p>
                {{ $editando
                    ? 'Actualiza los datos del almacén seleccionado.'
                    : 'Completa los datos para agregar un almacén al sistema.' }}
            </p>
        </div>

        <a href="{{ route('admin.almacenes.index') }}" class="almacen-form-boton">
            ← Volver a almacenes
        </a>
    </header>

    <section class="almacen-form-panel">
        @if ($errors->any())
            <div class="almacen-error-general" role="alert">
                <strong>Revisa los datos ingresados:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ $editando
                ? route('admin.almacenes.update', $almacen->id)
                : route('admin.almacenes.store') }}"
            method="POST"
            autocomplete="off"
        >
            @csrf

            @if ($editando)
                @method('PUT')
            @endif

            <div class="almacen-form-grid">
                <div class="almacen-campo">
                    <label for="codigo">Código <em>*</em></label>
                    <input
                        type="text"
                        id="codigo"
                        name="codigo"
                        value="{{ old('codigo', $almacen->codigo ?? '') }}"
                        maxlength="30"
                        required
                        placeholder="Ejemplo: ALM-001"
                    >
                    @error('codigo')
                        <div class="almacen-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="almacen-campo">
                    <label for="nombre">Nombre del almacén <em>*</em></label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        value="{{ old('nombre', $almacen->nombre ?? '') }}"
                        maxlength="120"
                        required
                        placeholder="Ejemplo: Almacén principal"
                    >
                    @error('nombre')
                        <div class="almacen-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="almacen-campo completo">
                    <label for="descripcion">Descripción</label>
                    <textarea
                        id="descripcion"
                        name="descripcion"
                        maxlength="255"
                        placeholder="Describe brevemente el uso de este almacén"
                    >{{ old('descripcion', $almacen->descripcion ?? '') }}</textarea>
                    @error('descripcion')
                        <div class="almacen-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="almacen-campo completo">
                    <label for="direccion">Dirección</label>
                    <input
                        type="text"
                        id="direccion"
                        name="direccion"
                        value="{{ old('direccion', $almacen->direccion ?? '') }}"
                        maxlength="255"
                        placeholder="Dirección o referencia del almacén"
                    >
                    @error('direccion')
                        <div class="almacen-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="almacen-campo">
                    <label for="capacidad_m3">Capacidad en m</label>
                    <input
                        type="number"
                        id="capacidad_m3"
                        name="capacidad_m3"
                        value="{{ old('capacidad_m3', $almacen->capacidad_m3 ?? '') }}"
                        min="0"
                        max="999999999.999"
                        step="0.001"
                        placeholder="Ejemplo: 80"
                    >
                    <span class="almacen-ayuda">Campo opcional. Usa metros cúbicos.</span>
                    @error('capacidad_m3')
                        <div class="almacen-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="almacen-campo">
                    <label for="estado">Estado <em>*</em></label>
                    <select id="estado" name="estado" required>
                        <option value="activo" @selected(old('estado', $almacen->estado ?? 'activo') === 'activo')>
                            Activo
                        </option>
                        <option value="inactivo" @selected(old('estado', $almacen->estado ?? 'activo') === 'inactivo')>
                            Inactivo
                        </option>
                    </select>
                    @error('estado')
                        <div class="almacen-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="almacen-form-acciones">
                <a href="{{ route('admin.almacenes.index') }}" class="almacen-form-boton">
                    Cancelar
                </a>

                <button type="submit" class="almacen-form-boton guardar">
                    {{ $editando ? 'Guardar cambios' : 'Registrar almacén' }}
                </button>
            </div>
        </form>
    </section>
</div>
@endsection