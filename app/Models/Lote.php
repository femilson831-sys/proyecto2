<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table = 'lotes';

    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'recepcion_detalle_id',
        'codigo_lote',
        'fecha_fabricacion',
        'fecha_vencimiento',
        'cantidad_inicial',
        'cantidad_actual',
    ];

    protected function casts(): array
    {
        return [
            'fecha_fabricacion' => 'date',
            'fecha_vencimiento' => 'date',
            'cantidad_inicial' => 'decimal:3',
            'cantidad_actual' => 'decimal:3',
        ];
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function recepcionDetalle()
    {
        return $this->belongsTo(
            RecepcionDetalle::class,
            'recepcion_detalle_id'
        );
    }

    public function escaneos()
    {
        return $this->hasMany(
            LoteEscaneo::class,
            'lote_id'
        );
    }

    public function escaneosAceptados()
    {
        return $this->hasMany(
            LoteEscaneo::class,
            'lote_id'
        )->where('estado', 'aceptado');
    }

    public function getCantidadEscaneadaAttribute(): int
    {
        if (array_key_exists('escaneos_aceptados_count', $this->attributes)) {
            return (int) $this->attributes['escaneos_aceptados_count'];
        }

        return $this->escaneosAceptados()->count();
    }

    public function getCantidadPendienteAttribute(): float
    {
        return max(
            0,
            (float) $this->cantidad_inicial -
            (float) $this->cantidad_escaneada
        );
    }

    public function getPorcentajeEscaneadoAttribute(): float
    {
        $esperado = (float) $this->cantidad_inicial;

        if ($esperado <= 0) {
            return 0;
        }

        return min(
            100,
            round(
                ($this->cantidad_escaneada / $esperado) * 100,
                2
            )
        );
    }

    public function estaCompleto(): bool
    {
        return $this->cantidad_escaneada >=
            (float) $this->cantidad_inicial;
    }
}