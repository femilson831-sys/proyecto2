<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnidadMedidaController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim((string) $request->query('buscar', ''));

        $unidades = UnidadMedida::query()
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($subquery) use ($buscar) {
                    $subquery
                        ->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('abreviatura', 'like', "%{$buscar}%")
                        ->orWhere('tipo', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('nombre')
            ->get();

        $unidadEditar = $request->filled('editar')
            ? UnidadMedida::findOrFail($request->query('editar'))
            : null;

        return view('admin.unidades-medida.index', compact(
            'unidades',
            'buscar',
            'unidadEditar'
        ));
    }

    public function store(Request $request)
    {
        $request->merge([
            'nombre' => trim((string) $request->input('nombre')),
            'abreviatura' => trim((string) $request->input('abreviatura')),
            'tipo' => trim((string) $request->input('tipo')),
        ]);

        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:80',
                'unique:unidades_medida,nombre',
            ],
            'abreviatura' => [
                'required',
                'string',
                'max:20',
                'unique:unidades_medida,abreviatura',
            ],
            'tipo' => ['nullable', 'string', 'max:40'],
        ], [
            'nombre.required' => 'Escribe el nombre de la unidad.',
            'nombre.unique' => 'Ya existe una unidad con ese nombre.',
            'abreviatura.required' => 'Escribe la abreviatura.',
            'abreviatura.unique' => 'Esa abreviatura ya está registrada.',
        ]);

        UnidadMedida::create([
            'nombre' => $datos['nombre'],
            'abreviatura' => $datos['abreviatura'],
            'tipo' => $datos['tipo'] ?: 'unidad',
            'estado' => true,
        ]);

        return redirect()
            ->route('admin.unidades-medida.index')
            ->with('success', 'Unidad creada correctamente.');
    }

    public function update(Request $request, UnidadMedida $unidadMedida)
    {
        $request->merge([
            'nombre' => trim((string) $request->input('nombre')),
            'abreviatura' => trim((string) $request->input('abreviatura')),
            'tipo' => trim((string) $request->input('tipo')),
        ]);

        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:80',
                Rule::unique('unidades_medida', 'nombre')
                    ->ignore($unidadMedida->id),
            ],
            'abreviatura' => [
                'required',
                'string',
                'max:20',
                Rule::unique('unidades_medida', 'abreviatura')
                    ->ignore($unidadMedida->id),
            ],
            'tipo' => ['nullable', 'string', 'max:40'],
        ], [
            'nombre.required' => 'Escribe el nombre de la unidad.',
            'nombre.unique' => 'Ya existe una unidad con ese nombre.',
            'abreviatura.required' => 'Escribe la abreviatura.',
            'abreviatura.unique' => 'Esa abreviatura ya está registrada.',
        ]);

        $unidadMedida->update([
            'nombre' => $datos['nombre'],
            'abreviatura' => $datos['abreviatura'],
            'tipo' => $datos['tipo'] ?: 'unidad',
        ]);

        return redirect()
            ->route('admin.unidades-medida.index')
            ->with('success', 'Unidad actualizada correctamente.');
    }

    public function cambiarEstado(UnidadMedida $unidadMedida)
    {
        $unidadMedida->update([
            'estado' => !$unidadMedida->estado,
        ]);

        return redirect()
            ->route('admin.unidades-medida.index')
            ->with(
                'success',
                $unidadMedida->estado
                    ? 'Unidad activada correctamente.'
                    : 'Unidad desactivada. Los productos existentes conservan esta unidad.'
            );
    }

    public function destroy(UnidadMedida $unidadMedida)
    {
        $estaEnUso = Producto::query()
            ->where('unidad_medida_id', $unidadMedida->id)
            ->exists();

        if ($estaEnUso) {
            return redirect()
                ->route('admin.unidades-medida.index')
                ->with(
                    'error',
                    'No se puede eliminar: esta unidad está asignada a productos. Puedes desactivarla.'
                );
        }

        $unidadMedida->delete();

        return redirect()
            ->route('admin.unidades-medida.index')
            ->with('success', 'Unidad eliminada correctamente.');
    }
}