@extends('admin.layouts.app')

@section('title', 'Proveedores')

@section('content')

<div class="proveedor-page">

    <div class="page-header proveedor-page-header">

        <div class="page-title">

            <span class="eyebrow">Compras e inventario</span>

            <h1>Proveedores</h1>

            <p>Consulta y administra los proveedores de Minisúper NANOS.</p>

        </div>

        <a href="{{ route('admin.proveedores.create') }}" class="btn-primary proveedor-btn-principal">

            + Registrar proveedor

        </a>

    </div>

    @if (session('success'))

        <div class="proveedor-mensaje proveedor-mensaje-exito" role="status">

            <strong>Listo:</strong> {{ session('success') }}

        </div>

    @endif

    @if (session('error'))

        <div class="proveedor-mensaje proveedor-mensaje-error" role="alert">

            {{ session('error') }}

        </div>

    @endif

    @if ($errors->any())
        <div class="proveedor-mensaje proveedor-mensaje-error" role="alert">
            <strong>No se pudo completar la acción:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="proveedor-panel">

        <form

            method="GET"

            action="{{ route('admin.proveedores.index') }}"

            class="proveedor-filtros"

        >

            <label class="buscador-proveedor">

                <span class="sr-only">Buscar proveedores</span>

                <input

                    type="search"

                    name="buscar"

                    value="{{ $buscar }}"

                    placeholder="Buscar por código, razón social, NIT o celular"

                >

            </label>

            <label class="filtro-estado">

                <span class="sr-only">Filtrar por estado</span>

                <select name="estado">

                    <option value="">Todos los estados</option>

                    <option value="activo" @selected($estado === 'activo')>

                        Activos

                    </option>

                    <option value="inactivo" @selected($estado === 'inactivo')>

                        Inactivos

                    </option>

                </select>

            </label>

            <button type="submit" class="proveedor-btn proveedor-btn-buscar">

                Buscar

            </button>

            @if ($buscar || $estado)

                <a

                    href="{{ route('admin.proveedores.index') }}"

                    class="proveedor-btn proveedor-btn-limpiar"

                >

                    Limpiar filtros

                </a>

            @endif

        </form>

        <div class="proveedor-resumen">

            Mostrando {{ $proveedores->firstItem() ?? 0 }}–{{ $proveedores->lastItem() ?? 0 }}

            de {{ $proveedores->total() }} proveedores

        </div>

        <div class="proveedor-tabla-contenedor">

            <table class="proveedor-tabla">

                <thead>

                    <tr>

                        <th scope="col">Proveedor</th>

                        <th scope="col">Código</th>

                        <th scope="col">NIT / CI</th>

                        <th scope="col">Contacto principal</th>

                        <th scope="col">Productos</th>

                        <th scope="col">Estado</th>

                        <th scope="col">Acciones</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($proveedores as $proveedor)

                        @php

                            $contacto = $proveedor->contactos->firstWhere('es_principal', true)

                                ?? $proveedor->contactos->first();

                            $nombreVisible = $proveedor->nombre_comercial

                                ?: $proveedor->razon_social;

                            $estadoProveedor = $proveedor->estado ?? 'inactivo';

                        @endphp

                        <tr>

                            <td>

                                <div class="proveedor-identidad">

                                    <span class="proveedor-avatar" aria-hidden="true">

                                        {{ mb_strtoupper(mb_substr($nombreVisible, 0, 1)) }}

                                    </span>

                                    <span class="proveedor-nombre">

                                        <strong>{{ $nombreVisible }}</strong>

                                        @if ($proveedor->nombre_comercial)

                                            <small>{{ $proveedor->razon_social }}</small>

                                        @endif

                                    </span>

                                </div>

                            </td>

                            <td>

                                <span class="proveedor-codigo">

                                    {{ $proveedor->codigo }}

                                </span>

                            </td>

                            <td>

                                {{ $proveedor->nit ?: $proveedor->ci ?: '—' }}

                            </td>

                            <td>

                                @if ($contacto)

                                    <span class="contacto-dato">

                                        {{ $contacto->nombre }}

                                    </span>

                                    <small class="contacto-secundario">

                                        {{ $contacto->celular ?: $contacto->telefono ?: $contacto->correo ?: 'Sin datos de contacto' }}

                                    </small>

                                @else

                                    <span class="contacto-dato">

                                        {{ $proveedor->celular ?: $proveedor->telefono ?: '—' }}

                                    </span>

                                    @if ($proveedor->correo)

                                        <small class="contacto-secundario">

                                            {{ $proveedor->correo }}

                                        </small>

                                    @endif

                                @endif

                            </td>

                            <td>

                                <span class="proveedor-productos-cantidad">

                                    {{ $proveedor->productos_count ?? 0 }}

                                </span>

                            </td>

                            <td>

                                <span class="proveedor-estado {{ $estadoProveedor === 'activo' ? 'activo' : 'inactivo' }}">

                                    <span class="estado-punto"></span>

                                    {{ ucfirst($estadoProveedor) }}

                                </span>

                            </td>

                            <td>

                                <div class="proveedor-acciones">

                                    <a

                                        href="{{ route('admin.proveedores.edit', $proveedor) }}"

                                        class="proveedor-btn proveedor-btn-editar"

                                    >

                                        Editar

                                    </a>

                                    <form

                                        method="POST"

                                        action="{{ route('admin.proveedores.estado', $proveedor) }}"

                                        class="proveedor-confirm-form"
                                        data-confirm-type="estado"
                                        data-confirm-message="{{ $estadoProveedor === 'activo' ? 'Al desactivarlo, no podrás seleccionarlo en nuevos productos o compras hasta volver a activarlo.' : 'El proveedor volverá a estar disponible para productos y compras.' }}"

                                    >

                                        @csrf

                                        @method('PATCH')

                                        <button

                                            type="submit"

                                            class="proveedor-btn {{ $estadoProveedor === 'activo' ? 'proveedor-btn-baja' : 'proveedor-btn-activar' }}"

                                        >

                                            {{ $estadoProveedor === 'activo' ? 'Dar de baja' : 'Activar' }}

                                        </button>

                                    </form>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.proveedores.destroy', $proveedor) }}"
                                        class="proveedor-confirm-form"
                                        data-confirm-type="eliminar"
                                        data-confirm-message="Se eliminará permanentemente {{ $proveedor->razon_social }}. Esta acción no se puede deshacer. Si tiene productos asociados, el sistema bloqueará la eliminación."
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="proveedor-btn proveedor-btn-eliminar">Eliminar</button>
                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7">

                                <div class="proveedor-vacio">

                                    <strong>No se encontraron proveedores</strong>

                                    <span>Prueba cambiando la búsqueda o los filtros.</span>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if ($proveedores->hasPages())

            <div class="proveedor-paginacion">

                {{ $proveedores->links() }}

            </div>

        @endif

    </section>

