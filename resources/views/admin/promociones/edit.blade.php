@extends('admin.layouts.app')

@section('title', 'Editar promoción')

@section('content')
    <h1>Editar promoción</h1>

    <form
        action="{{ route('admin.promociones.update', $promocion->id) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        @include('admin.promociones._form')
    </form>
@endsection