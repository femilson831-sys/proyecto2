@extends('admin.layouts.app')



@section('title', 'Historial individual del empleado')



@section('content')



@php

    $nombreCompleto = trim(

        $empleado->nombres . ' ' . $empleado->apellidos

    );



    $estaEliminado = !is_null($empleado->deleted_at);

@endphp



<style>

    .history-page {

        max-width: 1250px;

        margin: 0 auto;

    }



    .history-header {

        margin-bottom: 22px;

        padding: 24px;



        display: flex;

        justify-content: space-between;

        align-items: center;

        gap: 20px;



        background: #17181b;

        border: 1px solid #292b30;

        border-radius: 16px;

    }



    .history-header-left {

        display: flex;

        align-items: center;

        gap: 15px;

    }



    .employee-avatar {

        width: 58px;

        height: 58px;



        display: flex;

        align-items: center;

        justify-content: center;



        flex-shrink: 0;



        border-radius: 14px;



        background:

            linear-gradient(

                135deg,

                #d11922,

                #8f0f15

            );



        color: #fff;



        font-size: 23px;

        font-weight: 900;

    }



    .history-header h1 {

        margin: 0;



        color: #fff;



        font-size: 22px;

        font-weight: 800;

    }



    .history-header p {

        margin: 5px 0 0;



        color: #8d9199;



        font-size: 12px;

    }



    .header-actions {

        display: flex;

        flex-wrap: wrap;

        gap: 8px;

    }



    .history-btn {

        min-height: 40px;

        padding: 0 14px;



        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 7px;



        border: 1px solid #35373c;

        border-radius: 9px;



        background: #24262a;

        color: #fff;



        text-decoration: none;



        font-size: 11px;

        font-weight: 700;



        transition: .2s;

    }



    .history-btn:hover {

        background: #303237;

        color: #fff;

    }



    .history-btn.red {

        border-color: #c9151d;

        background: #c9151d;

    }



    .history-btn.red:hover {

        background: #a91118;

    }



    .history-btn.excel {

        border-color: #217346;

        background: #217346;

    }



    .history-btn.pdf {

        border-color: #c9151d;

        background: #c9151d;

    }



    /* ==========================================================

       RESUMEN DEL EMPLEADO

    ========================================================== */



    .employee-summary {

        margin-bottom: 22px;



        display: grid;

        grid-template-columns: repeat(5, 1fr);

        gap: 10px;

    }



    .summary-card {

        min-height: 95px;



        padding: 15px;



        background: #191a1e;



        border: 1px solid #292b30;

        border-radius: 12px;

    }



    .summary-label {

        margin-bottom: 8px;



        display: block;



        color: #737780;



        font-size: 9px;

        font-weight: 800;



        letter-spacing: .5px;



        text-transform: uppercase;

    }



    .summary-value {

        color: #fff;



        font-size: 13px;

        font-weight: 700;



        word-break: break-word;

    }



    .summary-value.red {

        color: #ef4444;

    }



    .summary-value.green {

        color: #34d399;

    }



    .status-pill {

        padding: 5px 9px;



        display: inline-block;



        border-radius: 999px;



        font-size: 9px;

        font-weight: 900;



        text-transform: uppercase;

    }



    .status-pill.active {

        background: rgba(52, 211, 153, .12);

        color: #34d399;

    }



    .status-pill.inactive {

        background: rgba(245, 158, 11, .12);

        color: #f59e0b;

    }



    .status-pill.deleted {

        background: rgba(239, 68, 68, .12);

        color: #ef4444;

    }



    /* ==========================================================

       CONTENEDOR

    ========================================================== */



    .history-panel {

        padding: 22px;



        background: #17181b;



        border: 1px solid #292b30;

        border-radius: 16px;

    }



    .panel-header {

        margin-bottom: 25px;



        display: flex;

        justify-content: space-between;

        align-items: center;



        gap: 15px;

    }



    .panel-title h2 {

        margin: 0;



        color: #fff;



        font-size: 16px;

    }



    .panel-title p {

        margin: 5px 0 0;



        color: #747880;



        font-size: 11px;

    }



    .event-count {

        padding: 6px 10px;



        border: 1px solid #33353a;

        border-radius: 999px;



        background: #222328;



        color: #a8abb1;



        font-size: 10px;

        font-weight: 700;

    }



    /* ==========================================================

       TIMELINE

    ========================================================== */



    .timeline {

        position: relative;



        padding-left: 42px;

    }



    .timeline::before {

        content: "";



        position: absolute;



        top: 10px;

        bottom: 10px;

        left: 15px;



        width: 2px;



        background:

            linear-gradient(

                to bottom,

                #c9151d,

                #34363b

            );

    }



    .timeline-item {

        position: relative;



        margin-bottom: 17px;

    }



    .timeline-item:last-child {

        margin-bottom: 0;

    }



    .timeline-dot {

        position: absolute;



        top: 18px;

        left: -35px;



        width: 18px;

        height: 18px;



        border: 4px solid #17181b;

        border-radius: 50%;



        background: #777b83;



        box-shadow:

            0 0 0 2px #35373d;

    }



    .timeline-dot.creado {

        background: #3b82f6;

    }



    .timeline-dot.editado {

        background: #8b5cf6;

    }



    .timeline-dot.activado {

        background: #10b981;

    }



    .timeline-dot.desactivado {

        background: #f59e0b;

    }



    .timeline-dot.eliminado {

        background: #ef4444;

    }



    .timeline-dot.restaurado {

        background: #06b6d4;

    }



    .event-card {

        padding: 17px;



        background: #1e2024;



        border: 1px solid #2d2f34;

        border-radius: 12px;



        transition: .2s;

    }



    .event-card:hover {

        border-color: #414349;



        transform: translateY(-1px);

    }



    .event-top {

        margin-bottom: 12px;



        display: flex;

        justify-content: space-between;

        align-items: flex-start;



        gap: 15px;

    }



    .event-action {

        display: flex;

        align-items: center;

        flex-wrap: wrap;

        gap: 9px;

    }



    .event-badge {

        padding: 5px 8px;



        display: inline-block;



        border-radius: 6px;



        font-size: 8px;

        font-weight: 900;



        letter-spacing: .4px;



        text-transform: uppercase;

    }



    .event-badge.creado {

        background: rgba(59, 130, 246, .13);

        color: #60a5fa;

    }



    .event-badge.editado {

        background: rgba(139, 92, 246, .13);

        color: #a78bfa;

    }



    .event-badge.activado {

        background: rgba(16, 185, 129, .13);

        color: #34d399;

    }



    .event-badge.desactivado {

        background: rgba(245, 158, 11, .13);

        color: #fbbf24;

    }



    .event-badge.eliminado {

        background: rgba(239, 68, 68, .13);

        color: #f87171;

    }



    .event-badge.restaurado {

        background: rgba(6, 182, 212, .13);

        color: #22d3ee;

    }



    .event-id {

        color: #60646c;



        font-size: 9px;

    }



    .event-date {

        color: #8e9299;



        font-size: 10px;



        text-align: right;

    }



    .event-date strong {

        display: block;



        margin-bottom: 2px;



        color: #ddd;



        font-size: 10px;

    }



    .event-description {

        margin-bottom: 13px;



        color: #c5c7cb;



        font-size: 11px;



        line-height: 1.55;

    }



    .event-info-grid {

        display: grid;

        grid-template-columns: 1.4fr 1fr 1fr;

        gap: 9px;

    }



    .event-info {

        padding: 10px;



        background: #191a1e;



        border: 1px solid #292b30;

        border-radius: 8px;

    }



    .event-info-label {

        margin-bottom: 5px;



        display: block;



        color: #676b72;



        font-size: 7.5px;

        font-weight: 800;



        letter-spacing: .4px;



        text-transform: uppercase;

    }



    .event-info-value {

        color: #e1e2e4;



        font-size: 9.5px;

        font-weight: 600;

    }



    .event-info-value small {

        display: block;



        margin-top: 3px;



        color: #777b82;



        font-size: 8px;

        font-weight: 500;

    }



    .state-change {

        display: flex;

        align-items: center;

        gap: 6px;

    }



    .state-old,

    .state-new {

        padding: 3px 6px;



        border-radius: 5px;



        font-size: 8px;

        font-weight: 800;



        text-transform: uppercase;

    }



    .state-old {

        background: #292b30;

        color: #999da4;

    }



    .state-new {

        background: rgba(201, 21, 29, .12);

        color: #ef4444;

    }



    .state-arrow {

        color: #666a71;

    }



    /* ==========================================================

       CAMBIOS DE EDICIÓN

    ========================================================== */



    .changes-box {

        margin-top: 12px;



        padding-top: 12px;



        border-top: 1px solid #303238;

    }



    .changes-title {

        margin-bottom: 8px;



        color: #8e9299;



        font-size: 8px;

        font-weight: 800;



        text-transform: uppercase;



        letter-spacing: .4px;

    }



    .changes-grid {

        display: grid;

        grid-template-columns: repeat(2, 1fr);

        gap: 7px;

    }



    .change-row {

        padding: 9px 10px;



        background: #191a1e;



        border-radius: 7px;

    }



    .change-field {

        display: block;



        margin-bottom: 5px;



        color: #999da4;



        font-size: 8px;

        font-weight: 800;



        text-transform: uppercase;

    }



    .change-values {

        display: flex;

        align-items: center;

        gap: 6px;



        color: #c8cace;



        font-size: 8px;

    }



    .old-value {

        color: #ef7777;



        text-decoration: line-through;

    }



    .new-value {

        color: #54d7a6;



        font-weight: 700;

    }



    /* ==========================================================

       VACÍO

    ========================================================== */



    .empty-history {

        padding: 50px 20px;



        text-align: center;

    }



    .empty-icon {

        width: 55px;

        height: 55px;



        margin: 0 auto 15px;



        display: flex;

        align-items: center;

        justify-content: center;



        border-radius: 14px;



        background: #23252a;



        color: #777b83;



        font-size: 22px;

    }



    .empty-history h3 {

        margin: 0 0 6px;



        color: #ddd;



        font-size: 14px;

    }



    .empty-history p {

        margin: 0;



        color: #747880;



        font-size: 10px;

    }



    /* ==========================================================

       PAGINACIÓN

    ========================================================== */



    .history-pagination {

        margin-top: 20px;

    }



    /* ==========================================================

       RESPONSIVE

    ========================================================== */



    @media (max-width: 1050px) {

        .employee-summary {

            grid-template-columns:

                repeat(3, 1fr);

        }

    }



    @media (max-width: 750px) {

        .history-header {

            flex-direction: column;

            align-items: flex-start;

        }



        .employee-summary {

            grid-template-columns:

                repeat(2, 1fr);

        }



        .event-info-grid {

            grid-template-columns: 1fr;

        }



        .changes-grid {

            grid-template-columns: 1fr;

        }

    }



    @media (max-width: 480px) {

        .employee-summary {

            grid-template-columns: 1fr;

        }



        .timeline {

            padding-left: 30px;

        }



        .timeline::before {

            left: 8px;

        }



        .timeline-dot {

            left: -30px;

        }

    }

