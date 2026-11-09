<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\DestinoPorRol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Mostrar formulario
    |--------------------------------------------------------------------------
    */

    public function mostrarLogin()
    {
        if (Auth::check()) {
            return redirect()->route(DestinoPorRol::ruta(Auth::user()));
        }

        return view('auth.login');
    }

    /*
    |--------------------------------------------------------------------------
    | Iniciar sesión
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        $datos = $request->validate([
            'username' => [
                'required',
                'string',
                'max:80',
            ],

            'password' => [
                'required',
                'string',
            ],

            'remember' => [
                'nullable',
                'boolean',
            ],
        ], [
            'username.required' => 'Ingrese su usuario.',
            'username.string' => 'El usuario no es válido.',
            'username.max' =>
                'El usuario no puede superar los 80 caracteres.',

            'password.required' => 'Ingrese su contraseña.',
            'password.string' => 'La contraseña no es válida.',

            'remember.boolean' =>
                'La opción de recordar sesión no es válida.',
        ]);

        $credenciales = [
            'username' => trim($datos['username']),
            'password' => $datos['password'],
            'estado' => 'activo',
        ];

        if (!Auth::attempt(
            $credenciales,
            $request->boolean('remember')
        )) {
            return back()
                ->withErrors([
                    'username' =>
                        'El usuario o la contraseña son incorrectos.',
                ])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        $usuario = $request->user();

        abort_unless($usuario, 401);

        $ahora = now();

        /*
         * Actualizar el último ingreso y guardar una entrada
         * independiente en el historial.
         */
        $accesoId = DB::transaction(function () use (
            $usuario,
            $request,
            $ahora
        ) {
            DB::table('usuarios')
                ->where(
                    'id',
                    $usuario->getAuthIdentifier()
                )
                ->update([
                    'ultimo_login' => $ahora,
                ]);

            return DB::table('historial_accesos')
                ->insertGetId([
                    'usuario_id' =>
                        $usuario->getAuthIdentifier(),

                    'fecha_ingreso' => $ahora,

                    'ultima_actividad' => $ahora,

                    'fecha_cierre' => null,

                    'origen' => 'login',

                    'ip' => $request->ip(),

                    'navegador' => Str::limit(
                        (string) $request->userAgent(),
                        500,
                        ''
                    ),
                ]);
        });

        /*
         * Cada sesión conserva el identificador de su propio
         * registro de acceso.
         */
        $request->session()->put(
            'historial_acceso_id',
            $accesoId
        );

        return redirect()
            ->intended(route(DestinoPorRol::ruta($usuario)))
            ->with(
                'success',
                'Bienvenido al sistema NANOS.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Cerrar sesión
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        $usuario = $request->user();

        $accesoId = $request->session()->get(
            'historial_acceso_id'
        );

        if ($usuario && $accesoId) {
            DB::table('historial_accesos')
                ->where('id', $accesoId)
                ->where(
                    'usuario_id',
                    $usuario->getAuthIdentifier()
                )
                ->whereNull('fecha_cierre')
                ->update([
                    'ultima_actividad' => now(),
                    'fecha_cierre' => now(),
                ]);
        }

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Sesión cerrada correctamente.'
            );
    }
}