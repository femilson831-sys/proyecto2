<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marcas';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'marca_id');
    }
}