</div>

<div class="confirmacion-overlay" id="modal-confirmacion-proveedor" aria-hidden="true">
    <section class="confirmacion-modal" role="dialog" aria-modal="true" aria-labelledby="titulo-confirmacion-proveedor">
        <div class="confirmacion-icono" aria-hidden="true">!</div>
        <h2 id="titulo-confirmacion-proveedor">Confirmar acción</h2>
        <p id="texto-confirmacion-proveedor"></p>
        <div class="confirmacion-acciones">
            <button type="button" class="proveedor-btn proveedor-btn-limpiar" id="cancelar-confirmacion-proveedor">Cancelar</button>
            <button type="button" class="proveedor-btn proveedor-btn-eliminar" id="aceptar-confirmacion-proveedor">Confirmar</button>
        </div>
    </section>
</div>

<style>

    .proveedor-page {

        color: #e5e7eb;

    }

    .proveedor-page-header {

        align-items: center;

        gap: 18px;

    }

    .proveedor-page-header .page-title h1 {

        color: #f3f4f6;

    }

    .proveedor-page-header .page-title p {

        color: #aeb4c0;

    }

    .proveedor-page .eyebrow {

        color: #ef4444;

    }

    .proveedor-btn-principal {

        flex-shrink: 0;

        padding: 12px 16px;

        border: 1px solid #c5222c;

        border-radius: 9px;

        background: #c5222c;

        color: #fff1f2;

        text-decoration: none;

        font-weight: 700;

    }

    .proveedor-btn-principal:hover,

    .proveedor-btn-buscar:hover {

        background: #a91d26;

    }

    .proveedor-mensaje {

        margin: 0 0 18px;

        padding: 14px 16px;

        border: 1px solid;

        border-radius: 10px;

    }

    .proveedor-mensaje-exito {

        border-color: #28634d;

        background: #153329;

        color: #c8f3df;

    }

    .proveedor-mensaje-error {

        border-color: #8f343b;

        background: #39191d;

        color: #ffd7d9;

    }

    .proveedor-panel {

        overflow: hidden;

        border: 1px solid #30323a;

        border-radius: 14px;

        background: #17181d;

        color: #e5e7eb;

    }

    .proveedor-filtros {

        display: flex;

        gap: 10px;

        flex-wrap: wrap;

        align-items: center;

        padding: 16px;

        border-bottom: 1px solid #30323a;

    }

    .buscador-proveedor {

        flex: 1 1 300px;

    }

    .proveedor-filtros input,

    .proveedor-filtros select {

        box-sizing: border-box;

        width: 100%;

        min-height: 42px;

        padding: 9px 12px;

        border: 1px solid #3b3e48;

        border-radius: 9px;

        outline: none;

        background: #22242b;

        color: #f3f4f6;

        font: inherit;

    }

    .proveedor-filtros input::placeholder {

        color: #a1a1aa;

    }

    .proveedor-filtros input:focus,

    .proveedor-filtros select:focus {

        border-color: #d3343e;

        box-shadow: 0 0 0 3px rgb(211 52 62 / 16%);

    }

    .proveedor-filtros select {

        min-width: 160px;

    }

    .proveedor-btn {

        display: inline-flex;

        min-height: 40px;

        box-sizing: border-box;

        align-items: center;

        justify-content: center;

        padding: 8px 12px;

        border: 1px solid #41434d;

        border-radius: 8px;

        background: #25262d;

        color: #e5e7eb;

        cursor: pointer;

        font: inherit;

        font-size: 13px;

        text-decoration: none;

        white-space: nowrap;

    }

    .proveedor-btn-buscar {

        border-color: #c5222c;

        background: #c5222c;

        color: #fff1f2;

        font-weight: 700;

    }

    .proveedor-btn-limpiar:hover,

    .proveedor-btn-editar:hover {

        border-color: #666a76;

        background: #30323a;

    }

    .proveedor-resumen {

        padding: 13px 16px;

        border-bottom: 1px solid #30323a;

        color: #aeb4c0;

        font-size: 13px;

    }

    .proveedor-tabla-contenedor {

        width: 100%;

        overflow-x: auto;

    }

    .proveedor-tabla {

        width: 100%;

        min-width: 900px;

        border-collapse: collapse;

        background: #17181d;

        color: #e5e7eb;

    }

    .proveedor-tabla th {

        padding: 13px 12px;

        border-bottom: 1px solid #30323a;

        background: #202127;

        color: #c7c9d1;

        font-size: 11px;

        font-weight: 700;

        text-align: left;

        text-transform: uppercase;

        white-space: nowrap;

    }

    .proveedor-tabla td {

        padding: 13px 12px;

        border-bottom: 1px solid #30323a;

        background: #17181d;

        color: #e5e7eb;

        font-size: 13px;

        vertical-align: middle;

    }

    .proveedor-tabla tbody tr:last-child td {

        border-bottom: 0;

    }

    .proveedor-tabla tbody tr:hover td {

        background: #22242b;

    }

    .proveedor-identidad {

        display: flex;

        min-width: 170px;

        align-items: center;

        gap: 10px;

    }

    .proveedor-avatar {

        display: inline-flex;

        width: 36px;

        height: 36px;

        flex: 0 0 36px;

        align-items: center;

        justify-content: center;

        border: 1px solid #522329;

        border-radius: 10px;

        background: #301a1d;

        color: #ff777f;

        font-weight: 800;

    }

    .proveedor-nombre strong,

    .proveedor-nombre small,

    .contacto-secundario {

        display: block;

    }

    .proveedor-nombre strong,

    .contacto-dato {

        color: #f3f4f6;

    }

    .proveedor-nombre small,

    .contacto-secundario {

        margin-top: 4px;

        color: #aeb4c0;

        font-size: 11px;

        overflow-wrap: anywhere;

    }

    .proveedor-codigo {

        display: inline-block;

        padding: 5px 8px;

        border: 1px solid #3b3e48;

        border-radius: 7px;

        background: #22242b;

        color: #d5d7de;

        font-family: monospace;

        font-size: 12px;

    }

    .proveedor-productos-cantidad {

        display: inline-flex;

        min-width: 28px;

        min-height: 26px;

        align-items: center;

        justify-content: center;

        border-radius: 8px;

        background: #22242b;

        color: #f3f4f6;

        font-weight: 700;

    }

    .proveedor-estado {

        display: inline-flex;

        align-items: center;

        gap: 6px;

        padding: 6px 9px;

        border: 1px solid;

        border-radius: 999px;

        font-size: 11px;

        font-weight: 700;

        white-space: nowrap;

    }

    .proveedor-estado.activo {

        border-color: #28634d;

        background: #153329;

        color: #a8e8c8;

    }

    .proveedor-estado.inactivo {

        border-color: #74383d;

        background: #39191d;

        color: #ffc0c4;

    }

    .estado-punto {

        width: 7px;

        height: 7px;

        border-radius: 50%;

        background: currentColor;

    }

    .proveedor-acciones {

        display: flex;

        align-items: center;

        gap: 7px;

    }

    .proveedor-acciones form {

        margin: 0;

    }

    .proveedor-btn-baja {

        border-color: #74383d;

        background: #301a1d;

        color: #ffb0b5;

    }

    .proveedor-btn-baja:hover {

        background: #492126;

    }

    .proveedor-btn-activar {

        border-color: #28634d;

        background: #153329;

        color: #a8e8c8;

    }

    .proveedor-btn-activar:hover {

        background: #1d4536;

    }

    .proveedor-vacio {

        display: flex;

        min-height: 120px;

        align-items: center;

        justify-content: center;

        flex-direction: column;

        gap: 7px;

        color: #e5e7eb;

        text-align: center;

    }

    .proveedor-vacio span {

        color: #aeb4c0;

        font-size: 13px;

    }

    .proveedor-paginacion {

        padding: 14px 16px;

        border-top: 1px solid #30323a;

        background: #17181d;

    }

    .proveedor-paginacion nav a,

    .proveedor-paginacion nav span {

        border-color: #3b3e48 !important;

        background: #22242b !important;

        color: #e5e7eb !important;

    }

    .proveedor-paginacion nav span[aria-current="page"] span {

        border-color: #c5222c !important;

        background: #c5222c !important;

        color: #fff1f2 !important;

    }

    .proveedor-btn-eliminar { border-color: #8f343b; background: #39191d; color: #ffd7d9; }
    .proveedor-btn-eliminar:hover { border-color: #b7444d; background: #512127; }
    .confirmacion-overlay {
        position: fixed; inset: 0; z-index: 9999; display: none;
        align-items: center; justify-content: center; padding: 20px;
        background: rgb(0 0 0 / 76%); backdrop-filter: blur(4px);
    }
    .confirmacion-overlay.abierto { display: flex; }
    .confirmacion-modal {
        width: min(100%, 440px); padding: 26px; border: 1px solid #41434d;
        border-radius: 16px; background: #17181d; color: #e5e7eb; text-align: center;
        box-shadow: 0 24px 80px rgb(0 0 0 / 55%);
    }
    .confirmacion-icono {
        display: grid; width: 48px; height: 48px; margin: 0 auto 14px; place-items: center;
        border: 1px solid #74383d; border-radius: 50%; background: #301a1d;
        color: #ffb0b5; font-size: 25px; font-weight: 800;
    }
    .confirmacion-modal h2 { margin: 0 0 10px; color: #f3f4f6; }
    .confirmacion-modal p { margin: 0; color: #aeb4c0; line-height: 1.6; }
    .confirmacion-acciones { display: flex; justify-content: center; gap: 10px; margin-top: 22px; }
    @media (max-width: 520px) {
        .confirmacion-acciones { flex-direction: column-reverse; }
        .confirmacion-acciones > * { width: 100%; }
    }

    .sr-only {

        position: absolute;

        width: 1px;

        height: 1px;

        overflow: hidden;

        clip: rect(0, 0, 0, 0);

        white-space: nowrap;

        clip-path: inset(50%);

    }

    @media (max-width: 700px) {

        .proveedor-page-header {

            align-items: stretch;

            flex-direction: column;

        }

        .proveedor-btn-principal {

            align-self: flex-start;

        }

        .proveedor-filtros {

            align-items: stretch;

            flex-direction: column;

        }

        .buscador-proveedor,

        .filtro-estado,

        .proveedor-filtros select,

        .proveedor-filtros button,

        .proveedor-filtros a {

            width: 100%;

        }

        .proveedor-acciones {

            align-items: stretch;

            flex-direction: column;

        }

    }

</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modal-confirmacion-proveedor');
        const title = document.getElementById('titulo-confirmacion-proveedor');
        const message = document.getElementById('texto-confirmacion-proveedor');
        const accept = document.getElementById('aceptar-confirmacion-proveedor');
        const cancel = document.getElementById('cancelar-confirmacion-proveedor');
        let formPendiente = null;

        function cerrarModal() {
            modal.classList.remove('abierto');
            modal.setAttribute('aria-hidden', 'true');
            formPendiente = null;
        }

        document.querySelectorAll('.proveedor-confirm-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                formPendiente = form;
                const esEliminar = form.dataset.confirmType === 'eliminar';
                title.textContent = esEliminar ? '¿Eliminar proveedor?' : '¿Cambiar estado?';
                message.textContent = form.dataset.confirmMessage || '¿Confirmas esta acción?';
                accept.textContent = esEliminar ? 'Sí, eliminar' : 'Sí, continuar';
                accept.classList.toggle('proveedor-btn-eliminar', esEliminar);
                modal.classList.add('abierto');
                modal.setAttribute('aria-hidden', 'false');
                cancel.focus();
            });
        });

        cancel.addEventListener('click', cerrarModal);
        modal.addEventListener('click', function (event) {
            if (event.target === modal) cerrarModal();
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal.classList.contains('abierto')) cerrarModal();
        });
        accept.addEventListener('click', function () {
            if (formPendiente) formPendiente.submit();
        });
    });
</script>
@endsection
