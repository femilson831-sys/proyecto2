@php
    /*
    |--------------------------------------------------------------------------
    | USUARIO Y EMPLEADO
    |--------------------------------------------------------------------------
    */

    $usuarioNav = auth()->user();
    $empleadoNav = $usuarioNav?->empleado;

    /*
    |--------------------------------------------------------------------------
    | NOMBRE DEL USUARIO
    |--------------------------------------------------------------------------
    */

    $nombreNav = trim((string) ($empleadoNav?->nombres ?? ''));
    $apellidoNav = trim((string) ($empleadoNav?->apellidos ?? ''));

    if ($nombreNav === '') {
        $nombreNav = trim((string) ($usuarioNav?->username ?? ''));

        if ($nombreNav === '') {
            $nombreNav = 'Administrador';
        }
    }

    $nombreCompletoNav = trim($nombreNav . ' ' . $apellidoNav);

    $inicialNav = mb_strtoupper(
        mb_substr($nombreNav, 0, 1, 'UTF-8'),
        'UTF-8'
    );

    /*
    |--------------------------------------------------------------------------
    | FOTO DEL EMPLEADO
    |--------------------------------------------------------------------------
    |
    | En la base de datos: empleados/imagen.jpg
    | En el proyecto: public/empleados/imagen.jpg
    |
    */

    $fotoNav = null;

    if ($empleadoNav && !empty($empleadoNav->foto)) {
        $rutaFotoNav = ltrim(
            str_replace('\\', '/', trim($empleadoNav->foto)),
            '/'
        );

        $fotoNav = asset($rutaFotoNav);
    }

    /*
    |--------------------------------------------------------------------------
    | ROL
    |--------------------------------------------------------------------------
    */

    $rolNombreNav = trim(
        (string) ($usuarioNav?->rol?->nombre ?? '')
    );

    if ($rolNombreNav === '') {
        $rolNombreNav = 'Sin rol asignado';
    }

    $rolNav = mb_convert_case(
        str_replace('_', ' ', $rolNombreNav),
        MB_CASE_TITLE,
        'UTF-8'
    );

    /*
    |--------------------------------------------------------------------------
    | ALERTAS PENDIENTES SIN LEER
    |--------------------------------------------------------------------------
    */

    $alertasNoLeidasNavbar = 0;

    if ($usuarioNav && \Illuminate\Support\Facades\Route::has('admin.alertas.index')) {
        $alertasNoLeidasNavbar = \Illuminate\Support\Facades\DB::table('alertas as a')
            ->where('a.estado', 'pendiente')
            ->whereNotExists(function ($query) use ($usuarioNav) {
                $query->selectRaw('1')
                    ->from('alerta_usuarios as au')
                    ->whereColumn('au.alerta_id', 'a.id')
                    ->where('au.usuario_id', $usuarioNav->id)
                    ->where('au.leida', 1);
            })
            ->count();
    }

    $urlAlertasNavbar = \Illuminate\Support\Facades\Route::has('admin.alertas.index')
        ? route('admin.alertas.index')
        : url('/admin/alertas');
@endphp

