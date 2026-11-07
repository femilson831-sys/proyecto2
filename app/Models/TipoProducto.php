<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoProducto extends Model
{
    protected $table = 'tipos_producto';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'permite_pesaje',
    ];

    protected function casts(): array
    {
        return [
            'permite_pesaje' => 'boolean',
        ];
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'tipo_producto_id');
    }
}