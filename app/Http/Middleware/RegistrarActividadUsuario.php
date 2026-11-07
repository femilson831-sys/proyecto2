<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RegistrarActividadUsuario
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (!$request->hasSession()) {
            return $next($request);
        }

        $usuario = $request->user();

        if (!$usuario) {
            return $next($request);
        }

        $usuarioId = $usuario->getAuthIdentifier();

        $accesoId = $request->session()->get(
            'historial_acceso_id'
        );

        $acceso = null;

        if ($accesoId) {
            $acceso = DB::table('historial_accesos')
                ->where('id', $accesoId)
                ->where('usuario_id', $usuarioId)
                ->whereNull('fecha_cierre')
                ->first(['id']);
        }

        $ahora = now();

        if ($acceso) {
            DB::table('historial_accesos')
                ->where('id', $acceso->id)
                ->where('usuario_id', $usuarioId)
                ->whereNull('fecha_cierre')
                ->update([
                    'ultima_actividad' => $ahora,
                ]);
        } else {
            /*
             * Registra una sesión que ya estaba iniciada
             * o que fue recuperada mediante Recordarme.
             */
            $nuevoAccesoId = DB::table('historial_accesos')
                ->insertGetId([
                    'usuario_id' => $usuarioId,

                    'fecha_ingreso' => $ahora,

                    'ultima_actividad' => $ahora,

                    'fecha_cierre' => null,

                    'origen' => 'sesion_recuperada',

                    'ip' => $request->ip(),

                    'navegador' => Str::limit(
                        (string) $request->userAgent(),
                        500,
                        ''
                    ),
                ]);

            $request->session()->put(
                'historial_acceso_id',
                $nuevoAccesoId
            );
        }

        return $next($request);
    }
}