@php

    $proveedor = $proveedor ?? null;



    $contactosFormulario = old(

        'contactos',

        $proveedor

            ? $proveedor->contactos->toArray()

            : [[]]

    );



    $contactosFormulario = array_values($contactosFormulario ?: [[]]);



    if (empty($contactosFormulario)) {

        $contactosFormulario = [[]];

    }

@endphp



@if ($errors->any())

    <div class="proveedor-form-error" role="alert">

        <strong>Revisa los datos del formulario:</strong>

        <ul>

            @foreach ($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif



<section class="proveedor-form-card" aria-labelledby="datos-proveedor-titulo">

    <div class="form-seccion-encabezado">

        <div>

            <h2 id="datos-proveedor-titulo">Datos del proveedor</h2>

            <p>Completa la información comercial y de contacto.</p>

        </div>

    </div>



    <div class="proveedor-form-grid">

        <label class="proveedor-campo">

            <span>Razón social <span class="campo-requerido">*</span></span>

            <input

                type="text"

                name="razon_social"

                maxlength="180"

                required

                autocomplete="organization"

                value="{{ old('razon_social', $proveedor->razon_social ?? '') }}"

            >

            @error('razon_social')

                <small class="campo-error">{{ $message }}</small>

            @enderror

        </label>



        <label class="proveedor-campo">

            <span>Nombre comercial</span>

            <input

                type="text"

                name="nombre_comercial"

                maxlength="180"

                value="{{ old('nombre_comercial', $proveedor->nombre_comercial ?? '') }}"

            >

            @error('nombre_comercial')

                <small class="campo-error">{{ $message }}</small>

            @enderror

        </label>



        <label class="proveedor-campo">

            <span>NIT</span>

            <input

                type="text"

                name="nit"

                maxlength="30"

                value="{{ old('nit', $proveedor->nit ?? '') }}"

            >

            @error('nit')

                <small class="campo-error">{{ $message }}</small>

            @enderror

        </label>



        <label class="proveedor-campo">

            <span>Cédula de identidad</span>

            <input

                type="text"

                name="ci"

                maxlength="30"

                value="{{ old('ci', $proveedor->ci ?? '') }}"

            >

            @error('ci')

                <small class="campo-error">{{ $message }}</small>

            @enderror

        </label>



        <label class="proveedor-campo">

            <span>Teléfono</span>

            <input

                type="tel"

                name="telefono"

                maxlength="30"

                autocomplete="tel"

                value="{{ old('telefono', $proveedor->telefono ?? '') }}"

            >

            @error('telefono')

                <small class="campo-error">{{ $message }}</small>

            @enderror

        </label>



        <label class="proveedor-campo">

            <span>Celular</span>

            <input

                type="tel"

                name="celular"

                maxlength="30"

                autocomplete="tel"

                value="{{ old('celular', $proveedor->celular ?? '') }}"

            >

            @error('celular')

                <small class="campo-error">{{ $message }}</small>

            @enderror

        </label>



        <label class="proveedor-campo">

            <span>Correo</span>

            <input

                type="email"

                name="correo"

                maxlength="150"

                autocomplete="email"

                value="{{ old('correo', $proveedor->correo ?? '') }}"

            >

            @error('correo')

                <small class="campo-error">{{ $message }}</small>

            @enderror

        </label>



        <label class="proveedor-campo">

            <span>Ciudad</span>

            <input

                type="text"

                name="ciudad"

                maxlength="100"

                autocomplete="address-level2"

                value="{{ old('ciudad', $proveedor->ciudad ?? '') }}"

            >

            @error('ciudad')

                <small class="campo-error">{{ $message }}</small>

            @enderror

        </label>



        <label class="proveedor-campo campo-ancho-completo">

            <span>Dirección</span>

            <input

                type="text"

                name="direccion"

                maxlength="255"

                autocomplete="street-address"

                value="{{ old('direccion', $proveedor->direccion ?? '') }}"

            >

            @error('direccion')

                <small class="campo-error">{{ $message }}</small>

            @enderror

        </label>



        <label class="proveedor-campo">

            <span>Estado <span class="campo-requerido">*</span></span>

            <select name="estado" required>

                <option

                    value="activo"

                    @selected(old('estado', $proveedor->estado ?? 'activo') === 'activo')

                >

                    Activo

                </option>

                <option

                    value="inactivo"

                    @selected(old('estado', $proveedor->estado ?? '') === 'inactivo')

                >

                    Inactivo

                </option>

            </select>

            @error('estado')

                <small class="campo-error">{{ $message }}</small>

            @enderror

        </label>



        <label class="proveedor-campo campo-ancho-completo">

            <span>Observaciones</span>

            <textarea

                name="observaciones"

                rows="4"

                maxlength="5000"

                placeholder="Notas importantes sobre este proveedor"

            >{{ old('observaciones', $proveedor->observaciones ?? '') }}</textarea>

            @error('observaciones')

                <small class="campo-error">{{ $message }}</small>

            @enderror

        </label>

    </div>

</section>



<section class="proveedor-form-card" aria-labelledby="contactos-proveedor-titulo">

    <div class="form-seccion-encabezado contactos-encabezado">

        <div>

            <h2 id="contactos-proveedor-titulo">Personas de contacto</h2>

            <p>Agrega a las personas con las que te comunicas en este proveedor.</p>

        </div>



        <button type="button" class="form-btn form-btn-agregar" id="agregar-contacto">

            + Añadir contacto

        </button>

    </div>



    <div id="contactos-container">

        @foreach ($contactosFormulario as $i => $contacto)

            <article class="contacto-form-card">

                @if (!empty($contacto['id']))

                    <input

                        type="hidden"

                        name="contactos[{{ $i }}][id]"

                        value="{{ $contacto['id'] }}"

                    >

                @endif



                <div class="contacto-form-encabezado">

                    <strong>

                        Contacto <span class="numero-contacto">{{ $i + 1 }}</span>

                    </strong>



                    <button type="button" class="quitar-contacto">

                        Quitar

                    </button>

                </div>



                <div class="proveedor-form-grid">

                    <label class="proveedor-campo">

                        <span>Nombre</span>

                        <input

                            type="text"

                            name="contactos[{{ $i }}][nombre]"

                            maxlength="150"

                            autocomplete="name"

                            value="{{ $contacto['nombre'] ?? '' }}"

                        >

                        @error("contactos.$i.nombre")

                            <small class="campo-error">{{ $message }}</small>

                        @enderror

                    </label>



                    <label class="proveedor-campo">

                        <span>Cargo</span>

                        <input

                            type="text"

                            name="contactos[{{ $i }}][cargo]"

                            maxlength="100"

                            value="{{ $contacto['cargo'] ?? '' }}"

                        >

                        @error("contactos.$i.cargo")

                            <small class="campo-error">{{ $message }}</small>

                        @enderror

                    </label>



                    <label class="proveedor-campo">

                        <span>Teléfono</span>

                        <input

                            type="tel"

                            name="contactos[{{ $i }}][telefono]"

                            maxlength="30"

                            value="{{ $contacto['telefono'] ?? '' }}"

                        >

                        @error("contactos.$i.telefono")

                            <small class="campo-error">{{ $message }}</small>

                        @enderror

                    </label>



                    <label class="proveedor-campo">

                        <span>Celular</span>

                        <input

                            type="tel"

                            name="contactos[{{ $i }}][celular]"

                            maxlength="30"

                            value="{{ $contacto['celular'] ?? '' }}"

                        >

                        @error("contactos.$i.celular")

                            <small class="campo-error">{{ $message }}</small>

                        @enderror

                    </label>



                    <label class="proveedor-campo">

                        <span>Correo</span>

                        <input

                            type="email"

                            name="contactos[{{ $i }}][correo]"

                            maxlength="150"

                            value="{{ $contacto['correo'] ?? '' }}"

                        >

                        @error("contactos.$i.correo")

                            <small class="campo-error">{{ $message }}</small>

                        @enderror

                    </label>



                    <label class="contacto-principal">

                        <input

                            type="checkbox"

                            name="contactos[{{ $i }}][es_principal]"

                            value="1"

                            @checked(!empty($contacto['es_principal']))

                        >

                        <span>Contacto principal</span>

                    </label>

                </div>

            </article>

        @endforeach

    </div>

</section>



<div class="proveedor-form-acciones">
    <a href="{{ route('admin.proveedores.index') }}" class="form-btn form-btn-cancelar">Cancelar</a>
    @if ($proveedor && $proveedor->exists)
        <button type="button" class="form-btn form-btn-eliminar" id="abrir-modal-eliminar-proveedor">Eliminar proveedor</button>
    @endif
    <button type="submit" class="form-btn form-btn-guardar">{{ $proveedor ? 'Guardar cambios' : 'Registrar proveedor' }}</button>
</div>
@if ($proveedor && $proveedor->exists)
    <div class="confirmacion-overlay" id="modal-eliminar-proveedor" aria-hidden="true">
        <section class="confirmacion-modal" role="dialog" aria-modal="true" aria-labelledby="titulo-eliminar-proveedor">
            <div class="confirmacion-icono" aria-hidden="true">!</div>
            <h2 id="titulo-eliminar-proveedor">¿Eliminar proveedor?</h2>
            <p>Se eliminará permanentemente <strong>{{ $proveedor->razon_social }}</strong>. Esta acción no se puede deshacer. Si tiene productos asociados, el sistema impedirá la eliminación.</p>
            <div class="confirmacion-acciones">
                <button type="button" class="form-btn form-btn-cancelar" id="cancelar-eliminar-proveedor">Cancelar</button>
                <button type="button" class="form-btn form-btn-eliminar" id="confirmar-eliminar-proveedor">Sí, eliminar</button>
            </div>
        </section>
    </div>
@endif



<template id="plantilla-contacto">

    <article class="contacto-form-card">

        <div class="contacto-form-encabezado">

            <strong>

                Contacto <span class="numero-contacto"></span>

            </strong>



            <button type="button" class="quitar-contacto">

                Quitar

            </button>

        </div>



        <div class="proveedor-form-grid">

            <label class="proveedor-campo">

                <span>Nombre</span>

                <input

                    type="text"

                    name="contactos[__INDEX__][nombre]"

                    maxlength="150"

                    autocomplete="name"

                >

            </label>



            <label class="proveedor-campo">

                <span>Cargo</span>

                <input

                    type="text"

                    name="contactos[__INDEX__][cargo]"

                    maxlength="100"

                >

            </label>



            <label class="proveedor-campo">

                <span>Teléfono</span>

                <input

                    type="tel"

                    name="contactos[__INDEX__][telefono]"

                    maxlength="30"

                >

            </label>



            <label class="proveedor-campo">

                <span>Celular</span>

                <input

                    type="tel"

                    name="contactos[__INDEX__][celular]"

                    maxlength="30"

                >

            </label>



            <label class="proveedor-campo">

                <span>Correo</span>

                <input

                    type="email"

                    name="contactos[__INDEX__][correo]"

                    maxlength="150"

                >

            </label>



            <label class="contacto-principal">

                <input

                    type="checkbox"

                    name="contactos[__INDEX__][es_principal]"

                    value="1"

                >

                <span>Contacto principal</span>

            </label>

        </div>

    </article>

</template>



<style>

    .proveedor-form-card {

        margin-bottom: 18px;

        padding: 22px;

        border: 1px solid #30323a;

        border-radius: 14px;

        background: #17181d;

        color: #e5e7eb;

    }



    .form-seccion-encabezado,

    .contacto-form-encabezado {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 14px;

    }



    .proveedor-form-card h2 {

        margin: 0;

        color: #f3f4f6;

        font-size: 18px;

    }



    .proveedor-form-card p {

        margin: 5px 0 0;

        color: #aeb4c0;

        font-size: 14px;

    }



    .proveedor-form-grid {

        display: grid;

        grid-template-columns: repeat(2, minmax(0, 1fr));

        gap: 16px;

        margin-top: 18px;

    }



    .proveedor-campo {

        display: flex;

        min-width: 0;

        flex-direction: column;

        gap: 7px;

        color: #e5e7eb;

        font-size: 13px;

        font-weight: 600;

    }



    .proveedor-campo input,

    .proveedor-campo select,

    .proveedor-campo textarea {

        box-sizing: border-box;

        width: 100%;

        min-height: 42px;

        padding: 10px 12px;

        border: 1px solid #3b3e48;

        border-radius: 9px;

        outline: none;

        background: #22242b;

        color: #f3f4f6;

        font: inherit;

        font-weight: 400;

    }



    .proveedor-campo input::placeholder,

    .proveedor-campo textarea::placeholder {

        color: #a1a1aa;

    }



    .proveedor-campo select option {

        background: #22242b;

        color: #f3f4f6;

    }



    .proveedor-campo textarea {

        min-height: 95px;

        resize: vertical;

    }



    .proveedor-campo input:focus,

    .proveedor-campo select:focus,

    .proveedor-campo textarea:focus {

        border-color: #d3343e;

        box-shadow: 0 0 0 3px rgb(211 52 62 / 16%);

    }



    .campo-ancho-completo {

        grid-column: 1 / -1;

    }



    .campo-requerido,

    .campo-error {

        color: #ff8d8d;

    }



    .campo-error {

        font-size: 12px;

        font-weight: 500;

    }



    .proveedor-form-error {

        margin-bottom: 18px;

        padding: 14px 16px;

        border: 1px solid #8f343b;

        border-radius: 10px;

        background: #39191d;

        color: #ffd7d9;

    }



    .proveedor-form-error ul {

        margin: 8px 0 0;

        padding-left: 20px;

    }



    .contactos-encabezado {

        margin-bottom: 16px;

    }



    .contacto-form-card {

        margin-top: 12px;

        padding: 16px;

        border: 1px solid #30323a;

        border-radius: 11px;

        background: #202127;

    }



    .contacto-form-encabezado {

        color: #f3f4f6;

    }



    .contacto-form-card .proveedor-form-grid {

        margin-top: 12px;

    }



    .contacto-principal {

        display: flex;

        min-height: 42px;

        align-items: center;

        align-self: end;

        gap: 9px;

        color: #e5e7eb;

        font-size: 13px;

        font-weight: 600;

    }



    .contacto-principal input {

        width: 17px;

        height: 17px;

        accent-color: #c5222c;

    }



    .form-btn {

        display: inline-flex;

        min-height: 42px;

        box-sizing: border-box;

        align-items: center;

        justify-content: center;

        padding: 10px 14px;

        border: 1px solid #41434d;

        border-radius: 9px;

        cursor: pointer;

        font: inherit;

        font-size: 13px;

        font-weight: 700;

        text-decoration: none;

    }



    .form-btn-agregar,

    .form-btn-guardar {

        border-color: #c5222c;

        background: #c5222c;

        color: #fff1f2;

    }



    .form-btn-agregar:hover,

    .form-btn-guardar:hover {

        background: #a91d26;

    }



    .form-btn-cancelar {

        background: #22242b;

        color: #e5e7eb;

    }



    .form-btn-cancelar:hover {

        background: #30323a;

    }



    .quitar-contacto {

        padding: 6px 8px;

        border: 0;

        background: transparent;

        color: #ff8d8d;

        cursor: pointer;

        font: inherit;

        font-size: 13px;

    }



    .quitar-contacto:hover {

        color: #ffc1c1;

        text-decoration: underline;

    }



    .proveedor-form-acciones {

        display: flex;

        justify-content: flex-end;

        gap: 10px;

        margin: 18px 0;

    }



    @media (max-width: 700px) {

        .proveedor-form-card {

            padding: 16px;

        }



        .proveedor-form-grid {

            grid-template-columns: 1fr;

        }



        .campo-ancho-completo {

            grid-column: auto;

        }



        .contactos-encabezado {

            align-items: stretch;

            flex-direction: column;

        }



        .proveedor-form-acciones {

            flex-direction: column-reverse;

        }



        .proveedor-form-acciones > * {

            width: 100%;

        }

    }


    .form-btn-eliminar { border-color: #8f1d26; background: #461b20; color: #ffd9dc; }
    .form-btn-eliminar:hover { background: #6f1b23; }
    .confirmacion-overlay { position: fixed; inset: 0; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 20px; background: rgb(0 0 0 / 72%); backdrop-filter: blur(4px); }
    .confirmacion-overlay.abierto { display: flex; }
    .confirmacion-modal { width: min(100%, 440px); padding: 26px; border: 1px solid #41434d; border-radius: 16px; background: #191a20; color: #f4f4f5; text-align: center; box-shadow: 0 24px 80px rgb(0 0 0 / 55%); }
    .confirmacion-icono { display: grid; width: 48px; height: 48px; margin: 0 auto 14px; place-items: center; border: 1px solid #8f1d26; border-radius: 50%; background: #461b20; color: #ffd9dc; font-size: 25px; font-weight: 800; }
    .confirmacion-modal h2 { margin: 0 0 10px; color: #fff; }
    .confirmacion-modal p { margin: 0; color: #c5c6ce; line-height: 1.6; }
    .confirmacion-modal strong { color: #fff; }
    .confirmacion-acciones { display: flex; justify-content: center; gap: 10px; margin-top: 22px; }
    @media (max-width: 520px) { .confirmacion-acciones { flex-direction: column-reverse; } .confirmacion-acciones > * { width: 100%; } }

</style>



<script>

    document.addEventListener('DOMContentLoaded', function () {

        const container = document.getElementById('contactos-container');

        const template = document.getElementById('plantilla-contacto');

        const addButton = document.getElementById('agregar-contacto');



        if (!container || !template || !addButton) {

            return;

        }



        let nextIndex = {{ count($contactosFormulario) }};



        function actualizarNumeros() {

            container.querySelectorAll('.contacto-form-card').forEach((card, index) => {

                const numero = card.querySelector('.numero-contacto');



                if (numero) {

                    numero.textContent = index + 1;

                }

            });

        }



        addButton.addEventListener('click', function () {

            const html = template.innerHTML.replaceAll('__INDEX__', nextIndex++);

            container.insertAdjacentHTML('beforeend', html);

            actualizarNumeros();



            container

                .querySelector('.contacto-form-card:last-child input[name$="[nombre]"]')

                ?.focus();

        });



        container.addEventListener('change', function (event) {

            const checkbox = event.target;



            if (!checkbox.matches('input[type="checkbox"][name$="[es_principal]"]')) {

                return;

            }



            if (checkbox.checked) {

                container

                    .querySelectorAll('input[type="checkbox"][name$="[es_principal]"]')

                    .forEach(function (otroCheckbox) {

                        if (otroCheckbox !== checkbox) {

                            otroCheckbox.checked = false;

                        }

                    });

            }

        });



        container.addEventListener('click', function (event) {

            const button = event.target.closest('.quitar-contacto');



            if (!button) {

                return;

            }



            button.closest('.contacto-form-card')?.remove();

            actualizarNumeros();

        });



        actualizarNumeros();

        const openDeleteButton = document.getElementById('abrir-modal-eliminar-proveedor');
        const deleteModal = document.getElementById('modal-eliminar-proveedor');
        const cancelDeleteButton = document.getElementById('cancelar-eliminar-proveedor');
        const confirmDeleteButton = document.getElementById('confirmar-eliminar-proveedor');
        function cerrarModalEliminar() {
            deleteModal?.classList.remove('abierto');
            deleteModal?.setAttribute('aria-hidden', 'true');
        }
        openDeleteButton?.addEventListener('click', function () {
            deleteModal?.classList.add('abierto');
            deleteModal?.setAttribute('aria-hidden', 'false');
            cancelDeleteButton?.focus();
        });
        cancelDeleteButton?.addEventListener('click', cerrarModalEliminar);
        deleteModal?.addEventListener('click', function (event) {
            if (event.target === deleteModal) cerrarModalEliminar();
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') cerrarModalEliminar();
        });
        confirmDeleteButton?.addEventListener('click', function () {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = @json($proveedor && $proveedor->exists ? route('admin.proveedores.destroy', $proveedor) : '');
            const csrf = document.createElement('input');
            csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = @json(csrf_token());
            const method = document.createElement('input');
            method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
            form.append(csrf, method);
            document.body.appendChild(form);
            form.submit();
        });
    });
</script>