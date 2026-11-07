@php
    $precioRegistrado = isset($producto)
        ? $producto->precioActual
        : null;

    $compraInicial = old(
        'precio_compra',
        $precioRegistrado?->precio_compra ?? ''
    );

    $ventaInicial = old(
        'precio_venta',
        $precioRegistrado?->precio_venta ?? ''
    );
@endphp

<style>
    .nanos-precios {
        --np-rojo: #dc2626;
        --np-borde: #e5e7eb;
        padding: 24px;
        border: 1px solid var(--np-borde);
        border-radius: 18px;
        background: #fff;
        color: #182230;
        margin: 20px 0;
    }

    .nanos-precios * {
        box-sizing: border-box;
    }

    .np-encabezado {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 22px;
    }

    .np-icono {
        display: grid;
        place-items: center;
        width: 46px;
        height: 46px;
        flex-shrink: 0;
        border-radius: 14px;
        background: #fef2f2;
        color: var(--np-rojo);
        font-weight: 800;
    }

    .np-encabezado h3 {
        margin: 0 0 4px;
        font-size: 18px;
        font-weight: 750;
    }

    .np-encabezado p,
    .np-ayuda {
        color: #667085;
        font-size: 13px;
        line-height: 1.5;
        margin: 0;
    }

    .np-modo {
        margin-bottom: 20px;
        max-width: 420px;
    }

    .np-grid,
    .np-resumen {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .np-campo label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .np-campo input,
    .np-campo select {
        width: 100%;
        min-height: 46px;
        padding: 11px 13px;
        border: 1px solid #d0d5dd;
        border-radius: 10px;
        background: #fff;
        color: #182230;
        font: inherit;
        outline: none;
    }

    .np-campo input:focus,
    .np-campo select:focus {
        border-color: var(--np-rojo);
        box-shadow: 0 0 0 3px #fee2e2;
    }

    .np-campo input:read-only {
        background: #f9fafb;
    }

    .np-campo input:disabled {
        background: #f3f4f6;
        color: #667085;
    }

    .np-ayuda {
        display: block;
        margin-top: 7px;
    }

    .np-resumen {
        margin-top: 22px;
    }

    .np-tarjeta {
        padding: 16px;
        background: #f8fafc;
        border: 1px solid #eef2f6;
        border-radius: 12px;
    }

    .np-tarjeta span {
        display: block;
        color: #667085;
        font-size: 12px;
        margin-bottom: 8px;
    }

    .np-tarjeta strong {
        font-size: 22px;
        font-weight: 800;
    }

    .np-tarjeta-ganancia {
        background: #ecfdf3;
        border-color: #d1fadf;
    }

    .np-tarjeta-ganancia strong {
        color: #067647;
    }

    .np-aviso,
    .np-error {
        color: #b42318;
        font-size: 13px;
    }

    .np-aviso {
        padding: 12px 14px;
        background: #fef3f2;
        border-radius: 10px;
        margin-top: 16px;
    }

    .np-error {
        display: block;
        margin-top: 6px;
    }

    @media (max-width: 700px) {
        .nanos-precios {
            padding: 18px;
        }

        .np-grid,
        .np-resumen {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="nanos-precios" data-calculadora-precios>
    <div class="np-encabezado">
        <div class="np-icono" aria-hidden="true">Bs</div>

        <div>
            <h3>Costos y precio de venta</h3>
            <p>Define tu precio y conoce la ganancia por unidad de venta.</p>
        </div>
    </div>

    <div class="np-campo np-modo">
        <label for="np-modo">Forma de calcular el precio</label>

        <select id="np-modo" name="modo_precio" data-modo>
            <option
                value="manual"
                @selected(old('modo_precio', 'manual') === 'manual')
            >
                Ingresar precio de venta
            </option>

            <option
                value="automatico"
                @selected(old('modo_precio', 'manual') === 'automatico')
            >
                Calcular con porcentaje de ganancia
            </option>
        </select>
    </div>

    <div class="np-grid">
        <div class="np-campo">
            <label for="np-compra">Costo unitario de compra (Bs)</label>

            <input
                id="np-compra"
                data-compra
                type="number"
                name="precio_compra"
                value="{{ $compraInicial }}"
                min="0"
                max="9999999999.99"
                step="0.01"
                inputmode="decimal"
                placeholder="Ej.: 10.00"
                required
            >

            <small class="np-ayuda">
                Costo de una unidad de venta: unidad, kilo, litro, etc.
            </small>

            @error('precio_compra')
                <span class="np-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="np-campo">
            <label for="np-porcentaje">Ganancia sobre el costo (%)</label>

            <input
                id="np-porcentaje"
                data-porcentaje
                type="number"
                name="ganancia_deseada"
                value="{{ old('ganancia_deseada', '15') }}"
                min="0"
                step="0.01"
                inputmode="decimal"
                placeholder="Ej.: 15"
            >

            <small class="np-ayuda">
                Se utiliza en el cálculo automático.
            </small>
        </div>

        <div class="np-campo">
            <label for="np-venta">Precio de venta (Bs)</label>

            <input
                id="np-venta"
                data-venta
                type="number"
                name="precio_venta"
                value="{{ $ventaInicial }}"
                min="0"
                max="9999999999.99"
                step="0.01"
                inputmode="decimal"
                placeholder="Ej.: 11.50"
                required
            >

            <small class="np-ayuda" data-ayuda-venta>
                Escribe el precio final para el cliente.
            </small>

            @error('precio_venta')
                <span class="np-error">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="np-resumen" aria-live="polite">
        <div class="np-tarjeta">
            <span>Precio de venta</span>
            <strong data-resumen-venta>—</strong>
        </div>

        <div class="np-tarjeta np-tarjeta-ganancia">
            <span>Ganancia bruta por unidad de venta</span>
            <strong data-resumen-ganancia>—</strong>
        </div>

        <div class="np-tarjeta">
            <span>Ganancia real sobre el costo</span>
            <strong data-resumen-porcentaje>—</strong>
        </div>
    </div>

    <p class="np-aviso" data-aviso role="alert" hidden></p>

    <p class="np-ayuda" style="margin-top: 16px;">
        Ejemplo: compra a Bs 10,00 + 15 % de ganancia = venta a Bs 11,50.
        La ganancia es bruta, antes de otros gastos.
    </p>
</section>

<script>
(() => {
    const script = document.currentScript;
    const contenedor = script.previousElementSibling;

    if (!contenedor?.matches('[data-calculadora-precios]')) return;

    const modo = contenedor.querySelector('[data-modo]');
    const compra = contenedor.querySelector('[data-compra]');
    const porcentaje = contenedor.querySelector('[data-porcentaje]');
    const venta = contenedor.querySelector('[data-venta]');
    const ayudaVenta = contenedor.querySelector('[data-ayuda-venta]');
    const resumenVenta = contenedor.querySelector('[data-resumen-venta]');
    const resumenGanancia = contenedor.querySelector('[data-resumen-ganancia]');
    const resumenPorcentaje = contenedor.querySelector('[data-resumen-porcentaje]');
    const aviso = contenedor.querySelector('[data-aviso]');

    const formato = new Intl.NumberFormat('es-BO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    function leerNumero(campo) {
        return campo.value.trim() === '' ? NaN : campo.valueAsNumber;
    }

    function dinero(numero) {
        return `Bs ${formato.format(numero)}`;
    }

    function actualizar() {
        const automatico = modo.value === 'automatico';
        const costo = leerNumero(compra);
        const tasa = leerNumero(porcentaje);

        venta.readOnly = automatico;
        porcentaje.disabled = !automatico;
        porcentaje.required = automatico;

        venta.setCustomValidity('');
        porcentaje.setCustomValidity('');
        aviso.hidden = true;
        aviso.textContent = '';

        ayudaVenta.textContent = automatico
            ? 'Calculado automáticamente y redondeado a dos decimales.'
            : 'Escribe el precio final para el cliente.';

        if (automatico) {
            if (
                Number.isFinite(costo) &&
                costo >= 0 &&
                Number.isFinite(tasa) &&
                tasa >= 0
            ) {
                // Trabajamos desde un costo expresado en centavos.
                const costoCentavos = Math.round(costo * 100);
                const ventaCentavos = Math.round(
                    costoCentavos * (1 + tasa / 100)
                );

                const calculado = ventaCentavos / 100;

                if (
                    Number.isFinite(calculado) &&
                    calculado <= Number(venta.max)
                ) {
                    venta.value = calculado.toFixed(2);
                } else {
                    venta.value = '';
                    porcentaje.setCustomValidity(
                        'El resultado supera el precio máximo permitido.'
                    );

                    aviso.textContent =
                        'Reduce el costo o el porcentaje: el precio calculado supera el máximo permitido.';
                    aviso.hidden = false;
                }
            } else {
                venta.value = '';
            }
        }

        const precioVenta = leerNumero(venta);

        resumenVenta.textContent =
            Number.isFinite(precioVenta) && precioVenta >= 0
                ? dinero(precioVenta)
                : '—';

        resumenGanancia.textContent = '—';
        resumenPorcentaje.textContent = '—';
        resumenGanancia.style.color = '';

        if (
            !Number.isFinite(costo) ||
            !Number.isFinite(precioVenta) ||
            costo < 0 ||
            precioVenta < 0
        ) {
            return;
        }

        const costoCentavos = Math.round(costo * 100);
        const ventaCentavos = Math.round(precioVenta * 100);
        const diferencia = ventaCentavos - costoCentavos;

        resumenGanancia.textContent = dinero(diferencia / 100);

        resumenPorcentaje.textContent = costoCentavos > 0
            ? `${formato.format(diferencia / costoCentavos * 100)} %`
            : 'No calculable';

        if (precioVenta < costo) {
            aviso.textContent =
                'El precio de venta es menor al costo. Corrígelo para guardar el producto.';
            aviso.hidden = false;

            resumenGanancia.style.color = '#b42318';

            venta.setCustomValidity(
                'El precio de venta debe ser mayor o igual al precio de compra.'
            );
        }
    }

    modo.addEventListener('change', actualizar);
    compra.addEventListener('input', actualizar);
    porcentaje.addEventListener('input', actualizar);
    venta.addEventListener('input', actualizar);

    actualizar();
})();
</script>