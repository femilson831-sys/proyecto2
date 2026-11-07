@extends('admin.layouts.app')

@section('title', 'Editar producto')

@push('styles')
<style>
    .producto-editar-acciones {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .producto-editar-mensaje {
        margin-bottom: 16px;
        padding: 12px 16px;
        border: 1px solid;
        border-radius: 8px;
        font-size: 13px;
        line-height: 1.5;
    }

    .producto-editar-mensaje.exito {
        border-color: rgba(56, 212, 120, .35);
        color: #8ee5ad;
        background: rgba(56, 212, 120, .08);
    }

    .producto-editar-mensaje.error {
        border-color: rgba(227, 27, 35, .35);
        color: #ff9297;
        background: rgba(227, 27, 35, .08);
    }

    .producto-editar-mensaje ul {
        margin: 8px 0 0;
        padding-left: 20px;
    }

    @media (max-width: 640px) {
        .producto-editar-acciones {
            width: 100%;
        }

        .producto-editar-acciones .btn-secondary {
            flex: 1;
            text-align: center;
        }
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <div class="page-title">
            <span class="eyebrow">
                {{ $producto->codigo_interno ?? 'Producto sin código' }}
            </span>

            <h1>Editar producto</h1>

            <p>
                Actualiza la información de
                <strong>{{ $producto->nombre }}</strong>.
            </p>
        </div>

        <div class="producto-editar-acciones">
            <a
                href="{{ route('admin.productos.index') }}"
                class="btn-secondary"
            >
                ← Productos
            </a>

            <a
                href="{{ route('admin.productos.show', $producto) }}"
                class="btn-secondary"
            >
                Ver producto
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="producto-editar-mensaje exito" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="producto-editar-mensaje error" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="producto-editar-mensaje error" role="alert">
            <strong>Revisa los siguientes campos:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('admin.productos.update', $producto) }}"
        method="POST"
        enctype="multipart/form-data"
        autocomplete="off"
    >
        @csrf
        @method('PUT')

        @include('admin.productos._form', [
            'producto' => $producto,
        ])
    </form>
@endsection