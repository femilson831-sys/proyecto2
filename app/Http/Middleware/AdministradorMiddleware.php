<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdministradorMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $usuario = $request->user();

        if (!$usuario) {
            return redirect()->guest(
                route('login')
            );
        }

        if (
            strtolower(trim((string) $usuario->estado))
            !== 'activo'
        ) {
            abort(
                403,
                'Tu cuenta no está activa.'
            );
        }

        $nombreRol = strtolower(
            trim((string) ($usuario->rol?->nombre ?? ''))
        );

        if ($nombreRol !== 'administrador') {
            abort(
                403,
                'No tienes permisos para acceder al panel administrativo.'
            );
        }

        return $next($request);
    }
}