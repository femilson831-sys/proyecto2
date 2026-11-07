@extends('admin.layouts.app')

@section('title', 'Detalle del historial')


@push('styles')
<style>

.history-detail {
    max-width: 1050px;
}

.history-detail-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;

    gap: 20px;

    margin-bottom: 20px;
}

.history-detail-title span {
    color: #e31b23;

    font-size: 10px;
    font-weight: 800;

    text-transform: uppercase;
}

.history-detail-title h1 {
    margin: 6px 0;

    color: #fff;

    font-size: 25px;
}

.history-detail-title p {
    margin: 0;

    color: #777d85;

    font-size: 12px;
}

.back-history {
    min-height: 40px;

    padding: 0 14px;

    display: inline-flex;
    align-items: center;

    border:
        1px solid
        rgba(255,255,255,.08);

    border-radius: 10px;

    background:
        rgba(255,255,255,.035);

    color: #bbbfc5;

    text-decoration: none;

    font-size: 11px;
    font-weight: 800;
}


/* PERFIL */

.history-profile {
    padding: 23px;

    border:
        1px solid
        rgba(255,255,255,.07);

    border-radius: 16px;

    background: #15171a;
}

.employee-heading {
    display: flex;
    align-items: center;

    gap: 14px;

    padding-bottom: 20px;

    border-bottom:
        1px solid
        rgba(255,255,255,.06);
}

.history-avatar {
    width: 55px;
    height: 55px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 14px;

    background:
        linear-gradient(
            145deg,
            #e31b23,
            #8d1016
        );

    color: #fff;

    font-size: 17px;
    font-weight: 900;
}

.employee-heading h2 {
    margin: 0 0 4px;

    color: #fff;

    font-size: 18px;
}

.employee-heading p {
    margin: 0;

    color: #777d85;

    font-size: 11px;
}

.deleted-label {
    margin-left: auto;

    padding: 6px 10px;

    border-radius: 999px;

    background:
        rgba(239,68,68,.08);

    color: #f87171;

    font-size: 9px;
    font-weight: 900;
}


/* DATOS */

.history-section {
    margin-top: 23px;
}

.history-section h3 {
    margin: 0 0 14px;

    color: #ddd;

    font-size: 12px;

    text-transform: uppercase;
}

.history-grid {
    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 12px;
}

.history-data {
    padding: 13px;

    border:
        1px solid
        rgba(255,255,255,.055);

    border-radius: 10px;

    background:
        rgba(255,255,255,.02);
}

.history-data label {
    display: block;

    margin-bottom: 5px;

    color: #656b73;

    font-size: 9px;
    font-weight: 800;

    text-transform: uppercase;
}

.history-data span {
    color: #d4d7da;

    font-size: 12px;
    font-weight: 600;
}


/* RESPONSABLE */

.audit-box {
    margin-top: 23px;

    padding: 17px;

    border:
        1px solid
        rgba(239,68,68,.13);

    border-radius: 12px;

    background:
        rgba(239,68,68,.04);
}

.audit-box h3 {
    margin: 0 0 12px;

    color: #f87171;

    font-size: 11px;
    text-transform: uppercase;
}

.audit-user {
    color: #fff;

    font-size: 13px;
    font-weight: 800;
}

.audit-username {
    margin-top: 3px;

    color: #8c7373;

    font-size: 11px;
}


/* RESTAURAR */

.history-footer {
    margin-top: 20px;

    display: flex;
    justify-content: flex-end;
}

.restore-button {
    min-height: 42px;

    padding: 0 16px;

    border:
        1px solid
        rgba(34,197,94,.20);

    border-radius: 10px;

    background:
        rgba(34,197,94,.07);

    color: #4ade80;

    font-size: 11px;
    font-weight: 800;

    cursor: pointer;
}

.restore-button:hover {
    background: #16a34a;

    color: #fff;
}


@media(max-width: 800px) {

    .history-grid {
        grid-template-columns: 1fr;
    }

    .history-detail-header {
        flex-direction: column;
    }

}

</style>
@endpush


@section('content')

@php

    $responsable =
        $empleado->eliminadoPor;

    $nombreResponsable =
        'Sin registro';

    if ($responsable) {

        if ($responsable->empleado) {

            $nombreResponsable =
                trim(
                    $responsable
                        ->empleado
                        ->nombres
                    .
                    ' '
                    .
                    $responsable
                        ->empleado
                        ->apellidos
                );

        } else {

            $nombreResponsable =
                $responsable->username;

        }
    }


    $iniciales =
        strtoupper(
            substr(
                $empleado->nombres ?? 'E',
                0,
                1
            )
            .
            substr(
                $empleado->apellidos ?? '',
                0,
                1
            )
        );

