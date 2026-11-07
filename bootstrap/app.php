<?php

use App\Http\Middleware\AdministradorMiddleware;
use App\Http\Middleware\RegistrarActividadUsuario;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(
    basePath: dirname(__DIR__)
)
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        /*
         * Reconocer los encabezados enviados por
         * Cloudflare Tunnel.
         */
        $middleware->trustProxies(
            at: '*',
            headers:
                Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO
        );

        /*
         * Control de acceso administrativo.
         */
        $middleware->alias([
            'administrador' => AdministradorMiddleware::class,
        ]);

        /*
         * Registrar actividad después de que el grupo
         * web haya iniciado la sesión.
         */
        $middleware->web(append: [
            RegistrarActividadUsuario::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();