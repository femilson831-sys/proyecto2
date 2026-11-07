@extends('admin.layouts.app')

@section('title', 'Registrar proveedor')

@section('content')
<div class="page-header">
    <div class="page-title">
        <span class="eyebrow">Compras e inventario</span>
        <h1>Registrar proveedor</h1>
        <p>Completa los datos del proveedor y agrega sus contactos.</p>
    </div>

    <a href="{{ route('admin.proveedores.index') }}" class="btn-secondary">
        ← Volver
    </a>
</div>

<form method="POST" action="{{ route('admin.proveedores.store') }}">
    @csrf
    @include('admin.proveedores._form')
</form>
@endsection