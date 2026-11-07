@extends('admin.layouts.app')

@section('title', 'Crear tarea')

@push('styles')
    @include('admin.tareas._styles')
@endpush

@section('content')
<div class="ta-page">
    <header class="ta-header">
        <div>
            <span class="ta-eyebrow">Organización del personal</span>
            <h1>Crear tarea</h1>
            <p>Selecciona la actividad, los productos y el empleado responsable.</p>
        </div>

        <a href="{{ route('admin.tareas.index') }}" class="ta-button">
            ← Volver a tareas
        </a>
    </header>

    @if ($errors->any())
        <div class="ta-error" role="alert">
            <strong>Revisa los siguientes datos:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('admin.tareas.store') }}"
        class="ta-page"
        id="tareaForm"
    >
        @csrf

        <section class="ta-card">
            <h2 class="ta-section-title">
                <b>1</b> Información de la tarea
            </h2>

            <div class="ta-grid">
                <label class="ta-field">
                    Tipo de tarea

                    <select name="tipo_tarea_id" id="tipoTarea" required>
                        <option value="">Selecciona una actividad</option>

                        @foreach ($tipos as $tipo)
                            <option
                                value="{{ $tipo->id }}"
                                data-clave="{{ $tipo->clave }}"
                                @selected(old('tipo_tarea_id') == $tipo->id)
                            >
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="ta-field">
                    Prioridad

                    <select name="prioridad" required>
                        @foreach ([
                            'normal' => 'Normal',
                            'baja' => 'Baja',
                            'alta' => 'Alta',
                            'urgente' => 'Urgente',
                        ] as $valor => $texto)
                            <option
                                value="{{ $valor }}"
                                @selected(old('prioridad', 'normal') === $valor)
                            >
                                {{ $texto }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="ta-field ta-full">
                    Título de la tarea

                    <input
                        type="text"
                        name="titulo"
                        value="{{ old('titulo') }}"
                        maxlength="180"
                        required
                        placeholder="Ejemplo: Conteo de bebidas en la ubicación AB1"
                    >
                </label>

                <label class="ta-field">
                    Empleado responsable

                    <select name="usuario_id" required>
                        <option value="">Selecciona un responsable</option>

                        @foreach ($usuarios as $usuario)
                            <option
                                value="{{ $usuario->id }}"
                                @selected(old('usuario_id') == $usuario->id)
                            >
                                {{ $usuario->nombre }} — {{ $usuario->username }}
                            </option>
                        @endforeach
                    </select>

                    <span class="ta-help">
                        Se muestran personas con una cuenta activa en el sistema.
                    </span>
                </label>

                <label class="ta-field">
                    Fecha y hora límite — opcional

                    <input
                        type="datetime-local"
                        name="fecha_limite"
                        value="{{ old('fecha_limite') }}"
                    >
                </label>
            </div>
        </section>

        <section class="ta-card">
            <h2 class="ta-section-title">
                <b>2</b> Lugar de trabajo
            </h2>

            <p class="ta-help" id="tipoExplicacion" style="margin-bottom:18px;">
                Selecciona un tipo de tarea para configurar el formulario.
            </p>

            <div class="ta-grid">
                <label class="ta-field">
                    <span id="origenAlmacenLabel">Almacén</span>

                    <select
                        name="almacen_origen_id"
                        id="almacenOrigen"
                        required
                    >
                        <option value="">Selecciona un almacén</option>

                        @foreach ($almacenes as $almacen)
                            <option value="{{ $almacen->id }}">
                                {{ $almacen->codigo }} — {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="ta-field">
                    <span id="origenUbicacionLabel">Ubicación</span>

                    <select
                        name="ubicacion_origen_id"
                        id="ubicacionOrigen"
                        required
                    >
                        <option value="">Primero selecciona un almacén</option>
                    </select>
                </label>

                <div class="ta-full" id="opcionDestino" hidden>
                    <label class="ta-field">
                        <span id="destinoTipoLabel">Destino de la operación</span>

                        <select name="destino_tipo" id="destinoTipo"></select>
                    </label>
                </div>

                <div class="ta-full" id="proveedorPanel" hidden>
                    <label class="ta-field">
                        Proveedor al que se devolverán los productos

                        <select name="proveedor_id" id="proveedor">
                            <option value="">Selecciona un proveedor</option>

                            @foreach ($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}">
                                    {{ $proveedor->nombre_comercial ?: $proveedor->razon_social }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div class="ta-full" id="destinoPanel" hidden>
                    <div class="ta-grid">
                        <label class="ta-field">
                            Almacén de destino

                            <select
                                name="almacen_destino_id"
                                id="almacenDestino"
                            >
                                <option value="">Selecciona un almacén</option>

                                @foreach ($almacenes as $almacen)
                                    <option value="{{ $almacen->id }}">
                                        {{ $almacen->codigo }} — {{ $almacen->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label class="ta-field">
                            Ubicación de destino

                            <select
                                name="ubicacion_destino_id"
                                id="ubicacionDestino"
                            >
                                <option value="">Primero selecciona un almacén</option>
                            </select>
                        </label>

                        <div class="ta-note ta-full" id="espacioDestino">
                            Selecciona una ubicación para consultar el espacio disponible.
                        </div>
                    </div>
                </div>

                <div class="ta-full" id="motivoPanel" hidden>
                    <label class="ta-field">
                        Motivo de la devolución o del retiro

                        <textarea
                            name="motivo"
                            id="motivo"
                            maxlength="1000"
                            placeholder="Explica por qué debe realizarse esta operación."
                        >{{ old('motivo') }}</textarea>
                    </label>
                </div>

                <div class="ta-full" id="revisionPanel" hidden>
                    <div class="ta-grid">
                        <label class="ta-field ta-full">
                            ¿Qué se revisará?

                            <select name="alcance_revision" id="alcanceRevision">
                                <option value="productos">Productos y lotes específicos</option>
                                <option value="ubicacion">La ubicación completa</option>
                            </select>
                        </label>

                        <div class="ta-full">
                            <p class="ta-help" style="margin-bottom:10px;">
                                Aspectos que debe verificar el empleado:
                            </p>

                            <div class="ta-checks">
                                @foreach ([
                                    'empaque' => 'Estado del empaque',
                                    'vencimiento' => 'Vencimientos',
                                    'limpieza' => 'Limpieza y orden',
                                    'etiquetado' => 'Etiquetado',
                                    'conservacion' => 'Conservación',
                                ] as $valor => $texto)
                                    <label class="ta-check">
                                        <input
                                            type="checkbox"
                                            name="controles[]"
                                            value="{{ $valor }}"
                                            @checked(in_array($valor, old('controles', []), true))
                                        >
                                        {{ $texto }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="ta-card" id="productosPanel" hidden>
            <h2 class="ta-section-title">
                <b>3</b> Productos y lotes
            </h2>

            <p class="ta-help" id="productosAyuda" style="margin-bottom:16px;"></p>

            <div class="ta-note" id="sinExistencias" hidden style="margin-bottom:16px;">
                Esta ubicación no tiene productos y lotes disponibles para esta actividad.
                Verifica primero sus existencias en Almacenes.
            </div>

            <div class="ta-items" id="itemsContainer"></div>

            <button type="button" class="ta-button" id="agregarProducto">
                + Agregar producto
            </button>
        </section>

        <section class="ta-card">
            <h2 class="ta-section-title">
                <b>4</b> Instrucciones y evidencia
            </h2>

            <div class="ta-grid">
                <label class="ta-field ta-full">
                    Instrucciones para el empleado

                    <textarea
                        name="descripcion"
                        maxlength="5000"
                        required
                        placeholder="Indica qué debe realizar, qué debe comprobar y qué debe informar."
                    >{{ old('descripcion') }}</textarea>
                </label>

                <label class="ta-check ta-full">
                    <input
                        type="checkbox"
                        name="requiere_evidencia"
                        value="1"
                        @checked(old('requiere_evidencia'))
                    >
                    Solicitar fotografías como evidencia de la tarea
                </label>
            </div>
        </section>

        <div class="ta-actions">
            <a href="{{ route('admin.tareas.index') }}" class="ta-button">
                Cancelar
            </a>

            <button type="submit" class="ta-button primary">
                Crear y asignar tarea
            </button>
        </div>
    </form>
</div>

<template id="itemTemplate">
    <article class="ta-item">
        <div class="ta-item-head">
            <strong class="item-title"></strong>

            <button type="button" class="ta-button danger item-remove">
                Quitar
            </button>
        </div>

        <div class="ta-grid">
            <label class="ta-field">
                Producto
                <select class="item-producto" required></select>
            </label>

            <label class="ta-field">
                Lote
                <select class="item-lote" required></select>
            </label>

            <div class="ta-stock ta-full item-stock">
                Selecciona un producto y un lote.
            </div>

            <label class="ta-field item-cantidad-panel">
                Cantidad solicitada
                <input
                    type="number"
                    class="item-cantidad"
                    inputmode="decimal"
                >
            </label>

            <label class="ta-field item-porcentaje-panel">
                Espacio previsto en destino (%) — opcional

                <input
                    type="number"
                    class="item-porcentaje"
                    min="0.01"
                    max="100"
                    step="0.01"
                >
            </label>
        </div>
    </article>
</template>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ubicaciones = {{ \Illuminate\Support\Js::from($ubicaciones) }};
    const existencias = {{ \Illuminate\Support\Js::from($existencias) }};

    const anterior = {{ \Illuminate\Support\Js::from([
        'almacen_origen_id' => old('almacen_origen_id', ''),
        'ubicacion_origen_id' => old('ubicacion_origen_id', ''),
        'almacen_destino_id' => old('almacen_destino_id', ''),
        'ubicacion_destino_id' => old('ubicacion_destino_id', ''),
        'destino_tipo' => old('destino_tipo', ''),
        'proveedor_id' => old('proveedor_id', ''),
        'alcance_revision' => old('alcance_revision', 'productos'),
        'items' => old('items', [[]]),
    ]) }};

    const tipo = document.getElementById('tipoTarea');
    const origenAlmacen = document.getElementById('almacenOrigen');
    const origenUbicacion = document.getElementById('ubicacionOrigen');
    const destinoAlmacen = document.getElementById('almacenDestino');
    const destinoUbicacion = document.getElementById('ubicacionDestino');
    const destinoTipo = document.getElementById('destinoTipo');
    const alcanceRevision = document.getElementById('alcanceRevision');
    const contenedor = document.getElementById('itemsContainer');
    const plantilla = document.getElementById('itemTemplate');

    let items = Array.isArray(anterior.items) && anterior.items.length
        ? anterior.items
        : [{}];

    const mensajes = {
        reposicion: 'Indica desde dónde se recogerán los productos y qué ubicación se abastecerá.',
        traslado: 'Selecciona el origen y el destino. Puedes trasladar entre ubicaciones del mismo almacén o entre almacenes.',
        devolucion: 'Selecciona los productos y lotes que se devolverán y el destino correspondiente.',
        inventario: 'Selecciona los productos y lotes que deberá contar el empleado en esta ubicación.',
        revision: 'Indica si se revisarán productos específicos o la ubicación completa.',
        retiro: 'Selecciona los productos que deben retirarse y si irán a cuarentena o se solicitará su baja.',
        preparacion: 'Indica qué productos deben reunirse y en qué ubicación deben quedar preparados.',
    };

    const cantidadFormato = new Intl.NumberFormat('es-BO', {
        maximumFractionDigits: 3
    });

    function clave() {
        return tipo.selectedOptions[0]?.dataset.clave || '';
    }

    function esMovimiento() {
        return [
            'reposicion',
            'traslado',
            'devolucion',
            'retiro',
            'preparacion'
        ].includes(clave());
    }

    function requiereDestino() {
        return ['reposicion', 'traslado', 'preparacion'].includes(clave())
            || (clave() === 'devolucion' && destinoTipo.value === 'interno')
            || (clave() === 'retiro' && destinoTipo.value === 'cuarentena');
    }

    function mostrar(id, visible) {
        const panel = document.getElementById(id);
        panel.hidden = !visible;

        panel.querySelectorAll('input, select, textarea, button').forEach(control => {
            control.disabled = !visible;
        });
    }

    function llenarUbicaciones(select, almacenId, seleccionado = '') {
        select.replaceChildren(
            new Option('Selecciona una ubicación', '')
        );

        ubicaciones
            .filter(u => String(u.almacen_id) === String(almacenId))
            .forEach(u => {
                select.add(new Option(
                    `${u.codigo}${u.nombre ? ' — ' + u.nombre : ''}`,
                    u.id
                ));
            });

        select.value = String(seleccionado || '');
    }

    function stockOrigen() {
        return existencias.filter(fila => {
            const corresponde = String(fila.ubicacion_id)
                === String(origenUbicacion.value);

            return corresponde
                && (!esMovimiento() || Number(fila.cantidad) > 0);
        });
    }

    function actualizarEspacio() {
        const seleccionada = ubicaciones.find(
            u => String(u.id) === String(destinoUbicacion.value)
        );

        const texto = document.getElementById('espacioDestino');

        if (!seleccionada) {
            texto.textContent = 'Selecciona una ubicación para consultar el espacio disponible.';
            return;
        }

        const ocupado = Number(seleccionada.ocupado || 0);
        const libre = Math.max(0, 100 - ocupado);

        texto.textContent = `Ocupado: ${cantidadFormato.format(ocupado)} % · Libre: ${cantidadFormato.format(libre)} %`;
    }

    function configurarOpcionesDestino(restaurar = false) {
        const actual = restaurar
            ? anterior.destino_tipo
            : destinoTipo.value;

        destinoTipo.replaceChildren();

        if (clave() === 'devolucion') {
            destinoTipo.add(new Option('Devolución a una ubicación interna', 'interno'));
            destinoTipo.add(new Option('Devolución al proveedor', 'proveedor'));
        }

        if (clave() === 'retiro') {
            destinoTipo.add(new Option('Separar en una ubicación de cuarentena', 'cuarentena'));
            destinoTipo.add(new Option('Solicitar baja del inventario', 'baja'));
        }

        if ([...destinoTipo.options].some(opcion => opcion.value === actual)) {
            destinoTipo.value = actual;
        }
    }

    function configurarFormulario() {
        const actividad = clave();
        const tieneDestino = requiereDestino();

        document.getElementById('tipoExplicacion').textContent =
            mensajes[actividad] || 'Selecciona un tipo de tarea para configurar el formulario.';

        document.getElementById('origenAlmacenLabel').textContent =
            esMovimiento() ? 'Almacén de origen' : 'Almacén donde se realizará la tarea';

        document.getElementById('origenUbicacionLabel').textContent =
            esMovimiento() ? 'Ubicación de origen' : 'Ubicación que se contará o revisará';

        mostrar('opcionDestino', ['devolucion', 'retiro'].includes(actividad));
        mostrar('destinoPanel', tieneDestino);
        mostrar(
            'proveedorPanel',
            actividad === 'devolucion' && destinoTipo.value === 'proveedor'
        );
        mostrar('motivoPanel', ['devolucion', 'retiro'].includes(actividad));
        mostrar('revisionPanel', actividad === 'revision');

        destinoAlmacen.required = tieneDestino;
        destinoUbicacion.required = tieneDestino;

        document.getElementById('proveedor').required =
            actividad === 'devolucion' && destinoTipo.value === 'proveedor';

        document.getElementById('motivo').required =
            ['devolucion', 'retiro'].includes(actividad);

        const necesitaProductos = Boolean(actividad)
            && !(actividad === 'revision' && alcanceRevision.value === 'ubicacion');

        mostrar('productosPanel', necesitaProductos);

        document.getElementById('productosAyuda').textContent =
            actividad === 'inventario'
                ? 'Aquí seleccionas qué debe contarse. La cantidad física la registrará el empleado al realizar la tarea.'
                : actividad === 'revision'
                    ? 'Agrega los productos y lotes que debe inspeccionar el empleado.'
                    : 'Selecciona productos y lotes del origen e indica las cantidades solicitadas.';

        renderizarItems();
        actualizarEspacio();
    }

    function renderizarItems() {
        contenedor.replaceChildren();

        const filas = stockOrigen();
        const productos = new Map();

        filas.forEach(fila => {
            productos.set(String(fila.producto_id), fila);
        });

        const panelVisible = !document.getElementById('productosPanel').hidden;

        document.getElementById('sinExistencias').hidden = filas.length > 0;
        document.getElementById('agregarProducto').disabled =
            !panelVisible || filas.length === 0;

        items.forEach((item, indice) => {
            const nodo = plantilla.content.firstElementChild.cloneNode(true);

            const productoSelect = nodo.querySelector('.item-producto');
            const loteSelect = nodo.querySelector('.item-lote');
            const cantidad = nodo.querySelector('.item-cantidad');
            const porcentaje = nodo.querySelector('.item-porcentaje');

            nodo.querySelector('.item-title').textContent = `Producto ${indice + 1}`;

            productoSelect.name = `items[${indice}][producto_id]`;
            loteSelect.name = `items[${indice}][lote_id]`;
            cantidad.name = `items[${indice}][cantidad_solicitada]`;
            porcentaje.name = `items[${indice}][porcentaje_ocupacion_destino]`;

            productoSelect.add(new Option('Selecciona un producto', ''));

            productos.forEach(producto => {
                productoSelect.add(new Option(
                    `${producto.producto} — ${producto.codigo_interno}`,
                    producto.producto_id
                ));
            });

            productoSelect.value = String(item.producto_id || '');

            loteSelect.add(new Option('Selecciona un lote', ''));

            filas
                .filter(fila => String(fila.producto_id) === productoSelect.value)
                .forEach(fila => {
                    loteSelect.add(new Option(
                        `${fila.codigo_lote} · ${cantidadFormato.format(Number(fila.cantidad))} ${fila.unidad || ''}`,
                        fila.lote_id
                    ));
                });

            loteSelect.value = String(item.lote_id || '');

            const seleccionado = filas.find(fila =>
                String(fila.producto_id) === productoSelect.value
                && String(fila.lote_id) === loteSelect.value
            );

            if (seleccionado) {
                nodo.querySelector('.item-stock').textContent =
                    `Registrado en esta ubicación: ${cantidadFormato.format(Number(seleccionado.cantidad))} ${seleccionado.unidad || ''}`
                    + (seleccionado.fecha_vencimiento
                        ? ` · Vencimiento: ${seleccionado.fecha_vencimiento}`
                        : '');

                cantidad.step = seleccionado.pesable ? '0.001' : '1';
                cantidad.min = seleccionado.pesable ? '0.001' : '1';
                cantidad.max = seleccionado.cantidad;
            } else {
                cantidad.step = '0.001';
                cantidad.min = '0.001';
            }

            cantidad.value = item.cantidad_solicitada ?? '';
            porcentaje.value = item.porcentaje_ocupacion_destino ?? '';

            nodo.querySelector('.item-cantidad-panel').hidden = !esMovimiento();
            nodo.querySelector('.item-porcentaje-panel').hidden = !requiereDestino();

            productoSelect.disabled = !panelVisible;
            loteSelect.disabled = !panelVisible;

            cantidad.disabled = !panelVisible || !esMovimiento();
            cantidad.required = panelVisible && esMovimiento();

            porcentaje.disabled = !panelVisible || !requiereDestino();

            productoSelect.addEventListener('change', () => {
                items[indice] = {
                    producto_id: productoSelect.value,
                    lote_id: '',
                    cantidad_solicitada: '',
                    porcentaje_ocupacion_destino:
                        item.porcentaje_ocupacion_destino ?? ''
                };

                renderizarItems();
            });

            loteSelect.addEventListener('change', () => {
                items[indice].lote_id = loteSelect.value;
                items[indice].cantidad_solicitada = '';
                renderizarItems();
            });

            cantidad.addEventListener('input', () => {
                items[indice].cantidad_solicitada = cantidad.value;
            });

            porcentaje.addEventListener('input', () => {
                items[indice].porcentaje_ocupacion_destino = porcentaje.value;
            });

            nodo.querySelector('.item-remove').addEventListener('click', () => {
                items.splice(indice, 1);

                if (items.length === 0) {
                    items = [{}];
                }

                renderizarItems();
            });

            contenedor.appendChild(nodo);
        });
    }

    tipo.addEventListener('change', () => {
        configurarOpcionesDestino();
        configurarFormulario();
    });

    destinoTipo.addEventListener('change', configurarFormulario);
    alcanceRevision.addEventListener('change', configurarFormulario);

    origenAlmacen.addEventListener('change', () => {
        llenarUbicaciones(origenUbicacion, origenAlmacen.value);
        items = [{}];
        renderizarItems();
    });

    origenUbicacion.addEventListener('change', () => {
        items = [{}];
        renderizarItems();
    });

    destinoAlmacen.addEventListener('change', () => {
        llenarUbicaciones(destinoUbicacion, destinoAlmacen.value);
        actualizarEspacio();
    });

    destinoUbicacion.addEventListener('change', actualizarEspacio);

    document.getElementById('agregarProducto').addEventListener('click', () => {
        items.push({});
        renderizarItems();
    });

    origenAlmacen.value = String(anterior.almacen_origen_id || '');
    destinoAlmacen.value = String(anterior.almacen_destino_id || '');

    llenarUbicaciones(
        origenUbicacion,
        origenAlmacen.value,
        anterior.ubicacion_origen_id
    );

    llenarUbicaciones(
        destinoUbicacion,
        destinoAlmacen.value,
        anterior.ubicacion_destino_id
    );

    alcanceRevision.value = anterior.alcance_revision;
    document.getElementById('proveedor').value =
        String(anterior.proveedor_id || '');

    configurarOpcionesDestino(true);
    configurarFormulario();
});
</script>
@endsection