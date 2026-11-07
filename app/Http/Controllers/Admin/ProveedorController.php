<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $estado = $request->query('estado', '');

        $proveedores = Proveedor::query()
            ->with('contactos')
            ->withCount('productos')
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($subquery) use ($buscar) {
                    $subquery
                        ->where('codigo', 'like', "%{$buscar}%")
                        ->orWhere('razon_social', 'like', "%{$buscar}%")
                        ->orWhere('nombre_comercial', 'like', "%{$buscar}%")
                        ->orWhere('nit', 'like', "%{$buscar}%")
                        ->orWhere('celular', 'like', "%{$buscar}%");
                });
            })
            ->when(
                in_array($estado, ['activo', 'inactivo'], true),
                fn ($query) => $query->where('estado', $estado)
            )
            ->orderBy('razon_social')
            ->paginate(12)
            ->withQueryString();

        return view('admin.proveedores.index', compact(
            'proveedores',
            'buscar',
            'estado'
        ));
    }

    public function create()
    {
        return view('admin.proveedores.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate($this->reglas());
        $contactos = $this->limpiarContactos($datos['contactos'] ?? []);

        if ($this->hayMasDeUnContactoPrincipal($contactos)) {
            return back()
                ->withInput()
                ->withErrors([
                    'contactos' => 'Solo un contacto puede ser el principal.',
                ]);
        }

        unset($datos['contactos']);

        $proveedor = DB::transaction(function () use ($datos, $contactos) {
            $proveedor = Proveedor::create([
                ...$datos,
                'codigo' => 'TMP-' . Str::uuid(),
            ]);

            $proveedor->update([
                'codigo' => 'PROV-' . str_pad(
                    (string) $proveedor->id,
                    6,
                    '0',
                    STR_PAD_LEFT
                ),
            ]);

            foreach ($contactos as $contacto) {
                $proveedor->contactos()->create($contacto);
            }

            return $proveedor;
        });

        return redirect()
            ->route('admin.proveedores.index')
            ->with('success', "Proveedor {$proveedor->codigo} registrado correctamente.");
    }

    public function edit(Proveedor $proveedor)
    {
        $proveedor->load('contactos');

        return view('admin.proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $datos = $request->validate($this->reglas());
        $contactos = $this->limpiarContactos($datos['contactos'] ?? []);

        if ($this->hayMasDeUnContactoPrincipal($contactos)) {
            return back()
                ->withInput()
                ->withErrors([
                    'contactos' => 'Solo un contacto puede ser el principal.',
                ]);
        }

        foreach ($contactos as $contacto) {
            if (
                !empty($contacto['id']) &&
                !$proveedor->contactos()->whereKey($contacto['id'])->exists()
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'contactos' => 'Uno de los contactos no pertenece a este proveedor.',
                    ]);
            }
        }

        unset($datos['contactos'], $datos['codigo']);

        DB::transaction(function () use ($proveedor, $datos, $contactos) {
            $proveedor->update($datos);

            $idsConservados = [];

            foreach ($contactos as $contacto) {
                $id = $contacto['id'] ?? null;
                unset($contacto['id']);

                if ($id) {
                    $proveedor->contactos()
                        ->whereKey($id)
                        ->update($contacto);

                    $idsConservados[] = $id;
                } else {
                    $nuevoContacto = $proveedor->contactos()
                        ->create($contacto);

                    $idsConservados[] = $nuevoContacto->id;
                }
            }

            if ($idsConservados) {
                $proveedor->contactos()
                    ->whereNotIn('id', $idsConservados)
                    ->delete();
            } else {
                $proveedor->contactos()->delete();
            }
        });

        return redirect()
            ->route('admin.proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    /**
     * Elimina permanentemente un proveedor.
     * Se bloquea la eliminación si todavía tiene productos asociados.
     */
    public function destroy(Proveedor $proveedor)
    {
        if ($proveedor->productos()->exists()) {
            return back()->withErrors([
                'eliminacion' => 'No se puede eliminar este proveedor porque tiene productos asociados. Desactívalo o quita primero la asociación con sus productos.',
            ]);
        }

        try {
            DB::transaction(function () use ($proveedor) {
                $proveedor->contactos()->delete();
                $proveedor->delete();
            });
        } catch (QueryException $exception) {
            report($exception);

            return back()->withErrors([
                'eliminacion' => 'No se pudo eliminar el proveedor porque tiene información relacionada en el sistema. Puedes desactivarlo para conservar su historial.',
            ]);
        }

        return redirect()
            ->route('admin.proveedores.index')
            ->with('success', 'Proveedor eliminado permanentemente.');
    }

    /**
     * Cambia entre activo e inactivo. Esta acción no elimina el proveedor.
     */
    public function cambiarEstado(Proveedor $proveedor)
    {
        $proveedor->update([
            'estado' => $proveedor->estado === 'activo'
                ? 'inactivo'
                : 'activo',
        ]);

        return redirect()
            ->route('admin.proveedores.index')
            ->with('success', 'Estado del proveedor actualizado.');
    }

    private function reglas(): array
    {
        return [
            'razon_social' => ['required', 'string', 'max:180'],
            'nombre_comercial' => ['nullable', 'string', 'max:180'],
            'nit' => ['nullable', 'string', 'max:30'],
            'ci' => ['nullable', 'string', 'max:30'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'celular' => ['nullable', 'string', 'max:30'],
            'correo' => ['nullable', 'email', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'estado' => ['required', 'in:activo,inactivo'],
            'observaciones' => ['nullable', 'string'],

            'contactos' => ['nullable', 'array'],
            'contactos.*.id' => ['nullable', 'integer'],
            'contactos.*.nombre' => ['nullable', 'string', 'max:150'],
            'contactos.*.cargo' => ['nullable', 'string', 'max:100'],
            'contactos.*.telefono' => ['nullable', 'string', 'max:30'],
            'contactos.*.celular' => ['nullable', 'string', 'max:30'],
            'contactos.*.correo' => ['nullable', 'email', 'max:150'],
            'contactos.*.es_principal' => ['nullable', 'boolean'],
        ];
    }

    private function limpiarContactos(array $contactos): array
    {
        return collect($contactos)
            ->filter(fn ($contacto) =>
                trim((string) ($contacto['nombre'] ?? '')) !== ''
            )
            ->map(fn ($contacto) => [
                'id' => $contacto['id'] ?? null,
                'nombre' => trim($contacto['nombre']),
                'cargo' => $contacto['cargo'] ?? null,
                'telefono' => $contacto['telefono'] ?? null,
                'celular' => $contacto['celular'] ?? null,
                'correo' => $contacto['correo'] ?? null,
                'es_principal' => !empty($contacto['es_principal']),
            ])
            ->values()
            ->all();
    }

    private function hayMasDeUnContactoPrincipal(array $contactos): bool
    {
        return collect($contactos)
            ->where('es_principal', true)
            ->count() > 1;
    }
}