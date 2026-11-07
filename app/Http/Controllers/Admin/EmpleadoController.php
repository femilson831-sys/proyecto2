<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Empleado;
use App\Models\Area;
use App\Models\Cargo;

use App\Models\HistorialEmpleado;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Validation\Rule;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Alignment;

use PhpOffice\PhpSpreadsheet\Style\Border;

use PhpOffice\PhpSpreadsheet\Style\Fill;

use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class EmpleadoController extends Controller

{

    /*

    |--------------------------------------------------------------------------

    | LISTADO

    |--------------------------------------------------------------------------

    */

    public function index(Request $request)

    {

        $buscar = trim((string) $request->get('buscar', ''));

        $empleados = Empleado::query()
            ->with(['area', 'cargo'])

            ->when($buscar !== '', function ($query) use ($buscar) {

                $query->where(function ($q) use ($buscar) {

                    $q->where('codigo_empleado', 'like', "%{$buscar}%")

                        ->orWhere('nombres', 'like', "%{$buscar}%")

                        ->orWhere('apellidos', 'like', "%{$buscar}%")

                        ->orWhere('ci', 'like', "%{$buscar}%")

                        ->orWhere('correo', 'like', "%{$buscar}%")

                        ->orWhere('celular', 'like', "%{$buscar}%");

                });

            })

            ->orderByDesc('id')

            ->paginate(10)

            ->withQueryString();

        return view(

            'admin.empleados.index',

            compact('empleados', 'buscar')

        );

    }

    /*

    |--------------------------------------------------------------------------

    | CREAR

    |--------------------------------------------------------------------------

    */

    public function create()

    {

        $areas = Area::where('estado', 1)
            ->orderBy('nombre')
            ->get();

        $cargos = Cargo::where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return view(
            'admin.empleados.create',
            compact('areas', 'cargos')
        );

    }

    /*

    |--------------------------------------------------------------------------

    | GUARDAR

    |--------------------------------------------------------------------------

    */

    public function store(Request $request)

    {
        $datos = $request->validate(

            [

                'nombres' => ['required', 'string', 'max:100'],
                'apellidos' => ['required', 'string', 'max:100'],
                'ci' => ['nullable', 'string', 'max:30', 'unique:empleados,ci'],
                'fecha_nacimiento' => ['nullable', 'date', 'before_or_equal:today'],
                'genero' => ['nullable', 'string', 'max:30'],
                'foto' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:4096'],
                'foto_capturada' => ['nullable', 'string'],
                'telefono' => ['nullable', 'string', 'max:30'],
                'celular' => ['nullable', 'string', 'max:30'],
                'correo' => ['nullable', 'email', 'max:150'],
                'direccion' => ['nullable', 'string', 'max:255'],
                'ciudad' => ['nullable', 'string', 'max:100'],
                'fecha_ingreso' => ['nullable', 'date'],
                'area_id' => ['nullable', 'integer', Rule::exists('areas', 'id')->where(fn ($query) => $query->where('estado', 1))],
                'cargo_id' => ['nullable', 'integer', Rule::exists('cargos', 'id')->where(fn ($query) => $query->where('estado', 1))],
                'tipo_contrato' => ['nullable', 'string', 'max:60'],
                'estado' => ['required', Rule::in(['activo', 'inactivo'])],
                'observaciones' => ['nullable', 'string'],

            ],

            [

                'nombres.required' => 'Los nombres son obligatorios.',
                'apellidos.required' => 'Los apellidos son obligatorios.',
                'ci.unique' => 'El CI ingresado ya pertenece a otro empleado.',
                'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser posterior a hoy.',
                'correo.email' => 'Ingrese un correo electrónico válido.',
                'foto.file' => 'La fotografía seleccionada no es un archivo válido.',
                'foto.mimetypes' => 'La fotografía debe ser JPG, JPEG, PNG o WEBP.',
                'foto.max' => 'La fotografía no puede superar los 4 MB.',
                'area_id.exists' => 'El área seleccionada no es válida o está inactiva.',
                'cargo_id.exists' => 'El cargo seleccionado no es válido o está inactivo.',
                'estado.required' => 'Debe seleccionar el estado del empleado.',
                'estado.in' => 'El estado seleccionado no es válido.',

            ]

        );

        $datos = $this->normalizarDatos($datos);

        unset($datos['foto_capturada']);

        if ($request->filled('foto_capturada')) {
            $datos['foto'] = $this->guardarFotoCapturada($request->input('foto_capturada'));
        } elseif ($request->hasFile('foto')) {
            $datos['foto'] = $this->guardarFotoArchivo($request->file('foto'));
        }

        if ($datos['estado'] === 'activo') {

            $datos['fecha_salida'] = null;

        } else {

            $datos['fecha_salida'] = now()->toDateString();

        }

        $empleado = DB::transaction(function () use ($datos) {

            /*
             * codigo_empleado es NOT NULL y UNIQUE.
             * Se usa un valor temporal único para obtener el ID
             * autoincremental y luego generar EMP-001, EMP-002, etc.
             */
            $datos['codigo_empleado'] = uniqid('TMP-', true);

            $empleado = Empleado::create($datos);

            $empleado->codigo_empleado =
                'EMP-' . str_pad(
                    (string) $empleado->id,
                    3,
                    '0',
                    STR_PAD_LEFT
                );

            $empleado->save();
            $empleado->refresh();

            $this->registrarHistorial(
                $empleado,
                'creado',
                'El empleado fue registrado en el sistema con el código ' . $empleado->codigo_empleado . '.',
                null,
                $empleado->estado,
                null,
                $empleado->toArray()
            );

            return $empleado;

        });

        return redirect()

            ->route('admin.empleados.index')

            ->with(

                'success',

                'Empleado registrado correctamente con el código ' .
                $empleado->codigo_empleado . '.'

            );

    }

    /*

    |--------------------------------------------------------------------------

    | VER

    |--------------------------------------------------------------------------

    */

    public function show(Empleado $empleado)

    {
        $empleado->load(['area', 'cargo']);

        return view(

            'admin.empleados.show',

            compact('empleado')

        );

    }

    /*

    |--------------------------------------------------------------------------

    | EDITAR

    |--------------------------------------------------------------------------

    */

    public function edit(Empleado $empleado)
    {
        $areas = Area::query()
            ->where(function ($query) use ($empleado) {
                $query->where('estado', 1)
                    ->orWhere('id', $empleado->area_id);
            })
            ->orderBy('nombre')
            ->get();

        $cargos = Cargo::query()
            ->where(function ($query) use ($empleado) {
                $query->where('estado', 1)
                    ->orWhere('id', $empleado->cargo_id);
            })
            ->orderBy('nombre')
            ->get();

        return view(
            'admin.empleados.edit',
            compact('empleado', 'areas', 'cargos')
        );
    }

    /*

    |--------------------------------------------------------------------------

    | ACTUALIZAR

    |--------------------------------------------------------------------------

    */

    public function update(

        Request $request,

        Empleado $empleado

    ) {

        $datos = $request->validate([

            'nombres' => [

                'required',

                'string',

                'max:100',

            ],

            'apellidos' => [

                'required',

                'string',

                'max:100',

            ],

            'ci' => [

                'nullable',

                'string',

                'max:30',

                Rule::unique(

                    'empleados',

                    'ci'

                )->ignore($empleado->id),

            ],

            'fecha_nacimiento' => [

                'nullable',

                'date',

                'before_or_equal:today',

            ],

            'genero' => [
                'nullable',
                'string',
                'max:30',
            ],

            'foto' => [
                'nullable',
                'file',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:4096',
            ],
            'foto_capturada' => ['nullable', 'string'],

            'telefono' => [

                'nullable',

                'string',

                'max:30',

            ],

            'celular' => [

                'nullable',

                'string',

                'max:30',

            ],

            'correo' => [

                'nullable',

                'email',

                'max:150',

            ],

            'direccion' => [

                'nullable',

                'string',

                'max:255',

            ],

            'ciudad' => [

                'nullable',

                'string',

                'max:100',

            ],

            'fecha_ingreso' => [
                'nullable',
                'date',
            ],

            'area_id' => [
                'nullable',
                'integer',
                Rule::exists('areas', 'id')->where(function ($query) use ($empleado) {
                    $query->where('estado', 1)->orWhere('id', $empleado->area_id);
                }),
            ],

            'cargo_id' => [
                'nullable',
                'integer',
                Rule::exists('cargos', 'id')->where(function ($query) use ($empleado) {
                    $query->where('estado', 1)->orWhere('id', $empleado->cargo_id);
                }),
            ],

            'tipo_contrato' => [

                'nullable',

                'string',

                'max:60',

            ],

            'estado' => [

                'required',

                Rule::in([

                    'activo',

                    'inactivo',

                ]),

            ],

            'observaciones' => [

                'nullable',

                'string',

            ],

        ], [
            'nombres.required' => 'Los nombres son obligatorios.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'ci.unique' => 'El CI ingresado ya pertenece a otro empleado.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser posterior a hoy.',
            'correo.email' => 'Ingrese un correo electrónico válido.',
            'foto.file' => 'La fotografía seleccionada no es un archivo válido.',
            'foto.mimetypes' => 'La fotografía debe ser JPG, JPEG, PNG o WEBP.',
            'foto.max' => 'La fotografía no puede superar los 4 MB.',
            'area_id.exists' => 'El área seleccionada no es válida o está inactiva.',
            'cargo_id.exists' => 'El cargo seleccionado no es válido o está inactivo.',
            'estado.required' => 'Debe seleccionar el estado del empleado.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ]);

        $datos = $this->normalizarDatos($datos);

        $fotoAnterior = $empleado->foto;
        $fotoNueva = false;

        unset($datos['foto_capturada']);

        if ($request->filled('foto_capturada')) {
            $datos['foto'] = $this->guardarFotoCapturada($request->input('foto_capturada'));
            $fotoNueva = true;
        } elseif ($request->hasFile('foto')) {
            $datos['foto'] = $this->guardarFotoArchivo($request->file('foto'));
            $fotoNueva = true;
        }

        $estadoAnterior = $empleado->estado;
        $datosAnteriores = $empleado->toArray();

        if ($datos['estado'] === 'activo') {

            $datos['fecha_salida'] = null;

        } elseif ($estadoAnterior === 'activo') {

            $datos['fecha_salida'] = now()->toDateString();

        }

        $empleado->update($datos);
        $empleado->refresh();

        if ($fotoNueva && $fotoAnterior && $fotoAnterior !== $empleado->foto) {
            $this->eliminarFotoEmpleado($fotoAnterior);
        }

        if ($estadoAnterior !== $empleado->estado) {
            $this->sincronizarEstadoUsuario($empleado);
        }

        $this->registrarHistorial(
            $empleado, 'editado', 'La información del empleado fue actualizada.',
            $estadoAnterior, $empleado->estado, $datosAnteriores, $empleado->toArray()
        );

        return redirect()

            ->route('admin.empleados.index')

            ->with(

                'success',

                'Empleado actualizado correctamente.'

            );

    }

    /*

    |--------------------------------------------------------------------------

    | ACTIVAR / DESACTIVAR

    |--------------------------------------------------------------------------

    */

    public function cambiarEstado(Empleado $empleado)

    {

        $nuevoEstado =

            $empleado->estado === 'activo'

                ? 'inactivo'

                : 'activo';

        /*

         * Evitar que el usuario conectado

         * desactive su propio empleado.

         */

        if (

            $nuevoEstado === 'inactivo' &&

            Auth::user()?->empleado_id === $empleado->id

        ) {

            return redirect()

                ->route('admin.empleados.index')

                ->with(

                    'error',

                    'No puedes desactivar tu propio registro mientras tienes la sesión iniciada.'

                );

        }

        $estadoAnterior = $empleado->estado;
        $datosAnteriores = $empleado->toArray();

        if ($nuevoEstado === 'activo') {

            $empleado->estado = 'activo';

            $empleado->fecha_salida = null;

        } else {

            $empleado->estado = 'inactivo';

            $empleado->fecha_salida = now()->toDateString();

        }

        $empleado->save();

        $this->sincronizarEstadoUsuario($empleado);

        $this->registrarHistorial(
            $empleado,
            $nuevoEstado === 'activo' ? 'activado' : 'desactivado',
            $nuevoEstado === 'activo' ? 'El empleado fue activado.' : 'El empleado fue desactivado.',
            $estadoAnterior, $nuevoEstado, $datosAnteriores, $empleado->fresh()->toArray()
        );

        $mensaje =

            $nuevoEstado === 'activo'

                ? 'Empleado activado correctamente.'

                : 'Empleado desactivado correctamente.';

        return redirect()

            ->route('admin.empleados.index')

            ->with('success', $mensaje);

    }

    /*

    |--------------------------------------------------------------------------

    | ELIMINAR

    |--------------------------------------------------------------------------

    |

    | NO se borra físicamente.

    |

    | Se registra:

    | - estado = inactivo

    | - fecha_salida

    | - eliminado_por = usuario conectado

    | - deleted_at = fecha/hora de eliminación

    |

    */

    public function destroy(Empleado $empleado)

    {

        /*

         * Evitar que el administrador elimine

         * su propio registro.

         */

        if (

            Auth::user()?->empleado_id ===

            $empleado->id

        ) {

            return redirect()

                ->route('admin.empleados.index')

                ->with(

                    'error',

                    'No puedes eliminar tu propio registro de empleado mientras tienes la sesión iniciada.'

                );

        }

        $estadoAnterior = $empleado->estado;
        $datosAnteriores = $empleado->toArray();

        $empleado->update([

            'estado' => 'inactivo',

            'fecha_salida' => now()->toDateString(),

            'eliminado_por' => Auth::id(),

        ]);

        $this->sincronizarEstadoUsuario(

            $empleado

        );

        /*

         * Soft Delete.

         * Laravel establece deleted_at.

         */

        $this->registrarHistorial(
            $empleado, 'eliminado', 'El empleado fue eliminado y enviado al historial.',
            $estadoAnterior, 'inactivo', $datosAnteriores, $empleado->fresh()->toArray()
        );

        $empleado->delete();

        return redirect()

            ->route('admin.empleados.index')

            ->with(

                'success',

                'Empleado eliminado correctamente y enviado al historial.'

            );

    }

    /*

    |--------------------------------------------------------------------------

    | HISTORIAL

    |--------------------------------------------------------------------------

    */

    public function historial(Request $request)

    {

        $buscar = trim(

            (string) $request->get(

                'buscar',

                ''

            )

        );

        $empleados =

            Empleado::onlyTrashed()

                ->with([

                    'eliminadoPor.empleadoConEliminados'

                ])

                ->when(

                    $buscar !== '',

                    function ($query) use ($buscar) {

                        $query->where(

                            function ($q) use ($buscar) {

                                $q->where(

                                    'codigo_empleado',

                                    'like',

                                    "%{$buscar}%"

                                )

                                    ->orWhere(

                                        'nombres',

                                        'like',

                                        "%{$buscar}%"

                                    )

                                    ->orWhere(

                                        'apellidos',

                                        'like',

                                        "%{$buscar}%"

                                    )

                                    ->orWhere(

                                        'ci',

                                        'like',

                                        "%{$buscar}%"

                                    );

                            }

                        );

                    }

                )

                ->orderByDesc('deleted_at')

                ->paginate(10)

                ->withQueryString();

        return view(

            'admin.empleados.historial',

            compact(

                'empleados',

                'buscar'

            )

        );

    }

    /*

    |--------------------------------------------------------------------------

    | VER DETALLE DEL HISTORIAL

    |--------------------------------------------------------------------------

    */

    public function historialEmpleado(int $id)
    {
        $empleado = Empleado::withTrashed()->findOrFail($id);

        $historial = HistorialEmpleado::with(['usuario.empleadoConEliminados'])
            ->where('empleado_id', $empleado->id)
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.empleados.historial-individual', compact('empleado', 'historial'));
    }

    public function verHistorial(int $id)

    {

        $empleado =

            Empleado::onlyTrashed()

                ->with([

                    'eliminadoPor.empleadoConEliminados'

                ])

                ->findOrFail($id);

        return view(

            'admin.empleados.historial-show',

            compact('empleado')

        );

    }

    /*

    |--------------------------------------------------------------------------

    | RESTAURAR

    |--------------------------------------------------------------------------

    */

    public function restaurar(int $id)

    {

        $empleado =

            Empleado::onlyTrashed()

                ->findOrFail($id);

        /*

         * Elimina el valor de deleted_at.

         */

        $estadoAnterior = 'eliminado';
        $datosAnteriores = $empleado->toArray();

        $empleado->restore();

        $empleado->update([

            'estado' => 'activo',

            'fecha_salida' => null,

            /*

             * Se limpia porque el empleado

             * ya no está eliminado.

             *

             * Más adelante podemos crear

             * una auditoría independiente para

             * conservar todos los eventos.

             */

            'eliminado_por' => null,

        ]);

        $this->sincronizarEstadoUsuario(

            $empleado

        );

        $empleado->refresh();
        $this->registrarHistorial(
            $empleado, 'restaurado', 'El empleado fue restaurado desde el historial.',
            $estadoAnterior, $empleado->estado, $datosAnteriores, $empleado->toArray()
        );

        return redirect()

            ->route('admin.empleados.historial')

            ->with(

                'success',

                'Empleado restaurado correctamente.'

            );

    }

    /*

    |--------------------------------------------------------------------------

    | EXPORTAR EXCEL PROFESIONAL (.XLSX)

    |--------------------------------------------------------------------------

    */

    public function exportarExcel(Request $request)

    {

        $buscar = trim(

            (string) $request->get(

                'buscar',

                ''

            )

        );

        $empleados =

            $this->consultaHistorial(

                $buscar

            )->get();

        $usuarioActual = Auth::user();

        $nombreGenerador =

            $usuarioActual?->username

            ?? 'Sistema';

        if (

            $usuarioActual &&

            $usuarioActual->empleado

        ) {

            $nombreGenerador = trim(

                $usuarioActual

                    ->empleado

                    ->nombres

                . ' ' .

                $usuarioActual

                    ->empleado

                    ->apellidos

            );

        }

        /*

        |--------------------------------------------------------------------------

        | CREAR LIBRO

        |--------------------------------------------------------------------------

        */

        $spreadsheet = new Spreadsheet();

        $spreadsheet

            ->getProperties()

            ->setCreator('Minisúper NANOS')

            ->setLastModifiedBy(

                $nombreGenerador

            )

            ->setTitle(

                'Historial de Empleados'

            )

            ->setSubject(

                'Historial de empleados eliminados'

            )

            ->setDescription(

                'Reporte generado por el Sistema Web NANOS.'

            );

        /*

        |--------------------------------------------------------------------------

        | HOJA 1 - HISTORIAL

        |--------------------------------------------------------------------------

        */

        $sheet =

            $spreadsheet

                ->getActiveSheet();

        $sheet->setTitle(

            'Historial'

        );

        /*

        |--------------------------------------------------------------------------

        | CABECERA PRINCIPAL

        |--------------------------------------------------------------------------

        */

        $sheet->mergeCells(

            'A1:N1'

        );

        $sheet->setCellValue(

            'A1',

            'MINISÚPER NANOS'

        );

        $sheet

            ->getStyle('A1')

            ->applyFromArray([

                'font' => [

                    'bold' => true,

                    'size' => 22,

                    'color' => [

                        'rgb' => 'FFFFFF',

                    ],

                ],

                'fill' => [

                    'fillType' =>

                        Fill::FILL_SOLID,

                    'startColor' => [

                        'rgb' => 'B5121B',

                    ],

                ],

                'alignment' => [

                    'horizontal' =>

                        Alignment::HORIZONTAL_CENTER,

                    'vertical' =>

                        Alignment::VERTICAL_CENTER,

                ],

            ]);

        $sheet

            ->getRowDimension(1)

            ->setRowHeight(38);

        /*

        |--------------------------------------------------------------------------

        | SUBTÍTULO

        |--------------------------------------------------------------------------

        */

        $sheet->mergeCells(

            'A2:N2'

        );

        $sheet->setCellValue(

            'A2',

            'HISTORIAL DE EMPLEADOS ELIMINADOS'

        );

        $sheet

            ->getStyle('A2')

            ->applyFromArray([

                'font' => [

                    'bold' => true,

                    'size' => 13,

                    'color' => [

                        'rgb' => 'B5121B',

                    ],

                ],

                'alignment' => [

                    'horizontal' =>

                        Alignment::HORIZONTAL_CENTER,

                    'vertical' =>

                        Alignment::VERTICAL_CENTER,

                ],

            ]);

        $sheet

            ->getRowDimension(2)

            ->setRowHeight(25);

        /*

        |--------------------------------------------------------------------------

        | INFORMACIÓN DEL REPORTE

        |--------------------------------------------------------------------------

        */

        $sheet->setCellValue(

            'A4',

            'FECHA DE GENERACIÓN'

        );

        $sheet->setCellValue(

            'B4',

            now()->format(

                'd/m/Y H:i:s'

            )

        );

        $sheet->setCellValue(

            'D4',

            'GENERADO POR'

        );

        $sheet->setCellValue(

            'E4',

            $nombreGenerador

        );

        $sheet->setCellValue(

            'H4',

            'USUARIO'

        );

        $sheet->setCellValue(

            'I4',

            $usuarioActual?->username

                ?? 'Sistema'

        );

        $sheet->setCellValue(

            'L4',

            'REGISTROS'

        );

        $sheet->setCellValue(

            'M4',

            $empleados->count()

        );

        $sheet

            ->getStyle(

                'A4:N4'

            )

            ->getFont()

            ->setSize(9);

        $sheet

            ->getStyle(

                'A4,D4,H4,L4'

            )

            ->getFont()

            ->setBold(true)

            ->getColor()

            ->setRGB('B5121B');

        /*

        |--------------------------------------------------------------------------

        | FILTRO UTILIZADO

        |--------------------------------------------------------------------------

        */

        if ($buscar !== '') {

            $sheet->mergeCells(

                'A5:N5'

            );

            $sheet->setCellValue(

                'A5',

                'Filtro aplicado: ' .

                $buscar

            );

            $sheet

                ->getStyle('A5')

                ->getFont()

                ->setItalic(true)

                ->getColor()

                ->setRGB('666666');

        }

        /*

        |--------------------------------------------------------------------------

        | ENCABEZADOS DE LA TABLA

        |--------------------------------------------------------------------------

        */

        $filaEncabezado = 7;

        $encabezados = [

            'N°',

            'Código',

            'Nombres',

            'Apellidos',

            'CI',

            'Celular',

            'Correo',

            'Ciudad',

            'Ingreso',

            'Salida',

            'Eliminación',

            'Eliminado por',

            'Usuario',

            'Observaciones',

        ];

        $columnas = range(

            'A',

            'N'

        );

        foreach (

            $encabezados

            as $indice => $encabezado

        ) {

            $sheet->setCellValue(

                $columnas[$indice] .

                $filaEncabezado,

                $encabezado

            );

        }

        $sheet

            ->getStyle('A7:N7')

            ->applyFromArray([

                'font' => [

                    'bold' => true,

                    'size' => 10,

                    'color' => [

                        'rgb' => 'FFFFFF',

                    ],

                ],

                'fill' => [

                    'fillType' =>

                        Fill::FILL_SOLID,

                    'startColor' => [

                        'rgb' => '292B2F',

                    ],

                ],

                'alignment' => [

                    'horizontal' =>

                        Alignment::HORIZONTAL_CENTER,

                    'vertical' =>

                        Alignment::VERTICAL_CENTER,

                    'wrapText' => true,

                ],

                'borders' => [

                    'allBorders' => [

                        'borderStyle' =>

                            Border::BORDER_THIN,

                        'color' => [

                            'rgb' => '44474C',

                        ],

                    ],

                ],

            ]);

        $sheet

            ->getRowDimension(7)

            ->setRowHeight(30);

        /*

        |--------------------------------------------------------------------------

        | REGISTROS

        |--------------------------------------------------------------------------

        */

        $fila = 8;

        $numero = 1;

        foreach (

            $empleados

            as $empleado

        ) {

            $responsable =

                $empleado->eliminadoPor;

            $nombreResponsable =

                $this->nombreResponsable(

                    $empleado

                );

            $sheet->setCellValue(

                "A{$fila}",

                $numero

            );

            $sheet->setCellValue(

                "B{$fila}",

                $empleado

                    ->codigo_empleado

            );

            $sheet->setCellValue(

                "C{$fila}",

                $empleado->nombres

            );

            $sheet->setCellValue(

                "D{$fila}",

                $empleado->apellidos

            );

            $sheet->setCellValue(

                "E{$fila}",

                $empleado->ci ?: '—'

            );

            $sheet->setCellValue(

                "F{$fila}",

                $empleado->celular ?: '—'

            );

            $sheet->setCellValue(

                "G{$fila}",

                $empleado->correo ?: '—'

            );

            $sheet->setCellValue(

                "H{$fila}",

                $empleado->ciudad ?: '—'

            );

            $sheet->setCellValue(

                "I{$fila}",

                $empleado->fecha_ingreso

                    ?->format('d/m/Y')

                    ?? '—'

            );

            $sheet->setCellValue(

                "J{$fila}",

                $empleado->fecha_salida

                    ?->format('d/m/Y')

                    ?? '—'

            );

            $sheet->setCellValue(

                "K{$fila}",

                $empleado->deleted_at

                    ?->format(

                        'd/m/Y H:i:s'

                    )

                    ?? '—'

            );

            $sheet->setCellValue(

                "L{$fila}",

                $nombreResponsable

            );

            $sheet->setCellValue(

                "M{$fila}",

                $responsable?->username

                    ?? '—'

            );

            $sheet->setCellValue(

                "N{$fila}",

                $empleado->observaciones

                    ?: '—'

            );

            /*

             * Filas alternadas.

             */

            if ($numero % 2 === 0) {

                $sheet

                    ->getStyle(

                        "A{$fila}:N{$fila}"

                    )

                    ->getFill()

                    ->setFillType(

                        Fill::FILL_SOLID

                    )

                    ->getStartColor()

                    ->setRGB(

                        'F5F5F5'

                    );

            }

            /*

             * Bordes.

             */

            $sheet

                ->getStyle(

                    "A{$fila}:N{$fila}"

                )

                ->applyFromArray([

                    'borders' => [

                        'bottom' => [

                            'borderStyle' =>

                                Border::BORDER_HAIR,

                            'color' => [

                                'rgb' =>

                                    'D9D9D9',

                            ],

                        ],

                    ],

                    'alignment' => [

                        'vertical' =>

                            Alignment::VERTICAL_CENTER,

                    ],

                ]);

            /*

             * Código en rojo.

             */

            $sheet

                ->getStyle(

                    "B{$fila}"

                )

                ->getFont()

                ->setBold(true)

                ->getColor()

                ->setRGB(

                    'B5121B'

                );

            /*

             * Nombre y responsable en negrita.

             */

            $sheet

                ->getStyle(

                    "C{$fila}:D{$fila}"

                )

                ->getFont()

                ->setBold(true);

            $sheet

                ->getStyle(

                    "L{$fila}"

                )

                ->getFont()

                ->setBold(true);

            $sheet

                ->getRowDimension(

                    $fila

                )

                ->setRowHeight(24);

            $fila++;

            $numero++;

        }

        /*

        |--------------------------------------------------------------------------

        | SI NO EXISTEN DATOS

        |--------------------------------------------------------------------------

        */

        if ($empleados->isEmpty()) {

            $sheet->mergeCells(

                'A8:N8'

            );

            $sheet->setCellValue(

                'A8',

                'No existen empleados eliminados en el historial.'

            );

            $sheet

                ->getStyle('A8')

                ->getAlignment()

                ->setHorizontal(

                    Alignment::HORIZONTAL_CENTER

                );

            $sheet

                ->getStyle('A8')

                ->getFont()

                ->setItalic(true)

                ->getColor()

                ->setRGB('777777');

            $sheet

                ->getRowDimension(8)

                ->setRowHeight(30);

        }

        /*

        |--------------------------------------------------------------------------

        | FILTROS

        |--------------------------------------------------------------------------

        */

        $ultimaFilaDatos =

            max(

                8,

                $fila - 1

            );

        if (!$empleados->isEmpty()) {

            $sheet->setAutoFilter(

                "A7:N{$ultimaFilaDatos}"

            );

        }

        /*

        |--------------------------------------------------------------------------

        | CONGELAR CABECERA

        |--------------------------------------------------------------------------

        */

        $sheet->freezePane(

            'A8'

        );

        /*

        |--------------------------------------------------------------------------

        | ANCHOS DE COLUMNAS

        |--------------------------------------------------------------------------

        */

        $anchos = [

            'A' => 7,

            'B' => 15,

            'C' => 20,

            'D' => 20,

            'E' => 15,

            'F' => 16,

            'G' => 29,

            'H' => 18,

            'I' => 14,

            'J' => 14,

            'K' => 21,

            'L' => 27,

            'M' => 18,

            'N' => 38,

        ];

        foreach (

            $anchos

            as $letra => $ancho

        ) {

            $sheet

                ->getColumnDimension(

                    $letra

                )

                ->setWidth(

                    $ancho

                );

        }

        /*

        |--------------------------------------------------------------------------

        | ALINEACIÓN

        |--------------------------------------------------------------------------

        */

        $sheet

            ->getStyle(

                "A8:B{$ultimaFilaDatos}"

            )

            ->getAlignment()

            ->setHorizontal(

                Alignment::HORIZONTAL_CENTER

            );

        $sheet

            ->getStyle(

                "I8:K{$ultimaFilaDatos}"

            )

            ->getAlignment()

            ->setHorizontal(

                Alignment::HORIZONTAL_CENTER

            );

        $sheet

            ->getStyle(

                "A7:N{$ultimaFilaDatos}"

            )

            ->getAlignment()

            ->setWrapText(true);

        /*

        |--------------------------------------------------------------------------

        | CONFIGURACIÓN DE IMPRESIÓN

        |--------------------------------------------------------------------------

        */

        $sheet

            ->getPageSetup()

            ->setOrientation(

                PageSetup::ORIENTATION_LANDSCAPE

            );

        $sheet

            ->getPageSetup()

            ->setPaperSize(

                PageSetup::PAPERSIZE_A4

            );

        $sheet

            ->getPageSetup()

            ->setFitToWidth(1);

        $sheet

            ->getPageSetup()

            ->setFitToHeight(0);

        $sheet

            ->getPageMargins()

            ->setTop(0.4);

        $sheet

            ->getPageMargins()

            ->setBottom(0.4);

        $sheet

            ->getPageMargins()

            ->setLeft(0.3);

        $sheet

            ->getPageMargins()

            ->setRight(0.3);

        $sheet

            ->getHeaderFooter()

            ->setOddFooter(

                '&LNANOS - Historial de empleados' .

                '&RPágina &P de &N'

            );

        /*

        |--------------------------------------------------------------------------

        | HOJA 2 - RESUMEN

        |--------------------------------------------------------------------------

        */

        $resumen =

            $spreadsheet

                ->createSheet();

        $resumen->setTitle(

            'Resumen'

        );

        $resumen->mergeCells(

            'A1:D1'

        );

        $resumen->setCellValue(

            'A1',

            'MINISÚPER NANOS'

        );

        $resumen

            ->getStyle('A1')

            ->applyFromArray([

                'font' => [

                    'bold' => true,

                    'size' => 20,

                    'color' => [

                        'rgb' => 'FFFFFF',

                    ],

                ],

                'fill' => [

                    'fillType' =>

                        Fill::FILL_SOLID,

                    'startColor' => [

                        'rgb' => 'B5121B',

                    ],

                ],

                'alignment' => [

                    'horizontal' =>

                        Alignment::HORIZONTAL_CENTER,

                    'vertical' =>

                        Alignment::VERTICAL_CENTER,

                ],

            ]);

        $resumen

            ->getRowDimension(1)

            ->setRowHeight(36);

        $resumen->mergeCells(

            'A2:D2'

        );

        $resumen->setCellValue(

            'A2',

            'RESUMEN DEL HISTORIAL DE EMPLEADOS'

        );

        $resumen

            ->getStyle('A2')

            ->getFont()

            ->setBold(true)

            ->setSize(12)

            ->getColor()

            ->setRGB(

                'B5121B'

            );

        $resumen

            ->getStyle('A2')

            ->getAlignment()

            ->setHorizontal(

                Alignment::HORIZONTAL_CENTER

            );

        /*

         * Total.

         */

        $resumen->setCellValue(

            'A4',

            'TOTAL DE EMPLEADOS ELIMINADOS'

        );

        $resumen->setCellValue(

            'B4',

            $empleados->count()

        );

        $resumen

            ->getStyle('A4:B4')

            ->applyFromArray([

                'font' => [

                    'bold' => true,

                ],

                'fill' => [

                    'fillType' =>

                        Fill::FILL_SOLID,

                    'startColor' => [

                        'rgb' => 'F3F3F3',

                    ],

                ],

                'borders' => [

                    'allBorders' => [

                        'borderStyle' =>

                            Border::BORDER_THIN,

                        'color' => [

                            'rgb' => 'DDDDDD',

                        ],

                    ],

                ],

            ]);

        $resumen

            ->getStyle('B4')

            ->getFont()

            ->setSize(16)

            ->getColor()

            ->setRGB(

                'B5121B'

            );

        /*

         * Información.

         */

        $resumen->setCellValue(

            'A6',

            'Generado por'

        );

        $resumen->setCellValue(

            'B6',

            $nombreGenerador

        );

        $resumen->setCellValue(

            'A7',

            'Usuario'

        );

        $resumen->setCellValue(

            'B7',

            $usuarioActual?->username

                ?? 'Sistema'

        );

        $resumen->setCellValue(

            'A8',

            'Fecha de generación'

        );

        $resumen->setCellValue(

            'B8',

            now()->format(

                'd/m/Y H:i:s'

            )

        );

        $resumen->setCellValue(

            'A9',

            'Filtro aplicado'

        );

        $resumen->setCellValue(

            'B9',

            $buscar !== ''

                ? $buscar

                : 'Sin filtro'

        );

        $resumen

            ->getStyle(

                'A6:A9'

            )

            ->getFont()

            ->setBold(true);

        $resumen

            ->getStyle(

                'A6:A9'

            )

            ->getFont()

            ->getColor()

            ->setRGB(

                '666666'

            );

        /*

         * Información institucional.

         */

        $resumen->mergeCells(

            'A12:D12'

        );

        $resumen->setCellValue(

            'A12',

            'Sistema Web de Optimización de la Gestión de Inventarios y Ventas'

        );

        $resumen

            ->getStyle('A12')

            ->getAlignment()

            ->setHorizontal(

                Alignment::HORIZONTAL_CENTER

            );

        $resumen

            ->getStyle('A12')

            ->getFont()

            ->setItalic(true)

            ->getColor()

            ->setRGB(

                '777777'

            );

        $resumen->mergeCells(

            'A13:D13'

        );

        $resumen->setCellValue(

            'A13',

            'Minisúper NANOS - El Alto - 2026'

        );

        $resumen

            ->getStyle('A13')

            ->getAlignment()

            ->setHorizontal(

                Alignment::HORIZONTAL_CENTER

            );

        $resumen

            ->getStyle('A13')

            ->getFont()

            ->setBold(true)

            ->getColor()

            ->setRGB(

                'B5121B'

            );

        /*

         * Anchos.

         */

        $resumen

            ->getColumnDimension('A')

            ->setWidth(35);

        $resumen

            ->getColumnDimension('B')

            ->setWidth(35);

        $resumen

            ->getColumnDimension('C')

            ->setWidth(20);

        $resumen

            ->getColumnDimension('D')

            ->setWidth(20);

        /*

        |--------------------------------------------------------------------------

        | VOLVER A LA HOJA HISTORIAL

        |--------------------------------------------------------------------------

        */

        $spreadsheet

            ->setActiveSheetIndex(0);

        /*

        |--------------------------------------------------------------------------

        | DESCARGAR XLSX

        |--------------------------------------------------------------------------

        */

        $nombreArchivo =

            'Historial_Empleados_NANOS_' .

            now()->format(

                'Y-m-d_H-i-s'

            ) .

            '.xlsx';

        $writer =

            new Xlsx(

                $spreadsheet

            );

        /*

         * Crear archivo temporal del sistema.

         */

        $rutaTemporal =

            tempnam(

                sys_get_temp_dir(),

                'nanos_excel_'

            );

        $writer->save(

            $rutaTemporal

        );

        /*

         * Liberar memoria del libro.

         */

        $spreadsheet

            ->disconnectWorksheets();

        unset(

            $spreadsheet

        );

        return response()

            ->download(

                $rutaTemporal,

                $nombreArchivo,

                [

                    'Content-Type' =>

                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

                ]

            )

            ->deleteFileAfterSend(

                true

            );

    }

    /*

    |--------------------------------------------------------------------------

    | REPORTE PARA PDF

    |--------------------------------------------------------------------------

    */

    public function exportarPdf(Request $request)

    {

        $buscar = trim(

            (string) $request->get(

                'buscar',

                ''

            )

        );

        $empleados =

            $this->consultaHistorial(

                $buscar

            )->get();

        $generadoPor =

            Auth::user();

        return view(

            'admin.empleados.historial-pdf',

            compact(

                'empleados',

                'generadoPor',

                'buscar'

            )

        );

    }

    /*

    |--------------------------------------------------------------------------

    | CONSULTA REUTILIZABLE DEL HISTORIAL

    |--------------------------------------------------------------------------

    */

    private function consultaHistorial(

        string $buscar = ''

    ) {

        return Empleado::onlyTrashed()

            ->with([

                'eliminadoPor.empleadoConEliminados'

            ])

            ->when(

                $buscar !== '',

                function ($query) use ($buscar) {

                    $query->where(

                        function ($q) use ($buscar) {

                            $q->where(

                                'codigo_empleado',

                                'like',

                                "%{$buscar}%"

                            )

                                ->orWhere(

                                    'nombres',

                                    'like',

                                    "%{$buscar}%"

                                )

                                ->orWhere(

                                    'apellidos',

                                    'like',

                                    "%{$buscar}%"

                                )

                                ->orWhere(

                                    'ci',

                                    'like',

                                    "%{$buscar}%"

                                );

                        }

                    );

                }

            )

            ->orderByDesc(

                'deleted_at'

            );

    }

    /*

    |--------------------------------------------------------------------------

    | NOMBRE DEL RESPONSABLE

    |--------------------------------------------------------------------------

    */

    private function nombreResponsable(

        Empleado $empleado

    ): string {

        $usuario =

            $empleado->eliminadoPor;

        if (!$usuario) {

            return 'Sin registro';

        }

        if ($usuario->empleado) {

            return trim(

                $usuario

                    ->empleado

                    ->nombres

                . ' ' .

                $usuario

                    ->empleado

                    ->apellidos

            );

        }

        return $usuario->username;

    }

    /*

    |--------------------------------------------------------------------------

    | SINCRONIZAR ESTADO DEL USUARIO

    |--------------------------------------------------------------------------

    */

    private function sincronizarEstadoUsuario(

        Empleado $empleado

    ): void {

        $empleado->loadMissing(

            'usuario'

        );

        if (!$empleado->usuario) {

            return;

        }

        $empleado

            ->usuario

            ->update([

                'estado' =>

                    $empleado->estado,

            ]);

    }

    /*

    |--------------------------------------------------------------------------

    | NORMALIZAR DATOS

    |--------------------------------------------------------------------------

    */

    private function normalizarDatos(

        array $datos

    ): array {

        $camposOpcionales = [

            'ci',

            'fecha_nacimiento',

            'genero',

            'telefono',

            'celular',

            'correo',

            'direccion',

            'ciudad',

            'fecha_ingreso',

            'tipo_contrato',

            'observaciones',

        ];

        foreach (

            $camposOpcionales

            as $campo

        ) {

            if (

                array_key_exists(

                    $campo,

                    $datos

                )

                &&

                is_string(

                    $datos[$campo]

                )

                &&

                trim(

                    $datos[$campo]

                ) === ''

            ) {

                $datos[$campo] = null;

            }

        }

        return $datos;

    }

    /*

|--------------------------------------------------------------------------

| REGISTRAR HISTORIAL DEL EMPLEADO

|--------------------------------------------------------------------------

*/

private function registrarHistorial(

    Empleado $empleado,

    string $accion,

    string $descripcion,

    ?string $estadoAnterior = null,

    ?string $estadoNuevo = null,

    ?array $datosAnteriores = null,

    ?array $datosNuevos = null

): void {

    HistorialEmpleado::create([

        'empleado_id' => $empleado->id,

        'usuario_id' => Auth::id(),

        'accion' => $accion,

        'descripcion' => $descripcion,

        'estado_anterior' => $estadoAnterior,

        'estado_nuevo' => $estadoNuevo,

        'datos_anteriores' => $datosAnteriores,

        'datos_nuevos' => $datosNuevos,

        'ip' => request()->ip(),

    ]);

    }

    /*

|--------------------------------------------------------------------------

| EXPORTAR HISTORIAL INDIVIDUAL A EXCEL

|--------------------------------------------------------------------------

*/

public function exportarHistorialEmpleadoExcel(int $id)

{

    $empleado = Empleado::withTrashed()

        ->findOrFail($id);

    $historial = HistorialEmpleado::with([

            'usuario.empleadoConEliminados'

        ])

        ->where('empleado_id', $empleado->id)

        ->orderBy('created_at')

        ->get();

    $spreadsheet = new Spreadsheet();

    $hoja = $spreadsheet->getActiveSheet();

    $hoja->setTitle('Historial');

    /*

    |--------------------------------------------------------------------------

    | TÍTULO

    |--------------------------------------------------------------------------

    */

    $hoja->mergeCells('A1:J1');

    $hoja->setCellValue(

        'A1',

        'MINISÚPER NANOS'

    );

    $hoja->mergeCells('A2:J2');

    $hoja->setCellValue(

        'A2',

        'HISTORIAL INDIVIDUAL DEL EMPLEADO'

    );

    $hoja->getStyle('A1:J1')

        ->getFill()

        ->setFillType(

            Fill::FILL_SOLID

        )

        ->getStartColor()

        ->setARGB('FFB5121B');

    $hoja->getStyle('A1:J1')

        ->getFont()

        ->setBold(true)

        ->setSize(18)

        ->getColor()

        ->setARGB('FFFFFFFF');

    $hoja->getStyle('A2:J2')

        ->getFont()

        ->setBold(true)

        ->setSize(12)

        ->getColor()

        ->setARGB('FFB5121B');

    $hoja->getStyle('A1:J2')

        ->getAlignment()

        ->setHorizontal(

            Alignment::HORIZONTAL_CENTER

        );

    /*

    |--------------------------------------------------------------------------

    | DATOS DEL EMPLEADO

    |--------------------------------------------------------------------------

    */

    $nombreCompleto = trim(

        $empleado->nombres . ' ' .

        $empleado->apellidos

    );

    $hoja->setCellValue(

        'A4',

        'Código:'

    );

    $hoja->setCellValue(

        'B4',

        $empleado->codigo_empleado

    );

    $hoja->setCellValue(

        'D4',

        'Empleado:'

    );

    $hoja->mergeCells('E4:G4');

    $hoja->setCellValue(

        'E4',

        $nombreCompleto

    );

    $hoja->setCellValue(

        'H4',

        'CI:'

    );

    $hoja->mergeCells('I4:J4');

    $hoja->setCellValue(

        'I4',

        $empleado->ci ?: 'No registrado'

    );

    $hoja->setCellValue(

        'A5',

        'Ingreso:'

    );

    $hoja->setCellValue(

        'B5',

        $empleado->fecha_ingreso

            ?->format('d/m/Y')

            ?? 'No registrado'

    );

    $hoja->setCellValue(

        'D5',

        'Estado actual:'

    );

    $estadoActual =

        $empleado->trashed()

            ? 'ELIMINADO'

            : strtoupper(

                $empleado->estado

            );

    $hoja->setCellValue(

        'E5',

        $estadoActual

    );

    $hoja->setCellValue(

        'H5',

        'Eventos:'

    );

    $hoja->setCellValue(

        'I5',

        $historial->count()

    );

    $hoja->setCellValue(

        'A6',

        'Generado:'

    );

    $hoja->setCellValue(

        'B6',

        now()->format(

            'd/m/Y H:i:s'

        )

    );

    $hoja->setCellValue(

        'D6',

        'Generado por:'

    );

    $usuarioActual = Auth::user();

    $responsableActual =

        $usuarioActual?->empleadoConEliminados;

    $nombreGenerador =

        $responsableActual

            ? trim(

                $responsableActual->nombres .

                ' ' .

                $responsableActual->apellidos

            )

            : (

                $usuarioActual?->username

                ?? 'Sistema'

            );

    $hoja->mergeCells('E6:G6');

    $hoja->setCellValue(

        'E6',

        $nombreGenerador

    );

    /*

    |--------------------------------------------------------------------------

    | CABECERA DE LA TABLA

    |--------------------------------------------------------------------------

    */

    $filaCabecera = 8;

    $cabeceras = [

        'N°',

        'Fecha',

        'Hora',

        'Acción',

        'Descripción',

        'Estado anterior',

        'Estado nuevo',

        'Responsable',

        'Usuario',

        'IP',

    ];

    $columna = 1;

    foreach ($cabeceras as $cabecera) {

        $hoja->setCellValue(

            [$columna, $filaCabecera],

            $cabecera

        );

        $columna++;

    }

    $hoja->getStyle(

        "A{$filaCabecera}:J{$filaCabecera}"

    )

        ->getFill()

        ->setFillType(

            Fill::FILL_SOLID

        )

        ->getStartColor()

        ->setARGB('FF202124');

    $hoja->getStyle(

        "A{$filaCabecera}:J{$filaCabecera}"

    )

        ->getFont()

        ->setBold(true)

        ->getColor()

        ->setARGB('FFFFFFFF');

    /*

    |--------------------------------------------------------------------------

    | DATOS DEL HISTORIAL

    |--------------------------------------------------------------------------

    */

    $fila = 9;

    foreach (

        $historial as $indice => $evento

    ) {

        $usuario =

            $evento->usuario;

        $empleadoResponsable =

            $usuario

                ?->empleadoConEliminados;

        if ($empleadoResponsable) {

            $nombreResponsable = trim(

                $empleadoResponsable->nombres .

                ' ' .

                $empleadoResponsable->apellidos

            );

        } elseif ($usuario) {

            $nombreResponsable =

                $usuario->username;

        } else {

            $nombreResponsable =

                'Sistema';

        }

        $hoja->setCellValue(

            "A{$fila}",

            $indice + 1

        );

        $hoja->setCellValue(

            "B{$fila}",

            $evento->created_at

                ?->format('d/m/Y')

                ?? '—'

        );

        $hoja->setCellValue(

            "C{$fila}",

            $evento->created_at

                ?->format('H:i:s')

                ?? '—'

        );

        $hoja->setCellValue(

            "D{$fila}",

            strtoupper(

                $evento->accion

            )

        );

        $hoja->setCellValue(

            "E{$fila}",

            $evento->descripcion

                ?: 'Acción registrada.'

        );

        $hoja->setCellValue(

            "F{$fila}",

            $evento->estado_anterior

                ?: '—'

        );

        $hoja->setCellValue(

            "G{$fila}",

            $evento->estado_nuevo

                ?: '—'

        );

        $hoja->setCellValue(

            "H{$fila}",

            $nombreResponsable

        );

        $hoja->setCellValue(

            "I{$fila}",

            $usuario?->username

                ?: 'Sistema'

        );

        $hoja->setCellValue(

            "J{$fila}",

            $evento->ip

                ?: 'No registrada'

        );

        /*

         * Filas alternadas.

         */

        if (

            ($indice + 1) % 2 === 0

        ) {

            $hoja->getStyle(

                "A{$fila}:J{$fila}"

            )

                ->getFill()

                ->setFillType(

                    Fill::FILL_SOLID

                )

                ->getStartColor()

                ->setARGB(

                    'FFF3F4F6'

                );

        }

        $fila++;

    }

    /*

    |--------------------------------------------------------------------------

    | BORDES

    |--------------------------------------------------------------------------

    */

    $ultimaFila =

        max(

            9,

            $fila - 1

        );

    $hoja->getStyle(

        "A{$filaCabecera}:J{$ultimaFila}"

    )

        ->getBorders()

        ->getAllBorders()

        ->setBorderStyle(

            Border::BORDER_THIN

        )

        ->getColor()

        ->setARGB('FFD1D5DB');

    /*

    |--------------------------------------------------------------------------

    | ALINEACIÓN

    |--------------------------------------------------------------------------

    */

    $hoja->getStyle(

        "A{$filaCabecera}:J{$ultimaFila}"

    )

        ->getAlignment()

        ->setVertical(

            Alignment::VERTICAL_CENTER

        );

    $hoja->getStyle(

        "E9:E{$ultimaFila}"

    )

        ->getAlignment()

        ->setWrapText(true);

    /*

    |--------------------------------------------------------------------------

    | ANCHOS

    |--------------------------------------------------------------------------

    */

    $hoja->getColumnDimension('A')

        ->setWidth(7);

    $hoja->getColumnDimension('B')

        ->setWidth(14);

    $hoja->getColumnDimension('C')

        ->setWidth(12);

    $hoja->getColumnDimension('D')

        ->setWidth(17);

    $hoja->getColumnDimension('E')

        ->setWidth(42);

    $hoja->getColumnDimension('F')

        ->setWidth(18);

    $hoja->getColumnDimension('G')

        ->setWidth(18);

    $hoja->getColumnDimension('H')

        ->setWidth(27);

    $hoja->getColumnDimension('I')

        ->setWidth(20);

    $hoja->getColumnDimension('J')

        ->setWidth(18);

    /*

    |--------------------------------------------------------------------------

    | FILTRO Y PANEL FIJO

    |--------------------------------------------------------------------------

    */

    if ($historial->count() > 0) {

        $hoja->setAutoFilter(

            "A{$filaCabecera}:J{$ultimaFila}"

        );

    }

    $hoja->freezePane('A9');

    /*

    |--------------------------------------------------------------------------

    | IMPRESIÓN

    |--------------------------------------------------------------------------

    */

    $hoja->getPageSetup()

        ->setOrientation(

            PageSetup::ORIENTATION_LANDSCAPE

        );

    $hoja->getPageSetup()

        ->setPaperSize(

            PageSetup::PAPERSIZE_A4

        );

    $hoja->getPageSetup()

        ->setFitToWidth(1);

    $hoja->getPageSetup()

        ->setFitToHeight(0);

    /*

    |--------------------------------------------------------------------------

    | ARCHIVO

    |--------------------------------------------------------------------------

    */

    $codigoSeguro =

        preg_replace(

            '/[^A-Za-z0-9_-]/',

            '_',

            $empleado->codigo_empleado

        );

    $nombreArchivo =

        'Historial_' .

        $codigoSeguro .

        '_' .

        now()->format(

            'Y-m-d_H-i-s'

        ) .

        '.xlsx';

    $rutaTemporal =

        storage_path(

            'app/' .

            uniqid(

                'historial_empleado_',

                true

            ) .

            '.xlsx'

        );

    $writer =

        new Xlsx(

            $spreadsheet

        );

    $writer->save(

        $rutaTemporal

    );

    return response()

        ->download(

            $rutaTemporal,

            $nombreArchivo

        )

        ->deleteFileAfterSend(true);

    }

    /*

|--------------------------------------------------------------------------

| REPORTE PDF / IMPRESIÓN - HISTORIAL INDIVIDUAL

|--------------------------------------------------------------------------

*/

public function exportarHistorialEmpleadoPdf(int $id)

{

    $empleado = Empleado::withTrashed()

        ->findOrFail($id);

    $historial = HistorialEmpleado::with([

            'usuario.empleadoConEliminados'

        ])

        ->where(

            'empleado_id',

            $empleado->id

        )

        ->orderBy(

            'created_at'

        )

        ->get();

    $generadoPor =

        Auth::user();

    return view(

        'admin.empleados.historial-individual-pdf',

        compact(

            'empleado',

            'historial',

            'generadoPor'

        )

    );

    }

    private function guardarFotoCapturada(string $dataUrl): string
    {
        if (!preg_match('~^data:image/(jpeg|jpg);base64,~i', $dataUrl)) {
            abort(422, 'La fotografía capturada no tiene un formato válido.');
        }

        $base64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
        $contenido = base64_decode($base64, true);

        if ($contenido === false || $contenido === '') {
            abort(422, 'No se pudo procesar la fotografía capturada.');
        }

        if (strlen($contenido) > 4 * 1024 * 1024) {
            abort(422, 'La fotografía capturada no puede superar los 4 MB.');
        }

        $info = @getimagesizefromstring($contenido);
        if ($info === false || ($info['mime'] ?? '') !== 'image/jpeg') {
            abort(422, 'La fotografía capturada no es una imagen JPEG válida.');
        }

        $nombre = 'empleado_' . now()->format('Ymd_His') . '_' . uniqid() . '.jpg';
        $ruta = 'empleados/' . $nombre;

        File::ensureDirectoryExists(public_path('empleados'));

        if (File::put(public_path($ruta), $contenido) === false) {
            abort(500, 'No se pudo guardar la fotografía capturada.');
        }

        return $ruta;
    }


    /**
     * Guarda una foto seleccionada dentro de public/empleados.
     */
    private function guardarFotoArchivo($archivo): string
    {
        $directorio = public_path('empleados');
        File::ensureDirectoryExists($directorio);

        $extension = strtolower($archivo->extension());
        $nombre = 'empleado_' . Str::uuid()->toString() . '.' . $extension;
        $archivo->move($directorio, $nombre);

        return 'empleados/' . $nombre;
    }

    /**
     * Borra fotos nuevas de public y fotos antiguas de storage/app/public.
     */
    private function eliminarFotoEmpleado(?string $ruta): void
    {
        if (!$ruta) {
            return;
        }

        $ruta = ltrim(str_replace('\\', '/', $ruta), '/');

        if (!str_starts_with($ruta, 'empleados/')) {
            return;
        }

        File::delete(public_path($ruta));
        Storage::disk('public')->delete($ruta);
    }

}
