<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RolMiddleware
{
    public function handle(Request $request, Closure $next, string $rol): Response
    {
        $usuario = $request->user();

        if (!$usuario) {
            return redirect()->guest(route('login'));
        }

        abort_unless($usuario->estado === 'activo', 403, 'Tu cuenta no está activa.');

        $rolActual = Str::slug(trim((string) ($usuario->rol?->nombre ?? '')));
        abort_unless($usuario->rol?->estado && $rolActual === $rol, 403, 'No tienes permisos para este módulo.');

        return $next($request);
    }
}
