<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AlmacenController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'in:todos,activo,inactivo'],
        ]);

        $buscar = trim($filtros['buscar'] ?? '');
        $estado = $filtros['estado'] ?? 'todos';

        $resumenInventario = DB::table('inventarios')
            ->select('almacen_id')
            ->selectRaw('COUNT(*) AS productos_registrados')
            ->selectRaw('SUM(CASE WHEN COALESCE(cantidad, 0) > 0 THEN 1 ELSE 0 END) AS productos_con_stock')
            ->selectRaw('SUM(COALESCE(cantidad, 0)) AS unidades_disponibles')
            ->groupBy('almacen_id');

        $consulta = DB::table('almacenes as a')
            ->leftJoinSub($resumenInventario, 'resumen', function ($join) {
                $join->on('resumen.almacen_id', '=', 'a.id');
            })
            ->select([
                'a.id',
                'a.codigo',
                'a.nombre',
                'a.descripcion',
                'a.direccion',
                'a.capacidad_m3',
                'a.estado',
            ])
            ->selectRaw('COALESCE(resumen.productos_registrados, 0) AS productos_registrados')
            ->selectRaw('COALESCE(resumen.productos_con_stock, 0) AS productos_con_stock')
            ->selectRaw('COALESCE(resumen.unidades_disponibles, 0) AS unidades_disponibles');

        if ($buscar !== '') {
            $consulta->where(function ($query) use ($buscar) {
                $query->where('a.codigo', 'like', "%{$buscar}%")
                    ->orWhere('a.nombre', 'like', "%{$buscar}%")
                    ->orWhere('a.direccion', 'like', "%{$buscar}%");
            });
        }

        if ($estado !== 'todos') {
            $consulta->where('a.estado', $estado);
        }

        $almacenes = $consulta
            ->orderBy('a.nombre')
            ->paginate(12)
            ->withQueryString();

        $totalAlmacenes = DB::table('almacenes')->count();
        $almacenesActivos = DB::table('almacenes')
            ->where('estado', 'activo')
            ->count();
        $almacenesInactivos = DB::table('almacenes')
            ->where('estado', 'inactivo')
            ->count();

        return view('admin.almacenes.index', compact(
            'almacenes',
            'buscar',
            'estado',
            'totalAlmacenes',
            'almacenesActivos',
            'almacenesInactivos'
        ));
    }

    public function create()
    {
        return view('admin.almacenes.form', [
            'almacen' => null,
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validarDatos($request);
        $datos['codigo'] = mb_strtoupper(trim($datos['codigo']), 'UTF-8');
        $datos['nombre'] = trim($datos['nombre']);

        DB::table('almacenes')->insert($datos);

        return redirect()
            ->route('admin.almacenes.index')
            ->with('success', 'El almacén se registró correctamente.');
    }

    public function edit(int $almacen)
    {
        $datosAlmacen = DB::table('almacenes')
            ->where('id', $almacen)
            ->first();

        abort_if(!$datosAlmacen, 404);

        return view('admin.almacenes.form', [
            'almacen' => $datosAlmacen,
        ]);
    }

    public function update(Request $request, int $almacen)
    {
        $existe = DB::table('almacenes')
            ->where('id', $almacen)
            ->exists();

        abort_if(!$existe, 404);

        $datos = $this->validarDatos($request, $almacen);
        $datos['codigo'] = mb_strtoupper(trim($datos['codigo']), 'UTF-8');
        $datos['nombre'] = trim($datos['nombre']);

        DB::table('almacenes')
            ->where('id', $almacen)
            ->update($datos);

        return redirect()
            ->route('admin.almacenes.index')
            ->with('success', 'Los datos del almacén se actualizaron correctamente.');
    }

    public function cambiarEstado(int $almacen)
    {
        $datosAlmacen = DB::table('almacenes')
            ->where('id', $almacen)
            ->first();

        abort_if(!$datosAlmacen, 404);

        $nuevoEstado = $datosAlmacen->estado === 'activo'
            ? 'inactivo'
            : 'activo';

        DB::table('almacenes')
            ->where('id', $almacen)
            ->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado === 'activo'
            ? 'El almacén se activó correctamente.'
            : 'El almacén se desactivó correctamente.';

        return redirect()
            ->route('admin.almacenes.index')
            ->with('success', $mensaje);
    }

    private function validarDatos(Request $request, ?int $almacenId = null): array
    {
        return $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:30',
                Rule::unique('almacenes', 'codigo')->ignore($almacenId),
            ],
            'nombre' => ['required', 'string', 'max:120'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'capacidad_m3' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999.999',
            ],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
        ], [
            'codigo.required' => 'Ingresa el código del almacén.',
            'codigo.unique' => 'Ese código ya está registrado.',
            'codigo.max' => 'El código no debe superar los 30 caracteres.',
            'nombre.required' => 'Ingresa el nombre del almacén.',
            'nombre.max' => 'El nombre no debe superar los 120 caracteres.',
            'descripcion.max' => 'La descripción no debe superar los 255 caracteres.',
            'direccion.max' => 'La dirección no debe superar los 255 caracteres.',
            'capacidad_m3.numeric' => 'La capacidad debe ser un número.',
            'capacidad_m3.min' => 'La capacidad no puede ser negativa.',
            'estado.required' => 'Selecciona el estado del almacén.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ]);
    }
}