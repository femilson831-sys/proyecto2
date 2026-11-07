@extends('admin.layouts.app')

@section('title', 'Roles')

@push('styles')
<style>
    .roles-page {
        --surface: #191a20;
        --surface-2: #20222a;
        --surface-3: #14161c;
        --border: #30333d;
        --border-soft: #262932;
        --muted: #a5a9b5;
        --muted-2: #7c8191;
        --red: #e31b23;
        --red-soft: rgba(227, 27, 35, .12);
        --red-glow: rgba(227, 27, 35, .35);
        --green: #4ade80;
        --green-soft: rgba(74, 222, 128, .1);
        --amber: #f59e0b;
        --amber-soft: rgba(245, 158, 11, .1);
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 9px;
        color: #f4f5f8;
    }

    .roles-page *,
    .role-confirm-dialog * {
        box-sizing: border-box;
    }

    /* ===================== HEADER ===================== */
    .roles-header {
        position: relative;
        margin-bottom: 26px;
        padding-left: 18px;
    }

    .roles-header::before {
        position: absolute;
        top: 4px;
        bottom: 4px;
        left: 0;
        width: 3px;
        border-radius: 3px;
        background: linear-gradient(180deg, var(--red), #7a0d12);
        content: "";
    }

    .roles-eyebrow {
        display: block;
        margin-bottom: 8px;
        color: #ff777d;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.8px;
        text-transform: uppercase;
    }

    .roles-header h1 {
        margin: 0 0 8px;
        font-size: 30px;
        font-weight: 800;
        letter-spacing: -.4px;
    }

    .roles-header p {
        margin: 0;
        max-width: 560px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.65;
    }

    /* ===================== SUMMARY ===================== */
    .roles-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .summary-card {
        position: relative;
        overflow: hidden;
        padding: 20px 20px 20px 22px;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        background:
            radial-gradient(circle at 100% 0%, rgba(255, 255, 255, .03), transparent 60%),
            var(--surface);
        transition: border-color .25s ease, transform .25s ease;
    }

    .summary-card::before {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .06), transparent);
        content: "";
    }

    .summary-card:hover {
        border-color: #3a3d48;
        transform: translateY(-1px);
    }

    .summary-card .summary-icon {
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        margin-bottom: 14px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: var(--surface-2);
        color: var(--muted);
    }

    .summary-card .summary-icon svg {
        width: 16px;
        height: 16px;
    }

    .summary-card[data-tone="total"] .summary-icon {
        border-color: rgba(255, 255, 255, .08);
        color: #cbd0dc;
    }

    .summary-card[data-tone="active"] .summary-icon {
        border-color: rgba(74, 222, 128, .25);
        background: var(--green-soft);
        color: #7ce8ab;
    }

    .summary-card[data-tone="inactive"] .summary-icon {
        border-color: rgba(160, 166, 180, .25);
        background: rgba(160, 166, 180, .08);
        color: #c2c7d3;
    }

    .summary-card .summary-label {
        display: block;
        color: var(--muted);
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .4px;
        text-transform: uppercase;
    }

    .summary-card strong {
        display: block;
        margin-top: 8px;
        font-size: 30px;
        font-weight: 800;
        letter-spacing: -.6px;
        line-height: 1;
    }

    /* ===================== PANEL ===================== */
    .roles-panel {
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        background: var(--surface);
        box-shadow: 0 12px 40px rgba(0, 0, 0, .28);
    }

    .roles-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 20px 22px;
        border-bottom: 1px solid var(--border);
        background: linear-gradient(180deg, rgba(255, 255, 255, .015), transparent);
    }

    .roles-toolbar h2 {
        margin: 0 0 5px;
        font-size: 15px;
        font-weight: 750;
        letter-spacing: -.2px;
    }

    .roles-toolbar p {
        margin: 0;
        color: var(--muted-2);
        font-size: 11px;
    }

    .roles-filters {
        display: flex;
        gap: 9px;
    }

    .roles-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .roles-input-wrap svg {
        position: absolute;
        left: 12px;
        width: 14px;
        height: 14px;
        color: var(--muted-2);
        pointer-events: none;
    }

    .roles-input,
    .roles-select {
        min-height: 40px;
        border: 1px solid #383b46;
        border-radius: var(--radius-sm);
        background: #111319;
        color: #f4f5f8;
        font: inherit;
        font-size: 12px;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .roles-input {
        width: 245px;
        padding: 9px 12px 9px 34px;
    }

    .roles-select {
        min-width: 145px;
        padding: 8px 30px 8px 12px;
        appearance: none;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237c8191' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");
        background-repeat: no-repeat;
        background-position: right 10px center;
    }

    .roles-input::placeholder {
        color: var(--muted-2);
    }

    .roles-input:hover,
    .roles-select:hover {
        border-color: #474b58;
    }

    .roles-input:focus,
    .roles-select:focus {
        border-color: #ef6068;
        background: #14161c;
        outline: 2px solid rgba(239, 96, 104, .18);
        outline-offset: 1px;
    }

    /* ===================== TABLE ===================== */
    .roles-table-wrap {
        overflow-x: auto;
    }

    .roles-table {
        width: 100%;
        min-width: 720px;
        border-collapse: collapse;
    }

    .roles-table th {
        padding: 13px 22px;
        background: var(--surface-3);
        color: #9ba1af;
        text-align: left;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 1.1px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .roles-table th:first-child {
        padding-left: 22px;
    }

    .roles-table td {
        padding: 18px 22px;
        border-top: 1px solid var(--border-soft);
        color: #e8eaf0;
        font-size: 12px;
        vertical-align: middle;
    }

    .roles-table tbody tr {
        transition: background .2s ease;
    }

    .roles-table tbody tr:hover {
        background: rgba(255, 255, 255, .025);
    }

    .roles-table tbody tr[hidden] {
        display: none;
    }

    .role-cell {
        display: flex;
        align-items: center;
        gap: 13px;
    }

    .role-avatar {
        display: grid;
        flex-shrink: 0;
        place-items: center;
        width: 38px;
        height: 38px;
        border: 1px solid var(--border);
        border-radius: 11px;
        background: linear-gradient(180deg, #24262f, #1a1c22);
        color: #d6dae4;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: .5px;
    }

    .role-avatar.is-admin {
        border-color: rgba(227, 27, 35, .35);
        background: linear-gradient(180deg, rgba(227, 27, 35, .18), rgba(227, 27, 35, .06));
        color: #ff8b90;
    }

    .role-name {
        display: block;
        font-size: 13px;
        font-weight: 750;
        letter-spacing: -.1px;
    }

    .role-description {
        display: block;
        max-width: 380px;
        margin-top: 4px;
        color: var(--muted);
        font-size: 11.5px;
        line-height: 1.5;
    }

    .role-user-count {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border: 1px solid var(--border);
        border-radius: 20px;
        background: var(--surface-2);
        font-size: 11px;
        font-weight: 700;
        color: #dfe2ea;
    }

    .role-user-count svg {
        width: 12px;
        height: 12px;
        color: var(--muted-2);
    }

    .role-user-count.is-zero {
        color: var(--muted-2);
        background: transparent;
        border-color: var(--border-soft);
    }

    .role-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 11px;
        border: 1px solid transparent;
        border-radius: 20px;
        font-size: 10.5px;
        font-weight: 750;
        letter-spacing: .3px;
        text-transform: uppercase;
    }

    .role-status::before {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        box-shadow: 0 0 0 3px currentColor;
        opacity: .9;
        content: "";
    }

    .role-status.active {
        border-color: rgba(74, 222, 128, .22);
        background: var(--green-soft);
        color: #70e5a5;
    }

    .role-status.inactive {
        border-color: rgba(160, 166, 180, .2);
        background: rgba(160, 166, 180, .08);
        color: #b3b8c4;
    }

    /* ===================== BUTTONS ===================== */
    .role-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-width: 108px;
        min-height: 36px;
        padding: 7px 13px;
        border: 1px solid #444854;
        border-radius: var(--radius-sm);
        background: #272a33;
        color: #f4f5f8;
        font: inherit;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .2px;
        cursor: pointer;
        transition: background .2s ease, border-color .2s ease, transform .15s ease, box-shadow .2s ease;
    }

    .role-action svg {
        width: 13px;
        height: 13px;
    }

    .role-action:hover {
        border-color: var(--red);
        background: var(--red);
        box-shadow: 0 6px 18px var(--red-glow);
    }

    .role-action:active {
        transform: translateY(1px);
    }

    .role-action.activate {
        border-color: rgba(74, 222, 128, .35);
        background: var(--green-soft);
        color: #8de8b4;
    }

    .role-action.activate:hover {
        border-color: #167a48;
        background: #167a48;
        color: #fff;
        box-shadow: 0 6px 18px rgba(22, 122, 72, .4);
    }

    .role-action:focus-visible {
        outline: 2px solid #ef6068;
        outline-offset: 2px;
    }

    .role-protected {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 11px;
        border: 1px solid rgba(227, 27, 35, .3);
        border-radius: 20px;
        background: var(--red-soft);
        color: #ffb1b5;
        font-size: 10.5px;
        font-weight: 750;
        letter-spacing: .3px;
        text-transform: uppercase;
    }

    .role-protected svg {
        width: 12px;
        height: 12px;
    }

    /* ===================== ALERTS ===================== */
    .roles-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 16px;
        padding: 13px 15px;
        border: 1px solid;
        border-radius: var(--radius-sm);
        font-size: 12px;
        line-height: 1.6;
    }

    .roles-alert svg {
        flex-shrink: 0;
        width: 16px;
        height: 16px;
        margin-top: 1px;
    }

    .roles-alert.success {
        border-color: rgba(74, 222, 128, .25);
        background: rgba(74, 222, 128, .07);
        color: #85e9b3;
    }

    .roles-alert.error {
        border-color: rgba(227, 27, 35, .3);
        background: rgba(227, 27, 35, .08);
        color: #ff9ea5;
    }

    /* ===================== EMPTY / FOOTER ===================== */
    .roles-empty {
        padding: 45px 18px !important;
        color: var(--muted) !important;
        text-align: center;
        font-size: 12.5px;
    }

    .roles-empty svg {
        display: block;
        width: 28px;
        height: 28px;
        margin: 0 auto 10px;
        color: var(--muted-2);
    }

    .roles-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 22px;
        border-top: 1px solid var(--border);
        background: var(--surface-3);
        color: var(--muted);
        font-size: 11px;
    }

    .roles-footer .roles-footer-dot {
        display: inline-block;
        width: 5px;
        height: 5px;
        margin-right: 8px;
        border-radius: 50%;
        background: var(--red);
        vertical-align: middle;
        box-shadow: 0 0 8px var(--red-glow);
    }

    .roles-page [hidden] {
        display: none !important;
    }

    /* ===================== DIALOG ===================== */
    .role-confirm-dialog {
        width: min(470px, calc(100% - 28px));
        margin: auto;
        padding: 0;
        overflow: hidden;
        border: 1px solid #393c47;
        border-radius: 18px;
        background: #191a20;
        color: #f4f5f8;
        box-shadow: 0 25px 80px rgba(0, 0, 0, .6);
    }

    .role-confirm-dialog::backdrop {
        background: rgba(5, 6, 10, .78);
        backdrop-filter: blur(5px);
    }

    .role-confirm-dialog[open] {
        animation: dialog-pop .22s ease-out;
    }

    @keyframes dialog-pop {
        from { opacity: 0; transform: translateY(8px) scale(.98); }
        to   { opacity: 1; transform: translateY(0)   scale(1); }
    }

    .confirm-dialog-content {
        padding: 26px;
    }

    .confirm-icon {
        display: grid;
        place-items: center;
        width: 46px;
        height: 46px;
        margin-bottom: 18px;
        border: 1px solid rgba(245, 158, 11, .3);
        border-radius: 13px;
        background: var(--amber-soft);
        color: #ffc46b;
        font-size: 22px;
        font-weight: 800;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, .06);
    }

    .confirm-dialog-content h2 {
        margin: 0 0 10px;
        font-size: 19px;
        font-weight: 800;
        letter-spacing: -.3px;
        line-height: 1.35;
    }

    .confirm-dialog-content h2 span {
        color: #ff9ea5;
    }

    .confirm-dialog-content p {
        margin: 0;
        color: #b6bac5;
        font-size: 13px;
        line-height: 1.7;
    }

    .confirm-impact {
        display: flex;
        gap: 10px;
        margin-top: 16px;
        padding: 13px 14px;
        border: 1px solid rgba(227, 27, 35, .25);
        border-radius: var(--radius-sm);
        background: rgba(227, 27, 35, .08);
        color: #ffb6bb;
        font-size: 12px;
        line-height: 1.6;
    }

    .confirm-impact svg {
        flex-shrink: 0;
        width: 15px;
        height: 15px;
        margin-top: 2px;
        color: #ff8b90;
    }

    .confirm-impact strong {
        color: #fff;
    }

    .confirm-dialog-actions {
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        margin-top: 24px;
    }

    .confirm-dialog-actions button {
        min-height: 40px;
        padding: 9px 15px;
        border: 1px solid #41444f;
        border-radius: var(--radius-sm);
        background: #262832;
        color: #f4f5f8;
        font: inherit;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: background .2s ease, border-color .2s ease, box-shadow .2s ease, transform .15s ease;
    }

    .confirm-dialog-actions button:hover {
        border-color: #555966;
        background: #2d303a;
    }

    .confirm-dialog-actions button:active {
        transform: translateY(1px);
    }

    .confirm-dialog-actions button:focus-visible {
        outline: 2px solid #ef6068;
        outline-offset: 2px;
    }

    .confirm-dialog-actions .confirm-deactivate {
        border-color: var(--red);
        background: var(--red);
        box-shadow: 0 6px 18px var(--red-glow);
    }

    .confirm-dialog-actions .confirm-deactivate:hover {
        background: #c9161e;
        border-color: #c9161e;
    }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 860px) {
        .roles-summary {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .summary-card {
            padding: 16px;
        }

        .summary-card strong {
            font-size: 24px;
        }

        .summary-card .summary-icon {
            width: 30px;
            height: 30px;
            margin-bottom: 10px;
        }
    }

    @media (max-width: 760px) {
        .roles-summary {
            grid-template-columns: 1fr;
        }

        .roles-toolbar {
            align-items: flex-start;
            flex-direction: column;
        }

        .roles-filters {
            width: 100%;
            flex-direction: column;
        }

        .roles-input,
        .roles-select {
            width: 100%;
        }

        .roles-input-wrap {
            width: 100%;
        }

        .confirm-dialog-actions {
            flex-direction: column-reverse;
        }

        .confirm-dialog-actions button {
            width: 100%;
        }

        .roles-footer {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
@php
    $totalRoles = $roles->count();
    $rolesActivos = $roles->where('estado', true)->count();
    $rolesInactivos = $totalRoles - $rolesActivos;
@endphp

<div class="roles-page">
    <header class="roles-header">
        <span class="roles-eyebrow">Seguridad del sistema</span>
        <h1>Roles y accesos</h1>
        <p>Activa o desactiva los perfiles habilitados para ingresar a NANOS.</p>
    </header>

    @if (session('success'))
        <div class="roles-alert success" role="status">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="roles-alert error" role="alert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <section class="roles-summary" aria-label="Resumen de roles">
        <article class="summary-card" data-tone="total">
            <div class="summary-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <span class="summary-label">Roles registrados</span>
            <strong>{{ $totalRoles }}</strong>
        </article>

        <article class="summary-card" data-tone="active">
            <div class="summary-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <span class="summary-label">Roles activos</span>
            <strong>{{ $rolesActivos }}</strong>
        </article>

        <article class="summary-card" data-tone="inactive">
            <div class="summary-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                </svg>
            </div>
            <span class="summary-label">Roles inactivos</span>
            <strong>{{ $rolesInactivos }}</strong>
        </article>
    </section>

    <section class="roles-panel">
        <div class="roles-toolbar">
            <div>
                <h2>Perfiles del sistema</h2>
                <p>Orden: Administrador, Cajero y Almacén.</p>
            </div>

            <div class="roles-filters">
                <div class="roles-input-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input
                        type="search"
                        id="roles-search"
                        class="roles-input"
                        placeholder="Buscar rol..."
                        aria-label="Buscar rol"
                        autocomplete="off"
                    >
                </div>

                <select
                    id="roles-state"
                    class="roles-select"
                    aria-label="Filtrar por estado"
                >
                    <option value="">Todos los estados</option>
                    <option value="activo">Activos</option>
                    <option value="inactivo">Inactivos</option>
                </select>
            </div>
        </div>

        <div class="roles-table-wrap">
            <table class="roles-table">
                <thead>
                    <tr>
                        <th scope="col">Rol</th>
                        <th scope="col">Usuarios activos</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($roles as $rol)
                        @php
                            $estaActivo = (bool) $rol->estado;
                            $esAdministrador = $rol->esAdministrador();
                            $usuariosActivos = (int) ($rol->usuarios_activos_count ?? 0);
                            $iniciales = \Illuminate\Support\Str::upper(
                                \Illuminate\Support\Str::substr(preg_replace('/\s+/', '', $rol->nombre), 0, 2)
                            );
                        @endphp

                        <tr
                            data-role-row
                            data-state="{{ $estaActivo ? 'activo' : 'inactivo' }}"
                            data-search="{{ \Illuminate\Support\Str::lower($rol->nombre . ' ' . ($rol->descripcion ?? '')) }}"
                        >
                            <td>
                                <div class="role-cell">
                                    <div class="role-avatar {{ $esAdministrador ? 'is-admin' : '' }}" aria-hidden="true">
                                        {{ $iniciales }}
                                    </div>
                                    <div>
                                        <span class="role-name">{{ $rol->nombre }}</span>
                                        <span class="role-description">
                                            {{ $rol->descripcion ?: 'Perfil de acceso al sistema.' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="role-user-count {{ $usuariosActivos === 0 ? 'is-zero' : '' }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    {{ $usuariosActivos }}
                                </span>
                            </td>

                            <td>
                                <span class="role-status {{ $estaActivo ? 'active' : 'inactive' }}">
                                    {{ $estaActivo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>

                            <td>
                                @if ($esAdministrador && $estaActivo)
                                    <span class="role-protected">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                        </svg>
                                        Siempre activo
                                    </span>
                                @else
                                    <form
                                        action="{{ route('admin.roles.estado', $rol) }}"
                                        method="POST"
                                        data-role-form
                                        data-operation="{{ $estaActivo ? 'desactivar' : 'activar' }}"
                                        data-role-name="{{ $rol->nombre }}"
                                        data-user-count="{{ $usuariosActivos }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="role-action {{ !$estaActivo ? 'activate' : '' }}"
                                        >
                                            @if ($estaActivo)
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                                                </svg>
                                                Desactivar
                                            @else
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                                </svg>
                                                Activar
                                            @endif
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="roles-empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                </svg>
                                No hay roles registrados.
                            </td>
                        </tr>
                    @endforelse

                    <tr id="roles-no-results" hidden>
                        <td colspan="4" class="roles-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            No hay roles que coincidan con la búsqueda.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <footer class="roles-footer" id="roles-results" aria-live="polite">
            <span>
                <span class="roles-footer-dot" aria-hidden="true"></span>
                {{ $totalRoles }} roles registrados
            </span>
        </footer>
    </section>
</div>

<dialog class="role-confirm-dialog" id="deactivate-dialog" aria-labelledby="confirm-dialog-title">
    <div class="confirm-dialog-content">
        <div class="confirm-icon" aria-hidden="true">!</div>

        <span class="roles-eyebrow">Confirmar desactivación</span>
        <h2 id="confirm-dialog-title">
            ¿Desactivar el rol <span id="confirm-role-name"></span>?
        </h2>

        <p>
            Al desactivar este rol, las cuentas que lo tienen asignado
            <strong>no podrán iniciar sesión</strong> mientras permanezca inactivo.
        </p>

        <div class="confirm-impact">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            <span>
                Hay <strong id="confirm-user-count">0</strong> usuarios activos con este rol.
                Para devolverles el acceso, reactiva el rol o asígnales otro rol activo.
            </span>
        </div>

        <div class="confirm-dialog-actions">
            <button type="button" id="cancel-deactivation">Cancelar</button>
            <button type="button" class="confirm-deactivate" id="confirm-deactivation">
                Sí, desactivar
            </button>
        </div>
    </div>
</dialog>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    /* ============================================================
     * Filtros de roles (búsqueda + estado)
     * ============================================================ */
    const searchInput = document.getElementById('roles-search');
    const stateFilter = document.getElementById('roles-state');
    const rows = Array.from(document.querySelectorAll('[data-role-row]'));
    const noResultsRow = document.getElementById('roles-no-results');
    const resultsFooter = document.getElementById('roles-results');

    const normalize = (value) => String(value ?? '')
        .toLocaleLowerCase('es')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');

    function filterRoles() {
        if (!searchInput || !stateFilter || !noResultsRow || !resultsFooter) {
            return;
        }

        const term = normalize(searchInput.value.trim());
        const state = stateFilter.value;
        let visible = 0;

        rows.forEach((row) => {
            const matchesText = normalize(row.dataset.search).includes(term);
            const matchesState = !state || row.dataset.state === state;
            const shouldShow = matchesText && matchesState;

            row.hidden = !shouldShow;

            if (shouldShow) {
                visible++;
            }
        });

        noResultsRow.hidden = visible !== 0;

        const dot = resultsFooter.querySelector('.roles-footer-dot');
        const textNode = resultsFooter.querySelector('span > span:not(.roles-footer-dot)') || resultsFooter;

        let label;
        if (rows.length === 0) {
            label = 'Sin roles registrados';
        } else if (visible === rows.length && !term && !state) {
            label = rows.length + ' roles registrados';
        } else {
            label = visible + ' de ' + rows.length + ' roles visibles';
        }

        // Reconstruir el contenido manteniendo el dot
        if (dot) {
            resultsFooter.innerHTML = '';
            resultsFooter.appendChild(dot.cloneNode(true));
            resultsFooter.appendChild(document.createTextNode(' ' + label));
        } else {
            resultsFooter.textContent = label;
        }
    }

    searchInput?.addEventListener('input', filterRoles);
    stateFilter?.addEventListener('change', filterRoles);
    filterRoles();

    /* ============================================================
     * Confirmación de desactivación de roles
     * ============================================================ */
    const dialog = document.getElementById('deactivate-dialog');
    const roleNameEl = document.getElementById('confirm-role-name');
    const userCountEl = document.getElementById('confirm-user-count');
    const cancelButton = document.getElementById('cancel-deactivation');
    const confirmButton = document.getElementById('confirm-deactivation');

    let pendingForm = null;
    let lastFocusedElement = null;

    if (!dialog || !roleNameEl || !userCountEl || !cancelButton || !confirmButton) {
        return;
    }

    function closeDialog() {
        pendingForm = null;
        dialog.close();
        lastFocusedElement?.focus();
        lastFocusedElement = null;
    }

    document.querySelectorAll('[data-role-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'yes') {
                form.dataset.confirmed = 'no';
                return;
            }

            if (form.dataset.operation !== 'desactivar') {
                return;
            }

            event.preventDefault();

            pendingForm = form;
            lastFocusedElement = document.activeElement;

            roleNameEl.textContent = form.dataset.roleName || '';
            userCountEl.textContent = form.dataset.userCount || '0';

            dialog.showModal();
        });
    });

    cancelButton.addEventListener('click', closeDialog);

    dialog.addEventListener('cancel', (event) => {
        event.preventDefault();
        closeDialog();
    });

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            closeDialog();
        }
    });

    confirmButton.addEventListener('click', () => {
        if (!pendingForm) {
            closeDialog();
            return;
        }

        const formToSubmit = pendingForm;
        pendingForm = null;

        formToSubmit.dataset.confirmed = 'yes';
        dialog.close();
        formToSubmit.requestSubmit();
    });
});
</script>
@endpush