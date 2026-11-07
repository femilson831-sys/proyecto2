<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial {{ $empleado->codigo_empleado }}</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 24px;
            background: #eceff1;
            color: #202124;
            font-family: Arial, Helvetica, sans-serif;
        }

        .toolbar {
            max-width: 1200px;
            margin: 0 auto 14px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .btn {
            padding: 10px 16px;
            border: 0;
            border-radius: 7px;
            background: #202124;
            color: #fff;
            cursor: pointer;
            font-size: 12px;
            font-weight: 700;
        }

        .btn-red { background: #b5121b; }

        .report {
            max-width: 1200px;
            margin: auto;
            padding: 30px;
            background: #fff;
            border-top: 7px solid #b5121b;
            box-shadow: 0 5px 25px rgba(0,0,0,.08);
        }

        .header {
            padding-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            border-bottom: 2px solid #202124;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .logo {
            width: 54px;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #b5121b;
            color: #fff;
            border-radius: 8px;
            font-size: 26px;
            font-weight: 900;
        }

        .brand h1 {
            margin: 0;
            color: #b5121b;
            font-size: 24px;
        }

        .brand p {
            margin: 4px 0 0;
            color: #777;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .report-type { text-align: right; }
        .report-type strong { display: block; font-size: 15px; }
        .report-type span { color: #777; font-size: 9px; }

        .employee-title {
            margin: 20px 0 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .employee-title h2 {
            margin: 0;
            font-size: 18px;
        }

        .employee-code {
            padding: 6px 10px;
            background: #b5121b;
            color: #fff;
            border-radius: 5px;
            font-size: 10px;
            font-weight: 700;
        }

        .employee-grid {
            margin-bottom: 22px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            border: 1px solid #ddd;
        }

        .info {
            min-height: 64px;
            padding: 11px;
            border-right: 1px solid #ddd;
        }

        .info:last-child { border-right: 0; }

        .label {
            display: block;
            margin-bottom: 6px;
            color: #888;
            font-size: 7px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .value {
            color: #222;
            font-size: 10px;
            font-weight: 700;
        }

        .section-title {
            margin: 0 0 10px;
            padding-left: 9px;
            border-left: 4px solid #b5121b;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }

        th {
            padding: 9px 7px;
            background: #202124;
            color: #fff;
            border: 1px solid #202124;
            text-align: left;
            font-size: 7px;
            text-transform: uppercase;
        }

        td {
            padding: 8px 7px;
            border: 1px solid #ddd;
            vertical-align: top;
            word-break: break-word;
        }

        tbody tr:nth-child(even) { background: #f7f7f7; }

        .action {
            display: inline-block;
            padding: 4px 6px;
            border-radius: 4px;
            background: #eee;
            font-size: 7px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .action.creado { color: #2563eb; }
        .action.editado { color: #7c3aed; }
        .action.activado { color: #059669; }
        .action.desactivado { color: #d97706; }
        .action.eliminado { color: #dc2626; }
        .action.restaurado { color: #0891b2; }

        .user { font-weight: 700; }
        .username { margin-top: 2px; color: #777; font-size: 7px; }

        .footer {
            margin-top: 25px;
            padding-top: 12px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
            border-top: 1px solid #ddd;
            color: #888;
            font-size: 7px;
        }

        .empty {
            padding: 30px;
            border: 1px solid #ddd;
            text-align: center;
            color: #777;
            font-size: 10px;
        }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        @media print {
            body {
                padding: 0;
                background: #fff;
            }

            .toolbar { display: none; }

            .report {
                max-width: none;
                padding: 0;
                box-shadow: none;
                border-top-width: 5px;
            }

            tr { page-break-inside: avoid; }
        }
    </style>
</head>

<body>
@php
    $nombreCompleto = trim(($empleado->nombres ?? '') . ' ' . ($empleado->apellidos ?? ''));

    $usuarioGenerador = $generadoPor?->empleadoConEliminados;

    $nombreGenerador = $usuarioGenerador
        ? trim(($usuarioGenerador->nombres ?? '') . ' ' . ($usuarioGenerador->apellidos ?? ''))
        : ($generadoPor?->username ?? 'Sistema');
@endphp

<div class="toolbar">
    <button type="button" class="btn" onclick="window.close()">Cerrar</button>
    <button type="button" class="btn btn-red" onclick="window.print()">Guardar como PDF</button>
</div>

<div class="report">
    <div class="header">
        <div class="brand">
            <div class="logo">N</div>
            <div>
                <h1>MINISÚPER NANOS</h1>
                <p>Sistema de gestión de inventarios y ventas</p>
            </div>
        </div>

        <div class="report-type">
            <strong>HISTORIAL INDIVIDUAL</strong>
            <span>Reporte de auditoría del empleado</span>
        </div>
    </div>

    <div class="employee-title">
        <h2>{{ $nombreCompleto }}</h2>
        <span class="employee-code">{{ $empleado->codigo_empleado }}</span>
    </div>

    <div class="employee-grid">
        <div class="info">
            <span class="label">Documento CI</span>
            <div class="value">{{ $empleado->ci ?: 'No registrado' }}</div>
        </div>

        <div class="info">
            <span class="label">Fecha ingreso</span>
            <div class="value">
                {{ $empleado->fecha_ingreso?->format('d/m/Y') ?? 'No registrada' }}
            </div>
        </div>

        <div class="info">
            <span class="label">Estado actual</span>
            <div class="value">
                {{ $empleado->trashed() ? 'ELIMINADO' : strtoupper($empleado->estado ?? 'SIN ESTADO') }}
            </div>
        </div>

        <div class="info">
            <span class="label">Total eventos</span>
            <div class="value">{{ $historial->count() }}</div>
        </div>
    </div>

    <h3 class="section-title">Línea de tiempo del empleado</h3>

    @if($historial->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width:4%">N°</th>
                    <th style="width:11%">Fecha</th>
                    <th style="width:9%">Acción</th>
                    <th style="width:25%">Descripción</th>
                    <th style="width:10%">Anterior</th>
                    <th style="width:10%">Nuevo</th>
                    <th style="width:16%">Responsable</th>
                    <th style="width:8%">Usuario</th>
                    <th style="width:7%">IP</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historial as $indice => $evento)
                    @php
                        $usuario = $evento->usuario;
                        $responsableEmpleado = $usuario?->empleadoConEliminados;

                        $responsable = $responsableEmpleado
                            ? trim(
                                ($responsableEmpleado->nombres ?? '') . ' ' .
                                ($responsableEmpleado->apellidos ?? '')
                            )
                            : ($usuario?->username ?? 'Sistema');
                    @endphp

                    <tr>
                        <td>{{ $indice + 1 }}</td>
                        <td>{{ $evento->created_at?->format('d/m/Y H:i:s') ?? '—' }}</td>
                        <td>
                            <span class="action {{ strtolower($evento->accion ?? '') }}">
                                {{ $evento->accion ?: '—' }}
                            </span>
                        </td>
                        <td>{{ $evento->descripcion ?: 'Acción registrada.' }}</td>
                        <td>{{ $evento->estado_anterior ?: '—' }}</td>
                        <td>{{ $evento->estado_nuevo ?: '—' }}</td>
                        <td>
                            <div class="user">{{ $responsable }}</div>
                        </td>
                        <td>
                            <div class="username">
                                {{ $usuario?->username ? '@' . $usuario->username : 'Sistema' }}
                            </div>
                        </td>
                        <td>{{ $evento->ip ?: '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">
            Este empleado todavía no tiene eventos registrados.
        </div>
    @endif

    <div class="footer">
        <div>
            Generado por:
            <strong>{{ $nombreGenerador }}</strong>
            @if($generadoPor?->username)
                · {{ '@' . $generadoPor->username }}
            @endif
        </div>

        <div>
            {{ now()->format('d/m/Y H:i:s') }}
            · El Alto · La Paz · Bolivia
        </div>
    </div>
</div>
</body>
</html>
