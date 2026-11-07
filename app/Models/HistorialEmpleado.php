<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialEmpleado extends Model
{
    /*
    |--------------------------------------------------------------------------
    | TABLA
    |--------------------------------------------------------------------------
    */

    protected $table = 'historial_empleados';


    /*
    |--------------------------------------------------------------------------
    | CAMPOS ASIGNABLES
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'empleado_id',
        'usuario_id',
        'accion',
        'descripcion',
        'estado_anterior',
        'estado_nuevo',
        'datos_anteriores',
        'datos_nuevos',
        'ip',
    ];


    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    |
    | Los campos datos_anteriores y datos_nuevos se guardan como JSON
    | y Laravel los convierte automáticamente en arrays.
    |
    */

    protected function casts(): array
    {
        return [
            'empleado_id' => 'integer',
            'usuario_id' => 'integer',

            'datos_anteriores' => 'array',
            'datos_nuevos' => 'array',

            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | EMPLEADO
    |--------------------------------------------------------------------------
    |
    | Permite conocer a qué empleado pertenece el evento.
    |
    | withTrashed() es importante porque el empleado puede haber sido
    | eliminado mediante SoftDeletes y aun así debemos poder consultar
    | todo su historial.
    |
    */

    public function empleado()
    {
        return $this->belongsTo(
            Empleado::class,
            'empleado_id'
        )->withTrashed();
    }


    /*
    |--------------------------------------------------------------------------
    | USUARIO RESPONSABLE
    |--------------------------------------------------------------------------
    |
    | Usuario del sistema que realizó la acción:
    |
    | - Crear
    | - Editar
    | - Activar
    | - Desactivar
    | - Eliminar
    | - Restaurar
    |
    */

    public function usuario()
    {
        return $this->belongsTo(
            Usuario::class,
            'usuario_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | NOMBRE DEL RESPONSABLE
    |--------------------------------------------------------------------------
    |
    | Devuelve el nombre completo del empleado asociado al usuario.
    |
    | Si ese empleado fue eliminado, empleadoConEliminados permite
    | seguir obteniendo su nombre.
    |
    | Si el usuario no tiene empleado asociado, devuelve el username.
    |
    | Si el usuario ya no existe, devuelve "Sistema".
    |
    */

    public function getNombreResponsableAttribute(): string
    {
        $usuario = $this->usuario;

        if (!$usuario) {
            return 'Sistema';
        }

        $empleadoResponsable =
            $usuario->empleadoConEliminados;

        if ($empleadoResponsable) {

            $nombreCompleto = trim(
                $empleadoResponsable->nombres
                . ' '
                . $empleadoResponsable->apellidos
            );

            if ($nombreCompleto !== '') {
                return $nombreCompleto;
            }
        }

        return $usuario->username
            ?: 'Usuario del sistema';
    }


    /*
    |--------------------------------------------------------------------------
    | USERNAME DEL RESPONSABLE
    |--------------------------------------------------------------------------
    */

    public function getUsernameResponsableAttribute(): string
    {
        return $this->usuario?->username
            ?: 'sistema';
    }


    /*
    |--------------------------------------------------------------------------
    | DESCRIPCIÓN DE LA ACCIÓN
    |--------------------------------------------------------------------------
    |
    | Devuelve una descripción predeterminada cuando el registro no tiene
    | una descripción personalizada.
    |
    */

    public function getDescripcionAccionAttribute(): string
    {
        if (
            $this->descripcion !== null &&
            trim($this->descripcion) !== ''
        ) {
            return $this->descripcion;
        }

        return match ($this->accion) {

            'creado' =>
                'El empleado fue registrado en el sistema.',

            'editado' =>
                'La información del empleado fue actualizada.',

            'activado' =>
                'El empleado fue activado.',

            'desactivado' =>
                'El empleado fue desactivado.',

            'eliminado' =>
                'El empleado fue eliminado y enviado a la papelera.',

            'restaurado' =>
                'El empleado fue restaurado desde la papelera.',

            default =>
                'Se realizó una acción sobre el empleado.',
        };
    }


    /*
    |--------------------------------------------------------------------------
    | ETIQUETA DE ACCIÓN
    |--------------------------------------------------------------------------
    |
    | Sirve para mostrar nombres más presentables en la interfaz.
    |
    */

    public function getAccionTextoAttribute(): string
    {
        return match ($this->accion) {

            'creado' =>
                'Creado',

            'editado' =>
                'Editado',

            'activado' =>
                'Activado',

            'desactivado' =>
                'Desactivado',

            'eliminado' =>
                'Eliminado',

            'restaurado' =>
                'Restaurado',

            default =>
                ucfirst(
                    str_replace(
                        '_',
                        ' ',
                        (string) $this->accion
                    )
                ),
        };
    }


    /*
    |--------------------------------------------------------------------------
    | ¿TIENE CAMBIO DE ESTADO?
    |--------------------------------------------------------------------------
    */

    public function tieneCambioEstado(): bool
    {
        return $this->estado_anterior !== null
            || $this->estado_nuevo !== null;
    }


    /*
    |--------------------------------------------------------------------------
    | ¿TIENE DATOS ANTERIORES?
    |--------------------------------------------------------------------------
    */

    public function tieneDatosAnteriores(): bool
    {
        return is_array($this->datos_anteriores)
            && count($this->datos_anteriores) > 0;
    }


    /*
    |--------------------------------------------------------------------------
    | ¿TIENE DATOS NUEVOS?
    |--------------------------------------------------------------------------
    */

    public function tieneDatosNuevos(): bool
    {
        return is_array($this->datos_nuevos)
            && count($this->datos_nuevos) > 0;
    }


    /*
    |--------------------------------------------------------------------------
    | ¿TIENE COMPARACIÓN DE DATOS?
    |--------------------------------------------------------------------------
    */

    public function tieneCambios(): bool
    {
        return $this->tieneDatosAnteriores()
            || $this->tieneDatosNuevos();
    }
}