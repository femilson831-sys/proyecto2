<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoPrecio extends Model
{
    protected $table = 'productos_precios';

    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'precio_compra',
        'precio_venta',
        'fecha_inicio',
        'fecha_fin',
        'usuario_id',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'precio_compra' => 'decimal:2',
            'precio_venta' => 'decimal:2',
            'fecha_inicio' => 'datetime',
            'fecha_fin' => 'datetime',
            'estado' => 'boolean',
        ];
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}