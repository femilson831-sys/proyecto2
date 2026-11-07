<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends Model
{
    use SoftDeletes;

    protected $table = 'empleados';

    protected $fillable = [
        'codigo_empleado',
        'nombres',
        'apellidos',
        'ci',
        'fecha_nacimiento',
        'genero',
        'foto',
        'telefono',
        'celular',
        'correo',
        'direccion',
        'ciudad',
        'fecha_ingreso',
        'fecha_salida',
        'area_id',
        'cargo_id',
        'tipo_contrato',
        'estado',
        'observaciones',
        'eliminado_por',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'fecha_ingreso' => 'date',
            'fecha_salida' => 'date',
            'deleted_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | USUARIO DEL EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function usuario()
    {
        return $this->hasOne(
            Usuario::class,
            'empleado_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | USUARIO QUE ELIMINÓ AL EMPLEADO
    |--------------------------------------------------------------------------
    */

    public function eliminadoPor()
    {
        return $this->belongsTo(
            Usuario::class,
            'eliminado_por'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HISTORIAL DEL EMPLEADO
    |--------------------------------------------------------------------------
    |
    | Aquí estarán:
    |
    | - creación
    | - edición
    | - activación
    | - desactivación
    | - eliminación
    | - restauración
    |
    */

    public function historial()
    {
        return $this->hasMany(
            HistorialEmpleado::class,
            'empleado_id'
        )->orderByDesc('created_at');
    }

    public function area()
    {
    return $this->belongsTo(
        Area::class,
        'area_id'
    );
    }

    public function cargo()
    {
    return $this->belongsTo(
        Cargo::class,
        'cargo_id'
    );
    }
}
