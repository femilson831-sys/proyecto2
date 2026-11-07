<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AlertaInventarioService
{
    private const PREFIJO = 'nanos:auto:';

    private const DIAS_VENCIMIENTO = 30;

    public function sincronizar(): void
    {
        DB::transaction(function (): void {
            /*
             * Evita que dos solicitudes generen las mismas
             * alertas simultáneamente en PostgreSQL.
             */
            DB::select(
                'SELECT pg_advisory_xact_lock(?)',
                [2026102201]
            );

            $tipos = $this->tipos();

            $clavesVigentes = [];

            /*
            |--------------------------------------------------------------------------
            | Stock general por producto
            |--------------------------------------------------------------------------
            |
            | El mínimo del producto se compara con el stock
            | total de todos los almacenes.
            |
            */

            $stock = DB::table('inventarios')
                ->select('producto_id')
                ->selectRaw(
                    'SUM(COALESCE(cantidad, 0)) AS cantidad'
                )
                ->groupBy('producto_id');

            $productos = DB::table('productos as p')
                ->leftJoinSub($stock, 's', function ($join) {
                    $join->on('s.producto_id', '=', 'p.id');
                })
                ->leftJoin(
                    'unidades_medida as um',
                    'um.id',
                    '=',
                    'p.unidad_medida_id'
                )
                ->where('p.estado', 'activo')
                ->where(function ($query) {
                    $query
                        ->whereExists(function ($sub) {
                            $sub->selectRaw('1')
                                ->from('inventarios as i')
                                ->whereColumn(
                                    'i.producto_id',
                                    'p.id'
                                );
                        })
                        ->orWhereExists(function ($sub) {
                            $sub->selectRaw('1')
                                ->from('lotes as l')
                                ->whereColumn(
                                    'l.producto_id',
                                    'p.id'
                                );
                        });
                })
                ->select([
                    'p.id',
                    'p.nombre',
                    'p.stock_minimo',
                    'um.abreviatura as unidad',
                ])
                ->selectRaw(
                    'COALESCE(s.cantidad, 0) AS cantidad'
                )
                ->get();

            foreach ($productos as $producto) {
                $cantidad = (float) $producto->cantidad;
                $minimo = (float) ($producto->stock_minimo ?? 0);
                $unidad = $producto->unidad ?? '';

                if ($cantidad <= 0) {
                    $clave = self::PREFIJO
                        . 'stock-agotado:'
                        . $producto->id;

                    $clavesVigentes[] = $clave;

                    $this->guardar($clave, [
                        'tipo_alerta_id' => $tipos['agotado'],
                        'producto_id' => $producto->id,
                        'titulo' => 'Stock agotado: ' . $producto->nombre,
                        'mensaje' =>
                            'El producto tiene un saldo total de '
                            . $this->cantidad($cantidad)
                            . ' ' . $unidad
                            . ' en el inventario de todos los almacenes.',
                        'valor_actual' => $cantidad,
                        'valor_referencia' => 0,
                    ]);
                } elseif ($cantidad <= $minimo) {
                    $clave = self::PREFIJO
                        . 'stock-bajo:'
                        . $producto->id;

                    $clavesVigentes[] = $clave;

                    $this->guardar($clave, [
                        'tipo_alerta_id' => $tipos['bajo'],
                        'producto_id' => $producto->id,
                        'titulo' => 'Stock bajo: ' . $producto->nombre,
                        'mensaje' =>
                            'Disponible en todos los almacenes: '
                            . $this->cantidad($cantidad)
                            . ' ' . $unidad
                            . '. Stock mínimo configurado: '
                            . $this->cantidad($minimo)
                            . ' ' . $unidad . '.',
                        'valor_actual' => $cantidad,
                        'valor_referencia' => $minimo,
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Cantidad ubicada por lote
            |--------------------------------------------------------------------------
            |
            | Comprueba que la asignación corresponda al mismo producto.
            |
            */

            $ubicado = DB::table('inventario_ubicaciones as iu')
                ->join(
                    'inventarios as i',
                    'i.id',
                    '=',
                    'iu.inventario_id'
                )
                ->whereNotNull('iu.lote_id')
                ->where('iu.cantidad', '>', 0)
                ->select([
                    'iu.lote_id',
                    'i.producto_id',
                ])
                ->selectRaw(
                    'SUM(iu.cantidad) AS cantidad_ubicada'
                )
                ->groupBy(
                    'iu.lote_id',
                    'i.producto_id'
                );

            $lotes = DB::table('lotes as l')
                ->join(
                    'productos as p',
                    'p.id',
                    '=',
                    'l.producto_id'
                )
                ->leftJoin(
                    'unidades_medida as um',
                    'um.id',
                    '=',
                    'p.unidad_medida_id'
                )
                ->leftJoinSub($ubicado, 'ub', function ($join) {
                    $join
                        ->on('ub.lote_id', '=', 'l.id')
                        ->on('ub.producto_id', '=', 'l.producto_id');
                })
                ->where('p.estado', 'activo')
                ->where('l.cantidad_actual', '>', 0)
                ->select([
                    'l.id',
                    'l.producto_id',
                    'l.codigo_lote',
                    'l.fecha_vencimiento',
                    'l.cantidad_actual',
                    'p.nombre as producto',
                    'um.abreviatura as unidad',
                ])
                ->selectRaw(
                    'COALESCE(ub.cantidad_ubicada, 0) AS cantidad_ubicada'
                )
                ->get();

            $hoy = now()->startOfDay();

            foreach ($lotes as $lote) {
                $saldo = (float) $lote->cantidad_actual;
                $asignado = (float) $lote->cantidad_ubicada;
                $sinUbicar = round(max($saldo - $asignado, 0), 3);
                $unidad = $lote->unidad ?? '';

                if ($sinUbicar > 0) {
                    $clave = self::PREFIJO
                        . 'sin-ubicacion:lote:'
                        . $lote->id;

                    $clavesVigentes[] = $clave;

                    $this->guardar($clave, [
                        'tipo_alerta_id' => $tipos['ubicacion'],
                        'producto_id' => $lote->producto_id,
                        'lote_id' => $lote->id,
                        'titulo' =>
                            'Unidades sin ubicación: lote '
                            . $lote->codigo_lote,
                        'mensaje' =>
                            $lote->producto
                            . ', lote ' . $lote->codigo_lote
                            . ': saldo actual de '
                            . $this->cantidad($saldo)
                            . ' ' . $unidad
                            . ', cantidad ubicada de '
                            . $this->cantidad($asignado)
                            . ' ' . $unidad
                            . ' y cantidad pendiente de ubicar de '
                            . $this->cantidad($sinUbicar)
                            . ' ' . $unidad
                            . '. La comparación considera las ubicaciones de todos los almacenes.',
                        'valor_actual' => $sinUbicar,
                        'valor_referencia' => 0,
                    ]);
                }

                if (!$lote->fecha_vencimiento) {
                    continue;
                }

                $vencimiento = Carbon::parse(
                    $lote->fecha_vencimiento
                )->startOfDay();

                $dias = (int) $hoy->diffInDays(
                    $vencimiento,
                    false
                );

                if ($dias < 0) {
                    $clave = self::PREFIJO
                        . 'vencido:lote:'
                        . $lote->id;

                    $clavesVigentes[] = $clave;

                    $this->guardar($clave, [
                        'tipo_alerta_id' => $tipos['vencido'],
                        'producto_id' => $lote->producto_id,
                        'lote_id' => $lote->id,
                        'titulo' =>
                            'Lote vencido: ' . $lote->codigo_lote,
                        'mensaje' =>
                            $lote->producto
                            . ', lote ' . $lote->codigo_lote
                            . ', venció el '
                            . $vencimiento->format('d/m/Y')
                            . ' y mantiene '
                            . $this->cantidad($saldo)
                            . ' ' . $unidad . '.',
                        'valor_actual' => $saldo,
                    ]);
                } elseif ($dias <= self::DIAS_VENCIMIENTO) {
                    $clave = self::PREFIJO
                        . 'proximo-vencimiento:lote:'
                        . $lote->id;

                    $clavesVigentes[] = $clave;

                    $this->guardar($clave, [
                        'tipo_alerta_id' => $tipos['proximo'],
                        'producto_id' => $lote->producto_id,
                        'lote_id' => $lote->id,
                        'titulo' =>
                            'Próximo vencimiento: lote '
                            . $lote->codigo_lote,
                        'mensaje' =>
                            $lote->producto
                            . ', lote ' . $lote->codigo_lote
                            . ', vence el '
                            . $vencimiento->format('d/m/Y')
                            . ($dias === 0
                                ? ' (hoy)'
                                : ' (en ' . $dias . ' días)')
                            . '. Saldo: '
                            . $this->cantidad($saldo)
                            . ' ' . $unidad . '.',
                        'valor_actual' => $dias,
                        'valor_referencia' => self::DIAS_VENCIMIENTO,
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Tareas vencidas
            |--------------------------------------------------------------------------
            */

            $tareas = DB::table('tareas as t')
                ->leftJoin(
                    'tarea_estados as te',
                    'te.id',
                    '=',
                    't.estado_id'
                )
                ->whereNotNull('t.fecha_limite')
                ->where('t.fecha_limite', '<', now())
                ->whereNull('t.fecha_finalizacion')
                ->whereRaw("
                    LOWER(TRIM(COALESCE(te.nombre, ''))) NOT IN (
                        'completada',
                        'completado',
                        'cancelada',
                        'cancelado'
                    )
                ")
                ->get([
                    't.id',
                    't.codigo',
                    't.almacen_id',
                    't.fecha_limite',
                ]);

            foreach ($tareas as $tarea) {
                $clave = self::PREFIJO
                    . 'tarea-vencida:'
                    . $tarea->id;

                $clavesVigentes[] = $clave;

                $this->guardar($clave, [
                    'tipo_alerta_id' => $tipos['tarea'],
                    'tarea_id' => $tarea->id,
                    'almacen_id' => $tarea->almacen_id,
                    'titulo' => 'Tarea vencida: ' . $tarea->codigo,
                    'mensaje' =>
                        'La tarea ' . $tarea->codigo
                        . ' tenía fecha límite '
                        . Carbon::parse($tarea->fecha_limite)
                            ->format('d/m/Y H:i')
                        . ' y todavía no registra finalización.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Resolver solamente las alertas administradas por este servicio
            |--------------------------------------------------------------------------
            */

            $resolver = DB::table('alertas')
                ->where(
                    'clave_automatica',
                    'like',
                    self::PREFIJO . '%'
                )
                ->where('estado', 'pendiente');

            if ($clavesVigentes !== []) {
                $resolver->whereNotIn(
                    'clave_automatica',
                    $clavesVigentes
                );
            }

            $resolver->update([
                'estado' => 'resuelta',
                'fecha_resolucion' => now(),
            ]);
        }, 3);
    }

    private function tipos(): array
    {
        $definiciones = [
            'agotado' => [
                'Stock agotado',
                'critica',
                'Producto sin existencias disponibles.',
            ],
            'bajo' => [
                'Stock bajo',
                'alta',
                'Stock total igual o inferior al mínimo.',
            ],
            'ubicacion' => [
                'Unidades sin ubicación',
                'media',
                'Saldo de un lote pendiente de asignar a ubicaciones.',
            ],
            'vencido' => [
                'Lote vencido',
                'critica',
                'Lote vencido con saldo positivo.',
            ],
            'proximo' => [
                'Próximo vencimiento',
                'alta',
                'Lote con saldo que vence en los próximos 30 días.',
            ],
            'tarea' => [
                'Tarea vencida',
                'alta',
                'Tarea que superó su fecha límite.',
            ],
        ];

        $ids = [];

        foreach ($definiciones as $clave => $definicion) {
            [$nombre, $nivel, $descripcion] = $definicion;

            DB::table('tipos_alerta')->updateOrInsert(
                ['nombre' => $nombre],
                [
                    'nivel' => $nivel,
                    'descripcion' => $descripcion,
                ]
            );

            $ids[$clave] = DB::table('tipos_alerta')
                ->where('nombre', $nombre)
                ->value('id');
        }

        return $ids;
    }

    private function guardar(string $clave, array $datos): void
    {
        $existente = DB::table('alertas')
            ->where('clave_automatica', $clave)
            ->first(['id', 'estado']);

        $valores = array_merge([
            'producto_id' => null,
            'lote_id' => null,
            'almacen_id' => null,
            'ubicacion_id' => null,
            'tarea_id' => null,
            'valor_actual' => null,
            'valor_referencia' => null,
        ], $datos);

        $valores['titulo'] = mb_substr(
            $valores['titulo'],
            0,
            180
        );

        $valores['estado'] = 'pendiente';
        $valores['fecha_resolucion'] = null;

        if (!$existente) {
            $valores['clave_automatica'] = $clave;
            $valores['fecha_generacion'] = now();

            DB::table('alertas')->insert($valores);

            return;
        }

        $reabierta = $existente->estado !== 'pendiente';

        if ($reabierta) {
            $valores['fecha_generacion'] = now();
        }

        DB::table('alertas')
            ->where('id', $existente->id)
            ->update($valores);

        if ($reabierta) {
            DB::table('alerta_usuarios')
                ->where('alerta_id', $existente->id)
                ->update([
                    'leida' => 0,
                    'fecha_lectura' => null,
                ]);
        }
    }

    private function cantidad(float $valor): string
    {
        return rtrim(
            rtrim(
                number_format($valor, 3, ',', '.'),
                '0'
            ),
            ','
        );
    }
}