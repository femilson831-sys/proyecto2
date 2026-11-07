<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'codigo_interno',
        'nombre',
        'descripcion',
        'categoria_id',
        'subcategoria_id',
        'marca_id',
        'unidad_medida_id',
        'tipo_producto_id',
        'imagen',
        'stock_minimo',
        'stock_maximo',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'stock_minimo' => 'decimal:3',
            'stock_maximo' => 'decimal:3',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function subcategoria(): BelongsTo
    {
        return $this->belongsTo(Subcategoria::class, 'subcategoria_id');
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }

    public function tipoProducto(): BelongsTo
    {
        return $this->belongsTo(TipoProducto::class, 'tipo_producto_id');
    }

    public function codigosBarras(): HasMany
    {
        return $this->hasMany(CodigoBarra::class, 'producto_id');
    }

    public function codigoBarraPrincipal(): HasOne
    {
        return $this->hasOne(CodigoBarra::class, 'producto_id')
            ->where('es_principal', 1);
    }

    public function precios(): HasMany
    {
        return $this->hasMany(ProductoPrecio::class, 'producto_id')
            ->orderByDesc('fecha_inicio');
    }

    public function precioActual(): HasOne
    {
        return $this->hasOne(ProductoPrecio::class, 'producto_id')
            ->where('estado', 1)
            ->whereNull('fecha_fin')
            ->latestOfMany('fecha_inicio');
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventario::class, 'producto_id');
    }

    public function lotes(): HasMany
    {
        return $this->hasMany(Lote::class, 'producto_id');
    }

    public function escaneosLote(): HasMany
    {
        return $this->hasMany(LoteEscaneo::class, 'producto_id');
    }

    /**
     * Proveedores asociados mediante proveedor_producto.
     */
    public function proveedores(): BelongsToMany
    {
        return $this->belongsToMany(
            Proveedor::class,
            'proveedor_producto',
            'producto_id',
            'proveedor_id'
        )->withPivot([
            'codigo_proveedor',
            'precio_compra',
            'tiempo_entrega_dias',
            'es_principal',
        ]);
    }

    public function getStockTotalAttribute(): float
    {
        return (float) $this->inventarios()->sum('cantidad');
    }

    /**
     * Obtiene la URL de una imagen guardada en public/.
     * También admite imágenes antiguas de storage/app/public/.
     */
    public function getImagenUrlAttribute(): ?string
    {
        if (empty($this->imagen)) {
            return null;
        }

        $ruta = ltrim($this->imagen, '/');

        if (is_file(public_path($ruta))) {
            return asset($ruta);
        }

        return asset('storage/' . $ruta);
    }

    public function estaActivo(): bool
    {
        return $this->estado === 'activo';
    }
}