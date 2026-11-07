@php
    /*
    |--------------------------------------------------------------------------
    | Usuario actual
    |--------------------------------------------------------------------------
    */

    $usuarioActualSidebar = auth()->user();
    $empleadoActualSidebar = $usuarioActualSidebar?->empleado;

    $nombresSidebar = trim(
        (string) ($empleadoActualSidebar?->nombres ?? '')
    );

    $apellidosSidebar = trim(
        (string) ($empleadoActualSidebar?->apellidos ?? '')
    );

    $nombreUsuarioSidebar = trim(
        $nombresSidebar . ' ' . $apellidosSidebar
    );

    if ($nombreUsuarioSidebar === '') {
        $nombreUsuarioSidebar = trim(
            (string) ($usuarioActualSidebar?->username ?? '')
        );
    }

    if ($nombreUsuarioSidebar === '') {
        $nombreUsuarioSidebar = 'Administrador';
    }

    $rolUsuarioSidebar = trim(
        (string) ($usuarioActualSidebar?->rol?->nombre ?? '')
    );

    if ($rolUsuarioSidebar === '') {
        $rolUsuarioSidebar = 'Sin rol asignado';
    }

    $rolMostrarSidebar = mb_convert_case(
        str_replace('_', ' ', $rolUsuarioSidebar),
        MB_CASE_TITLE,
        'UTF-8'
    );

    $inicialSidebar = mb_strtoupper(
        mb_substr($nombreUsuarioSidebar, 0, 1, 'UTF-8'),
        'UTF-8'
    );

    /*
    |--------------------------------------------------------------------------
    | Foto del empleado
    |--------------------------------------------------------------------------
    */

    $fotoSidebar = null;

    $fotoEmpleadoSidebar = trim(
        (string) ($empleadoActualSidebar?->foto ?? '')
    );

    if ($fotoEmpleadoSidebar !== '') {
        $rutaFotoSidebar = str_replace(
            '\\',
            '/',
            $fotoEmpleadoSidebar
        );

        if (
            preg_match('~^https?://~i', $rutaFotoSidebar)
            && filter_var($rutaFotoSidebar, FILTER_VALIDATE_URL)
        ) {
            $fotoSidebar = $rutaFotoSidebar;
        } else {
            $rutaFotoSidebar = ltrim($rutaFotoSidebar, '/');

            if (str_starts_with($rutaFotoSidebar, 'public/')) {
                $rutaFotoSidebar = substr($rutaFotoSidebar, 7);
            }

            if (
                !str_starts_with($rutaFotoSidebar, 'empleados/')
                && !str_starts_with($rutaFotoSidebar, 'storage/')
            ) {
                $rutaFotoSidebar = 'empleados/' . $rutaFotoSidebar;
            }

            $fotoSidebar = asset($rutaFotoSidebar);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Alertas pendientes sin leer
    |--------------------------------------------------------------------------
    */

    $alertasNoLeidasSidebar = 0;

    if (
        $usuarioActualSidebar
        && \Illuminate\Support\Facades\Route::has('admin.alertas.index')
    ) {
        $alertasNoLeidasSidebar = \Illuminate\Support\Facades\DB::table('alertas as a')
            ->where('a.estado', 'pendiente')
            ->whereNotExists(function ($query) use ($usuarioActualSidebar) {
                $query
                    ->selectRaw('1')
                    ->from('alerta_usuarios as au')
                    ->whereColumn('au.alerta_id', 'a.id')
                    ->where(
                        'au.usuario_id',
                        $usuarioActualSidebar->id
                    )
                    ->where('au.leida', 1);
            })
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Iconos
    |--------------------------------------------------------------------------
    |
    | El HTML de estos iconos es estático y está definido aquí.
    |
    */

    $iconosSidebar = [
        'dashboard' => '
            <rect x="3" y="3" width="7" height="7" rx="1"/>
            <rect x="14" y="3" width="7" height="7" rx="1"/>
            <rect x="3" y="14" width="7" height="7" rx="1"/>
            <rect x="14" y="14" width="7" height="7" rx="1"/>
        ',

        'areas' => '
            <rect x="8" y="3" width="8" height="5" rx="1"/>
            <rect x="2" y="16" width="8" height="5" rx="1"/>
            <rect x="14" y="16" width="8" height="5" rx="1"/>
            <path d="M12 8v4M6 16v-4h12v4"/>
        ',

        'empleados' => '
            <circle cx="9" cy="7" r="4"/>
            <path d="M2 21v-2a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v2"/>
            <path d="M16 3.2a4 4 0 0 1 0 7.6"/>
            <path d="M22 21v-2a4 4 0 0 0-3-3.9"/>
        ',

        'usuarios' => '
            <circle cx="10" cy="8" r="4"/>
            <path d="M3 21v-2a7 7 0 0 1 14 0v2"/>
            <path d="M21 7v6M18 10h6"/>
        ',

        'roles' => '
            <path d="m12 3-8 4v5c0 5 3.4 8 8 9 4.6-1 8-4 8-9V7l-8-4Z"/>
            <path d="m9 12 2 2 4-4"/>
        ',

        'proveedores' => '
            <path d="M3 7h11v10H3z"/>
            <path d="M14 10h4l3 4v3h-7"/>
            <circle cx="7" cy="18" r="2"/>
            <circle cx="17" cy="18" r="2"/>
        ',

        'catalogos' => '
            <rect x="3" y="3" width="7" height="7" rx="1"/>
            <rect x="14" y="3" width="7" height="7" rx="1"/>
            <rect x="3" y="14" width="7" height="7" rx="1"/>
            <path d="M14 15h7M14 19h7"/>
        ',

        'unidades' => '
            <path d="m3 16 13-13 5 5L8 21l-5-5Z"/>
            <path d="m7 12 2 2m2-6 2 2m2-6 2 2"/>
        ',

        'productos' => '
            <path d="m12 3 9 5-9 5-9-5 9-5Z"/>
            <path d="M3 8v9l9 5 9-5V8"/>
            <path d="M12 13v9M7.5 5.5l9 5"/>
        ',

        'almacenes' => '
            <path d="M3 21V9l9-6 9 6v12"/>
            <path d="M7 21V11h10v10"/>
            <path d="M7 15h10M7 18h10"/>
        ',

        'inventario' => '
            <rect x="5" y="4" width="14" height="17" rx="2"/>
            <rect x="9" y="2" width="6" height="4" rx="1"/>
            <path d="m8 11 1 1 2-2M13 11h3"/>
            <path d="m8 16 1 1 2-2M13 16h3"/>
        ',

        'movimientos' => '
            <path d="M4 7h16m-4-4 4 4-4 4"/>
            <path d="M20 17H4m4-4-4 4 4 4"/>
        ',

        'pesaje' => '
            <path d="M12 3v17M5 7h14M8 21h8"/>
            <path d="m7 7-3 9h6L7 7Z"/>
            <path d="m17 7-3 9h6l-3-9Z"/>
        ',

        'promociones' => '
            <path d="M20 13 11 22 2 13V3h10l8 8a1.5 1.5 0 0 1 0 2Z"/>
            <circle cx="7" cy="8" r="1.5"/>
            <path d="m10 16 5-5"/>
        ',

        'cobros' => '
            <rect x="3" y="5" width="18" height="14" rx="2"/>
            <path d="M3 10h18M7 15h3M15 15h2"/>
        ',

        'cajas' => '
            <path d="M5 10h14l2 11H3l2-11Z"/>
            <rect x="7" y="3" width="10" height="7" rx="1"/>
            <path d="M10 6h4M7 17h10"/>
        ',

        'tareas' => '
            <rect x="4" y="3" width="16" height="18" rx="2"/>
            <path d="m7 8 1 1 2-2M12 8h5"/>
            <path d="m7 13 1 1 2-2M12 13h5M7 18h10"/>
        ',

        'reportes' => '
            <path d="M4 3v18h17"/>
            <path d="M8 16v-5M13 16V7M18 16v-8"/>
        ',

        'alertas' => '
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
            <path d="M10 21h4"/>
        ',

        'ml' => '
            <rect x="5" y="5" width="14" height="14" rx="3"/>
            <rect x="9" y="9" width="6" height="6" rx="1"/>
            <path d="M9 1v4M15 1v4M9 19v4M15 19v4"/>
            <path d="M1 9h4M1 15h4M19 9h4M19 15h4"/>
        ',

        'configuracion' => '
            <circle cx="12" cy="12" r="3"/>
            <path d="m9 3-1 3-3 1-2 3 2 2-1 3 3 3 3-1 2 2 3-1 1-3 3-1 2-3-2-2 1-3-3-3-3 1-2-2-3 1Z"/>
        ',
    ];

    /*
    |--------------------------------------------------------------------------
    | Secciones del menú
    |--------------------------------------------------------------------------
    |
    | Cada opción contiene:
    | etiqueta, ruta, patrones activos e icono.
    |
    */

    $seccionesSidebar = [
        'Principal' => [
            [
                'Dashboard',
                'admin.dashboard',
                ['admin.dashboard'],
                'dashboard',
            ],
        ],

        'Personal' => [
            [
                'Áreas y cargos',
                'admin.areas-cargos.index',
                [
                    'admin.areas-cargos.*',
                    'admin.areas.*',
                    'admin.cargos.*',
                ],
                'areas',
            ],
            [
                'Empleados',
                'admin.empleados.index',
                ['admin.empleados.*'],
                'empleados',
            ],
            [
                'Usuarios',
                'admin.usuarios.index',
                ['admin.usuarios.*'],
                'usuarios',
            ],
            [
                'Roles',
                'admin.roles.index',
                ['admin.roles.*'],
                'roles',
            ],
        ],

        'Gestión de inventario' => [
            [
                'Proveedores',
                'admin.proveedores.index',
                ['admin.proveedores.*'],
                'proveedores',
            ],
            [
                'Catálogos de productos',
                'admin.catalogos-productos.index',
                ['admin.catalogos-productos.*'],
                'catalogos',
            ],
            [
                'Unidades de medida',
                'admin.unidades-medida.index',
                ['admin.unidades-medida.*'],
                'unidades',
            ],
            [
                'Productos',
                'admin.productos.index',
                ['admin.productos.*'],
                'productos',
            ],
            [
                'Almacenes',
                'admin.almacenes.index',
                ['admin.almacenes.*'],
                'almacenes',
            ],
            [
                'Inventario',
                'admin.inventario.index',
                ['admin.inventario.*'],
                'inventario',
            ],
            [
                'Movimientos',
                'admin.movimientos.index',
                ['admin.movimientos.*'],
                'movimientos',
            ],
        ],

        'Operaciones' => [
            [
                'Pesaje',
                'admin.pesajes.index',
                ['admin.pesajes.*'],
                'pesaje',
            ],
            [
                'Promociones',
                'admin.promociones.index',
                ['admin.promociones.*'],
                'promociones',
            ],
            [
                'Cobros por cajero',
                'admin.ventas.reportes.cobros',
                [
                    'admin.ventas.reportes.*',
                    'admin.ventas.pagos',
                ],
                'cobros',
            ],
            [
                'Cajas y turnos',
                'admin.cajas.index',
                ['admin.cajas.*'],
                'cajas',
            ],
            [
                'Tareas',
                'admin.tareas.index',
                ['admin.tareas.*'],
                'tareas',
            ],
        ],

        'Análisis' => [
            [
                'Reportes',
                'admin.reportes.index',
                ['admin.reportes.*'],
                'reportes',
            ],
            [
                'Alertas',
                'admin.alertas.index',
                ['admin.alertas.*'],
                'alertas',
            ],
            [
                'Machine Learning',
                null,
                [],
                'ml',
            ],
        ],

        'Sistema' => [
            [
                'Configuración',
                null,
                [],
                'configuracion',
            ],
        ],
    ];

    $inicioSidebar = \Illuminate\Support\Facades\Route::has('admin.dashboard')
        ? route('admin.dashboard')
        : url('/');
@endphp

<style>
    .sidebar .menu-item svg {
        width: 20px;
        height: 20px;
        min-width: 20px;
        flex-shrink: 0;
    }

    .sidebar .menu-item > span:first-of-type {
        min-width: 0;
    }

    .sidebar .menu-item:focus-visible,
    .sidebar .brand:focus-visible {
        outline: 2px solid #ff777d;
        outline-offset: 3px;
    }

    .sidebar .menu-item[aria-disabled="true"] {
        opacity: .5;
        cursor: not-allowed;
    }

    .sidebar .menu-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 23px;
        min-height: 21px;
        margin-left: auto;
        padding: 2px 6px;
        border-radius: 999px;
        color: #fff;
        background: #ce1b25;
        font-size: 10px;
        font-weight: 800;
        line-height: 1.2;
    }

    .sidebar .sidebar-user {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 0;
    }

    .sidebar .sidebar-avatar {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        min-width: 42px;
        overflow: hidden;
        border: 2px solid rgba(227, 27, 35, .4);
        border-radius: 50%;
        color: #ff777d;
        background: rgba(227, 27, 35, .12);
        font-size: 15px;
        font-weight: 800;
    }

    .sidebar .sidebar-avatar img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        border-radius: 50%;
    }

    .sidebar .sidebar-avatar-fallback {
        display: grid;
        place-items: center;
        width: 100%;
        height: 100%;
    }

    .sidebar .sidebar-avatar-fallback[hidden] {
        display: none !important;
    }

    .sidebar .sidebar-user-info {
        min-width: 0;
        overflow: hidden;
    }

    .sidebar .sidebar-user-info strong,
    .sidebar .sidebar-user-info span {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sidebar .sidebar-user-info strong {
        color: #eeeef3;
        font-size: 12px;
        line-height: 1.5;
    }

    .sidebar .sidebar-user-info span {
        margin-top: 4px;
        color: #a0a6b4;
        font-size: 10px;
        line-height: 1.5;
    }
</style>

<aside class="sidebar" id="sidebar" aria-label="Menú administrativo">

    {{-- MARCA --}}

    <div class="sidebar-header">
        <a href="{{ $inicioSidebar }}" class="brand">
            <div class="brand-logo">
                <img
                    src="{{ asset('img/logo.png') }}"
                    alt="Logo NANOS"
                >
            </div>

            <div class="brand-info">
                <strong>NANOS</strong>
                <span>Administración</span>
            </div>
        </a>
    </div>

    {{-- NAVEGACIÓN --}}

    <nav class="sidebar-scroll" aria-label="Secciones del sistema">
        @foreach ($seccionesSidebar as $tituloSidebar => $opcionesSidebar)
            @php
                /*
                 * Mostrar únicamente las rutas registradas.
                 * Las opciones con ruta nula permanecen deshabilitadas.
                 */
                $opcionesVisiblesSidebar = array_filter(
                    $opcionesSidebar,
                    static function (array $opcion): bool {
                        return $opcion[1] === null
                            || \Illuminate\Support\Facades\Route::has($opcion[1]);
                    }
                );
            @endphp

            @if (count($opcionesVisiblesSidebar) > 0)
                <div class="menu-section">
                    <div class="menu-title">
                        {{ $tituloSidebar }}
                    </div>

                    @foreach ($opcionesVisiblesSidebar as $opcionSidebar)
                        @php
                            [
                                $etiquetaSidebar,
                                $rutaSidebar,
                                $patronesSidebar,
                                $iconoSidebar,
                            ] = $opcionSidebar;

                            $activoSidebar = $rutaSidebar !== null
                                && request()->routeIs(...$patronesSidebar);

                            $esAlertaSidebar = $rutaSidebar === 'admin.alertas.index';

                            $descripcionSidebar = $etiquetaSidebar;

                            if ($esAlertaSidebar && $alertasNoLeidasSidebar > 0) {
                                $descripcionSidebar .= ', '
                                    . $alertasNoLeidasSidebar
                                    . ' sin leer';
                            }
                        @endphp

                        @if ($rutaSidebar !== null)
                            <a
                                href="{{ route($rutaSidebar) }}"
                                class="menu-item {{ $activoSidebar ? 'active' : '' }}"
                                aria-label="{{ $descripcionSidebar }}"
                                @if ($activoSidebar)
                                    aria-current="page"
                                @endif
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true"
                                >
                                    {!! $iconosSidebar[$iconoSidebar] !!}
                                </svg>

                                <span>{{ $etiquetaSidebar }}</span>

                                @if ($esAlertaSidebar && $alertasNoLeidasSidebar > 0)
                                    <span
                                        class="menu-badge"
                                        aria-hidden="true"
                                    >
                                        {{ $alertasNoLeidasSidebar > 99
                                            ? '99+'
                                            : $alertasNoLeidasSidebar }}
                                    </span>
                                @endif
                            </a>
                        @else
                            <span
                                class="menu-item"
                                aria-disabled="true"
                                title="Módulo pendiente de habilitar"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true"
                                >
                                    {!! $iconosSidebar[$iconoSidebar] !!}
                                </svg>

                                <span>{{ $etiquetaSidebar }}</span>
                            </span>
                        @endif
                    @endforeach
                </div>
            @endif
        @endforeach
    </nav>

    {{-- USUARIO ACTUAL --}}

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                @if ($fotoSidebar)
                    <img
                        src="{{ $fotoSidebar }}"
                        alt="Foto de {{ $nombreUsuarioSidebar }}"
                        decoding="async"
                        onerror="
                            this.onerror = null;
                            this.style.display = 'none';

                            if (this.nextElementSibling) {
                                this.nextElementSibling.hidden = false;
                            }
                        "
                    >
                @endif

                <span
                    class="sidebar-avatar-fallback"
                    aria-hidden="true"
                    @if ($fotoSidebar)
                        hidden
                    @endif
                >
                    {{ $inicialSidebar }}
                </span>
            </div>

            <div class="sidebar-user-info">
                <strong title="{{ $nombreUsuarioSidebar }}">
                    {{ $nombreUsuarioSidebar }}
                </strong>

                <span title="{{ $rolMostrarSidebar }}">
                    {{ $rolMostrarSidebar }}
                </span>
            </div>
        </div>
    </div>
</aside>