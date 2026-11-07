<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    /*
    |--------------------------------------------------------------------------
    | TABLA
    |--------------------------------------------------------------------------
    */

    protected $table = 'usuarios';

    /*
    |--------------------------------------------------------------------------
    | CAMPOS ASIGNABLES
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'empleado_id',
        'rol_id',
        'username',
        'email',
        'password',
        'ultimo_login',
        'estado',
    ];

    /*
    |--------------------------------------------------------------------------
    | CAMPOS OCULTOS
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'ultimo_login' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | EMPLEADO DEL USUARIO
    |--------------------------------------------------------------------------
    |
    | Relación normal.
    |
    | Esta relación solamente obtiene al empleado cuando no está
    | eliminado mediante Soft Delete.
    |
    */

    public function empleado()
    {
        return $this->belongsTo(
            Empleado::class,
            'empleado_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EMPLEADO INCLUYENDO ELIMINADOS
    |--------------------------------------------------------------------------
    |
    | Esta relación es especialmente importante para auditoría.
    |
    | Permite seguir obteniendo los datos del empleado aunque tenga
    | un valor en deleted_at.
    |
    | Ejemplo:
    |
    | Emilson Flores
    | @admin
    |
    | seguirá apareciendo como responsable de una acción aunque su
    | registro de empleado se encuentre eliminado lógicamente.
    |
    */

    public function empleadoConEliminados()
    {
        return $this->belongsTo(
            Empleado::class,
            'empleado_id'
        )->withTrashed();
    }

    /*
    |--------------------------------------------------------------------------
    | ROL DEL USUARIO
    |--------------------------------------------------------------------------
    */

    public function rol()
    {
        return $this->belongsTo(
            Rol::class,
            'rol_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HISTORIAL DE ACCIONES SOBRE EMPLEADOS
    |--------------------------------------------------------------------------
    |
    | Obtiene todas las acciones realizadas por este usuario sobre
    | los empleados.
    |
    | Ejemplos:
    |
    | - creado
    | - editado
    | - activado
    | - desactivado
    | - eliminado
    | - restaurado
    |
    */

    public function historialEmpleados()
    {
        return $this->hasMany(
            HistorialEmpleado::class,
            'usuario_id'
        )->orderByDesc('created_at');
    }

    /*
    |--------------------------------------------------------------------------
    | COMPROBAR SI EL USUARIO ESTÁ ACTIVO
    |--------------------------------------------------------------------------
    */

    public function estaActivo(): bool
    {
        return $this->estado === 'activo';
    }
}