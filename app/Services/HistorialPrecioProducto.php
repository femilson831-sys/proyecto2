<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HistorialPrecioProducto
{
    public function registrar(
        int $productoId,
        float $costoCompra,
        float $precioVenta,
        ?int $usuarioId,
        ?string $fechaEfectiva = null
    ): int {
        $fecha = $fechaEfectiva
            ? Carbon::parse($fechaEfectiva)
            : now();

        $actual = DB::table('productos_precios')
            ->where('producto_id', $productoId)
            ->where('estado', 1)
            ->whereNull('fecha_fin')
            ->orderByDesc('fecha_inicio')
            ->lockForUpdate()
            ->first();

        if ($actual) {
            $fechaAnterior = Carbon::parse($actual->fecha_inicio);

            if ($fecha->lt($fechaAnterior)) {
                throw ValidationException::withMessages([
                    'fecha_recepcion' =>
                        'La fecha de recepción no puede ser anterior al último cambio de precio.',
                ]);
            }

            $mismoCosto = round((float) $actual->precio_compra, 2)
                === round($costoCompra, 2);

            $mismoPrecio = round((float) $actual->precio_venta, 2)
                === round($precioVenta, 2);

            if ($mismoCosto && $mismoPrecio) {
                return (int) $actual->id;
            }

            DB::table('productos_precios')
                ->where('id', $actual->id)
                ->update([
                    'fecha_fin' => $fecha->copy()->subSecond()->format('Y-m-d H:i:s'),
                    'estado' => 0,
                ]);
        }

        return (int) DB::table('productos_precios')->insertGetId([
            'producto_id' => $productoId,
            'precio_compra' => round($costoCompra, 2),
            'precio_venta' => round($precioVenta, 2),
            'fecha_inicio' => $fecha->format('Y-m-d H:i:s'),
            'fecha_fin' => null,
            'usuario_id' => $usuarioId,
            'estado' => 1,
        ]);
    }
}