<?php

namespace App\Support;

use App\Models\Usuario;
use Illuminate\Support\Str;

class DestinoPorRol
{
    public static function ruta(Usuario $usuario): string
    {
        return match (Str::slug(trim((string) ($usuario->rol?->nombre ?? '')))) {
            'administrador' => 'admin.dashboard',
            'personal-de-pesaje', 'personal-pesaje' => 'pesaje.dashboard',
            default => 'acceso.sin-panel',
        };
    }
}
