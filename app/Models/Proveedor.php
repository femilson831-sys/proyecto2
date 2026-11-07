<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'razon_social',
        'nombre_comercial',
        'nit',
        'ci',
        'telefono',
        'celular',
        'correo',
        'direccion',
        'ciudad',
        'estado',
        'observaciones',
    ];

    public function contactos(): HasMany
    {
        return $this->hasMany(
            ContactoProveedor::class,
            'proveedor_id'
        );
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(
            Producto::class,
            'proveedor_producto',
            'proveedor_id',
            'producto_id'
        )->withPivot([
            'codigo_proveedor',
            'precio_compra',
            'tiempo_entrega_dias',
            'es_principal',
        ]);
    }

    public function contactoPrincipal(): HasMany
    {
        return $this->contactos()
            ->where('es_principal', 1);
    }
}