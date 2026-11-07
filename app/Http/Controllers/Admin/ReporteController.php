<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteController extends Controller
{
    private ?array $definicionesCache = null;

    /*
    |--------------------------------------------------------------------------
    | Pantalla principal
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $filtros      = $this->validar($request);
        $definiciones = $this->definiciones();
        $reporte      = $definiciones[$filtros['tipo']];

        $consulta       = $this->consulta($filtros);
        $totalRegistros = (clone $consulta)->count();

        $resumen = $this->resumen(
            clone $consulta,
            $filtros['tipo'],
            $totalRegistros
        );

        $grafico = $this->grafico(
            clone $consulta,
            $reporte['grafico']
        );

        $ordenada = $this->ordenar(clone $consulta, $reporte);

        $imprimir = $request->boolean('imprimir');

        $registros = $imprimir
            ? $ordenada->get()
            : $ordenada->paginate(20)->withQueryString();

        return view('admin.reportes.index', [
            'definiciones'   => $definiciones,
            'reporte'        => $reporte,
            'filtros'        => $filtros,
            'resumen'        => $resumen,
            'grafico'        => $grafico,
            'registros'      => $registros,
            'totalRegistros' => $totalRegistros,
            'imprimir'       => $imprimir,
            'generadoPor'    => $this->nombreUsuario($request),
            'generadoEn'     => now(),

            'almacenes' => DB::table('almacenes')
                ->orderBy('nombre')
                ->get(['id', 'codigo', 'nombre']),

            'productos' => DB::table('productos')
                ->orderBy('nombre')
                ->get(['id', 'codigo_interno', 'nombre']),

            'usuarios' => DB::table('usuarios')
                ->orderBy('username')
                ->get(['id', 'username']),

            'tiposMovimiento' => DB::table('tipos_movimiento')
                ->orderBy('nombre')
                ->get(['id', 'nombre']),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Exportación Excel con logo y diseño rojo
    |--------------------------------------------------------------------------
    */

    public function exportar(Request $request)
    {
        // Reportes grandes: más tiempo y memoria para PhpSpreadsheet.
        @set_time_limit(180);
        @ini_set('memory_limit', '512M');

        $filtros = $this->validar($request);
        $reporte = $this->definiciones()[$filtros['tipo']];

        $consulta = $this->consulta($filtros);

        $resumen = $this->resumen(
            clone $consulta,
            $filtros['tipo'],
            (clone $consulta)->count()
        );

        $consulta = $this->ordenar($consulta, $reporte);

        $rutaLogo    = public_path('img/logo.png');
        $generadoEn  = now();
        $generadoPor = $this->nombreUsuario($request);

        $excel = new Spreadsheet();

        $excel->getProperties()
            ->setCreator($generadoPor)
            ->setLastModifiedBy($generadoPor)
            ->setTitle($reporte['titulo'])
            ->setSubject('Reporte administrativo')
            ->setDescription('Reporte del Minisúper NANOS')
            ->setCompany('Minisúper NANOS');

        $hoja = $excel->getActiveSheet();
        $hoja->setTitle('Reporte NANOS');
        $hoja->setShowGridlines(false);

        $excel->getDefaultStyle()
            ->getFont()
            ->setName('Calibri')
            ->setSize(11);

        $excel->getDefaultStyle()
            ->getFont()
            ->getColor()
            ->setRGB('252A34');

        $excel->getDefaultStyle()
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $cantidadColumnas = count($reporte['columnas']);
        $columnasDiseno   = max(8, $cantidadColumnas);

        $ultimaDatos = Coordinate::stringFromColumnIndex($cantidadColumnas);
        $ultima      = Coordinate::stringFromColumnIndex($columnasDiseno);

        $rojo       = 'C81925';
        $rojoOscuro = '94131D';
        $rojoClaro  = 'FCEDEF';

        /*
         * Dimensiones.
         */
        for ($i = 1; $i <= $columnasDiseno; $i++) {
            $letra = Coordinate::stringFromColumnIndex($i);
            $hoja->getColumnDimension($letra)->setWidth(20);
        }

        foreach ($reporte['columnas'] as $indice => $columna) {
            $letra = Coordinate::stringFromColumnIndex($indice + 1);

            $ancho = match ($columna['formato']) {
                'fecha_hora' => 23,
                'fecha'      => 17,
                'cantidad', 'moneda', 'entero' => 19,
                default      => 28,
            };

            $hoja->getColumnDimension($letra)->setWidth($ancho);
        }

        /*
         * Encabezado.
         */
        $hoja->getStyle("A1:{$ultima}3")->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $rojo],
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF'],
            ],
        ]);

        foreach ([1, 2, 3] as $numeroFila) {
            $hoja->getRowDimension($numeroFila)->setRowHeight(25);
        }

        $hoja->mergeCells('A1:B3');
        $hoja->mergeCells("C1:{$ultima}2");
        $hoja->mergeCells("C3:{$ultima}3");

        $hoja->setCellValueExplicit('C1', 'MINISÚPER NANOS', DataType::TYPE_STRING);
        $hoja->setCellValueExplicit('C3', $reporte['titulo'], DataType::TYPE_STRING);

        $hoja->getStyle('C1')->getFont()->setBold(true)->setSize(24);
        $hoja->getStyle('C3')->getFont()->setBold(true)->setSize(13);

        // El logo es opcional: si no existe, el reporte se genera igual.
        if (is_file($rutaLogo)) {
            $logo = new Drawing();
            $logo->setName('Logo NANOS');
            $logo->setDescription('Logo Minisúper NANOS');
            $logo->setPath($rutaLogo);
            $logo->setCoordinates('A1');
            $logo->setHeight(78);
            $logo->setOffsetX(15);
            $logo->setOffsetY(9);
            $logo->setWorksheet($hoja);
        }

        /*
         * Información del documento.
         */
        $fila = 5;

        $metadatos = [
            'Generado: ' . $generadoEn->format('d/m/Y H:i:s'),
            'Generado por: ' . $generadoPor,
            $reporte['fechas']
                ? 'Período: '
                    . Carbon::parse($filtros['desde'])->format('d/m/Y')
                    . ' al '
                    . Carbon::parse($filtros['hasta'])->format('d/m/Y')
                : 'Referencia: existencias actuales al momento de consultar',
        ];

        foreach ($reporte['filtros'] as $filtro) {
            [$etiqueta, $valor] = $this->describirFiltro($filtro, $filtros);
            $metadatos[] = $etiqueta . ': ' . $valor;
        }

        foreach ($metadatos as $texto) {
            $hoja->mergeCells("A{$fila}:{$ultima}{$fila}");
            $hoja->setCellValueExplicit("A{$fila}", $texto, DataType::TYPE_STRING);
            $hoja->getRowDimension($fila)->setRowHeight(21);
            $fila++;
        }

        $hoja->mergeCells("A{$fila}:{$ultima}{$fila}");
        $hoja->setCellValueExplicit(
            "A{$fila}",
            $reporte['descripcion'],
            DataType::TYPE_STRING
        );

        $hoja->getStyle("A{$fila}")->getAlignment()->setWrapText(true);
        $hoja->getStyle("A{$fila}")->getFont()->getColor()->setRGB('687080');
        $hoja->getRowDimension($fila)->setRowHeight(42);

        $fila += 2;

        /*
         * Resumen.
         */
        $hoja->mergeCells("A{$fila}:{$ultima}{$fila}");
        $hoja->setCellValueExplicit("A{$fila}", 'RESUMEN DEL REPORTE', DataType::TYPE_STRING);

        $hoja->getStyle("A{$fila}:{$ultima}{$fila}")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $rojoOscuro],
            ],
        ]);

        $hoja->getRowDimension($fila)->setRowHeight(27);
        $fila++;

        foreach ($resumen as $indicador) {
            $hoja->mergeCells("A{$fila}:D{$fila}");
            $hoja->mergeCells("E{$fila}:{$ultima}{$fila}");

            $hoja->setCellValueExplicit(
                "A{$fila}",
                $indicador['titulo'],
                DataType::TYPE_STRING
            );

            $hoja->setCellValueExplicit(
                "E{$fila}",
                (float) $indicador['valor'],
                DataType::TYPE_NUMERIC
            );

            $hoja->getStyle("A{$fila}:{$ultima}{$fila}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $rojoClaro],
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'FFFFFF'],
                    ],
                ],
            ]);

            $hoja->getStyle("E{$fila}")->getFont()->setBold(true);
            $hoja->getStyle("E{$fila}")->getFont()->getColor()->setRGB($rojoOscuro);
            $hoja->getStyle("E{$fila}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $hoja->getStyle("E{$fila}")
                ->getNumberFormat()
                ->setFormatCode(
                    $indicador['formato'] === 'moneda'
                        ? '"Bs" #,##0.00'
                        : '#,##0'
                );

            $hoja->getRowDimension($fila)->setRowHeight(24);
            $fila++;
        }

        $fila += 2;
        $filaCabecera = $fila;

        /*
         * Encabezados de la tabla.
         */
        foreach ($reporte['columnas'] as $indice => $columna) {
            $letra = Coordinate::stringFromColumnIndex($indice + 1);

            $hoja->setCellValueExplicit(
                "{$letra}{$fila}",
                $columna['titulo'],
                DataType::TYPE_STRING
            );
        }

        $hoja->getStyle("A{$fila}:{$ultimaDatos}{$fila}")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $rojo],
            ],
            'alignment' => [
                'wrapText' => true,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $hoja->getRowDimension($fila)->setRowHeight(32);

        $fila++;
        $inicioDatos = $fila;

        /*
         * Resultados.
         */
        foreach ($consulta->cursor() as $registro) {
            foreach ($reporte['columnas'] as $indice => $columna) {
                $letra = Coordinate::stringFromColumnIndex($indice + 1);
                $celda = "{$letra}{$fila}";
                $valor = $registro->{$columna['campo']} ?? null;

                if ($valor === null || $valor === '') {
                    $hoja->setCellValueExplicit($celda, '—', DataType::TYPE_STRING);
                    continue;
                }

                switch ($columna['formato']) {
                    case 'moneda':
                    case 'cantidad':
                    case 'entero':
                        $hoja->setCellValueExplicit(
                            $celda,
                            (float) $valor,
                            DataType::TYPE_NUMERIC
                        );

                        // '#,##0.###' mostraba "5." en números enteros.
                        $formato = match ($columna['formato']) {
                            'moneda'   => '"Bs" #,##0.00',
                            'cantidad' => '#,##0.00#',
                            default    => '#,##0',
                        };

                        $hoja->getStyle($celda)
                            ->getNumberFormat()
                            ->setFormatCode($formato);

                        break;

                    case 'fecha':
                    case 'fecha_hora':
                        $hoja->setCellValueExplicit(
                            $celda,
                            ExcelDate::PHPToExcel(Carbon::parse($valor)),
                            DataType::TYPE_NUMERIC
                        );

                        $hoja->getStyle($celda)
                            ->getNumberFormat()
                            ->setFormatCode(
                                $columna['formato'] === 'fecha'
                                    ? 'dd/mm/yyyy'
                                    : 'dd/mm/yyyy hh:mm:ss'
                            );

                        break;

                    default:
                        $hoja->setCellValueExplicit(
                            $celda,
                            (string) $valor,
                            DataType::TYPE_STRING
                        );
                }
            }

            $hoja->getStyle("A{$fila}:{$ultimaDatos}{$fila}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => (($fila - $inicioDatos) % 2 === 0)
                            ? 'FFFFFF'
                            : 'FFF3F4',
                    ],
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'E2E5EB'],
                    ],
                ],
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => Alignment::VERTICAL_TOP,
                ],
            ]);

            $fila++;
        }

        $finDatos = $fila - 1;

        if ($finDatos >= $inicioDatos) {
            $hoja->setAutoFilter("A{$filaCabecera}:{$ultimaDatos}{$finDatos}");
        } else {
            $hoja->mergeCells("A{$fila}:{$ultimaDatos}{$fila}");

            $hoja->setCellValueExplicit(
                "A{$fila}",
                'No hay registros que coincidan con los filtros.',
                DataType::TYPE_STRING
            );

            $hoja->getRowDimension($fila)->setRowHeight(28);
            $fila++;
        }

        // Nota: se quitó freezePane('A'.$inicioDatos). Congelaba todo el
        // encabezado (logo + metadatos + resumen) y ocupaba la pantalla entera.

        /*
         * Pie del documento.
         */
        $fila += 2;

        $hoja->mergeCells("A{$fila}:{$ultima}{$fila}");

        $hoja->setCellValueExplicit(
            "A{$fila}",
            'Minisúper NANOS · Reporte generado desde los registros del sistema.',
            DataType::TYPE_STRING
        );

        $hoja->getStyle("A{$fila}")->getFont()
            ->setSize(9)
            ->getColor()
            ->setRGB('687080');

        /*
         * Configuración de impresión.
         */
        $hoja->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToPage(true)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setPrintArea("A1:{$ultima}{$fila}");

        $hoja->getPageSetup()
            ->setRowsToRepeatAtTopByStartAndEnd($filaCabecera, $filaCabecera);

        $hoja->getPageMargins()->setTop(0.35);
        $hoja->getPageMargins()->setBottom(0.35);
        $hoja->getPageMargins()->setLeft(0.25);
        $hoja->getPageMargins()->setRight(0.25);

        $hoja->getHeaderFooter()->setOddFooter(
            '&LMinisúper NANOS&C Página &P de &N&R '
            . $generadoEn->format('d/m/Y')
        );

        $archivo = 'NANOS-'
            . $filtros['tipo']
            . '-'
            . $generadoEn->format('Y-m-d-His')
            . '.xlsx';

        return response()->streamDownload(
            function () use ($excel): void {
                try {
                    $writer = new Xlsx($excel);
                    $writer->save('php://output');
                } finally {
                    $excel->disconnectWorksheets();
                }
            },
            $archivo,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'no-store, max-age=0',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Exportación CSV con diseño corporativo
    |--------------------------------------------------------------------------
    */

    public function exportarCsv(Request $request)
    {
        @set_time_limit(180);

        $filtros  = $this->validar($request);
        $reporte  = $this->definiciones()[$filtros['tipo']];
        $consulta = $this->consulta($filtros);

        $totalRegistros = (clone $consulta)->count();

        $resumen = $this->resumen(
            clone $consulta,
            $filtros['tipo'],
            $totalRegistros
        );

        $consulta = $this->ordenar($consulta, $reporte);

        $generadoEn  = now();
        $generadoPor = $this->nombreUsuario($request);

        // Delimitador y salto de línea compatibles con Excel en español.
        $delimitador = ';';
        $salto       = "\r\n";

        $archivo = 'NANOS-'
            . $filtros['tipo']
            . '-'
            . $generadoEn->format('Y-m-d-His')
            . '.csv';

        $callback = function () use (
            $reporte,
            $filtros,
            $consulta,
            $resumen,
            $generadoEn,
            $generadoPor,
            $totalRegistros,
            $delimitador,
            $salto
        ) {
            $salida = fopen('php://output', 'w');

            // UTF-8 BOM para que Excel respete tildes y ñ.
            fwrite($salida, "\xEF\xBB\xBF");

            $escribir = static function (array $celdas) use (
                $salida,
                $delimitador,
                $salto
            ): void {
                $limpio = array_map(
                    static function ($valor) use ($delimitador) {
                        $valor = (string) ($valor ?? '');

                        // Evita inyección de fórmulas en Excel (=, +, -, @),
                        // sin tocar números ya formateados como "-1.234,50".
                        if (
                            $valor !== ''
                            && preg_match('/^[=+\-@\t\r]/', $valor)
                            && !preg_match('/^-?[\d.]+(,\d+)?$/', $valor)
                        ) {
                            $valor = "'" . $valor;
                        }

                        if (
                            str_contains($valor, $delimitador)
                            || str_contains($valor, '"')
                            || str_contains($valor, "\n")
                            || str_contains($valor, "\r")
                        ) {
                            $valor = '"'
                                . str_replace('"', '""', $valor)
                                . '"';
                        }

                        return $valor;
                    },
                    $celdas
                );

                fwrite($salida, implode($delimitador, $limpio) . $salto);
            };

            // Encabezado corporativo.
            $escribir(['MINISÚPER NANOS']);
            $escribir([$reporte['titulo']]);
            $escribir(['Generado: ' . $generadoEn->format('d/m/Y H:i:s')]);
            $escribir(['Generado por: ' . $generadoPor]);

            if ($reporte['fechas']) {
                $escribir([
                    'Período: '
                    . Carbon::parse($filtros['desde'])->format('d/m/Y')
                    . ' al '
                    . Carbon::parse($filtros['hasta'])->format('d/m/Y'),
                ]);
            } else {
                $escribir([
                    'Referencia: existencias actuales al momento de consultar',
                ]);
            }

            foreach ($reporte['filtros'] as $filtro) {
                [$etiqueta, $valor] = $this->describirFiltro($filtro, $filtros);
                $escribir([$etiqueta . ': ' . $valor]);
            }

            $escribir([$reporte['descripcion']]);
            $escribir([]);

            // Resumen.
            $escribir(['RESUMEN DEL REPORTE']);

            foreach ($resumen as $indicador) {
                $valor = $indicador['formato'] === 'moneda'
                    ? 'Bs ' . number_format((float) $indicador['valor'], 2, ',', '.')
                    : number_format((float) $indicador['valor'], 0, ',', '.');

                $escribir([$indicador['titulo'], $valor]);
            }

            $escribir([
                'Total de registros:',
                number_format($totalRegistros, 0, ',', '.'),
            ]);

            $escribir([]);

            // Encabezados de la tabla.
            $escribir(
                array_map(
                    static fn (array $col): string => $col['titulo'],
                    $reporte['columnas']
                )
            );

            // Filas de datos.
            foreach ($consulta->cursor() as $registro) {
                $fila = [];

                foreach ($reporte['columnas'] as $columna) {
                    $valor = $registro->{$columna['campo']} ?? null;

                    if ($valor === null || $valor === '') {
                        $fila[] = '—';
                        continue;
                    }

                    $fila[] = match ($columna['formato']) {
                        'moneda' => number_format((float) $valor, 2, ',', '.'),

                        'cantidad' => rtrim(
                            rtrim(
                                number_format((float) $valor, 3, ',', '.'),
                                '0'
                            ),
                            ','
                        ),

                        'entero' => number_format((float) $valor, 0, ',', '.'),

                        'fecha' => Carbon::parse($valor)->format('d/m/Y'),

                        'fecha_hora' => Carbon::parse($valor)
                            ->format('d/m/Y H:i:s'),

                        default => (string) $valor,
                    };
                }

                $escribir($fila);
            }

            // Pie.
            $escribir([]);
            $escribir([
                'Minisúper NANOS · Reporte generado desde los registros del sistema.',
            ]);

            fclose($salida);
        };

        // streamDownload ya define Content-Disposition con el nombre.
        return response()->streamDownload(
            $callback,
            $archivo,
            [
                'Content-Type'  => 'text/csv; charset=UTF-8',
                'Cache-Control' => 'no-store, max-age=0',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Compatibilidad con enlaces anteriores
    |--------------------------------------------------------------------------
    */

    public function inventario(Request $request)
    {
        $request->merge(['tipo' => 'inventario']);

        return $this->index($request);
    }

    public function exportarInventario(Request $request)
    {
        $request->merge(['tipo' => 'inventario']);

        return $this->exportar($request);
    }

    /*
    |--------------------------------------------------------------------------
    | Reportes disponibles
    |--------------------------------------------------------------------------
    */

    private function definiciones(): array
    {
        return $this->definicionesCache ??= $this->construirDefiniciones();
    }

    private function construirDefiniciones(): array
    {
        $c = static fn (
            string $campo,
            string $titulo,
            string $formato = 'texto'
        ): array => compact('campo', 'titulo', 'formato');

        return [
            'accesos' => [
                'titulo' => 'Historial de acceso de usuarios',
                'descripcion' =>
                    'Ingresos, última actividad y cierres confirmados. El período filtra la fecha de ingreso. La duración observada no representa tiempo de trabajo efectivo.',
                'fechas'    => true,
                'filtros'   => ['usuario'],
                'orden'     => 'fecha',
                'direccion' => 'desc',
                'grafico'   => [
                    'campo'  => 'usuario',
                    'medida' => 'conteo',
                    'titulo' => 'Hasta 8 usuarios con más ingresos',
                ],
                'columnas' => [
                    $c('usuario', 'Usuario'),
                    $c('fecha', 'Ingreso al sistema', 'fecha_hora'),
                    $c('ultima_actividad', 'Última actividad', 'fecha_hora'),
                    $c('cierre', 'Cierre confirmado', 'fecha_hora'),
                    $c('duracion', 'Duración observada'),
                    $c('estado', 'Estado'),
                    $c('origen', 'Tipo de ingreso'),
                    $c('ip', 'Dirección IP'),
                ],
            ],

            'inventario' => [
                'titulo' => 'Inventario actual',
                'descripcion' =>
                    'Existencias actuales de productos activos. Los mínimos y máximos corresponden a la configuración general del producto.',
                'fechas'    => false,
                'filtros'   => ['almacen', 'producto', 'stock'],
                'orden'     => 'producto',
                'direccion' => 'asc',
                'grafico'   => [
                    'campo'  => 'estado_stock',
                    'medida' => 'conteo',
                    'titulo' => 'Productos por estado del stock',
                ],
                'columnas' => [
                    $c('codigo', 'Código'),
                    $c('producto', 'Producto'),
                    $c('cantidad', 'Stock actual', 'cantidad'),
                    $c('unidad', 'Unidad'),
                    $c('minimo', 'Stock mínimo', 'cantidad'),
                    $c('maximo', 'Stock máximo', 'cantidad'),
                    $c('estado_stock', 'Estado'),
                ],
            ],

            'lotes' => [
                'titulo' => 'Lotes y vencimientos',
                'descripcion' =>
                    'Lotes con saldo positivo. El almacén corresponde a la recepción original y no refleja necesariamente traslados posteriores.',
                'fechas'    => false,
                'filtros'   => ['almacen', 'producto', 'vencimiento'],
                'orden'     => 'vencimiento',
                'direccion' => 'asc',
                'grafico'   => [
                    'campo'  => 'estado_lote',
                    'medida' => 'conteo',
                    'titulo' => 'Lotes por condición de vencimiento',
                ],
                'columnas' => [
                    $c('codigo', 'Código'),
                    $c('producto', 'Producto'),
                    $c('lote', 'Lote'),
                    $c('almacen', 'Almacén de recepción'),
                    $c('fecha', 'Ingreso', 'fecha'),
                    $c('vencimiento', 'Vencimiento', 'fecha'),
                    $c('cantidad', 'Saldo del lote', 'cantidad'),
                    $c('unidad', 'Unidad'),
                    $c('estado_lote', 'Condición'),
                ],
            ],

            'movimientos' => [
                'titulo' => 'Movimientos de inventario',
                'descripcion' =>
                    'Cada fila representa una línea del detalle de un movimiento.',
                'fechas'    => true,
                'filtros'   => ['almacen', 'producto', 'usuario', 'movimiento'],
                'orden'     => 'fecha',
                'direccion' => 'desc',
                'grafico'   => [
                    'campo'  => 'tipo_movimiento',
                    'medida' => 'conteo',
                    'titulo' => 'Hasta 8 tipos con más líneas de movimiento',
                ],
                'columnas' => [
                    $c('numero', 'Movimiento'),
                    $c('fecha', 'Fecha', 'fecha_hora'),
                    $c('tipo_movimiento', 'Tipo'),
                    $c('producto', 'Producto'),
                    $c('lote', 'Lote'),
                    $c('cantidad', 'Cantidad', 'cantidad'),
                    $c('unidad', 'Unidad'),
                    $c('origen', 'Origen'),
                    $c('destino', 'Destino'),
                    $c('usuario', 'Responsable'),
                    $c('estado', 'Estado'),
                ],
            ],

            'cobros' => [
                'titulo' => 'Cobros por cajero',
                'descripcion' =>
                    'Pagos de ventas completadas, atribuidos al usuario del turno de caja. Cada fila representa un pago.',
                'fechas'    => true,
                'filtros'   => ['usuario', 'pago'],
                'orden'     => 'fecha',
                'direccion' => 'desc',
                'grafico'   => [
                    'campo'  => 'metodo',
                    'medida' => 'importe',
                    'titulo' => 'Importes cobrados por método de pago',
                ],
                'columnas' => [
                    $c('numero', 'Venta'),
                    $c('fecha', 'Fecha del cobro', 'fecha_hora'),
                    $c('usuario', 'Cajero'),
                    $c('metodo', 'Método'),
                    $c('importe', 'Monto cobrado', 'moneda'),
                    $c('referencia', 'Referencia'),
                ],
            ],

            'vendidos' => [
                'titulo' => 'Productos vendidos',
                'descripcion' =>
                    'Detalles de ventas completadas. Sus importes pueden no incluir descuentos generales guardados en la cabecera de la venta.',
                'fechas'    => true,
                'filtros'   => ['producto', 'usuario'],
                'orden'     => 'importe',
                'direccion' => 'desc',
                'grafico'   => [
                    'campo'  => 'producto',
                    'medida' => 'importe',
                    'titulo' => 'Hasta 8 productos con mayor importe',
                ],
                'columnas' => [
                    $c('codigo', 'Código'),
                    $c('producto', 'Producto'),
                    $c('cantidad', 'Cantidad vendida', 'cantidad'),
                    $c('unidad', 'Unidad'),
                    $c('ventas', 'Ventas', 'entero'),
                    $c('importe', 'Importe de detalles', 'moneda'),
                ],
            ],

            'tareas' => [
                'titulo' => 'Tareas del personal',
                'descripcion' =>
                    'Tareas creadas en el período, con su estado actual y usuarios asignados.',
                'fechas'    => true,
                'filtros'   => ['almacen', 'usuario'],
                'orden'     => 'fecha',
                'direccion' => 'desc',
                'grafico'   => [
                    'campo'  => 'estado',
                    'medida' => 'conteo',
                    'titulo' => 'Tareas por estado actual',
                ],
                'columnas' => [
                    $c('codigo', 'Código'),
                    $c('tipo_tarea', 'Tipo'),
                    $c('estado', 'Estado'),
                    $c('prioridad', 'Prioridad'),
                    $c('almacen', 'Almacén'),
                    $c('asignados', 'Usuarios asignados'),
                    $c('fecha', 'Creación', 'fecha_hora'),
                    $c('limite', 'Fecha límite', 'fecha_hora'),
                    $c('finalizacion', 'Finalización', 'fecha_hora'),
                ],
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Validación
    |--------------------------------------------------------------------------
    */

    private function validar(Request $request): array
    {
        $definiciones = $this->definiciones();

        $datos = $request->validate([
            'tipo' => [
                'nullable',
                Rule::in(array_keys($definiciones)),
            ],
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => ['nullable', 'date_format:Y-m-d'],
            'almacen_id' => ['nullable', 'integer', 'exists:almacenes,id'],
            'producto_id' => ['nullable', 'integer', 'exists:productos,id'],
            'usuario_id' => ['nullable', 'integer', 'exists:usuarios,id'],
            'tipo_movimiento_id' => [
                'nullable',
                'integer',
                'exists:tipos_movimiento,id',
            ],
            'estado_stock' => [
                'nullable',
                Rule::in(['todos', 'Agotado', 'Bajo', 'Normal']),
            ],
            'metodo_pago' => [
                'nullable',
                Rule::in(['todos', 'efectivo', 'qr']),
            ],
            'vencimiento' => [
                'nullable',
                Rule::in([
                    'todos',
                    'Vencido',
                    'Próximo a vencer',
                    'Vigente',
                    'Sin fecha',
                ]),
            ],
            'imprimir' => ['nullable', 'boolean'],
        ], [
            'in'          => 'Selecciona una opción válida para :attribute.',
            'exists'      => 'El registro seleccionado en :attribute no existe.',
            'integer'     => 'Selecciona una opción válida para :attribute.',
            'date_format' => 'La :attribute debe tener formato año-mes-día.',
            'boolean'     => 'La opción de impresión no es válida.',
        ], [
            'tipo'               => 'tipo de reporte',
            'desde'              => 'fecha inicial',
            'hasta'              => 'fecha final',
            'almacen_id'         => 'almacén',
            'producto_id'        => 'producto',
            'usuario_id'         => 'usuario',
            'tipo_movimiento_id' => 'tipo de movimiento',
            'estado_stock'       => 'estado del stock',
            'metodo_pago'        => 'método de pago',
            'vencimiento'        => 'vencimiento',
        ]);

        $tipo = $datos['tipo'] ?? 'accesos';

        $desde = $datos['desde'] ?? now()->startOfMonth()->toDateString();
        $hasta = $datos['hasta'] ?? now()->toDateString();

        if ($definiciones[$tipo]['fechas'] && $desde > $hasta) {
            throw ValidationException::withMessages([
                'hasta' => 'La fecha final no puede ser anterior a la inicial.',
            ]);
        }

        return [
            'tipo'  => $tipo,
            'desde' => $desde,
            'hasta' => $hasta,

            'almacen_id' => isset($datos['almacen_id'])
                ? (int) $datos['almacen_id'] : null,

            'producto_id' => isset($datos['producto_id'])
                ? (int) $datos['producto_id'] : null,

            'usuario_id' => isset($datos['usuario_id'])
                ? (int) $datos['usuario_id'] : null,

            'tipo_movimiento_id' => isset($datos['tipo_movimiento_id'])
                ? (int) $datos['tipo_movimiento_id'] : null,

            'estado_stock' => $datos['estado_stock'] ?? 'todos',
            'metodo_pago'  => $datos['metodo_pago'] ?? 'todos',
            'vencimiento'  => $datos['vencimiento'] ?? 'todos',
        ];
    }

    private function periodo(
        Builder $consulta,
        string|Expression $campo,
        array $f
    ): void {
        $inicio = Carbon::parse($f['desde'])->startOfDay();
        $fin    = Carbon::parse($f['hasta'])->addDay()->startOfDay();

        $consulta
            ->where($campo, '>=', $inicio->format('Y-m-d H:i:s'))
            ->where($campo, '<', $fin->format('Y-m-d H:i:s'));
    }

    /*
    |--------------------------------------------------------------------------
    | Consulta seleccionada
    |--------------------------------------------------------------------------
    */

    private function consulta(array $f): Builder
    {
        $origen = match ($f['tipo']) {
            'accesos'     => $this->accesosQuery($f),
            'inventario'  => $this->inventarioQuery($f),
            'lotes'       => $this->lotesQuery($f),
            'movimientos' => $this->movimientosQuery($f),
            'cobros'      => $this->cobrosQuery($f),
            'vendidos'    => $this->vendidosQuery($f),
            'tareas'      => $this->tareasQuery($f),
        };

        $q = DB::query()
            ->fromSub($origen, 'r')
            ->select('r.*');

        if ($f['tipo'] === 'inventario' && $f['estado_stock'] !== 'todos') {
            $q->where('r.estado_stock', $f['estado_stock']);
        }

        if ($f['tipo'] === 'lotes' && $f['vencimiento'] !== 'todos') {
            $q->where('r.estado_lote', $f['vencimiento']);
        }

        if ($f['tipo'] === 'cobros') {
            if ($f['metodo_pago'] === 'efectivo') {
                $q->where('r.metodo', 'Efectivo');
            } elseif ($f['metodo_pago'] === 'qr') {
                $q->where('r.metodo', 'QR / Transferencia');
            }
        }

        return $q;
    }

    /*
    |--------------------------------------------------------------------------
    | Historial de accesos
    |--------------------------------------------------------------------------
    */

    private function accesosQuery(array $f): Builder
    {
        $limiteActividad = now()
            ->subMinutes(max(1, (int) config('session.lifetime', 120)))
            ->format('Y-m-d H:i:s');

        $base = DB::table('historial_accesos as h')
            ->join('usuarios as u', 'u.id', '=', 'h.usuario_id')
            ->when($f['usuario_id'], function ($q) use ($f) {
                $q->where('h.usuario_id', $f['usuario_id']);
            })
            ->select([
                'h.id',
                'h.usuario_id',
                'u.username as usuario',
                'h.fecha_ingreso as fecha',
                'h.fecha_cierre as cierre',
                'h.ip',
            ])
            // Si no hay última actividad se usa el ingreso (antes quedaba
            // como "Con actividad reciente" por comparar contra NULL).
            ->selectRaw('COALESCE(h.ultima_actividad, h.fecha_ingreso) AS ultima_actividad')
            ->selectRaw("
                CASE
                    WHEN h.fecha_cierre IS NOT NULL
                        THEN 'Cerrada por el usuario'
                    WHEN COALESCE(h.ultima_actividad, h.fecha_ingreso)
                        < CAST(? AS TIMESTAMP)
                        THEN 'Sin actividad reciente'
                    ELSE 'Con actividad reciente'
                END AS estado
            ", [$limiteActividad])
            ->selectRaw("
                CASE
                    WHEN h.origen = 'login'
                        THEN 'Inicio de sesión'
                    ELSE 'Sesión existente o recuperada'
                END AS origen
            ")
            ->selectRaw("
                FLOOR(
                    GREATEST(
                        EXTRACT(
                            EPOCH FROM (
                                COALESCE(
                                    h.fecha_cierre,
                                    h.ultima_actividad,
                                    h.fecha_ingreso
                                ) - h.fecha_ingreso
                            )
                        ),
                        0
                    )
                )::BIGINT AS segundos
            ");

        $this->periodo($base, 'h.fecha_ingreso', $f);

        return DB::query()
            ->fromSub($base, 'ac')
            ->select([
                'ac.id',
                'ac.usuario_id',
                'ac.usuario',
                'ac.fecha',
                'ac.ultima_actividad',
                'ac.cierre',
                'ac.ip',
                'ac.estado',
                'ac.origen',
            ])
            ->selectRaw("
                CONCAT(
                    (ac.segundos / 3600),
                    ' h ',
                    LPAD(((ac.segundos % 3600) / 60)::TEXT, 2, '0'),
                    ' min ',
                    LPAD((ac.segundos % 60)::TEXT, 2, '0'),
                    ' s'
                ) AS duracion
            ");
    }

    /*
    |--------------------------------------------------------------------------
    | Inventario
    |--------------------------------------------------------------------------
    */

    private function inventarioQuery(array $f): Builder
    {
        $stock = DB::table('inventarios')
            ->select('producto_id')
            ->selectRaw('SUM(COALESCE(cantidad, 0)) AS cantidad')
            ->when($f['almacen_id'], function ($q) use ($f) {
                $q->where('almacen_id', $f['almacen_id']);
            })
            ->groupBy('producto_id');

        return DB::table('productos as p')
            ->leftJoinSub($stock, 's', function ($join) {
                $join->on('s.producto_id', '=', 'p.id');
            })
            ->leftJoin('unidades_medida as um', 'um.id', '=', 'p.unidad_medida_id')
            ->where('p.estado', 'activo')
            ->when($f['producto_id'], function ($q) use ($f) {
                $q->where('p.id', $f['producto_id']);
            })
            ->select([
                'p.id',
                'p.codigo_interno as codigo',
                'p.nombre as producto',
                'um.abreviatura as unidad',
                'p.stock_minimo as minimo',
                'p.stock_maximo as maximo',
            ])
            ->selectRaw('COALESCE(s.cantidad, 0) AS cantidad')
            ->selectRaw("
                CASE
                    WHEN COALESCE(s.cantidad, 0) <= 0 THEN 'Agotado'
                    WHEN COALESCE(s.cantidad, 0)
                        <= COALESCE(p.stock_minimo, 0) THEN 'Bajo'
                    ELSE 'Normal'
                END AS estado_stock
            ");
    }

    /*
    |--------------------------------------------------------------------------
    | Lotes
    |--------------------------------------------------------------------------
    */

    private function lotesQuery(array $f): Builder
    {
        return DB::table('lotes as l')
            ->join('productos as p', 'p.id', '=', 'l.producto_id')
            ->leftJoin('unidades_medida as um', 'um.id', '=', 'p.unidad_medida_id')
            ->leftJoin('recepcion_detalles as rd', function ($join) {
                $join->on('rd.id', '=', 'l.recepcion_detalle_id')
                    ->on('rd.producto_id', '=', 'l.producto_id');
            })
            ->leftJoin('recepciones as re', 're.id', '=', 'rd.recepcion_id')
            ->leftJoin('almacenes as a', 'a.id', '=', 're.almacen_id')
            ->where('l.cantidad_actual', '>', 0)
            ->when($f['almacen_id'], function ($q) use ($f) {
                $q->where('re.almacen_id', $f['almacen_id']);
            })
            ->when($f['producto_id'], function ($q) use ($f) {
                $q->where('l.producto_id', $f['producto_id']);
            })
            ->select([
                'l.id',
                'p.codigo_interno as codigo',
                'p.nombre as producto',
                'l.codigo_lote as lote',
                'a.nombre as almacen',
                're.fecha_recepcion as fecha',
                'l.fecha_vencimiento as vencimiento',
                'l.cantidad_actual as cantidad',
                'um.abreviatura as unidad',
            ])
            ->selectRaw("
                CASE
                    WHEN l.fecha_vencimiento IS NULL THEN 'Sin fecha'
                    WHEN l.fecha_vencimiento < CAST(? AS DATE)
                        THEN 'Vencido'
                    WHEN l.fecha_vencimiento <= CAST(? AS DATE)
                        THEN 'Próximo a vencer'
                    ELSE 'Vigente'
                END AS estado_lote
            ", [
                now()->toDateString(),
                now()->addDays(30)->toDateString(),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Movimientos
    |--------------------------------------------------------------------------
    */

    private function movimientosQuery(array $f): Builder
    {
        $q = DB::table('movimiento_detalles as d')
            ->join('movimientos_inventario as m', 'm.id', '=', 'd.movimiento_id')
            ->join('productos as p', 'p.id', '=', 'd.producto_id')
            ->leftJoin('unidades_medida as um', 'um.id', '=', 'p.unidad_medida_id')
            ->leftJoin('lotes as l', 'l.id', '=', 'd.lote_id')
            ->leftJoin('tipos_movimiento as tm', 'tm.id', '=', 'm.tipo_movimiento_id')
            ->leftJoin('usuarios as u', 'u.id', '=', 'm.usuario_id')
            ->leftJoin('almacenes as ao', 'ao.id', '=', 'm.almacen_origen_id')
            ->leftJoin('almacenes as ad', 'ad.id', '=', 'm.almacen_destino_id')
            ->leftJoin('ubicaciones as uo', 'uo.id', '=', 'm.ubicacion_origen_id')
            ->leftJoin('ubicaciones as ud', 'ud.id', '=', 'm.ubicacion_destino_id')
            ->when($f['producto_id'], function ($q) use ($f) {
                $q->where('d.producto_id', $f['producto_id']);
            })
            ->when($f['usuario_id'], function ($q) use ($f) {
                $q->where('m.usuario_id', $f['usuario_id']);
            })
            ->when($f['tipo_movimiento_id'], function ($q) use ($f) {
                $q->where('m.tipo_movimiento_id', $f['tipo_movimiento_id']);
            })
            ->when($f['almacen_id'], function ($q) use ($f) {
                $q->where(function ($sub) use ($f) {
                    $sub
                        ->where('m.almacen_origen_id', $f['almacen_id'])
                        ->orWhere('m.almacen_destino_id', $f['almacen_id']);
                });
            })
            ->select([
                'd.id',
                'm.numero_movimiento as numero',
                'm.fecha_movimiento as fecha',
                'tm.nombre as tipo_movimiento',
                'p.nombre as producto',
                'l.codigo_lote as lote',
                'd.cantidad',
                'um.abreviatura as unidad',
                'u.username as usuario',
                'm.estado',
            ])
            ->selectRaw("
                NULLIF(CONCAT_WS(' / ', ao.nombre, uo.codigo), '') AS origen,
                NULLIF(CONCAT_WS(' / ', ad.nombre, ud.codigo), '') AS destino
            ");

        $this->periodo($q, 'm.fecha_movimiento', $f);

        return $q;
    }

    /*
    |--------------------------------------------------------------------------
    | Cobros
    |--------------------------------------------------------------------------
    */

    private function cobrosQuery(array $f): Builder
    {
        $q = DB::table('venta_pagos as vp')
            ->join('ventas as v', 'v.id', '=', 'vp.venta_id')
            ->join('turnos_caja as tc', 'tc.id', '=', 'v.turno_caja_id')
            ->join('usuarios as u', 'u.id', '=', 'tc.usuario_id')
            ->join('metodos_pago as mp', 'mp.id', '=', 'vp.metodo_pago_id')
            ->whereRaw(
                "LOWER(TRIM(COALESCE(v.estado, ''))) = ?",
                ['completada']
            )
            ->whereRaw(
                'LOWER(TRIM(mp.nombre)) IN (?, ?, ?, ?, ?)',
                [
                    'efectivo',
                    'qr',
                    'transferencia',
                    'qr/transferencia',
                    'qr / transferencia',
                ]
            )
            ->when($f['usuario_id'], function ($q) use ($f) {
                $q->where('tc.usuario_id', $f['usuario_id']);
            })
            ->select([
                'vp.id',
                'v.id as venta_id',
                'v.numero_venta as numero',
                'u.username as usuario',
                'vp.monto as importe',
                'vp.referencia',
            ])
            ->selectRaw('COALESCE(vp.fecha_pago, v.fecha_venta) AS fecha')
            ->selectRaw("
                CASE
                    WHEN LOWER(TRIM(mp.nombre)) = 'efectivo'
                        THEN 'Efectivo'
                    ELSE 'QR / Transferencia'
                END AS metodo
            ");

        $this->periodo(
            $q,
            DB::raw('COALESCE(vp.fecha_pago, v.fecha_venta)'),
            $f
        );

        return $q;
    }

    /*
    |--------------------------------------------------------------------------
    | Productos vendidos
    |--------------------------------------------------------------------------
    */

    private function vendidosQuery(array $f): Builder
    {
        $q = DB::table('ventas_detalles as d')
            ->join('ventas as v', 'v.id', '=', 'd.venta_id')
            ->join('turnos_caja as tc', 'tc.id', '=', 'v.turno_caja_id')
            ->join('productos as p', 'p.id', '=', 'd.producto_id')
            ->leftJoin('unidades_medida as um', 'um.id', '=', 'p.unidad_medida_id')
            ->whereRaw(
                "LOWER(TRIM(COALESCE(v.estado, ''))) = ?",
                ['completada']
            )
            ->when($f['producto_id'], function ($q) use ($f) {
                $q->where('p.id', $f['producto_id']);
            })
            ->when($f['usuario_id'], function ($q) use ($f) {
                $q->where('tc.usuario_id', $f['usuario_id']);
            })
            ->select([
                'p.id',
                'p.codigo_interno as codigo',
                'p.nombre as producto',
                'um.abreviatura as unidad',
            ])
            ->selectRaw('SUM(d.cantidad) AS cantidad')
            ->selectRaw('SUM(d.subtotal) AS importe')
            ->selectRaw('COUNT(DISTINCT v.id) AS ventas')
            ->groupBy(
                'p.id',
                'p.codigo_interno',
                'p.nombre',
                'um.abreviatura'
            );

        $this->periodo($q, 'v.fecha_venta', $f);

        return $q;
    }

    /*
    |--------------------------------------------------------------------------
    | Tareas
    |--------------------------------------------------------------------------
    */

    private function tareasQuery(array $f): Builder
    {
        $asignados = DB::table('tarea_asignaciones as ta')
            ->join('usuarios as u', 'u.id', '=', 'ta.usuario_id')
            ->select('ta.tarea_id')
            ->selectRaw("
                STRING_AGG(
                    DISTINCT u.username,
                    ', ' ORDER BY u.username
                ) AS asignados
            ")
            ->groupBy('ta.tarea_id');

        $q = DB::table('tareas as t')
            ->leftJoin('tipos_tarea as tt', 'tt.id', '=', 't.tipo_tarea_id')
            ->leftJoin('tarea_estados as te', 'te.id', '=', 't.estado_id')
            ->leftJoin('almacenes as a', 'a.id', '=', 't.almacen_id')
            ->leftJoinSub($asignados, 'asig', function ($join) {
                $join->on('asig.tarea_id', '=', 't.id');
            })
            ->when($f['almacen_id'], function ($q) use ($f) {
                $q->where('t.almacen_id', $f['almacen_id']);
            })
            ->when($f['usuario_id'], function ($q) use ($f) {
                $q->whereExists(function ($sub) use ($f) {
                    $sub
                        ->selectRaw('1')
                        ->from('tarea_asignaciones as filtro_asig')
                        ->whereColumn('filtro_asig.tarea_id', 't.id')
                        ->where('filtro_asig.usuario_id', $f['usuario_id']);
                });
            })
            ->select([
                't.id',
                't.codigo',
                'tt.nombre as tipo_tarea',
                'te.nombre as estado',
                't.prioridad',
                'a.nombre as almacen',
                'asig.asignados',
                't.fecha_creacion as fecha',
                't.fecha_limite as limite',
                't.fecha_finalizacion as finalizacion',
            ]);

        $this->periodo($q, 't.fecha_creacion', $f);

        return $q;
    }

    /*
    |--------------------------------------------------------------------------
    | Indicadores (una sola consulta agregada por reporte)
    |--------------------------------------------------------------------------
    */

    private function resumen(Builder $consulta, string $tipo, int $total): array
    {
        $resumen = [
            [
                'titulo'  => $tipo === 'accesos'
                    ? 'Ingresos registrados'
                    : 'Registros encontrados',
                'valor'   => $total,
                'formato' => 'entero',
            ],
        ];

        // [alias, título, expresión SQL, formato]
        $indicadores = match ($tipo) {
            'accesos' => [
                ['usuarios', 'Usuarios con ingresos',
                    'COUNT(DISTINCT a.usuario_id)', 'entero'],
                ['cierres', 'Cierres confirmados',
                    'COUNT(*) FILTER (WHERE a.cierre IS NOT NULL)', 'entero'],
                ['activos', 'Con actividad reciente',
                    "COUNT(*) FILTER (WHERE a.estado = 'Con actividad reciente')", 'entero'],
                ['inactivos', 'Sin actividad reciente',
                    "COUNT(*) FILTER (WHERE a.estado = 'Sin actividad reciente')", 'entero'],
            ],

            'inventario' => [
                ['agotados', 'Productos agotados',
                    "COUNT(*) FILTER (WHERE a.estado_stock = 'Agotado')", 'entero'],
                ['bajos', 'Productos con stock bajo',
                    "COUNT(*) FILTER (WHERE a.estado_stock = 'Bajo')", 'entero'],
                ['normales', 'Productos con stock normal',
                    "COUNT(*) FILTER (WHERE a.estado_stock = 'Normal')", 'entero'],
            ],

            'cobros' => [
                ['efectivo', 'Cobrado en efectivo',
                    "COALESCE(SUM(a.importe) FILTER (WHERE a.metodo = 'Efectivo'), 0)", 'moneda'],
                ['qr', 'Cobrado en QR / transferencia',
                    "COALESCE(SUM(a.importe) FILTER (WHERE a.metodo = 'QR / Transferencia'), 0)", 'moneda'],
                ['total', 'Total cobrado',
                    'COALESCE(SUM(a.importe), 0)', 'moneda'],
                ['ventas', 'Ventas con pagos',
                    'COUNT(DISTINCT a.venta_id)', 'entero'],
            ],

            'vendidos' => [
                ['importe', 'Importe de detalles',
                    'COALESCE(SUM(a.importe), 0)', 'moneda'],
            ],

            default => [],
        };

        if ($indicadores === []) {
            return $resumen;
        }

        $expresiones = [];

        foreach ($indicadores as [$alias, , $sql]) {
            $expresiones[$alias] = $sql;
        }

        $fila = $this->agregar($consulta, $expresiones);

        foreach ($indicadores as [$alias, $titulo, , $formato]) {
            $valor = $fila->{$alias} ?? 0;

            $resumen[] = [
                'titulo'  => $titulo,
                'valor'   => $formato === 'entero' ? (int) $valor : (float) $valor,
                'formato' => $formato,
            ];
        }

        return $resumen;
    }

    private function agregar(Builder $consulta, array $expresiones): object
    {
        $q = DB::query()->fromSub($consulta, 'a');

        foreach ($expresiones as $alias => $sql) {
            $q->selectRaw("{$sql} AS {$alias}");
        }

        return $q->first() ?? (object) [];
    }

    /*
    |--------------------------------------------------------------------------
    | Gráficos y orden
    |--------------------------------------------------------------------------
    */

    private function grafico(Builder $consulta, array $config): array
    {
        $campo = $config['campo'];

        $q = DB::query()
            ->fromSub($consulta, 'g')
            // Las etiquetas nulas aparecían vacías en el gráfico.
            ->selectRaw("COALESCE(CAST(g.{$campo} AS TEXT), 'Sin dato') AS etiqueta")
            ->groupBy('etiqueta');

        if ($config['medida'] === 'importe') {
            $q->selectRaw('COALESCE(SUM(g.importe), 0) AS valor');
        } else {
            $q->selectRaw('COUNT(*) AS valor');
        }

        return [
            'titulo' => $config['titulo'],
            'moneda' => $config['medida'] === 'importe',
            'datos'  => $q
                ->orderByDesc('valor')
                ->orderBy('etiqueta')
                ->limit(8)
                ->get(),
        ];
    }

    private function ordenar(Builder $consulta, array $reporte): Builder
    {
        $consulta->orderBy('r.' . $reporte['orden'], $reporte['direccion']);

        if ($reporte['orden'] !== 'id') {
            $consulta->orderBy('r.id');
        }

        return $consulta;
    }

    /*
    |--------------------------------------------------------------------------
    | Nombre del usuario y descripción de filtros
    |--------------------------------------------------------------------------
    */

    private function nombreUsuario(Request $request): string
    {
        $usuario  = $request->user();
        $empleado = $usuario?->empleado;

        $nombre = trim(
            ($empleado?->nombres ?? '')
            . ' '
            . ($empleado?->apellidos ?? '')
        );

        if ($nombre !== '') {
            return $nombre;
        }

        return trim((string) ($usuario?->username ?? '')) ?: 'Administrador';
    }

    private function describirFiltro(string $tipo, array $f): array
    {
        return match ($tipo) {
            'usuario' => [
                'Usuario filtrado',
                $f['usuario_id']
                    ? (string) DB::table('usuarios')
                        ->where('id', $f['usuario_id'])
                        ->value('username')
                    : 'Todos',
            ],

            'producto' => [
                'Producto',
                $f['producto_id']
                    ? (string) DB::table('productos')
                        ->where('id', $f['producto_id'])
                        ->value('nombre')
                    : 'Todos',
            ],

            'almacen' => [
                'Almacén',
                $f['almacen_id']
                    ? (string) DB::table('almacenes')
                        ->where('id', $f['almacen_id'])
                        ->value('nombre')
                    : 'Todos',
            ],

            'movimiento' => [
                'Tipo de movimiento',
                $f['tipo_movimiento_id']
                    ? (string) DB::table('tipos_movimiento')
                        ->where('id', $f['tipo_movimiento_id'])
                        ->value('nombre')
                    : 'Todos',
            ],

            'stock' => ['Estado del stock', $f['estado_stock']],

            'vencimiento' => ['Vencimiento', $f['vencimiento']],

            'pago' => [
                'Método de pago',
                match ($f['metodo_pago']) {
                    'efectivo' => 'Efectivo',
                    'qr'       => 'QR / Transferencia',
                    default    => 'Todos',
                },
            ],

            default => [$tipo, 'Todos'],
        };
    }
}