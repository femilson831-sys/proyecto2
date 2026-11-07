<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class PesajeController extends Controller
{
    public function index()
    {
        $pesajes = DB::table('pesajes as pe')
            ->join('pesaje_detalles as pd', 'pd.pesaje_id', '=', 'pe.id')
            ->join('productos as p', 'p.id', '=', 'pd.producto_id')
            ->leftJoin('unidades_medida as um', 'um.id', '=', 'pe.unidad_peso_id')
            ->leftJoin('balanzas as b', 'b.id', '=', 'pe.balanza_id')
            ->leftJoin('etiquetas_peso as ep', 'ep.pesaje_id', '=', 'pe.id')
            ->select([
                'pe.id',
                'pe.numero_pesaje',
                'pe.fecha',
                'pe.estado',
                'p.codigo_interno',
                'p.nombre as producto',
                'pd.peso',
                'pd.precio_por_unidad',
                'pd.importe',
                'um.abreviatura as unidad',
                'b.nombre as balanza',
                'ep.codigo_barra',
            ])
            ->orderByDesc('pe.fecha')
            ->orderByDesc('pe.id')
            ->paginate(15);

        return view('admin.pesajes.index', compact('pesajes'));
    }

    public function create()
    {
        $ahora = now();

        /*
         * Obtiene el precio de venta vigente más reciente
         * para cada producto.
         */
        $preciosVigentes = DB::table('productos_precios')
            ->select('producto_id')
            ->selectRaw('MAX(fecha_inicio) AS fecha_inicio')
            ->where('estado', 1)
            ->where('fecha_inicio', '<=', $ahora)
            ->where(function ($query) use ($ahora) {
                $query->whereNull('fecha_fin')
                    ->orWhere('fecha_fin', '>=', $ahora);
            })
            ->groupBy('producto_id');

        /*
         * Solo trae productos activos cuyo tipo permite pesaje.
         * Los productos por unidad o paquete quedan excluidos.
         */
        $productos = DB::table('productos as p')
            ->join('tipos_producto as tp', 'tp.id', '=', 'p.tipo_producto_id')
            ->join('unidades_medida as um', 'um.id', '=', 'p.unidad_medida_id')
            ->leftJoin('categorias as c', 'c.id', '=', 'p.categoria_id')
            ->joinSub($preciosVigentes, 'pv', function ($join) {
                $join->on('pv.producto_id', '=', 'p.id');
            })
            ->join('productos_precios as pp', function ($join) {
                $join->on('pp.producto_id', '=', 'p.id')
                    ->on('pp.fecha_inicio', '=', 'pv.fecha_inicio')
                    ->where('pp.estado', '=', 1);
            })
            ->where('p.estado', 'activo')
            ->where('tp.permite_pesaje', 1)
            ->whereNotNull('pp.precio_venta')
            ->select([
                'p.id',
                'p.codigo_interno',
                'p.nombre',
                'p.imagen',
                'c.nombre as categoria',
                'um.nombre as unidad_nombre',
                'um.abreviatura as unidad',
                'pp.precio_venta',
            ])
            ->orderBy('p.nombre')
            ->get()
            ->map(function ($producto) {
                $producto->imagen_url = $this->obtenerUrlImagen($producto->imagen);

                return $producto;
            });

        $balanzas = DB::table('balanzas')
            ->where('estado', 'activa')
            ->orderBy('nombre')
            ->get([
                'id',
                'codigo',
                'nombre',
                'capacidad_maxima',
            ]);

        return view('admin.pesajes.create', compact('productos', 'balanzas'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'producto_id' => ['required', 'integer', 'exists:productos,id'],
            'peso' => ['required', 'numeric', 'gt:0', 'max:999999999.999'],
            'balanza_id' => ['nullable', 'integer', 'exists:balanzas,id'],
        ], [
            'producto_id.required' => 'Selecciona un producto pesable.',
            'producto_id.exists' => 'El producto seleccionado no existe.',
            'peso.required' => 'Ingresa el peso del producto.',
            'peso.numeric' => 'El peso debe ser un número.',
            'peso.gt' => 'El peso debe ser mayor que cero.',
            'balanza_id.exists' => 'La balanza seleccionada no existe.',
        ]);

        /*
         * Verifica nuevamente en el servidor que el producto
         * esté activo y que su tipo permita pesaje.
         */
        $producto = DB::table('productos as p')
            ->join('tipos_producto as tp', 'tp.id', '=', 'p.tipo_producto_id')
            ->join('unidades_medida as um', 'um.id', '=', 'p.unidad_medida_id')
            ->where('p.id', $datos['producto_id'])
            ->where('p.estado', 'activo')
            ->where('tp.permite_pesaje', 1)
            ->select([
                'p.id',
                'p.nombre',
                'um.id as unidad_id',
                'um.nombre as unidad_nombre',
                'um.abreviatura as unidad',
            ])
            ->first();

        if (!$producto) {
            throw ValidationException::withMessages([
                'producto_id' => 'El producto no está activo o su tipo no permite pesaje.',
            ]);
        }

        $precio = DB::table('productos_precios')
            ->where('producto_id', $producto->id)
            ->where('estado', 1)
            ->where('fecha_inicio', '<=', now())
            ->where(function ($query) {
                $query->whereNull('fecha_fin')
                    ->orWhere('fecha_fin', '>=', now());
            })
            ->orderByDesc('fecha_inicio')
            ->orderByDesc('id')
            ->first();

        if (
            !$precio
            || $precio->precio_venta === null
            || (float) $precio->precio_venta <= 0
        ) {
            throw ValidationException::withMessages([
                'producto_id' => 'El producto no tiene un precio de venta vigente válido.',
            ]);
        }

        if (!empty($datos['balanza_id'])) {
            $balanza = DB::table('balanzas')
                ->where('id', $datos['balanza_id'])
                ->where('estado', 'activa')
                ->first();

            if (!$balanza) {
                throw ValidationException::withMessages([
                    'balanza_id' => 'La balanza seleccionada no está activa.',
                ]);
            }

            if (
                $balanza->capacidad_maxima !== null
                && (float) $datos['peso'] > (float) $balanza->capacidad_maxima
            ) {
                throw ValidationException::withMessages([
                    'peso' => 'El peso supera la capacidad máxima de la balanza.',
                ]);
            }
        }

        $peso = round((float) $datos['peso'], 3);
        $precioUnitario = round((float) $precio->precio_venta, 2);
        $importe = round($peso * $precioUnitario, 2);

        $pesajeId = DB::transaction(function () use (
            $datos,
            $producto,
            $peso,
            $precioUnitario,
            $importe
        ) {
            $pesajeId = DB::table('pesajes')->insertGetId([
                'numero_pesaje' => $this->generarNumeroPesaje(),
                'balanza_id' => $datos['balanza_id'] ?? null,
                'usuario_id' => auth()->id(),
                'cliente_id' => null,
                'fecha' => now(),
                'peso_total' => $peso,
                'unidad_peso_id' => $producto->unidad_id,
                'estado' => 'generado',
            ]);

            DB::table('pesaje_detalles')->insert([
                'pesaje_id' => $pesajeId,
                'producto_id' => $producto->id,
                'peso' => $peso,
                'precio_por_unidad' => $precioUnitario,
                'importe' => $importe,
            ]);

            DB::table('etiquetas_peso')->insert([
                'pesaje_id' => $pesajeId,
                'codigo_barra' => $this->generarCodigoBarra(),
                'archivo' => null,
                'fecha_generacion' => now(),
            ]);

            return $pesajeId;
        });

        return redirect()
            ->route('admin.pesajes.etiqueta', $pesajeId)
            ->with('success', 'Pesaje registrado y etiqueta generada.');
    }

    public function etiqueta(int $pesaje)
    {
        $registro = DB::table('pesajes as pe')
            ->join('pesaje_detalles as pd', 'pd.pesaje_id', '=', 'pe.id')
            ->join('productos as p', 'p.id', '=', 'pd.producto_id')
            ->join('etiquetas_peso as ep', 'ep.pesaje_id', '=', 'pe.id')
            ->leftJoin('unidades_medida as um', 'um.id', '=', 'pe.unidad_peso_id')
            ->leftJoin('balanzas as b', 'b.id', '=', 'pe.balanza_id')
            ->where('pe.id', $pesaje)
            ->select([
                'pe.id',
                'pe.numero_pesaje',
                'pe.fecha',
                'pe.estado',
                'p.codigo_interno',
                'p.nombre as producto',
                'pd.peso',
                'pd.precio_por_unidad',
                'pd.importe',
                'um.abreviatura as unidad',
                'b.nombre as balanza',
                'ep.codigo_barra',
            ])
            ->first();

        abort_if(!$registro, 404, 'No se encontró el pesaje solicitado.');

        return view('admin.pesajes.etiqueta', compact('registro'));
    }

    private function obtenerUrlImagen(?string $imagen): ?string
    {
        $imagen = trim((string) $imagen);

        if ($imagen === '') {
            return null;
        }

        $imagen = str_replace('\\', '/', $imagen);

        if (filter_var($imagen, FILTER_VALIDATE_URL)) {
            return $imagen;
        }

        $imagen = ltrim($imagen, '/');

        if (str_starts_with($imagen, 'public/')) {
            $imagen = substr($imagen, 7);
        }

        if (str_starts_with($imagen, 'storage/app/public/')) {
            $imagen = substr($imagen, strlen('storage/app/public/'));
        }

        if (
            !str_starts_with($imagen, 'productos/')
            && !str_starts_with($imagen, 'storage/')
        ) {
            $imagen = 'productos/' . $imagen;
        }

        return asset($imagen);
    }

    private function generarNumeroPesaje(): string
    {
        do {
            $numero = 'PES-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            $existe = DB::table('pesajes')
                ->where('numero_pesaje', $numero)
                ->exists();
        } while ($existe);

        return $numero;
    }

    private function generarCodigoBarra(): string
    {
        do {
            $base = '20' . str_pad(
                (string) random_int(0, 9999999999),
                10,
                '0',
                STR_PAD_LEFT
            );

            $codigo = $base . $this->digitoVerificadorEan13($base);

            $existe = DB::table('etiquetas_peso')
                ->where('codigo_barra', $codigo)
                ->exists();
        } while ($existe);

        return $codigo;
    }

    private function digitoVerificadorEan13(string $doceDigitos): int
    {
        $suma = 0;

        for ($i = 0; $i < 12; $i++) {
            $digito = (int) $doceDigitos[$i];
            $suma += ($i % 2 === 0) ? $digito : ($digito * 3);
        }

        return (10 - ($suma % 10)) % 10;
    }
}