@php
    $usuarioMenuTareas = auth()->user()?->getAuthIdentifier();

    $nuevasRespuestasTareas = \Illuminate\Support\Facades\DB::table('alerta_usuarios as au')
        ->join('alertas as a', 'a.id', '=', 'au.alerta_id')
        ->join('tipos_alerta as tipo', 'tipo.id', '=', 'a.tipo_alerta_id')
        ->where('au.usuario_id', $usuarioMenuTareas)
        ->where('a.estado', 'pendiente')
        ->where('tipo.nombre', 'Respuesta de tarea')
        ->where(function ($query) {
            $query->where('au.leida', false)
                ->orWhereNull('au.leida');
        })
        ->distinct()
        ->count('a.id');
@endphp

<nav class="ta-actions" aria-label="Opciones de tareas" style="justify-content:flex-start;">
    <a href="{{ route('admin.tareas.index') }}" class="ta-button">
        Tareas
    </a>

    <a href="{{ route('admin.tareas.respuestas.index') }}" class="ta-button">
        Respuestas
    </a>

    <a
        href="{{ route('admin.tareas.respuestas.index', ['vista' => 'notificaciones']) }}"
        class="ta-button {{ $nuevasRespuestasTareas > 0 ? 'primary' : '' }}"
    >
        Notificaciones
        <span class="ta-badge">{{ $nuevasRespuestasTareas }}</span>
    </a>
</nav>