@endphp


<div class="history-detail">


    <div class="history-detail-header">

        <div class="history-detail-title">

            <span>
                Historial de empleados
            </span>

            <h1>
                Detalle del empleado
            </h1>

            <p>
                Información histórica del registro eliminado.
            </p>

        </div>


        <a
            href="{{ route(
                'admin.empleados.historial'
            ) }}"
            class="back-history"
        >
            ← Volver al historial
        </a>

    </div>


    <div class="history-profile">


        <div class="employee-heading">

            <div class="history-avatar">

                {{ $iniciales }}

            </div>


            <div>

                <h2>

                    {{ $empleado->nombres }}

                    {{ $empleado->apellidos }}

                </h2>

                <p>

                    {{
                        $empleado
                            ->codigo_empleado
                    }}

                </p>

            </div>


            <span class="deleted-label">
                Eliminado
            </span>

        </div>


        {{-- DATOS PERSONALES --}}

        <div class="history-section">

            <h3>
                Información personal
            </h3>

            <div class="history-grid">

                <div class="history-data">

                    <label>
                        Código
                    </label>

                    <span>
                        {{
                            $empleado
                                ->codigo_empleado
                        }}
                    </span>

                </div>


                <div class="history-data">

                    <label>
                        CI
                    </label>

                    <span>
                        {{
                            $empleado->ci
                                ?: 'No registrado'
                        }}
                    </span>

                </div>


                <div class="history-data">

                    <label>
                        Género
                    </label>

                    <span>
                        {{
                            $empleado->genero
                                ?: 'No registrado'
                        }}
                    </span>

                </div>


                <div class="history-data">

                    <label>
                        Celular
                    </label>

                    <span>
                        {{
                            $empleado->celular
                                ?: 'No registrado'
                        }}
                    </span>

                </div>


                <div class="history-data">

                    <label>
                        Correo
                    </label>

                    <span>
                        {{
                            $empleado->correo
                                ?: 'No registrado'
                        }}
                    </span>

                </div>


                <div class="history-data">

                    <label>
                        Ciudad
                    </label>

                    <span>
                        {{
                            $empleado->ciudad
                                ?: 'No registrada'
                        }}
                    </span>

                </div>

            </div>

        </div>


        {{-- DATOS LABORALES --}}

        <div class="history-section">

            <h3>
                Información laboral
            </h3>


            <div class="history-grid">

                <div class="history-data">

                    <label>
                        Tipo de contrato
                    </label>

                    <span>

                        {{
                            $empleado
                                ->tipo_contrato
                                ?: 'No registrado'
                        }}

                    </span>

                </div>


                <div class="history-data">

                    <label>
                        Fecha de ingreso
                    </label>

                    <span>

                        {{
                            $empleado
                                ->fecha_ingreso
                                ? $empleado
                                    ->fecha_ingreso
                                    ->format('d/m/Y')
                                : 'No registrada'
                        }}

                    </span>

                </div>


                <div class="history-data">

                    <label>
                        Fecha de salida
                    </label>

                    <span>

                        {{
                            $empleado
                                ->fecha_salida
                                ? $empleado
                                    ->fecha_salida
                                    ->format('d/m/Y')
                                : 'No registrada'
                        }}

                    </span>

                </div>

            </div>

        </div>


        {{-- AUDITORÍA --}}

        <div class="audit-box">

            <h3>
                Información de eliminación
            </h3>


            <div class="audit-user">

                Eliminado por:
                {{ $nombreResponsable }}

            </div>


            @if($responsable)

                <div class="audit-username">

                    Usuario:
                    {{ $responsable->username }}

                </div>

            @endif


            <div class="audit-username">

                Fecha y hora:

                {{
                    $empleado->deleted_at
                        ? $empleado
                            ->deleted_at
                            ->format(
                                'd/m/Y H:i:s'
                            )
                        : 'Sin registro'
                }}

            </div>

        </div>


        {{-- OBSERVACIONES --}}

        @if($empleado->observaciones)

            <div class="history-section">

                <h3>
                    Observaciones
                </h3>


                <div class="history-data">

                    <span>

                        {{
                            $empleado
                                ->observaciones
                        }}

                    </span>

                </div>

            </div>

        @endif


        <div class="history-footer">

            <form
                method="POST"
                action="{{ route(
                    'admin.empleados.restaurar',
                    $empleado->id
                ) }}"
                onsubmit="
                    return confirm(
                        '¿Deseas restaurar este empleado?'
                    );
                "
            >

                @csrf

                @method('PATCH')


                <button
                    type="submit"
                    class="restore-button"
                >
                    Restaurar empleado
                </button>

            </form>

        </div>

    </div>

</div>

@endsection