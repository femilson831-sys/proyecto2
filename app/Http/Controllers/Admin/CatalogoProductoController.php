<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Subcategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class CatalogoProductoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PANTALLA PRINCIPAL
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $buscarCategoria = trim(
            (string) $request->input('buscar_categoria')
        );

        $buscarSubcategoria = trim(
            (string) $request->input('buscar_subcategoria')
        );

        $buscarMarca = trim(
            (string) $request->input('buscar_marca')
        );

        $categorias = Categoria::query()
            ->when(
                $buscarCategoria !== '',
                function ($query) use ($buscarCategoria) {
                    $query->where(
                        'nombre',
                        'like',
                        "%{$buscarCategoria}%"
                    );
                }
            )
            ->orderBy('nombre')
            ->get();

        $subcategorias = Subcategoria::query()
            ->with('categoria')
            ->when(
                $buscarSubcategoria !== '',
                function ($query) use ($buscarSubcategoria) {
                    $query->where(
                        'nombre',
                        'like',
                        "%{$buscarSubcategoria}%"
                    );
                }
            )
            ->when(
                $request->filled('categoria_id'),
                function ($query) use ($request) {
                    $query->where(
                        'categoria_id',
                        $request->categoria_id
                    );
                }
            )
            ->orderBy('categoria_id')
            ->orderBy('nombre')
            ->get();

        $marcas = Marca::query()
            ->when(
                $buscarMarca !== '',
                function ($query) use ($buscarMarca) {
                    $query->where(
                        'nombre',
                        'like',
                        "%{$buscarMarca}%"
                    );
                }
            )
            ->orderBy('nombre')
            ->get();

        $categoriasSelector = Categoria::query()
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return view(
            'admin.catalogos-productos.index',
            compact(
                'categorias',
                'subcategorias',
                'marcas',
                'categoriasSelector'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CATEGORÍAS
    |--------------------------------------------------------------------------
    */

    public function storeCategoria(Request $request)
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                'unique:categorias,nombre',
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:255',
            ],
        ], [
            'nombre.required' =>
                'El nombre de la categoría es obligatorio.',

            'nombre.unique' =>
                'Ya existe una categoría con ese nombre.',

            'nombre.max' =>
                'El nombre no puede superar los 100 caracteres.',

            'descripcion.max' =>
                'La descripción no puede superar los 255 caracteres.',
        ]);

        Categoria::create([
            'nombre' => trim($datos['nombre']),
            'descripcion' =>
                isset($datos['descripcion'])
                    ? trim($datos['descripcion'])
                    : null,
            'estado' => 1,
        ]);

        return redirect()
            ->route('admin.catalogos-productos.index')
            ->with(
                'success',
                'Categoría registrada correctamente.'
            );
    }

    public function updateCategoria(
        Request $request,
        Categoria $categoria
    ) {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'categorias',
                    'nombre'
                )->ignore($categoria->id),
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:255',
            ],
        ], [
            'nombre.required' =>
                'El nombre de la categoría es obligatorio.',

            'nombre.unique' =>
                'Ya existe una categoría con ese nombre.',

            'nombre.max' =>
                'El nombre no puede superar los 100 caracteres.',

            'descripcion.max' =>
                'La descripción no puede superar los 255 caracteres.',
        ]);

        $categoria->update([
            'nombre' => trim($datos['nombre']),
            'descripcion' =>
                isset($datos['descripcion'])
                    ? trim($datos['descripcion'])
                    : null,
        ]);

        return redirect()
            ->route('admin.catalogos-productos.index')
            ->with(
                'success',
                'Categoría actualizada correctamente.'
            );
    }

    public function estadoCategoria(
        Categoria $categoria
    ) {
        $categoria->update([
            'estado' => !$categoria->estado,
        ]);

        return back()->with(
            'success',
            $categoria->estado
                ? 'Categoría activada correctamente.'
                : 'Categoría desactivada correctamente.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR CATEGORÍA
    |--------------------------------------------------------------------------
    */

    public function destroyCategoria(
        Categoria $categoria
    ) {
        /*
         * No eliminamos una categoría si ya tiene
         * productos asociados.
         */
        $productos = DB::table('productos')
            ->where(
                'categoria_id',
                $categoria->id
            )
            ->count();

        if ($productos > 0) {
            return back()->with(
                'error',
                'No se puede eliminar la categoría "' .
                $categoria->nombre .
                '" porque tiene ' .
                $productos .
                ' producto(s) asociado(s). ' .
                'Puedes desactivarla.'
            );
        }

        /*
         * Tampoco eliminamos una categoría si tiene
         * subcategorías registradas.
         */
        $subcategorias = Subcategoria::query()
            ->where(
                'categoria_id',
                $categoria->id
            )
            ->count();

        if ($subcategorias > 0) {
            return back()->with(
                'error',
                'No se puede eliminar la categoría "' .
                $categoria->nombre .
                '" porque tiene ' .
                $subcategorias .
                ' subcategoría(s) registrada(s).'
            );
        }

        try {
            $nombre = $categoria->nombre;

            $categoria->delete();

            return redirect()
                ->route(
                    'admin.catalogos-productos.index'
                )
                ->with(
                    'success',
                    'La categoría "' .
                    $nombre .
                    '" fue eliminada correctamente.'
                );
        } catch (Throwable $e) {
            return back()->with(
                'error',
                'No se pudo eliminar la categoría porque está siendo utilizada por otros registros.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SUBCATEGORÍAS
    |--------------------------------------------------------------------------
    */

    public function storeSubcategoria(
        Request $request
    ) {
        $datos = $request->validate([
            'categoria_id' => [
                'required',
                'integer',
                'exists:categorias,id',
            ],

            'nombre' => [
                'required',
                'string',
                'max:100',
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:255',
            ],
        ], [
            'categoria_id.required' =>
                'Selecciona una categoría.',

            'categoria_id.exists' =>
                'La categoría seleccionada no existe.',

            'nombre.required' =>
                'El nombre de la subcategoría es obligatorio.',

            'nombre.max' =>
                'El nombre no puede superar los 100 caracteres.',

            'descripcion.max' =>
                'La descripción no puede superar los 255 caracteres.',
        ]);

        /*
         * Verificar que la categoría esté activa.
         */
        $categoriaActiva = Categoria::query()
            ->where('id', $datos['categoria_id'])
            ->where('estado', 1)
            ->exists();

        if (!$categoriaActiva) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'No puedes registrar una subcategoría en una categoría inactiva.'
                );
        }

        /*
         * Evitar nombres repetidos dentro
         * de la misma categoría.
         */
        $existe = Subcategoria::query()
            ->where(
                'categoria_id',
                $datos['categoria_id']
            )
            ->where(
                'nombre',
                trim($datos['nombre'])
            )
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Esa subcategoría ya existe dentro de la categoría seleccionada.'
                );
        }

        Subcategoria::create([
            'categoria_id' =>
                $datos['categoria_id'],

            'nombre' =>
                trim($datos['nombre']),

            'descripcion' =>
                isset($datos['descripcion'])
                    ? trim($datos['descripcion'])
                    : null,

            'estado' => 1,
        ]);

        return redirect()
            ->route(
                'admin.catalogos-productos.index'
            )
            ->with(
                'success',
                'Subcategoría registrada correctamente.'
            );
    }

    public function updateSubcategoria(
        Request $request,
        Subcategoria $subcategoria
    ) {
        $datos = $request->validate([
            'categoria_id' => [
                'required',
                'integer',
                'exists:categorias,id',
            ],

            'nombre' => [
                'required',
                'string',
                'max:100',
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:255',
            ],
        ], [
            'categoria_id.required' =>
                'Selecciona una categoría.',

            'categoria_id.exists' =>
                'La categoría seleccionada no existe.',

            'nombre.required' =>
                'El nombre de la subcategoría es obligatorio.',

            'nombre.max' =>
                'El nombre no puede superar los 100 caracteres.',

            'descripcion.max' =>
                'La descripción no puede superar los 255 caracteres.',
        ]);

        $existe = Subcategoria::query()
            ->where(
                'categoria_id',
                $datos['categoria_id']
            )
            ->where(
                'nombre',
                trim($datos['nombre'])
            )
            ->where(
                'id',
                '!=',
                $subcategoria->id
            )
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Esa subcategoría ya existe dentro de la categoría seleccionada.'
                );
        }

        $subcategoria->update([
            'categoria_id' =>
                $datos['categoria_id'],

            'nombre' =>
                trim($datos['nombre']),

            'descripcion' =>
                isset($datos['descripcion'])
                    ? trim($datos['descripcion'])
                    : null,
        ]);

        return redirect()
            ->route(
                'admin.catalogos-productos.index'
            )
            ->with(
                'success',
                'Subcategoría actualizada correctamente.'
            );
    }

    public function estadoSubcategoria(
        Subcategoria $subcategoria
    ) {
        $subcategoria->update([
            'estado' => !$subcategoria->estado,
        ]);

        return back()->with(
            'success',
            $subcategoria->estado
                ? 'Subcategoría activada correctamente.'
                : 'Subcategoría desactivada correctamente.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR SUBCATEGORÍA
    |--------------------------------------------------------------------------
    */

    public function destroySubcategoria(
        Subcategoria $subcategoria
    ) {
        $productos = DB::table('productos')
            ->where(
                'subcategoria_id',
                $subcategoria->id
            )
            ->count();

        if ($productos > 0) {
            return back()->with(
                'error',
                'No se puede eliminar la subcategoría "' .
                $subcategoria->nombre .
                '" porque tiene ' .
                $productos .
                ' producto(s) asociado(s). ' .
                'Puedes desactivarla.'
            );
        }

        try {
            $nombre = $subcategoria->nombre;

            $subcategoria->delete();

            return redirect()
                ->route(
                    'admin.catalogos-productos.index'
                )
                ->with(
                    'success',
                    'La subcategoría "' .
                    $nombre .
                    '" fue eliminada correctamente.'
                );
        } catch (Throwable $e) {
            return back()->with(
                'error',
                'No se pudo eliminar la subcategoría porque está siendo utilizada por otros registros.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MARCAS
    |--------------------------------------------------------------------------
    */

    public function storeMarca(
        Request $request
    ) {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                'unique:marcas,nombre',
            ],
        ], [
            'nombre.required' =>
                'El nombre de la marca es obligatorio.',

            'nombre.unique' =>
                'Ya existe una marca con ese nombre.',

            'nombre.max' =>
                'El nombre no puede superar los 100 caracteres.',
        ]);

        Marca::create([
            'nombre' => trim($datos['nombre']),
            'estado' => 1,
        ]);

        return redirect()
            ->route(
                'admin.catalogos-productos.index'
            )
            ->with(
                'success',
                'Marca registrada correctamente.'
            );
    }

    public function updateMarca(
        Request $request,
        Marca $marca
    ) {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'marcas',
                    'nombre'
                )->ignore($marca->id),
            ],
        ], [
            'nombre.required' =>
                'El nombre de la marca es obligatorio.',

            'nombre.unique' =>
                'Ya existe una marca con ese nombre.',

            'nombre.max' =>
                'El nombre no puede superar los 100 caracteres.',
        ]);

        $marca->update([
            'nombre' => trim($datos['nombre']),
        ]);

        return redirect()
            ->route(
                'admin.catalogos-productos.index'
            )
            ->with(
                'success',
                'Marca actualizada correctamente.'
            );
    }

    public function estadoMarca(
        Marca $marca
    ) {
        $marca->update([
            'estado' => !$marca->estado,
        ]);

        return back()->with(
            'success',
            $marca->estado
                ? 'Marca activada correctamente.'
                : 'Marca desactivada correctamente.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR MARCA
    |--------------------------------------------------------------------------
    */

    public function destroyMarca(
        Marca $marca
    ) {
        $productos = DB::table('productos')
            ->where(
                'marca_id',
                $marca->id
            )
            ->count();

        if ($productos > 0) {
            return back()->with(
                'error',
                'No se puede eliminar la marca "' .
                $marca->nombre .
                '" porque tiene ' .
                $productos .
                ' producto(s) asociado(s). ' .
                'Puedes desactivarla.'
            );
        }

        try {
            $nombre = $marca->nombre;

            $marca->delete();

            return redirect()
                ->route(
                    'admin.catalogos-productos.index'
                )
                ->with(
                    'success',
                    'La marca "' .
                    $nombre .
                    '" fue eliminada correctamente.'
                );
        } catch (Throwable $e) {
            return back()->with(
                'error',
                'No se pudo eliminar la marca porque está siendo utilizada por otros registros.'
            );
        }
    }
}