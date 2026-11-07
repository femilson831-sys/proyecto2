@extends('admin.layouts.app')

@section('title', 'Editar rol')

@section('content')
<div class="page-header">
    <div class="page-title"><span class="eyebrow">Usuarios y roles</span><h1>Editar rol</h1><p>Actualiza el nombre o la descripción de «{{ $rol->nombre }}».</p></div>
</div>
<style>
    .role-form-card{max-width:720px;padding:26px;background:#17181c;border:1px solid rgba(255,255,255,.08);border-radius:16px}
    .role-form-card .form-group{display:flex;flex-direction:column;gap:8px;margin-bottom:20px}
    .role-form-card label{color:#e8e8eb;font-size:13px;font-weight:700}
    .role-form-card input,.role-form-card textarea{width:100%;padding:12px 14px;border:1px solid rgba(255,255,255,.12);border-radius:9px;background:#101115;color:#f5f5f6;outline:none}
    .role-form-card input:focus,.role-form-card textarea:focus{border-color:#e31b23}
    .role-form-card .field-error{color:#ff9297;font-size:12px}
    .role-form-actions{display:flex;gap:10px;align-items:center}
    .role-cancel{padding:11px 15px;border:1px solid rgba(255,255,255,.13);border-radius:9px;color:#ddd;font-size:13px}
</style>
<div class="role-form-card">
    <form action="{{ route('admin.roles.update', $rol) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.roles._form')
        <div class="role-form-actions"><button class="btn-primary" type="submit">Guardar cambios</button><a class="role-cancel" href="{{ route('admin.roles.index') }}">Cancelar</a></div>
    </form>
</div>
@endsection