</style>





<div class="history-page">



    {{-- =========================================================

         CABECERA

    ========================================================== --}}



    <div class="history-header">



        <div class="history-header-left">



            <div class="employee-avatar">

                {{ strtoupper(substr($empleado->nombres, 0, 1)) }}

            </div>



            <div>



                <h1>

                    Historial individual

                </h1>



                <p>

                    {{ $empleado->codigo_empleado }}

                    ·

                    {{ $nombreCompleto }}

                </p>



            </div>



        </div>





        <div class="header-actions">



            <a

                href="{{ route('admin.empleados.index') }}"

                class="history-btn"

            >

                ← Empleados

            </a>





            <a

                href="{{ route('admin.empleados.historial') }}"

                class="history-btn"

            >

                Historial general

            </a>





            {{--

                Estos dos botones los activaremos

                en el siguiente paso.

            --}}



            <a

                href="{{ route('admin.empleados.historial.individual.excel', $empleado->id) }}"

                class="history-btn excel"

                title="Descargar historial en Excel"

            >

                Excel

            </a>





            <a

                href="{{ route('admin.empleados.historial.individual.pdf', $empleado->id) }}"

                class="history-btn pdf"

                title="Ver reporte PDF"

                target="_blank"

                rel="noopener"

            >

                PDF

            </a>



        </div>



    </div>





    {{-- =========================================================

         INFORMACIÓN DEL EMPLEADO

    ========================================================== --}}



    <div class="employee-summary">



        <div class="summary-card">



            <span class="summary-label">

                Código

            </span>



            <div class="summary-value red">

                {{ $empleado->codigo_empleado }}

            </div>



        </div>





        <div class="summary-card">



            <span class="summary-label">

                Empleado

            </span>



            <div class="summary-value">

                {{ $nombreCompleto }}

            </div>



        </div>





        <div class="summary-card">



            <span class="summary-label">

                CI

            </span>



            <div class="summary-value">

                {{ $empleado->ci ?: 'No registrado' }}

            </div>



        </div>





        <div class="summary-card">



            <span class="summary-label">

                Fecha de ingreso

            </span>



            <div class="summary-value">



                {{

                    $empleado->fecha_ingreso

                        ?->format('d/m/Y')

                    ?? 'No registrada'

                }}



            </div>



        </div>





        <div class="summary-card">



            <span class="summary-label">

                Estado actual

            </span>



            <div>



                @if($estaEliminado)



                    <span class="status-pill deleted">

                        Eliminado

                    </span>



                @elseif($empleado->estado === 'activo')



                    <span class="status-pill active">

                        Activo

                    </span>



                @else



                    <span class="status-pill inactive">

                        Inactivo

                    </span>



                @endif



            </div>



        </div>



    </div>





    {{-- =========================================================

         HISTORIAL

    ========================================================== --}}



    <div class="history-panel">



        <div class="panel-header">



            <div class="panel-title">



                <h2>

                    Línea de tiempo

                </h2>



                <p>

                    Registro cronológico de las acciones

                    realizadas sobre este empleado.

                </p>



            </div>





            <div class="event-count">



                {{ $historial->total() }}



                {{

                    $historial->total() === 1

                        ? 'evento'

                        : 'eventos'

                }}



            </div>



        </div>





        @if($historial->count() > 0)



            <div class="timeline">



                @foreach($historial as $evento)



                    @php



                        $accion =

                            strtolower(

                                $evento->accion

                            );



                        $usuario =

                            $evento->usuario;



                        $responsable =

                            'Sistema';



                        $username =

                            null;



                        if ($usuario) {



                            $username =

                                $usuario->username;



                            if (

                                $usuario

                                    ->empleadoConEliminados

                            ) {



                                $responsable =

                                    trim(

                                        $usuario

                                            ->empleadoConEliminados

                                            ->nombres

                                        .

                                        ' '

                                        .

                                        $usuario

                                            ->empleadoConEliminados

                                            ->apellidos

                                    );



                            } else {



                                $responsable =

                                    $usuario->username;

                            }

                        }





                        /*

                          * Comparar datos anteriores

                          * y nuevos para las ediciones.

                         */



                        $cambios = [];



                        $anteriores =

                            $evento->datos_anteriores

                            ?? [];



                        $nuevos =

                            $evento->datos_nuevos

                            ?? [];





                        if (

                            $accion === 'editado' &&

                            is_array($anteriores) &&

                            is_array($nuevos)

                        ) {



                            $ignorar = [

                                'id',

                                'created_at',

                                'updated_at',

                                'deleted_at',

                                'eliminado_por',

                            ];



                            foreach (

                                $nuevos

                                as $campo => $nuevo

                            ) {



                                if (

                                    in_array(

                                        $campo,

                                        $ignorar,

                                        true

                                    )

                                ) {

                                    continue;

                                }





                                $anterior =

                                    $anteriores[$campo]

                                    ?? null;





                                /*

                                  * Normalizar para comparar.

                                 */



                                $anteriorComparar =

                                    is_array($anterior)

                                        ? json_encode($anterior)

                                        : (string) $anterior;



                                $nuevoComparar =

                                    is_array($nuevo)

                                        ? json_encode($nuevo)

                                        : (string) $nuevo;





                                if (

                                    $anteriorComparar !==

                                    $nuevoComparar

                                ) {



                                    $cambios[$campo] = [

                                        'anterior' =>

                                            $anterior,



                                        'nuevo' =>

                                            $nuevo,

                                    ];

                                }

                            }

                        }





                        /*

                          * Nombres más bonitos para

                          * los campos.

                         */



                        $nombresCampos = [

                            'codigo_empleado' =>

                                'Código',



                            'nombres' =>

                                'Nombres',



                            'apellidos' =>

                                'Apellidos',



                            'ci' =>

                                'CI',



                            'fecha_nacimiento' =>

                                'Fecha nacimiento',



                            'genero' =>

                                'Género',



                            'telefono' =>

                                'Teléfono',



                            'celular' =>

                                'Celular',



                            'correo' =>

                                'Correo',



                            'direccion' =>

                                'Dirección',



                            'ciudad' =>

                                'Ciudad',



                            'fecha_ingreso' =>

                                'Fecha ingreso',



                            'fecha_salida' =>

                                'Fecha salida',



                            'area_id' =>

                                'Área',



                            'cargo_id' =>

                                'Cargo',



                            'tipo_contrato' =>

                                'Tipo contrato',



                            'estado' =>

                                'Estado',



                            'observaciones' =>

                                'Observaciones',

                        ];



                    @endphp





                    <div class="timeline-item">



                        <div

                            class="

                                timeline-dot

                                {{ $accion }}

                            "

                        ></div>





                        <div class="event-card">





                            {{-- CABECERA DEL EVENTO --}}



                            <div class="event-top">



                                <div class="event-action">



                                    <span

                                        class="

                                            event-badge

                                            {{ $accion }}

                                        "

                                    >

                                        {{ $evento->accion }}

                                    </span>





                                    <span class="event-id">



                                        Evento

                                        #{{ $evento->id }}



                                    </span>



                                </div>





                                <div class="event-date">



                                    <strong>



                                        {{

                                            $evento

                                                ->created_at

                                                ?->format(

                                                    'd/m/Y'

                                                )

                                            ?? '—'

                                        }}



                                    </strong>



                                    {{

                                        $evento

                                            ->created_at

                                            ?->format(

                                                'H:i:s'

                                            )

                                        ?? ''

                                    }}



                                </div>



                            </div>





                            {{-- DESCRIPCIÓN --}}



                            <div class="event-description">



                                {{

                                    $evento->descripcion

                                    ?: 'Acción registrada en el sistema.'

                                }}



                            </div>





                            {{-- INFORMACIÓN DEL EVENTO --}}



                            <div class="event-info-grid">





                                {{-- RESPONSABLE --}}



                                <div class="event-info">



                                    <span class="event-info-label">

                                        Realizado por

                                    </span>



                                    <div class="event-info-value">



                                        {{ $responsable }}



                                        @if($username)



                                            <small>

                                                {{ '@' . $username }}

                                            </small>



                                        @endif



                                    </div>



                                </div>





                                {{-- CAMBIO DE ESTADO --}}



                                <div class="event-info">



                                    <span class="event-info-label">

                                        Cambio de estado

                                    </span>





                                    @if(

                                        $evento->estado_anterior ||

                                        $evento->estado_nuevo

                                    )



                                        <div class="state-change">



                                            <span class="state-old">



                                                {{

                                                    $evento

                                                        ->estado_anterior

                                                    ?: '—'

                                                }}



                                            </span>



                                            <span class="state-arrow">

                                                →

                                            </span>



                                            <span class="state-new">



                                                {{

                                                    $evento

                                                        ->estado_nuevo

                                                    ?: '—'

                                                }}



                                            </span>



                                        </div>



                                    @else



                                        <div class="event-info-value">

                                            Sin cambio

                                        </div>



                                    @endif



                                </div>





                                {{-- IP --}}



                                <div class="event-info">



                                    <span class="event-info-label">

                                        Dirección IP

                                    </span>



                                    <div class="event-info-value">



                                        {{

                                            $evento->ip

                                            ?: 'No registrada'

                                        }}



                                    </div>



                                </div>



                            </div>





                            {{-- =========================================

                                 CAMPOS MODIFICADOS

                            ========================================== --}}



                            @if(

                                $accion === 'editado' &&

                                count($cambios) > 0

                            )



                                <div class="changes-box">



                                    <div class="changes-title">



                                        Campos modificados

                                        ·

                                        {{ count($cambios) }}



                                    </div>





                                    <div class="changes-grid">



                                        @foreach(

                                            $cambios

                                            as $campo => $cambio

                                        )



                                            <div class="change-row">



                                                <span class="change-field">



                                                    {{

                                                        $nombresCampos[$campo]

                                                        ?? ucfirst(

                                                            str_replace(

                                                                '\_',

                                                                ' ',

                                                                $campo

                                                            )

                                                        )

                                                    }}



                                                </span>





                                                <div class="change-values">



                                                    <span class="old-value">



                                                        {{

                                                            $cambio['anterior']

                                                            === null ||

                                                            $cambio['anterior']

                                                            === ''

                                                                ? 'Vacío'

                                                                : (

                                                                    is_array(

                                                                        $cambio['anterior']

                                                                    )

                                                                        ? json_encode(

                                                                            $cambio['anterior']

                                                                        )

                                                                        : $cambio['anterior']

                                                                )

                                                        }}



                                                    </span>





                                                    <span>

                                                        →

                                                    </span>





                                                    <span class="new-value">



                                                        {{

                                                            $cambio['nuevo']

                                                            === null ||

                                                            $cambio['nuevo']

                                                            === ''

                                                                ? 'Vacío'

                                                                : (

                                                                    is_array(

                                                                        $cambio['nuevo']

                                                                    )

                                                                        ? json_encode(

                                                                            $cambio['nuevo']

                                                                        )

                                                                        : $cambio['nuevo']

                                                                )

                                                        }}



                                                    </span>



                                                </div>



                                            </div>



                                        @endforeach



                                    </div>



                                </div>



                            @endif





                        </div>



                    </div>



                @endforeach



            </div>





            <div class="history-pagination">



                {{ $historial->links() }}



            </div>



        @else



            <div class="empty-history">



                <div class="empty-icon">

                    ⟳

                </div>



                <h3>

                    Sin historial registrado

                </h3>



                <p>

                    Este empleado todavía no tiene

                    acciones registradas en su historial.

                </p>



            </div>



        @endif



    </div>



</div>



@endsection