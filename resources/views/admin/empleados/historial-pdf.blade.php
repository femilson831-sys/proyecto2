<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Historial de Empleados | Minisúper NANOS
    </title>

    <style>

        /* ============================================================
           CONFIGURACIÓN GENERAL
        ============================================================ */

        * {
            box-sizing: border-box;
        }

        :root {
            --red: #c9151d;
            --red-dark: #991118;
            --red-soft: #fff1f2;

            --black: #18191c;
            --dark: #24262a;

            --gray-900: #27292d;
            --gray-700: #555a61;
            --gray-500: #81868d;
            --gray-300: #d8dadd;
            --gray-200: #e8e9eb;
            --gray-100: #f5f6f7;
            --gray-50: #fafafa;

            --white: #ffffff;
        }

        html,
        body {
            margin: 0;
            padding: 0;

            background: #e9eaec;

            color: var(--gray-900);

            font-family:
                "Segoe UI",
                Arial,
                Helvetica,
                sans-serif;

            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }


        /* ============================================================
           BARRA DE ACCIONES
        ============================================================ */

        .screen-toolbar {
            width: min(1180px, calc(100% - 40px));

            margin: 20px auto 12px;

            display: flex;
            justify-content: space-between;
            align-items: center;

            gap: 15px;
        }

        .toolbar-message {
            color: #666b72;

            font-size: 12px;
        }

        .toolbar-actions {
            display: flex;

            gap: 8px;
        }

        .toolbar-button {
            min-height: 42px;

            padding: 0 17px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            gap: 7px;

            border: 0;
            border-radius: 8px;

            font-family: inherit;
            font-size: 11px;
            font-weight: 700;

            cursor: pointer;

            transition:
                transform .15s ease,
                background .15s ease;
        }

        .toolbar-button:hover {
            transform: translateY(-1px);
        }

        .toolbar-button.secondary {
            border: 1px solid #d5d7da;

            background: #fff;

            color: #4e5359;
        }

        .toolbar-button.primary {
            background: var(--red);

            color: #fff;

            box-shadow:
                0 6px 16px
                rgba(201, 21, 29, .18);
        }

        .toolbar-button.primary:hover {
            background: var(--red-dark);
        }


        /* ============================================================
           HOJA DEL REPORTE
        ============================================================ */

        .report-sheet {
            width: min(1180px, calc(100% - 40px));

            min-height: 760px;

            margin: 0 auto 35px;

            overflow: hidden;

            background: #fff;

            box-shadow:
                0 14px 45px
                rgba(0, 0, 0, .10);
        }


        /* ============================================================
           FRANJA SUPERIOR
        ============================================================ */

        .top-accent {
            height: 8px;

            display: grid;

            grid-template-columns:
                72% 28%;
        }

        .top-accent .red {
            background: var(--red);
        }

        .top-accent .dark {
            background: var(--black);
        }


        /* ============================================================
           CABECERA
        ============================================================ */

        .report-header {
            padding: 25px 30px 21px;

            display: flex;
            justify-content: space-between;
            align-items: center;

            gap: 30px;

            border-bottom:
                1px solid
                var(--gray-200);
        }

        .brand {
            display: flex;
            align-items: center;

            gap: 14px;
        }

        .brand-logo {
            width: 56px;
            height: 56px;

            display: flex;
            align-items: center;
            justify-content: center;

            flex-shrink: 0;

            border-radius: 13px;

            background:
                linear-gradient(
                    145deg,
                    var(--red),
                    #9f1118
                );

            color: #fff;

            font-size: 24px;
            font-weight: 900;

            box-shadow:
                0 7px 16px
                rgba(201, 21, 29, .18);
        }

        .brand-copy {
            min-width: 0;
        }

        .brand-copy h1 {
            margin: 0;

            color: var(--black);

            font-size: 22px;
            font-weight: 900;

            letter-spacing: -.5px;
        }

        .brand-copy p {
            margin: 4px 0 0;

            color: var(--gray-500);

            font-size: 9px;
            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .7px;
        }


        /* TÍTULO DERECHO */

        .document-heading {
            text-align: right;
        }

        .document-heading .label {
            display: block;

            margin-bottom: 5px;

            color: var(--red);

            font-size: 8px;
            font-weight: 800;

            text-transform: uppercase;

            letter-spacing: 1px;
        }

        .document-heading h2 {
            margin: 0;

            color: var(--black);

            font-size: 17px;
            font-weight: 800;
        }

        .document-heading p {
            margin: 4px 0 0;

            color: var(--gray-500);

            font-size: 9px;
        }


        /* ============================================================
           CONTENIDO
        ============================================================ */

        .report-body {
            padding: 22px 30px 25px;
        }


        /* ============================================================
           INFORMACIÓN DEL REPORTE
        ============================================================ */

        .metadata-grid {
            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 9px;

            margin-bottom: 17px;
        }

        .metadata-card {
            min-height: 61px;

            padding: 11px 13px;

            border:
                1px solid
                var(--gray-200);

            border-radius: 8px;

            background:
                var(--gray-50);
        }

        .metadata-card .label {
            display: block;

            margin-bottom: 6px;

            color: var(--gray-500);

            font-size: 7.5px;
            font-weight: 800;

            text-transform: uppercase;

            letter-spacing: .55px;
        }

        .metadata-card .value {
            display: block;

            overflow: hidden;

            color: var(--gray-900);

            font-size: 10px;
            font-weight: 700;

            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .metadata-card.highlight {
            border-color:
                rgba(201, 21, 29, .18);

            background: var(--red-soft);
        }

        .metadata-card.highlight .value {
            color: var(--red);

            font-size: 17px;
            font-weight: 900;
        }


        /* ============================================================
           RESUMEN
        ============================================================ */

        .summary-strip {
            margin-bottom: 19px;

            padding: 12px 14px;

            display: flex;
            justify-content: space-between;
            align-items: center;

            gap: 15px;

            border-left:
                4px solid
                var(--red);

            border-radius:
                0 7px 7px 0;

            background:
                linear-gradient(
                    90deg,
                    #f6f6f7,
                    #fbfbfb
                );
        }

        .summary-strip h3 {
            margin: 0 0 3px;

            color: #33363a;

            font-size: 10px;
            font-weight: 800;
        }

        .summary-strip p {
            margin: 0;

            color: var(--gray-500);

            font-size: 8px;

            line-height: 1.45;
        }

        .filter-chip {
            max-width: 280px;

            padding: 5px 9px;

            border:
                1px solid
                rgba(201, 21, 29, .13);

            border-radius: 999px;

            background: #fff;

            color: var(--red);

            font-size: 8px;
            font-weight: 700;

            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }


        /* ============================================================
           TÍTULO DE TABLA
        ============================================================ */

        .section-heading {
            margin-bottom: 8px;

            display: flex;
            justify-content: space-between;
            align-items: flex-end;

            gap: 15px;
        }

        .section-heading h3 {
            margin: 0;

            color: #34373b;

            font-size: 10px;
            font-weight: 900;

            text-transform: uppercase;

            letter-spacing: .55px;
        }

        .section-heading span {
            color: var(--gray-500);

            font-size: 8px;
        }


        /* ============================================================
           TABLA
        ============================================================ */

        .table-wrapper {
            overflow: hidden;

            border:
                1px solid
                var(--gray-200);

            border-radius: 8px;
        }

        table {
            width: 100%;

            border-collapse: collapse;

            table-layout: fixed;
        }

        thead {
            display: table-header-group;
        }

        thead th {
            padding: 9px 6px;

            border-right:
                1px solid
                rgba(255, 255, 255, .08);

            background: var(--dark);

            color: #fff;

            font-size: 7.3px;
            font-weight: 800;

            text-align: left;

            text-transform: uppercase;

            letter-spacing: .25px;

            vertical-align: middle;
        }

        thead th:last-child {
            border-right: none;
        }

        tbody td {
            padding: 9px 6px;

            border-bottom:
                1px solid
                var(--gray-200);

            color: #4a4e53;

            font-size: 8px;

            line-height: 1.35;

            vertical-align: middle;

            overflow-wrap: anywhere;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:nth-child(even) {
            background: #fafafa;
        }


        /* COLUMNAS */

        .col-number {
            width: 4%;
            text-align: center;
        }

        .col-code {
            width: 10%;
        }

        .col-employee {
            width: 20%;
        }

        .col-ci {
            width: 10%;
        }

        .col-date {
            width: 10%;
        }

        .col-deleted {
            width: 14%;
        }

        .col-responsible {
            width: 19%;
        }

        .col-status {
            width: 9%;
            text-align: center;
        }


        /* DATOS */

        .row-number {
            color: #92969b;

            font-weight: 700;

            text-align: center;
        }

        .employee-code {
            color: var(--red);

            font-weight: 800;
        }

        .employee-name {
            display: block;

            color: #292b2f;

            font-weight: 800;
        }

        .employee-secondary {
            display: block;

            margin-top: 2px;

            color: #969a9f;

            font-size: 7px;
        }

        .responsible-name {
            display: block;

            color: #303338;

            font-weight: 800;
        }

        .responsible-user {
            display: block;

            margin-top: 2px;

            color: var(--gray-500);

            font-size: 7px;
        }

        .status-badge {
            padding: 4px 6px;

            display: inline-block;

            border:
                1px solid
                #f4cdd0;

            border-radius: 999px;

            background: #fff1f2;

            color: var(--red);

            font-size: 6.8px;
            font-weight: 900;

            text-transform: uppercase;
        }


        /* SIN REGISTROS */

        .empty-state {
            padding: 35px 15px !important;

            color: #8c9095 !important;

            text-align: center;

            font-size: 9px !important;
        }


        /* ============================================================
           NOTA DE AUDITORÍA
        ============================================================ */

        .audit-note {
            margin-top: 16px;

            padding: 10px 12px;

            display: flex;
            align-items: flex-start;

            gap: 9px;

            border:
                1px solid
                #ececee;

            border-radius: 7px;

            background: #fafafa;
        }

        .audit-icon {
            width: 23px;
            height: 23px;

            display: flex;
            align-items: center;
            justify-content: center;

            flex-shrink: 0;

            border-radius: 6px;

            background: #f2f2f3;

            color: var(--red);

            font-size: 10px;
            font-weight: 900;
        }

        .audit-note strong {
            display: block;

            margin-bottom: 2px;

            color: #44474b;

            font-size: 8px;
        }

        .audit-note p {
            margin: 0;

            color: #85898e;

            font-size: 7.5px;

            line-height: 1.45;
        }


        /* ============================================================
           PIE DE PÁGINA
        ============================================================ */

        .report-footer {
            margin-top: 18px;

            padding-top: 12px;

            display: grid;

            grid-template-columns:
                1fr auto 1fr;

            align-items: center;

            gap: 15px;

            border-top:
                1px solid
                var(--gray-200);

            color: #85898e;

            font-size: 7.5px;
        }

        .footer-brand {
            color: var(--red);

            font-weight: 900;
        }

        .footer-center {
            text-align: center;
        }

        .footer-right {
            text-align: right;
        }


        /* ============================================================
           IMPRESIÓN
        ============================================================ */

        @media print {

            html,
            body {
                background: #fff;
            }

            .screen-toolbar {
                display: none !important;
            }

            .report-sheet {
                width: 100%;

                min-height: auto;

                margin: 0;

                box-shadow: none;
            }

            .report-header {
                padding:
                    12px 16px 10px;
            }

            .report-body {
                padding:
                    12px 16px 14px;
            }

            .brand-logo {
                width: 45px;
                height: 45px;

                font-size: 19px;
            }

            .brand-copy h1 {
                font-size: 18px;
            }

            .document-heading h2 {
                font-size: 14px;
            }

            .metadata-card {
                min-height: 52px;

                padding: 8px 10px;
            }

            .metadata-grid {
                margin-bottom: 10px;
            }

            .summary-strip {
                margin-bottom: 12px;

                padding: 9px 11px;
            }

            thead th {
                padding: 7px 5px;

                font-size: 6.8px;
            }

            tbody td {
                padding: 7px 5px;

                font-size: 7.4px;
            }

            tr {
                page-break-inside: avoid;
            }

            .audit-note {
                page-break-inside: avoid;
            }

            .report-footer {
                page-break-inside: avoid;
            }

            @page {
                size: A4 landscape;

                margin: 7mm;
            }
        }


        /* ============================================================
           RESPONSIVE
        ============================================================ */

        @media screen and (max-width: 850px) {

            .screen-toolbar,
            .report-sheet {
                width:
                    calc(100% - 20px);
            }

            .report-header {
                flex-direction: column;

                align-items: flex-start;
            }

            .document-heading {
                text-align: left;
            }

            .metadata-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .report-body {
                overflow-x: auto;
            }

            table {
                min-width: 900px;
            }
        }

    </style>

</head>


<body>

@php

    /*
    |--------------------------------------------------------------------------
    | USUARIO QUE GENERA EL REPORTE
    |--------------------------------------------------------------------------
    */

    $nombreGenerador =
        $generadoPor->username
        ?? 'Sistema';


    if (
        $generadoPor &&
        $generadoPor->empleado
    ) {

        $nombreGenerador =
            trim(
                $generadoPor
                    ->empleado
                    ->nombres
                .
                ' '
                .
                $generadoPor
                    ->empleado
                    ->apellidos
            );
    }


    $usuarioGenerador =
        $generadoPor->username
        ?? 'Sistema';


    $totalRegistros =
        $empleados->count();


    $fechaGeneracion =
        now()->format(
            'd/m/Y'
        );


    $horaGeneracion =
        now()->format(
            'H:i:s'
        );

@endphp


{{-- ============================================================
     BOTONES VISIBLES SOLO EN PANTALLA
============================================================ --}}

<div class="screen-toolbar">

    <div class="toolbar-message">

        Vista previa del reporte antes de
        guardarlo como PDF.

    </div>


    <div class="toolbar-actions">

        <button
            type="button"
            class="toolbar-button secondary"
            onclick="window.close()"
        >
            ✕ Cerrar
        </button>


        <button
            type="button"
            class="toolbar-button primary"
            onclick="window.print()"
        >
            ↓ Guardar como PDF
        </button>

    </div>

</div>


{{-- ============================================================
     DOCUMENTO
============================================================ --}}

<main class="report-sheet">


    {{-- FRANJA SUPERIOR --}}

    <div class="top-accent">

        <div class="red"></div>

        <div class="dark"></div>

    </div>


    {{-- ========================================================
         CABECERA
    ========================================================= --}}

    <header class="report-header">


        <div class="brand">

            <div class="brand-logo">
                N
            </div>


            <div class="brand-copy">

                <h1>
                    MINISÚPER NANOS
                </h1>

                <p>
                    Sistema Web de Gestión
                    de Inventarios y Ventas
                </p>

            </div>

        </div>


        <div class="document-heading">

            <span class="label">
                Reporte administrativo
            </span>

            <h2>
                Historial de Empleados
            </h2>

            <p>
                Registro de empleados eliminados
            </p>

        </div>

    </header>


    {{-- ========================================================
         CONTENIDO
    ========================================================= --}}

    <section class="report-body">


        {{-- ====================================================
             DATOS DEL REPORTE
        ===================================================== --}}

        <div class="metadata-grid">


            <div class="metadata-card">

                <span class="label">
                    Fecha y hora
                </span>

                <span class="value">

                    {{ $fechaGeneracion }}

                    ·

                    {{ $horaGeneracion }}

                </span>

            </div>


            <div class="metadata-card">

                <span class="label">
                    Generado por
                </span>

                <span class="value">

                    {{ $nombreGenerador }}

                </span>

            </div>


            <div class="metadata-card">

                <span class="label">
                    Usuario responsable
                </span>

                <span class="value">

                    {{ '@' . $usuarioGenerador }}

                </span>

            </div>


            <div class="metadata-card highlight">

                <span class="label">
                    Registros encontrados
                </span>

                <span class="value">

                    {{ $totalRegistros }}

                </span>

            </div>

        </div>


        {{-- ====================================================
             RESUMEN
        ===================================================== --}}

        <div class="summary-strip">

            <div>

                <h3>
                    Registro histórico de empleados
                </h3>

                <p>

                    El reporte muestra los empleados
                    que actualmente se encuentran
                    eliminados del sistema mediante
                    eliminación lógica.

                </p>

            </div>


            @if($buscar)

                <div class="filter-chip">

                    Filtro:
                    {{ $buscar }}

                </div>

            @else

                <div class="filter-chip">

                    Todos los registros

                </div>

            @endif

        </div>


        {{-- ====================================================
             CABECERA DE TABLA
        ===================================================== --}}

        <div class="section-heading">

            <h3>
                Detalle del historial
            </h3>

            <span>

                Total:
                {{ $totalRegistros }}

                {{ $totalRegistros === 1
                    ? 'registro'
                    : 'registros'
                }}

            </span>

        </div>


        {{-- ====================================================
             TABLA
        ===================================================== --}}

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th class="col-number">
                            N°
                        </th>

                        <th class="col-code">
                            Código
                        </th>

                        <th class="col-employee">
                            Empleado
                        </th>

                        <th class="col-ci">
                            CI
                        </th>

                        <th class="col-date">
                            Ingreso
                        </th>

                        <th class="col-date">
                            Salida
                        </th>

                        <th class="col-deleted">
                            Eliminación
                        </th>

                        <th class="col-responsible">
                            Eliminado por
                        </th>

                        <th class="col-status">
                            Estado
                        </th>

                    </tr>

                </thead>


                <tbody>

                @forelse(
                    $empleados
                    as $empleado
                )

                    @php

                        /*
                        |--------------------------------------------------------------------------
                        | RESPONSABLE DE ELIMINACIÓN
                        |--------------------------------------------------------------------------
                        */

                        $usuario =
                            $empleado
                                ->eliminadoPor;


                        $responsable =
                            'Sin registro';


                        $usernameResponsable =
                            null;


                        if ($usuario) {

                            $usernameResponsable =
                                $usuario
                                    ->username;


                            if (
                                $usuario->empleado
                            ) {

                                $responsable =
                                    trim(
                                        $usuario
                                            ->empleado
                                            ->nombres
                                        .
                                        ' '
                                        .
                                        $usuario
                                            ->empleado
                                            ->apellidos
                                    );

                            } else {

                                $responsable =
                                    $usuario
                                        ->username;

                            }
                        }

                    @endphp


                    <tr>


                        {{-- NÚMERO --}}

                        <td class="row-number">

                            {{ $loop->iteration }}

                        </td>


                        {{-- CÓDIGO --}}

                        <td>

                            <span class="employee-code">

                                {{
                                    $empleado
                                        ->codigo_empleado
                                }}

                            </span>

                        </td>


                        {{-- EMPLEADO --}}

                        <td>

                            <span class="employee-name">

                                {{
                                    $empleado
                                        ->nombres
                                }}

                                {{
                                    $empleado
                                        ->apellidos
                                }}

                            </span>


                            @if($empleado->correo)

                                <span class="employee-secondary">

                                    {{
                                        $empleado
                                            ->correo
                                    }}

                                </span>

                            @endif

                        </td>


                        {{-- CI --}}

                        <td>

                            {{
                                $empleado->ci
                                    ?: '—'
                            }}

                        </td>


                        {{-- INGRESO --}}

                        <td>

                            {{
                                $empleado
                                    ->fecha_ingreso
                                    ?->format(
                                        'd/m/Y'
                                    )
                                ?? '—'
                            }}

                        </td>


                        {{-- SALIDA --}}

                        <td>

                            {{
                                $empleado
                                    ->fecha_salida
                                    ?->format(
                                        'd/m/Y'
                                    )
                                ?? '—'
                            }}

                        </td>


                        {{-- FECHA DE ELIMINACIÓN --}}

                        <td>

                            @if($empleado->deleted_at)

                                <strong>

                                    {{
                                        $empleado
                                            ->deleted_at
                                            ->format(
                                                'd/m/Y'
                                            )
                                    }}

                                </strong>

                                <span class="employee-secondary">

                                    {{
                                        $empleado
                                            ->deleted_at
                                            ->format(
                                                'H:i:s'
                                            )
                                    }}

                                </span>

                            @else

                                —

                            @endif

                        </td>


                        {{-- ELIMINADO POR --}}

                        <td>

                            <span class="responsible-name">

                                {{ $responsable }}

                            </span>


                            @if($usernameResponsable)

                                <span class="responsible-user">

                                    {{
                                        '@' .
                                        $usernameResponsable
                                    }}

                                </span>

                            @else

                                <span class="responsible-user">

                                    Usuario no registrado

                                </span>

                            @endif

                        </td>


                        {{-- ESTADO --}}

                        <td
                            style="
                                text-align:center;
                            "
                        >

                            <span class="status-badge">

                                Eliminado

                            </span>

                        </td>

                    </tr>


                @empty


                    <tr>

                        <td
                            colspan="9"
                            class="empty-state"
                        >

                            No existen empleados
                            eliminados para mostrar
                            en este reporte.

                        </td>

                    </tr>


                @endforelse

                </tbody>

            </table>

        </div>


        {{-- ====================================================
             NOTA
        ===================================================== --}}

        <div class="audit-note">

            <div class="audit-icon">
                i
            </div>


            <div>

                <strong>
                    Información del historial
                </strong>

                <p>

                    Los registros mostrados permanecen
                    almacenados en la base de datos y
                    pueden ser restaurados desde el
                    módulo Historial de Empleados.

                </p>

            </div>

        </div>


        {{-- ====================================================
             PIE DEL DOCUMENTO
        ===================================================== --}}

        <footer class="report-footer">


            <div>

                <span class="footer-brand">
                    NANOS
                </span>

                &nbsp;|&nbsp;

                Gestión de Inventarios y Ventas

            </div>


            <div class="footer-center">

                El Alto · La Paz · Bolivia

            </div>


            <div class="footer-right">

                Generado:
                {{ $fechaGeneracion }}

                ·

                {{ $horaGeneracion }}

            </div>


        </footer>

    </section>

</main>


</body>

</html>