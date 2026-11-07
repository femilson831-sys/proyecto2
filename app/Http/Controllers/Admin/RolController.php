<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RolController extends Controller
{
    public function index(): View
    {
        $prioridad = [
            'administrador' => 1,
            'cajero' => 2,
            'almacen' => 3,
            'encargado-de-almacen' => 3,
        ];

        $roles = Rol::query()
            ->withCount([
                'usuarios as usuarios_activos_count' => function ($query): void {
                    $query->where('estado', 'activo');
                },
            ])
            ->get()
            ->sortBy(function (Rol $rol) use ($prioridad): string {
                $clave = Str::slug(trim((string) $rol->nombre));
                $orden = $prioridad[$clave] ?? 9;

                return sprintf('%02d-%s', $orden, Str::lower($rol->nombre));
            })
            ->values();

        return view('admin.roles.index', compact('roles'));
    }

    public function cambiarEstado(Rol $rol): RedirectResponse
    {
        $nuevoEstado = !(bool) $rol->estado;

        if ($rol->esAdministrador() && !$nuevoEstado) {
            return back()->with(
                'error',
                'El rol Administrador siempre debe permanecer activo.'
            );
        }

        if (
            !$nuevoEstado
            && $rol->usuarios()->where('estado', 'activo')->exists()
        ) {
            return back()->with(
                'error',
                'No puedes desactivar este rol mientras tenga usuarios activos. Reasígnalos primero.'
            );
        }

        $rol->estado = $nuevoEstado;
        $rol->save();

        return back()->with(
            'success',
            $nuevoEstado
                ? 'Rol activado correctamente.'
                : 'Rol desactivado correctamente.'
        );
    }
}