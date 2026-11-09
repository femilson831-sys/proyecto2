<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolesSistemaSeeder extends Seeder
{
    public function run(): void
    {
        $rolesBase = [
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso administrativo al sistema.',
            ],
            [
                'nombre' => 'Personal de pesaje',
                'descripcion' => 'Registro y etiquetado de productos por peso.',
            ],
            [
                'nombre' => 'Encargado de almacén',
                'descripcion' => 'Supervisión de almacenes e inventario.',
            ],
            [
                'nombre' => 'Personal de almacén',
                'descripcion' => 'Operaciones y tareas de almacén.',
            ],
            [
                'nombre' => 'Cajero',
                'descripcion' => 'Acceso a las operaciones de caja.',
            ],
            [
                'nombre' => 'Almacén',
                'descripcion' => 'Acceso a las operaciones de almacén e inventario.',
            ],
        ];

        $rolesExistentes = Rol::query()->get();

        foreach ($rolesBase as $datos) {
            $nombreClave = Str::slug($datos['nombre']);

            $rol = $rolesExistentes->first(
                fn (Rol $item): bool =>
                    Str::slug(trim((string) $item->nombre)) === $nombreClave
            );

            if (!$rol) {
                $rol = new Rol();
                $rol->nombre = $datos['nombre'];
                $rol->descripcion = $datos['descripcion'];
                $rol->estado = true;
                $rol->save();

                $rolesExistentes->push($rol);

                continue;
            }

            if (!$rol->estado) {
                $rol->estado = true;
                $rol->save();
            }
        }
    }
}