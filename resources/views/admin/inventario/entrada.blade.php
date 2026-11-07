@extends('admin.layouts.app')

@section('title', 'Registrar entrada de inventario')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="page-title">
            <span class="eyebrow">Inventario</span>
            <h1>Registrar entrada</h1>
            <p>Registra el producto recibido, su lote y fecha de vencimiento.</p>
        </div>

        <a href="{{ route('admin.inventario.index') }}" class="btn-secondary">
            ← Volver al inventario
        </a>
    </div>

    @if ($errors->any())
        <div class="error-message" role="alert">
            <strong>Revisa los siguientes campos:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('admin.inventario.entrada.store') }}"
        method="POST"
        autocomplete="off"
    >
        @csrf

        <div class="card">
            <div class="card-body">
                <h2>Datos de recepción</h2>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="almacen_id">Almacén de destino *</label>
                        <select name="almacen_id" id="almacen_id" class="form-control" required>
                            <option value="">Selecciona un almacén</option>
                            @foreach ($almacenes as $almacen)
                                <option
                                    value="{{ $almacen->id }}"
                                    @selected(old('almacen_id') == $almacen->id)
                                >
                                    {{ $almacen->codigo }} — {{ $almacen->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="fecha_recepcion">Fecha de recepción *</label>
                        <input
                            type="datetime-local"
                            name="fecha_recepcion"
                            id="fecha_recepcion"
                            class="form-control"
                            value="{{ old('fecha_recepcion', now()->format('Y-m-d\TH:i')) }}"
                            required
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="numero_factura">Número de factura o nota</label>
                        <input
                            type="text"
                            name="numero_factura"
                            id="numero_factura"
                            class="form-control"
                            maxlength="80"
                            value="{{ old('numero_factura') }}"
                            placeholder="Ej.: FACT-00125"
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="producto_id">Producto *</label>
                        <select name="producto_id" id="producto_id" class="form-control" required>
                            <option value="">Selecciona un producto</option>
                            @foreach ($productos as $producto)
                                <option
                                    value="{{ $producto->id }}"
                                    @selected(old('producto_id') == $producto->id)
                                >
                                    {{ $producto->codigo_interno }} — {{ $producto->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="proveedor_nombre">Proveedor (automático)</label>
                        <input
                            type="text"
                            id="proveedor_nombre"
                            class="form-control"
                            readonly
                            placeholder="Selecciona un producto"
                        >
                        <input type="hidden" name="proveedor_id" id="proveedor_id">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="unidad_medida">Unidad</label>
                        <input
                            type="text"
                            id="unidad_medida"
                            class="form-control"
                            readonly
                            placeholder="—"
                        >
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="cantidad">Cantidad recibida *</label>
                        <input
                            type="number"
                            name="cantidad"
                            id="cantidad"
                            class="form-control"
                            min="0.001"
                            step="0.001"
                            value="{{ old('cantidad') }}"
                            required
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="costo_unitario">Costo unitario (Bs) *</label>
                        <input
                            type="number"
                            name="costo_unitario"
                            id="costo_unitario"
                            class="form-control"
                            min="0"
                            step="0.01"
                            value="{{ old('costo_unitario') }}"
                            required
                        >
                        <small>Se completa con el precio registrado y puedes corregirlo según la factura.</small>
                    </div>
                </div>

                <hr>

                <h2>Datos del lote</h2>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="codigo_lote">Código del lote *</label>
                        <input
                            type="text"
                            name="codigo_lote"
                            id="codigo_lote"
                            class="form-control"
                            maxlength="100"
                            value="{{ old('codigo_lote') }}"
                            placeholder="Ej.: L-2026-015"
                            required
                        >
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="fecha_fabricacion">Fecha de fabricación</label>
                        <input
                            type="date"
                            name="fecha_fabricacion"
                            id="fecha_fabricacion"
                            class="form-control"
                            value="{{ old('fecha_fabricacion') }}"
                        >
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="fecha_vencimiento">Fecha de vencimiento</label>
                        <input
                            type="date"
                            name="fecha_vencimiento"
                            id="fecha_vencimiento"
                            class="form-control"
                            value="{{ old('fecha_vencimiento') }}"
                        >
                    </div>

                    <div class="col-12 mb-3">
                        <label for="observaciones">Observaciones</label>
                        <textarea
                            name="observaciones"
                            id="observaciones"
                            class="form-control"
                            rows="3"
                            maxlength="5000"
                            placeholder="Estado del producto, detalles de recepción u otra información"
                        >{{ old('observaciones') }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn-primary">
                        Registrar entrada
                    </button>

                    <a href="{{ route('admin.inventario.index') }}" class="btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const producto = document.getElementById('producto_id');
    const proveedorNombre = document.getElementById('proveedor_nombre');
    const proveedorId = document.getElementById('proveedor_id');
    const unidad = document.getElementById('unidad_medida');
    const costo = document.getElementById('costo_unitario');

    const urlBase = @json(
        route('admin.inventario.producto-datos', ['producto' => 'PRODUCTO_ID'])
    );

    async function cargarDatosProducto() {
        const id = producto.value;

        proveedorNombre.value = '';
        proveedorId.value = '';
        unidad.value = '';
        costo.value = '';

        if (!id) return;

        proveedorNombre.value = 'Cargando...';

        try {
            const url = urlBase.replace('PRODUCTO_ID', encodeURIComponent(id));
            const respuesta = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });

            const datos = await respuesta.json();

            if (!respuesta.ok) {
                throw new Error(datos.message || 'No se pudieron cargar los datos.');
            }

            proveedorNombre.value = datos.proveedor || 'Sin proveedor relacionado';
            proveedorId.value = datos.proveedor_id || '';
            unidad.value = datos.unidad || '';

            if (datos.costo_unitario !== null) {
                costo.value = datos.costo_unitario;
            }
        } catch (error) {
            proveedorNombre.value = error.message;
        }
    }

    producto.addEventListener('change', cargarDatosProducto);

    if (producto.value) {
        cargarDatosProducto();
    }
});
</script>
@endsection