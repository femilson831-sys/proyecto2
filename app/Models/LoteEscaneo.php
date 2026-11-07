<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteEscaneo extends Model
{
    protected $table = 'lote_escaneos';

    public $timestamps = false;

    protected $fillable = [
        'lote_id',
        'producto_id',
        'codigo_barras',
        'usuario_id',
        'empleado_id',
        'fecha_escaneo',
        'estado',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_escaneo' => 'datetime',
        ];
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}