<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\CodigoBarra;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\ProductoPrecio;
use App\Models\Proveedor;
use App\Models\Subcategoria;
use App\Models\TipoProducto;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim((string) $request->get('buscar', ''));
        $categoriaId = $request->get('categoria_id');
        $estado = $request->get('estado');

        $productos = Producto::query()
            ->with([
                'categoria',
                'subcategoria',
                'marca',
                'unidadMedida',
                'tipoProducto',
                'codigoBarraPrincipal',
                'precioActual',
            ])
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('codigo_interno', 'like', "%{$buscar}%")
                        ->orWhere('nombre', 'like', "%{$buscar}%")
                        ->orWhere('descripcion', 'like', "%{$buscar}%")
                        ->orWhereHas('codigosBarras', function ($barcodeQuery) use ($buscar) {
                            $barcodeQuery->where('codigo', 'like', "%{$buscar}%");
                        });
                });
            })
            ->when($categoriaId, function ($query) use ($categoriaId) {
                $query->where('categoria_id', $categoriaId);
            })
            ->when(
                in_array($estado, ['activo', 'inactivo'], true),
                function ($query) use ($estado) {
                    $query->where('estado', $estado);
                }
            )
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $categorias = Categoria::query()
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return view(
            'admin.productos.index',
            compact(
                'productos',
                'categorias',
                'buscar',
                'categoriaId',
                'estado'
            )
        );
    }

    public function create()
    {
        return view(
            'admin.productos.create',
            $this->obtenerCatalogos()
        );
    }

    public function store(Request $request)
    {
        $datos = $this->validarProducto($request);

        $this->validarSubcategoria(
            $datos['categoria_id'],
            $datos['subcategoria_id'] ?? null
        );

        $rutaImagen = null;

        try {
            if ($request->hasFile('imagen')) {
                $rutaImagen = $this->guardarImagenPublica(
                    'productos',
                    $request->file('imagen')
                );

                $datos['imagen'] = $rutaImagen;
            }

            DB::transaction(function () use ($datos) {
                $datosProducto = [
                    'codigo_interno' => uniqid('TMP-', true),
                    'nombre' => trim($datos['nombre']),
                    'descripcion' => $this->normalizarTextoNullable(
                        $datos['descripcion'] ?? null
                    ),
                    'categoria_id' => $datos['categoria_id'],
                    'subcategoria_id' => $datos['subcategoria_id'] ?? null,
                    'marca_id' => $datos['marca_id'] ?? null,
                    'unidad_medida_id' => $datos['unidad_medida_id'],
                    'tipo_producto_id' => $datos['tipo_producto_id'],
                    'imagen' => $datos['imagen'] ?? null,
                    'stock_minimo' => $datos['stock_minimo'] ?? 0,
                    'stock_maximo' => $datos['stock_maximo'] ?? null,
                    'estado' => $datos['estado'],
                ];

                $producto = Producto::create($datosProducto);

                $producto->proveedores()->sync($datos['proveedores'] ?? []);

                $producto->codigo_interno = 'PRD-' . str_pad(
                    (string) $producto->id,
                    3,
                    '0',
                    STR_PAD_LEFT
                );

                $producto->save();

                $codigoEscaneado = trim((string) ($datos['codigo_barras'] ?? ''));

                $codigoBarra = $codigoEscaneado !== ''
                    ? $codigoEscaneado
                    : 'NAN-P-' . str_pad(
                        (string) $producto->id,
                        9,
                        '0',
                        STR_PAD_LEFT
                    );

                CodigoBarra::create([
                    'producto_id' => $producto->id,
                    'codigo' => $codigoBarra,
                    'tipo' => 'nanos_producto',
                    'es_principal' => 1,
                ]);

                ProductoPrecio::create([
                    'producto_id' => $producto->id,
                    'precio_compra' => $datos['precio_compra'],
                    'precio_venta' => $datos['precio_venta'],
                    'fecha_inicio' => now(),
                    'fecha_fin' => null,
                    'usuario_id' => Auth::id(),
                    'estado' => 1,
                ]);
            });

            return redirect()
                ->route('admin.productos.index')
                ->with('success', 'Producto registrado correctamente.');
        } catch (Throwable $e) {
            if ($rutaImagen) {
                $this->eliminarImagen('productos', $rutaImagen);
            }

            report($e);

            return back()
                ->withInput()
                ->with('error', 'No se pudo registrar el producto. Inténtalo nuevamente.');
        }
    }

    public function show(Producto $producto)
    {
        $producto->load([
            'categoria',
            'subcategoria',
            'marca',
            'unidadMedida',
            'tipoProducto',
            'codigosBarras',
            'precios.usuario',
            'inventarios',
        ]);

        return view('admin.productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $producto->load([
            'categoria',
            'subcategoria',
            'marca',
            'unidadMedida',
            'tipoProducto',
            'codigoBarraPrincipal',
            'precioActual',
            'proveedores',
        ]);

        return view(
            'admin.productos.edit',
            array_merge(
                ['producto' => $producto],
                $this->obtenerCatalogos($producto)
            )
        );
    }

    public function update(Request $request, Producto $producto)
    {
        $datos = $this->validarProducto($request, $producto);

        $this->validarSubcategoria(
            $datos['categoria_id'],
            $datos['subcategoria_id'] ?? null
        );

        $imagenAnterior = $producto->imagen;
        $imagenNueva = null;

        try {
            if ($request->hasFile('imagen')) {
                $imagenNueva = $this->guardarImagenPublica(
                    'productos',
                    $request->file('imagen')
                );

                $datos['imagen'] = $imagenNueva;
            }

            DB::transaction(function () use ($datos, $producto) {
                $datosProducto = [
                    'nombre' => trim($datos['nombre']),
                    'descripcion' => $this->normalizarTextoNullable(
                        $datos['descripcion'] ?? null
                    ),
                    'categoria_id' => $datos['categoria_id'],
                    'subcategoria_id' => $datos['subcategoria_id'] ?? null,
                    'marca_id' => $datos['marca_id'] ?? null,
                    'unidad_medida_id' => $datos['unidad_medida_id'],
                    'tipo_producto_id' => $datos['tipo_producto_id'],
                    'stock_minimo' => $datos['stock_minimo'] ?? 0,
                    'stock_maximo' => $datos['stock_maximo'] ?? null,
                    'estado' => $datos['estado'],
                ];

                if (array_key_exists('imagen', $datos)) {
                    $datosProducto['imagen'] = $datos['imagen'];
                }

                $producto->update($datosProducto);
                $producto->proveedores()->sync($datos['proveedores'] ?? []);

                $codigoPrincipal = $producto
                    ->codigoBarraPrincipal()
                    ->first();

                $codigoEscaneado = trim((string) ($datos['codigo_barras'] ?? ''));

                if ($codigoEscaneado !== '') {
                    if ($codigoPrincipal) {
                        if ($codigoPrincipal->codigo !== $codigoEscaneado) {
                            $codigoPrincipal->update([
                                'codigo' => $codigoEscaneado,
                                'tipo' => 'nanos_producto',
                            ]);
                        }
                    } else {
                        CodigoBarra::create([
                            'producto_id' => $producto->id,
                            'codigo' => $codigoEscaneado,
                            'tipo' => 'nanos_producto',
                            'es_principal' => 1,
                        ]);
                    }
                } elseif (!$codigoPrincipal) {
                    CodigoBarra::create([
                        'producto_id' => $producto->id,
                        'codigo' => 'NAN-P-' . str_pad(
                            (string) $producto->id,
                            9,
                            '0',
                            STR_PAD_LEFT
                        ),
                        'tipo' => 'nanos_producto',
                        'es_principal' => 1,
                    ]);
                }

                $precioActual = $producto
                    ->precioActual()
                    ->first();

                $precioCompraNuevo = round((float) $datos['precio_compra'], 2);
                $precioVentaNuevo = round((float) $datos['precio_venta'], 2);
                $cambioPrecio = true;

                if ($precioActual) {
                    $precioCompraActual = round((float) $precioActual->precio_compra, 2);
                    $precioVentaActual = round((float) $precioActual->precio_venta, 2);

                    $cambioPrecio =
                        $precioCompraActual !== $precioCompraNuevo ||
                        $precioVentaActual !== $precioVentaNuevo;
                }

                if ($cambioPrecio) {
                    if ($precioActual) {
                        $precioActual->update([
                            'fecha_fin' => now(),
                            'estado' => 0,
                        ]);
                    }

                    ProductoPrecio::create([
                        'producto_id' => $producto->id,
                        'precio_compra' => $precioCompraNuevo,
                        'precio_venta' => $precioVentaNuevo,
                        'fecha_inicio' => now(),
                        'fecha_fin' => null,
                        'usuario_id' => Auth::id(),
                        'estado' => 1,
                    ]);
                }
            });

            if (
                $imagenNueva &&
                $imagenAnterior &&
                $imagenAnterior !== $imagenNueva
            ) {
                $this->eliminarImagen('productos', $imagenAnterior);
            }

            return redirect()
                ->route('admin.productos.show', $producto)
                ->with('success', 'Producto actualizado correctamente.');
        } catch (Throwable $e) {
            if ($imagenNueva) {
                $this->eliminarImagen('productos', $imagenNueva);
            }

            report($e);

            return back()
                ->withInput()
                ->with('error', 'No se pudo actualizar el producto. Inténtalo nuevamente.');
        }
    }

    public function cambiarEstado(Producto $producto)
    {
        $nuevoEstado = $producto->estado === 'activo'
            ? 'inactivo'
            : 'activo';

        $producto->update([
            'estado' => $nuevoEstado,
        ]);

        return back()->with(
            'success',
            $nuevoEstado === 'activo'
                ? 'Producto activado correctamente.'
                : 'Producto desactivado correctamente.'
        );
    }

    /**
     * Elimina el producto permanentemente solo si no tiene historial asociado.
     */
    public function destroy(Producto $producto)
    {
        $tablasConHistorial = [
            'ventas_detalles',
            'recepcion_detalles',
            'movimiento_detalles',
            'ajuste_detalles',
            'devolucion_detalles',
            'pesaje_detalles',
            'inventarios',
            'lotes',
            'lote_escaneos',
        ];

        foreach ($tablasConHistorial as $tabla) {
            if (
                Schema::hasTable($tabla)
                && Schema::hasColumn($tabla, 'producto_id')
                && DB::table($tabla)
                    ->where('producto_id', $producto->id)
                    ->exists()
            ) {
                return back()->with(
                    'error',
                    'No se puede eliminar este producto porque tiene inventario o historial. Puedes desactivarlo.'
                );
            }
        }

        $imagenAnterior = $producto->imagen;

        try {
            DB::transaction(function () use ($producto): void {
                $producto->proveedores()->detach();
                $producto->codigosBarras()->delete();
                $producto->precios()->delete();
                $producto->delete();
            });

            if ($imagenAnterior) {
                $this->eliminarImagen('productos', $imagenAnterior);
            }

            return redirect()
                ->route('admin.productos.index')
                ->with('success', 'Producto eliminado permanentemente.');
        } catch (Throwable $e) {
            report($e);

            return back()->with(
                'error',
                'No se pudo eliminar porque otros registros todavía hacen referencia al producto.'
            );
        }
    }

    public function subcategorias(Categoria $categoria)
    {
        $subcategorias = Subcategoria::query()
            ->where('categoria_id', $categoria->id)
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get([
                'id',
                'categoria_id',
                'nombre',
            ]);

        return response()->json($subcategorias);
    }

    private function obtenerCatalogos(?Producto $producto = null): array
    {
        $categorias = Categoria::query()
            ->where(function ($query) use ($producto) {
                $query->where('estado', 1);

                if ($producto?->categoria_id) {
                    $query->orWhere('id', $producto->categoria_id);
                }
            })
            ->orderBy('nombre')
            ->get();

        $subcategorias = Subcategoria::query()
            ->where(function ($query) use ($producto) {
                $query->where('estado', 1);

                if ($producto?->subcategoria_id) {
                    $query->orWhere('id', $producto->subcategoria_id);
                }
            })
            ->orderBy('nombre')
            ->get();

        $marcas = Marca::query()
            ->where(function ($query) use ($producto) {
                $query->where('estado', 1);

                if ($producto?->marca_id) {
                    $query->orWhere('id', $producto->marca_id);
                }
            })
            ->orderBy('nombre')
            ->get();

        $unidadesMedida = UnidadMedida::query()
            ->orderBy('nombre')
            ->get();

        $tiposProducto = TipoProducto::query()
            ->orderBy('nombre')
            ->get();

        $proveedoresRelacionados = $producto
            ? $producto->proveedores()->pluck('proveedores.id')->all()
            : [];

        $proveedores = Proveedor::query()
            ->where(function ($query) use ($proveedoresRelacionados) {
                $query->where('estado', 'activo');

                if (!empty($proveedoresRelacionados)) {
                    $query->orWhereIn('id', $proveedoresRelacionados);
                }
            })
            ->orderBy('razon_social')
            ->get();

        return compact(
            'categorias',
            'subcategorias',
            'marcas',
            'unidadesMedida',
            'tiposProducto',
            'proveedores'
        );
    }

    private function validarProducto(Request $request, ?Producto $producto = null): array
    {
        $codigoPrincipalId = null;

        if ($producto) {
            $codigoPrincipalId = $producto
                ->codigoBarraPrincipal()
                ->value('id');
        }

        $categoriaExiste = Rule::exists('categorias', 'id')
            ->where(function ($query) use ($producto) {
                $query->where('estado', 1);

                if ($producto?->categoria_id) {
                    $query->orWhere('id', $producto->categoria_id);
                }
            });

        $subcategoriaExiste = Rule::exists('subcategorias', 'id')
            ->where(function ($query) use ($producto) {
                $query->where('estado', 1);

                if ($producto?->subcategoria_id) {
                    $query->orWhere('id', $producto->subcategoria_id);
                }
            });

        $marcaExiste = Rule::exists('marcas', 'id')
            ->where(function ($query) use ($producto) {
                $query->where('estado', 1);

                if ($producto?->marca_id) {
                    $query->orWhere('id', $producto->marca_id);
                }
            });

        return $request->validate(
            [
                'codigo_barras' => [
                    'nullable',
                    'string',
                    'max:80',
                    Rule::unique('codigos_barras', 'codigo')->ignore($codigoPrincipalId),
                ],
                'nombre' => ['required', 'string', 'max:150'],
                'descripcion' => ['nullable', 'string'],
                'categoria_id' => ['required', 'integer', $categoriaExiste],
                'subcategoria_id' => ['nullable', 'integer', $subcategoriaExiste],
                'marca_id' => ['nullable', 'integer', $marcaExiste],
                'unidad_medida_id' => [
                    'required',
                    'integer',
                    Rule::exists('unidades_medida', 'id'),
                ],
                'tipo_producto_id' => [
                    'required',
                    'integer',
                    Rule::exists('tipos_producto', 'id'),
                ],
                'proveedores' => ['sometimes', 'array'],
                'proveedores.*' => [
                    'integer',
                    'distinct',
                    Rule::exists('proveedores', 'id'),
                ],
                'precio_compra' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:9999999999.99',
                ],
                'precio_venta' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:9999999999.99',
                    'gte:precio_compra',
                ],
                'stock_minimo' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:999999999.999',
                ],
                'stock_maximo' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:999999999.999',
                    'gte:stock_minimo',
                ],
                'imagen' => [
                    'nullable',
                    'file',
                    'mimetypes:image/jpeg,image/png,image/webp',
                    'max:4096',
                ],
                'estado' => [
                    'required',
                    Rule::in(['activo', 'inactivo']),
                ],
            ],
            [
                'codigo_barras.unique' => 'Este código de barras ya está asignado a otro producto.',
                'codigo_barras.max' => 'El código de barras no puede superar los 80 caracteres.',
                'nombre.required' => 'El nombre del producto es obligatorio.',
                'nombre.max' => 'El nombre no puede superar los 150 caracteres.',
                'categoria_id.required' => 'Debe seleccionar una categoría.',
                'categoria_id.exists' => 'La categoría seleccionada no existe o no está disponible.',
                'subcategoria_id.exists' => 'La subcategoría seleccionada no existe o no está disponible.',
                'marca_id.exists' => 'La marca seleccionada no existe o no está disponible.',
                'unidad_medida_id.required' => 'Debe seleccionar una unidad de medida.',
                'unidad_medida_id.exists' => 'La unidad de medida seleccionada no es válida.',
                'tipo_producto_id.required' => 'Debe seleccionar un tipo de producto.',
                'tipo_producto_id.exists' => 'El tipo de producto seleccionado no es válido.',
                'precio_compra.required' => 'El precio de compra es obligatorio.',
                'precio_compra.numeric' => 'El precio de compra debe ser un número válido.',
                'precio_compra.min' => 'El precio de compra no puede ser negativo.',
                'precio_venta.required' => 'El precio de venta es obligatorio.',
                'precio_venta.numeric' => 'El precio de venta debe ser un número válido.',
                'precio_venta.min' => 'El precio de venta no puede ser negativo.',
                'precio_venta.gte' => 'El precio de venta debe ser mayor o igual al precio de compra.',
                'stock_minimo.numeric' => 'El stock mínimo debe ser un número válido.',
                'stock_minimo.min' => 'El stock mínimo no puede ser negativo.',
                'stock_maximo.numeric' => 'El stock máximo debe ser un número válido.',
                'stock_maximo.min' => 'El stock máximo no puede ser negativo.',
                'stock_maximo.gte' => 'El stock máximo debe ser mayor o igual al stock mínimo.',
                'imagen.file' => 'La imagen seleccionada no es un archivo válido.',
                'imagen.mimetypes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
                'imagen.max' => 'La imagen no puede superar los 4 MB.',
                'estado.required' => 'Debe seleccionar el estado del producto.',
                'estado.in' => 'El estado seleccionado no es válido.',
            ]
        );
    }

    private function validarSubcategoria(
        int|string $categoriaId,
        int|string|null $subcategoriaId
    ): void {
        if ($subcategoriaId === null || $subcategoriaId === '') {
            return;
        }

        $pertenece = Subcategoria::query()
            ->where('id', $subcategoriaId)
            ->where('categoria_id', $categoriaId)
            ->exists();

        if (!$pertenece) {
            abort(
                422,
                'La subcategoría seleccionada no pertenece a la categoría indicada.'
            );
        }
    }

    private function normalizarTextoNullable(mixed $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $valor = trim((string) $valor);

        return $valor === '' ? null : $valor;
    }

    private function guardarImagenPublica(string $carpeta, $archivo): string
    {
        if (!in_array($carpeta, ['productos'], true)) {
            throw new \InvalidArgumentException('Carpeta de imagen no permitida.');
        }

        $directorio = public_path($carpeta);
        File::ensureDirectoryExists($directorio);

        $extension = strtolower($archivo->extension());
        $nombre = Str::uuid()->toString() . '.' . $extension;

        $archivo->move($directorio, $nombre);

        return $carpeta . '/' . $nombre;
    }

    private function eliminarImagen(string $carpeta, ?string $ruta): void
    {
        if (!$ruta || !in_array($carpeta, ['productos'], true)) {
            return;
        }

        $ruta = ltrim(str_replace('\\', '/', $ruta), '/');

        if (!str_starts_with($ruta, $carpeta . '/')) {
            return;
        }

        File::delete(public_path($ruta));
        Storage::disk('public')->delete($ruta);
    }
}