<style>
    /* PERFIL */

    .profile {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    /* AVATAR */

    .profile-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        min-width: 42px;
        overflow: hidden;
        border: 2px solid rgba(227, 27, 35, .35);
        border-radius: 50%;
        background: rgba(227, 27, 35, .10);
        color: #ff646b;
        font-size: 14px;
        font-weight: 800;
        box-shadow: 0 0 0 3px rgba(227, 27, 35, .05);
    }

    .profile-avatar img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        border-radius: 50%;
    }

    .profile-avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .profile-avatar-initial[hidden] {
        display: none !important;
    }

    /* INFORMACIÓN DEL USUARIO */

    .profile-info {
        min-width: 0;
    }

    .profile-info strong {
        display: block;
        max-width: 180px;
        overflow: hidden;
        color: #eeeeee;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.5;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .profile-info span {
        display: block;
        max-width: 180px;
        margin-top: 3px;
        overflow: hidden;
        color: #969dac;
        font-size: 10px;
        font-weight: 600;
        line-height: 1.5;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .navbar-logout-form {
        display: flex;
        align-items: center;
        margin: 0;
    }

    /* NOTIFICACIONES */

    .notification-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .notification-count {
        position: absolute;
        top: -5px;
        right: -5px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border: 2px solid #17181c;
        border-radius: 999px;
        color: #fff;
        background: #e31b23;
        font-size: 9px;
        font-weight: 800;
        line-height: 1;
    }

    /* RESPONSIVE */

    @media (max-width: 700px) {
        .profile {
            gap: 9px;
        }

        .profile-avatar {
            width: 38px;
            height: 38px;
            min-width: 38px;
        }

        .profile-info strong,
        .profile-info span {
            max-width: 120px;
        }
    }

    @media (max-width: 520px) {
        .profile-info {
            display: none;
        }
    }
</style>

<header class="navbar">

    {{-- LADO IZQUIERDO --}}
    <div class="navbar-left">

        {{-- BOTÓN DEL MENÚ --}}
        <button
            type="button"
            class="menu-toggle"
            id="menuToggle"
            aria-label="Abrir o cerrar menú"
            aria-controls="sidebar"
        >
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                aria-hidden="true"
            >
                <path d="M4 6h16"/>
                <path d="M4 12h16"/>
                <path d="M4 18h16"/>
            </svg>
        </button>

        {{-- TÍTULO --}}
        <div class="navbar-title">
            <strong>Panel Administrativo</strong>
            <span>Minisúper NANOS</span>
        </div>
    </div>

    {{-- LADO DERECHO --}}
    <div class="navbar-right">

        {{-- NOTIFICACIONES --}}
        <a
            href="{{ $urlAlertasNavbar }}"
            class="nav-button notification-link"
            title="Ver alertas"
            aria-label="Ver alertas{{ $alertasNoLeidasNavbar > 0 ? ', ' . $alertasNoLeidasNavbar . ' sin leer' : '' }}"
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
                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                <path d="M10 21h4"/>
            </svg>

            @if ($alertasNoLeidasNavbar > 0)
                <span class="notification-count" aria-hidden="true">
                    {{ $alertasNoLeidasNavbar > 99 ? '99+' : $alertasNoLeidasNavbar }}
                </span>
            @endif
        </a>

        {{-- PERFIL --}}
        <div class="profile">
            <div class="profile-avatar">
                @if ($fotoNav)
                    <img
                        src="{{ $fotoNav }}"
                        alt="Foto de {{ $nombreCompletoNav }}"
                        id="navbarProfilePhoto"
                        decoding="async"
                        onerror="
                            this.style.display = 'none';
                            const inicial = this.nextElementSibling;
                            if (inicial) {
                                inicial.hidden = false;
                            }
                        "
                    >

                    <span
                        class="profile-avatar-initial"
                        id="navbarProfileInitial"
                        aria-hidden="true"
                        hidden
                    >
                        {{ $inicialNav }}
                    </span>
                @else
                    <span
                        class="profile-avatar-initial"
                        id="navbarProfileInitial"
                        aria-hidden="true"
                    >
                        {{ $inicialNav }}
                    </span>
                @endif
            </div>

            {{-- NOMBRE Y ROL --}}
            <div class="profile-info">
                <strong title="{{ $nombreCompletoNav }}">
                    {{ $nombreCompletoNav }}
                </strong>

                <span title="{{ $rolNav }}">
                    {{ $rolNav }}
                </span>
            </div>
        </div>

        {{-- CERRAR SESIÓN --}}
        <form
            class="navbar-logout-form"
            method="POST"
            action="{{ route('logout') }}"
        >
            @csrf

            <button
                type="submit"
                class="logout-button"
                title="Cerrar sesión"
                aria-label="Cerrar sesión"
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
                    <path d="M10 17l5-5-5-5"/>
                    <path d="M15 12H3"/>
                    <path d="M14 3h5a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-5"/>
                </svg>
            </button>
        </form>
    </div>
</header>