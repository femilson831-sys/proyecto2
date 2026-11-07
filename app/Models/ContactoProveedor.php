<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactoProveedor extends Model
{
    protected $table = 'contactos_proveedor';

    public $timestamps = false;

    protected $fillable = [
        'proveedor_id',
        'nombre',
        'cargo',
        'telefono',
        'celular',
        'correo',
        'es_principal',
    ];

    protected function casts(): array
    {
        return [
            'es_principal' => 'boolean',
        ];
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(
            Proveedor::class,
            'proveedor_id'
        );
    }
}