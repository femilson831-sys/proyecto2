@extends('admin.layouts.app')

@section('title', 'Editar proveedor')

@section('content')
<div class="page-header">
    <div class="page-title">
        <span class="eyebrow">{{ $proveedor->codigo }}</span>
        <h1>Editar proveedor</h1>
        <p>Actualiza los datos del proveedor.</p>
    </div>

    <a href="{{ route('admin.proveedores.index') }}" class="btn-secondary">
        ← Volver
    </a>
</div>

<form method="POST" action="{{ route('admin.proveedores.update', $proveedor) }}">
    @csrf
    @method('PUT')
    @include('admin.proveedores._form', ['proveedor' => $proveedor])
</form>
@endsection