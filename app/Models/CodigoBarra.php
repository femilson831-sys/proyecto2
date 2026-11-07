<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoBarra extends Model
{
    protected $table = 'codigos_barras';

    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'codigo',
        'tipo',
        'es_principal',
    ];

    protected function casts(): array
    {
        return [
            'es_principal' => 'boolean',
        ];
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}