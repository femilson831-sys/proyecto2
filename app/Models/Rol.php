<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Rol extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (Rol $rol): void {
            if ($rol->esRolDelSistema() && $rol->isDirty('nombre')) {
                throw ValidationException::withMessages([
                    'nombre' => 'No se puede cambiar el nombre de un rol del sistema.',
                ]);
            }

            if ($rol->esAdministrador() && $rol->isDirty('estado') && !$rol->estado) {
                throw ValidationException::withMessages([
                    'estado' => 'El rol Administrador debe permanecer activo.',
                ]);
            }
        });

        static::deleting(function (Rol $rol): void {
            if ($rol->esRolDelSistema()) {
                throw ValidationException::withMessages([
                    'rol' => 'Los roles del sistema no se pueden eliminar.',
                ]);
            }
        });
    }

    public function esRolDelSistema(): bool
    {
        return in_array(
            Str::slug(trim((string) $this->nombre)),
            [
                'administrador',
                'cajero',
                'almacen',
                'encargado-de-almacen',
            ],
            true
        );
    }

    public function esAdministrador(): bool
    {
        return Str::slug(trim((string) $this->nombre)) === 'administrador';
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'rol_id');
    }
}