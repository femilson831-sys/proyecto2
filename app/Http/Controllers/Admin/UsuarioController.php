<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO DE USUARIOS
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $buscar = trim(
            (string) $request->get('buscar', '')
        );

        $usuarios = Usuario::query()
            ->with([
                'empleado',
                'rol',
            ])
            ->when(
                $buscar !== '',
                function ($query) use ($buscar) {

                    $query->where(function ($q) use ($buscar) {

                        $q->where(
                            'username',
                            'like',
                            "%{$buscar}%"
                        )
                        ->orWhere(
                            'email',
                            'like',
                            "%{$buscar}%"
                        )
                        ->orWhereHas(
                            'empleado',
                            function ($empleadoQuery) use ($buscar) {

                                $empleadoQuery
                                    ->where(
                                        'codigo_empleado',
                                        'like',
                                        "%{$buscar}%"
                                    )
                                    ->orWhere(
                                        'nombres',
                                        'like',
                                        "%{$buscar}%"
                                    )
                                    ->orWhere(
                                        'apellidos',
                                        'like',
                                        "%{$buscar}%"
                                    );
                            }
                        )
                        ->orWhereHas(
                            'rol',
                            function ($rolQuery) use ($buscar) {

                                $rolQuery->where(
                                    'nombre',
                                    'like',
                                    "%{$buscar}%"
                                );
                            }
                        );

                    });
                }
            )
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();


        $totalUsuarios = Usuario::count();

        $usuariosActivos = Usuario::where(
            'estado',
            'activo'
        )->count();

        $usuariosInactivos = Usuario::where(
            'estado',
            'inactivo'
        )->count();


        return view(
            'admin.usuarios.index',
            compact(
                'usuarios',
                'totalUsuarios',
                'usuariosActivos',
                'usuariosInactivos',
                'buscar'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FORMULARIO CREAR
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        /*
         * Solo empleados:
         *
         * - activos
         * - no eliminados
         * - que todavía no tengan usuario
         */

        $empleados = Empleado::query()
            ->where('estado', 'activo')
            ->whereDoesntHave('usuario')
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->get();


        /*
         * Solo roles activos.
         */

        $roles = Rol::query()
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();


        return view(
            'admin.usuarios.create',
            compact(
                'empleados',
                'roles'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GUARDAR USUARIO
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $datos = $request->validate(
            [
                'empleado_id' => [
                    'required',
                    'integer',
                    'exists:empleados,id',
                    'unique:usuarios,empleado_id',
                ],

                'rol_id' => [
                    'required',
                    'integer',
                    'exists:roles,id',
                ],

                'username' => [
                    'required',
                    'string',
                    'min:4',
                    'max:80',
                    'alpha_dash',
                    'unique:usuarios,username',
                ],

                'email' => [
                    'nullable',
                    'email',
                    'max:150',
                    'unique:usuarios,email',
                ],

                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                ],

                'estado' => [
                    'required',
                    Rule::in([
                        'activo',
                        'inactivo',
                    ]),
                ],
            ],
            [
                'empleado_id.required' =>
                    'Debes seleccionar un empleado.',

                'empleado_id.exists' =>
                    'El empleado seleccionado no existe.',

                'empleado_id.unique' =>
                    'Este empleado ya tiene una cuenta de usuario.',

                'rol_id.required' =>
                    'Debes seleccionar un rol.',

                'rol_id.exists' =>
                    'El rol seleccionado no existe.',

                'username.required' =>
                    'El nombre de usuario es obligatorio.',

                'username.min' =>
                    'El nombre de usuario debe tener al menos 4 caracteres.',

                'username.max' =>
                    'El nombre de usuario no puede superar los 80 caracteres.',

                'username.alpha_dash' =>
                    'El usuario solo puede contener letras, números, guiones y guiones bajos.',

                'username.unique' =>
                    'Este nombre de usuario ya está registrado.',

                'email.email' =>
                    'Ingresa un correo electrónico válido.',

                'email.unique' =>
                    'Este correo electrónico ya está registrado.',

                'password.required' =>
                    'La contraseña es obligatoria.',

                'password.min' =>
                    'La contraseña debe tener al menos 8 caracteres.',

                'password.confirmed' =>
                    'La confirmación de contraseña no coincide.',

                'estado.required' =>
                    'Debes seleccionar el estado del usuario.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | VERIFICAR EMPLEADO
        |--------------------------------------------------------------------------
        */

        $empleado = Empleado::query()
            ->whereKey($datos['empleado_id'])
            ->where('estado', 'activo')
            ->first();


        if (!$empleado) {

            return back()
                ->withInput()
                ->withErrors([
                    'empleado_id' =>
                        'Solo puedes crear usuarios para empleados activos.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFICAR ROL
        |--------------------------------------------------------------------------
        */

        $rol = Rol::query()
            ->whereKey($datos['rol_id'])
            ->where('estado', 1)
            ->first();


        if (!$rol) {

            return back()
                ->withInput()
                ->withErrors([
                    'rol_id' =>
                        'El rol seleccionado no está activo.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | CREAR USUARIO
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($datos) {

            Usuario::create([
                'empleado_id' =>
                    $datos['empleado_id'],

                'rol_id' =>
                    $datos['rol_id'],

                'username' =>
                    strtolower(
                        trim($datos['username'])
                    ),

                'email' =>
                    !empty($datos['email'])
                        ? strtolower(
                            trim($datos['email'])
                        )
                        : null,

                /*
                 * El modelo Usuario tiene:
                 *
                 * 'password' => 'hashed'
                 *
                 * Laravel realizará el hash automáticamente.
                 */

                'password' =>
                    $datos['password'],

                'estado' =>
                    $datos['estado'],
            ]);

        });


        return redirect()
            ->route('admin.usuarios.index')
            ->with(
                'success',
                'Usuario creado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | VER USUARIO
    |--------------------------------------------------------------------------
    */

    public function show(Usuario $usuario)
    {
        $usuario->load([
            'empleado',
            'rol',
        ]);


        return view(
            'admin.usuarios.show',
            compact('usuario')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FORMULARIO EDITAR
    |--------------------------------------------------------------------------
    */

    public function edit(Usuario $usuario)
    {
        $usuario->load([
            'empleado',
            'rol',
        ]);


        /*
         * El empleado asociado no se cambia.
         */

        $empleados = Empleado::query()
            ->whereKey($usuario->empleado_id)
            ->get();


        /*
         * Roles activos.
         *
         * También conservamos el rol actual aunque
         * posteriormente haya sido desactivado.
         */

        $roles = Rol::query()
            ->where(function ($query) use ($usuario) {

                $query->where('estado', 1)
                    ->orWhere(
                        'id',
                        $usuario->rol_id
                    );

            })
            ->orderBy('nombre')
            ->get();


        return view(
            'admin.usuarios.edit',
            compact(
                'usuario',
                'empleados',
                'roles'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR USUARIO
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Usuario $usuario
    ) {
        $datos = $request->validate(
            [
                'rol_id' => [
                    'required',
                    'integer',
                    'exists:roles,id',
                ],

                'username' => [
                    'required',
                    'string',
                    'min:4',
                    'max:80',
                    'alpha_dash',

                    Rule::unique(
                        'usuarios',
                        'username'
                    )->ignore($usuario->id),
                ],

                'email' => [
                    'nullable',
                    'email',
                    'max:150',

                    Rule::unique(
                        'usuarios',
                        'email'
                    )->ignore($usuario->id),
                ],

                'password' => [
                    'nullable',
                    'string',
                    'min:8',
                    'confirmed',
                ],

                'estado' => [
                    'required',
                    Rule::in([
                        'activo',
                        'inactivo',
                    ]),
                ],
            ],
            [
                'rol_id.required' =>
                    'Debes seleccionar un rol.',

                'rol_id.exists' =>
                    'El rol seleccionado no existe.',

                'username.required' =>
                    'El nombre de usuario es obligatorio.',

                'username.min' =>
                    'El nombre de usuario debe tener al menos 4 caracteres.',

                'username.max' =>
                    'El nombre de usuario no puede superar los 80 caracteres.',

                'username.alpha_dash' =>
                    'El usuario solo puede contener letras, números, guiones y guiones bajos.',

                'username.unique' =>
                    'Este nombre de usuario ya está registrado.',

                'email.email' =>
                    'Ingresa un correo electrónico válido.',

                'email.unique' =>
                    'Este correo electrónico ya está registrado.',

                'password.min' =>
                    'La nueva contraseña debe tener al menos 8 caracteres.',

                'password.confirmed' =>
                    'La confirmación de contraseña no coincide.',

                'estado.required' =>
                    'Debes seleccionar el estado.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | EVITAR DESACTIVAR LA PROPIA CUENTA
        |--------------------------------------------------------------------------
        */

        if (
            Auth::id() === $usuario->id
            && $datos['estado'] === 'inactivo'
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'estado' =>
                        'No puedes desactivar tu propia cuenta.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFICAR ROL
        |--------------------------------------------------------------------------
        */

        $rol = Rol::find(
            $datos['rol_id']
        );


        if (!$rol) {

            return back()
                ->withInput()
                ->withErrors([
                    'rol_id' =>
                        'El rol seleccionado no existe.',
                ]);
        }


        /*
         * Si intenta cambiar hacia otro rol,
         * el nuevo rol debe estar activo.
         */

        if (
            (int) $usuario->rol_id !==
            (int) $rol->id
            &&
            !(bool) $rol->estado
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'rol_id' =>
                        'No puedes asignar un rol inactivo.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | SI SE ACTIVA, EL EMPLEADO DEBE ESTAR ACTIVO
        |--------------------------------------------------------------------------
        */

        if (
            $datos['estado'] === 'activo'
            && $usuario->empleado
            && $usuario->empleado->estado !== 'activo'
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'estado' =>
                        'No puedes activar esta cuenta porque el empleado está inactivo.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | DATOS A ACTUALIZAR
        |--------------------------------------------------------------------------
        */

        $actualizar = [
            'rol_id' =>
                $datos['rol_id'],

            'username' =>
                strtolower(
                    trim($datos['username'])
                ),

            'email' =>
                !empty($datos['email'])
                    ? strtolower(
                        trim($datos['email'])
                    )
                    : null,

            'estado' =>
                $datos['estado'],
        ];


        /*
         * Si password está vacío,
         * conservamos la contraseña actual.
         */

        if (!empty($datos['password'])) {

            $actualizar['password'] =
                $datos['password'];
        }


        DB::transaction(
            function () use (
                $usuario,
                $actualizar
            ) {

                $usuario->update(
                    $actualizar
                );

            }
        );


        return redirect()
            ->route('admin.usuarios.index')
            ->with(
                'success',
                'Usuario actualizado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ACTIVAR / DESACTIVAR USUARIO
    |--------------------------------------------------------------------------
    */

    public function cambiarEstado(
        Usuario $usuario
    ) {
        /*
         * El administrador conectado no puede
         * desactivar su propia cuenta.
         */

        if (Auth::id() === $usuario->id) {

            return back()
                ->with(
                    'error',
                    'No puedes desactivar tu propia cuenta.'
                );
        }


        $nuevoEstado =
            $usuario->estado === 'activo'
                ? 'inactivo'
                : 'activo';


        /*
        |--------------------------------------------------------------------------
        | VALIDAR EMPLEADO AL REACTIVAR
        |--------------------------------------------------------------------------
        */

        if ($nuevoEstado === 'activo') {

            $empleado = $usuario->empleado;

            if (!$empleado) {

                return back()
                    ->with(
                        'error',
                        'No se puede activar la cuenta porque no tiene un empleado activo asociado.'
                    );
            }


            if ($empleado->estado !== 'activo') {

                return back()
                    ->with(
                        'error',
                        'No se puede activar la cuenta porque el empleado está inactivo.'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CAMBIAR ESTADO
        |--------------------------------------------------------------------------
        */

        $usuario->update([
            'estado' => $nuevoEstado,
        ]);


        return back()
            ->with(
                'success',
                $nuevoEstado === 'activo'
                    ? 'Usuario activado correctamente.'
                    : 'Usuario desactivado correctamente.'
            );
    }
}