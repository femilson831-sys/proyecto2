@extends('admin.layouts.app')

@section('title', 'Editar empleado')

@section('content')

<div class="page-header">

    <div class="page-title">

        <span class="eyebrow">
            Personal
        </span>

        <h1>Editar empleado</h1>

        <p>
            Actualiza la información de
            {{ $empleado->nombres }}
            {{ $empleado->apellidos }}.
        </p>

    </div>

</div>


@if($errors->any())

    <div class="error-message">

        <strong>
            Revisa la información ingresada.
        </strong>

        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

    </div>

@endif


<form
    method="POST"
    action="{{ route('admin.empleados.update', $empleado) }}"
    class="form-panel-admin"
>

    @csrf
    @method('PUT')

    @include('admin.empleados._form')


    <div class="form-actions">

        <a
            href="{{ route('admin.empleados.index') }}"
            class="btn-secondary"
        >
            Cancelar
        </a>

        <button
            type="submit"
            class="btn-primary"
        >
            Guardar cambios
        </button>

    </div>

</form>

@endsection