<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaReporteController extends Controller
{
    public function cobros(Request $request)
    {
        $filtros = $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date'],
            'cajero_id' => ['nullable', 'integer', 'exists:usuarios,id'],
            'metodo' => ['nullable', 'in:todos,efectivo,qr_transferencia'],
        ]);

        $desde = $filtros['desde'] ?? now()->toDateString();
        $hasta = $filtros['hasta'] ?? now()->toDateString();
        $cajeroId = $filtros['cajero_id'] ?? null;
        $metodo = $filtros['metodo'] ?? 'todos';

        if ($desde > $hasta) {
            throw ValidationException::withMessages([
                'desde' => 'La fecha inicial no puede ser posterior a la fecha final.',
            ]);
        }

        /*
         * Lista de usuarios que ya tienen ventas vinculadas
         * a un turno de caja.
         */
        $cajeros = DB::table('usuarios as u')
            ->join('turnos_caja as tc', 'tc.usuario_id', '=', 'u.id')
            ->join('ventas as v', 'v.turno_caja_id', '=', 'tc.id')
            ->select('u.id', 'u.username')
            ->distinct()
            ->orderBy('u.username')
            ->get();

        /*
         * La fecha usada es la del pago. Si está vacía,
         * el sistema toma la fecha de la venta.
         *
         * Los métodos QR y Transferencia se muestran
         * juntos como QR / transferencia.
         */
        $consultaBase = DB::table('venta_pagos as vp')
            ->join('ventas as v', 'v.id', '=', 'vp.venta_id')
            ->join('turnos_caja as tc', 'tc.id', '=', 'v.turno_caja_id')
            ->join('usuarios as u', 'u.id', '=', 'tc.usuario_id')
            ->join('metodos_pago as mp', 'mp.id', '=', 'vp.metodo_pago_id')
            ->whereRaw(
                'CAST(COALESCE(vp.fecha_pago, v.fecha_venta) AS DATE) BETWEEN ? AND ?',
                [$desde, $hasta]
            )
            ->whereRaw(
                "LOWER(COALESCE(v.estado, '')) NOT IN (?, ?, ?, ?)",
                ['anulada', 'anulado', 'cancelada', 'cancelado']
            )
            ->where(function ($query) {
                $query
                    ->whereRaw('LOWER(mp.nombre) = ?', ['efectivo'])
                    ->orWhereRaw(
                        'LOWER(mp.nombre) IN (?, ?)',
                        ['qr', 'transferencia']
                    );
            });

        if ($cajeroId !== null && $cajeroId !== '') {
            $consultaBase->where('tc.usuario_id', (int) $cajeroId);
        }

        if ($metodo === 'efectivo') {
            $consultaBase->whereRaw('LOWER(mp.nombre) = ?', ['efectivo']);
        } elseif ($metodo === 'qr_transferencia') {
            $consultaBase->whereRaw(
                'LOWER(mp.nombre) IN (?, ?)',
                ['qr', 'transferencia']
            );
        }

        /*
         * Resumen general de cobros.
         */
        $resumen = (clone $consultaBase)
            ->selectRaw("
                COALESCE(
                    SUM(
                        CASE
                            WHEN LOWER(mp.nombre) = 'efectivo'
                            THEN vp.monto
                            ELSE 0
                        END
                    ),
                    0
                ) AS total_efectivo
            ")
            ->selectRaw("
                COALESCE(
                    SUM(
                        CASE
                            WHEN LOWER(mp.nombre) IN ('qr', 'transferencia')
                            THEN vp.monto
                            ELSE 0
                        END
                    ),
                    0
                ) AS total_qr_transferencia
            ")
            ->selectRaw('COALESCE(SUM(vp.monto), 0) AS total_general')
            ->first();

        /*
         * Totales agrupados por cajero.
         * COUNT(DISTINCT v.id) evita contar dos veces
         * una venta que tenga pagos divididos.
         */
        $reporteCajeros = (clone $consultaBase)
            ->select([
                'u.id as cajero_id',
                'u.username as cajero',
            ])
            ->selectRaw("
                COALESCE(
                    SUM(
                        CASE
                            WHEN LOWER(mp.nombre) = 'efectivo'
                            THEN vp.monto
                            ELSE 0
                        END
                    ),
                    0
                ) AS total_efectivo
            ")
            ->selectRaw("
                COALESCE(
                    SUM(
                        CASE
                            WHEN LOWER(mp.nombre) IN ('qr', 'transferencia')
                            THEN vp.monto
                            ELSE 0
                        END
                    ),
                    0
                ) AS total_qr_transferencia
            ")
            ->selectRaw('COALESCE(SUM(vp.monto), 0) AS total_cobrado')
            ->selectRaw('COUNT(DISTINCT v.id) AS cantidad_ventas')
            ->groupBy('u.id', 'u.username')
            ->orderBy('u.username')
            ->get();

        /*
         * Desglose diario por cajero.
         */
        $fechaPagoSql = 'CAST(COALESCE(vp.fecha_pago, v.fecha_venta) AS DATE)';

        $detalleDiario = (clone $consultaBase)
            ->selectRaw($fechaPagoSql . ' AS fecha')
            ->addSelect('u.username as cajero')
            ->selectRaw("
                COALESCE(
                    SUM(
                        CASE
                            WHEN LOWER(mp.nombre) = 'efectivo'
                            THEN vp.monto
                            ELSE 0
                        END
                    ),
                    0
                ) AS total_efectivo
            ")
            ->selectRaw("
                COALESCE(
                    SUM(
                        CASE
                            WHEN LOWER(mp.nombre) IN ('qr', 'transferencia')
                            THEN vp.monto
                            ELSE 0
                        END
                    ),
                    0
                ) AS total_qr_transferencia
            ")
            ->selectRaw('COALESCE(SUM(vp.monto), 0) AS total_cobrado')
            ->selectRaw('COUNT(DISTINCT v.id) AS cantidad_ventas')
            ->groupByRaw($fechaPagoSql . ', u.username')
            ->orderByDesc('fecha')
            ->orderBy('cajero')
            ->paginate(20)
            ->withQueryString();

        return view('admin.ventas.reportes.cobros', [
            'cajeros' => $cajeros,
            'reporteCajeros' => $reporteCajeros,
            'detalleDiario' => $detalleDiario,
            'resumen' => $resumen,
            'desde' => $desde,
            'hasta' => $hasta,
            'cajeroId' => $cajeroId,
            'metodo' => $metodo,
        ]);
    }
}