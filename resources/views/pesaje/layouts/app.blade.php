<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pesaje') | NANOS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root { color-scheme: dark; font-family: system-ui, sans-serif; }
        body { margin: 0; background: #101116; color: #f2f3f6; }
        .topbar { background: #1a1b21; border-bottom: 1px solid #363842; padding: 16px 24px;
                  display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; }
        .brand { font-weight: 850; color: #ff6067; letter-spacing: .08em; text-decoration: none; }
        nav { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
        nav a { color: #f0f0f4; text-decoration: none; font-size: 14px; }
        nav a:hover { color: #ff6067; }
        .logout { background: transparent; border: 1px solid #555865; color: white; padding: 8px 12px; border-radius: 8px; cursor: pointer; }
        main { max-width: 1360px; margin: 0 auto; padding: 22px; }
        .flash { border: 1px solid #327659; background: #143527; border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; }
    </style>
    @stack('styles')
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ route('pesaje.dashboard') }}">NANOS · PERSONAL DE PESAJE</a>
        <nav aria-label="Navegación principal">
            <a href="{{ route('pesaje.dashboard') }}">Inicio</a>
            <a href="{{ route('pesaje.index') }}">Historial</a>
            <a href="{{ route('pesaje.create') }}">Nuevo pesaje</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="logout" type="submit">Cerrar sesión</button>
            </form>
        </nav>
    </header>
    <main>
        @if(session('success'))
            <div class="flash" role="status">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
