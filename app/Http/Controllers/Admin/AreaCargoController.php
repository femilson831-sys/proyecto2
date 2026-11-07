<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Cargo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AreaCargoController extends Controller
{
    /**
     * Listado general de áreas y cargos.
     */
    public function index()
    {
        $areas = Area::withCount('empleados')
            ->orderBy('nombre')
            ->get();

        $cargos = Cargo::withCount('empleados')
            ->orderBy('nombre')
            ->get();

        return view(
            'admin.areas-cargos.index',
            compact('areas', 'cargos')
        );
    }


    /**
     * Crear área.
     */
    public function storeArea(Request $request)
    {
        $datos = $request->validate(
            [
                'nombre' => [
                    'required',
                    'string',
                    'max:100',
                    'unique:areas,nombre',
                ],

                'descripcion' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
            ],
            [
                'nombre.required' =>
                    'El nombre del área es obligatorio.',

                'nombre.unique' =>
                    'Ya existe un área con ese nombre.',

                'nombre.max' =>
                    'El nombre no puede superar los 100 caracteres.',

                'descripcion.max' =>
                    'La descripción no puede superar los 255 caracteres.',
            ]
        );

        Area::create([
            'nombre' => trim($datos['nombre']),

            'descripcion' =>
                !empty($datos['descripcion'])
                    ? trim($datos['descripcion'])
                    : null,

            'estado' => true,
        ]);

        return redirect()
            ->route('admin.areas-cargos.index')
            ->with(
                'success',
                'Área registrada correctamente.'
            );
    }


    /**
     * Actualizar área.
     */
    public function updateArea(
        Request $request,
        Area $area
    ) {
        $datos = $request->validate(
            [
                'nombre' => [
                    'required',
                    'string',
                    'max:100',

                    Rule::unique(
                        'areas',
                        'nombre'
                    )->ignore($area->id),
                ],

                'descripcion' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
            ],
            [
                'nombre.required' =>
                    'El nombre del área es obligatorio.',

                'nombre.unique' =>
                    'Ya existe un área con ese nombre.',

                'nombre.max' =>
                    'El nombre no puede superar los 100 caracteres.',

                'descripcion.max' =>
                    'La descripción no puede superar los 255 caracteres.',
            ]
        );

        $area->update([
            'nombre' => trim($datos['nombre']),

            'descripcion' =>
                !empty($datos['descripcion'])
                    ? trim($datos['descripcion'])
                    : null,
        ]);

        return redirect()
            ->route('admin.areas-cargos.index')
            ->with(
                'success',
                'Área actualizada correctamente.'
            );
    }


    /**
     * Activar / desactivar área.
     */
    public function cambiarEstadoArea(Area $area)
    {
        $nuevoEstado = !$area->estado;

        /*
         * Evitamos desactivar un área que todavía
         * tenga empleados activos asociados.
         */
        if (!$nuevoEstado) {

            $empleadosActivos = $area
                ->empleados()
                ->where('estado', 'activo')
                ->count();

            if ($empleadosActivos > 0) {

                return redirect()
                    ->route('admin.areas-cargos.index')
                    ->with(
                        'error',
                        'No puedes desactivar esta área porque tiene empleados activos asociados.'
                    );
            }
        }

        $area->update([
            'estado' => $nuevoEstado,
        ]);

        return redirect()
            ->route('admin.areas-cargos.index')
            ->with(
                'success',
                $nuevoEstado
                    ? 'Área activada correctamente.'
                    : 'Área desactivada correctamente.'
            );
    }


    /**
     * Crear cargo.
     */
    public function storeCargo(Request $request)
    {
        $datos = $request->validate(
            [
                'nombre' => [
                    'required',
                    'string',
                    'max:100',
                    'unique:cargos,nombre',
                ],

                'descripcion' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
            ],
            [
                'nombre.required' =>
                    'El nombre del cargo es obligatorio.',

                'nombre.unique' =>
                    'Ya existe un cargo con ese nombre.',

                'nombre.max' =>
                    'El nombre no puede superar los 100 caracteres.',

                'descripcion.max' =>
                    'La descripción no puede superar los 255 caracteres.',
            ]
        );

        Cargo::create([
            'nombre' => trim($datos['nombre']),

            'descripcion' =>
                !empty($datos['descripcion'])
                    ? trim($datos['descripcion'])
                    : null,

            'estado' => true,
        ]);

        return redirect()
            ->route('admin.areas-cargos.index')
            ->with(
                'success',
                'Cargo registrado correctamente.'
            );
    }


    /**
     * Actualizar cargo.
     */
    public function updateCargo(
        Request $request,
        Cargo $cargo
    ) {
        $datos = $request->validate(
            [
                'nombre' => [
                    'required',
                    'string',
                    'max:100',

                    Rule::unique(
                        'cargos',
                        'nombre'
                    )->ignore($cargo->id),
                ],

                'descripcion' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
            ],
            [
                'nombre.required' =>
                    'El nombre del cargo es obligatorio.',

                'nombre.unique' =>
                    'Ya existe un cargo con ese nombre.',

                'nombre.max' =>
                    'El nombre no puede superar los 100 caracteres.',

                'descripcion.max' =>
                    'La descripción no puede superar los 255 caracteres.',
            ]
        );

        $cargo->update([
            'nombre' => trim($datos['nombre']),

            'descripcion' =>
                !empty($datos['descripcion'])
                    ? trim($datos['descripcion'])
                    : null,
        ]);

        return redirect()
            ->route('admin.areas-cargos.index')
            ->with(
                'success',
                'Cargo actualizado correctamente.'
            );
    }


    /**
     * Activar / desactivar cargo.
     */
    public function cambiarEstadoCargo(Cargo $cargo)
    {
        $nuevoEstado = !$cargo->estado;

        if (!$nuevoEstado) {

            $empleadosActivos = $cargo
                ->empleados()
                ->where('estado', 'activo')
                ->count();

            if ($empleadosActivos > 0) {

                return redirect()
                    ->route('admin.areas-cargos.index')
                    ->with(
                        'error',
                        'No puedes desactivar este cargo porque tiene empleados activos asociados.'
                    );
            }
        }

        $cargo->update([
            'estado' => $nuevoEstado,
        ]);

        return redirect()
            ->route('admin.areas-cargos.index')
            ->with(
                'success',
                $nuevoEstado
                    ? 'Cargo activado correctamente.'
                    : 'Cargo desactivado correctamente.'
            );
    }
}