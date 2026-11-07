@extends('admin.layouts.app')

@section('title', 'Crear promoción')

@section('content')
    <h1>Crear promoción</h1>

    <form action="{{ route('admin.promociones.store') }}" method="POST">
        @csrf

        @include('admin.promociones._form')
    </form>
@endsection