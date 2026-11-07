@extends('admin.layouts.app')

@section('title', 'Registrar producto')

@push('styles')
<style>
    .product-create-page {
        --surface: #17181c;
        --border: #30333d;
        --muted: #85878e;
        --text: #e6e6e8;
        --red: #e31b23;
        --red-light: #ff777d;
        --red-dark: #a5121a;
        --red-soft: rgba(227, 27, 35, .09);
        --red-glow: rgba(227, 27, 35, .35);
        --radius-lg: 16px;
        --radius-md: 12px;

        display: flex;
        flex-direction: column;
        gap: 20px;
        color: var(--text);
    }

    .product-create-page *,
    .product-create-page *::before,
    .product-create-page *::after {
        box-sizing: border-box;
    }

    .product-create-header {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        flex-wrap: wrap;
        padding: 22px 24px 22px 26px;
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        background:
            radial-gradient(circle at 100% 0%, rgba(227, 27, 35, .07), transparent 55%),
            linear-gradient(180deg, rgba(255, 255, 255, .015), transparent),
            var(--surface);
    }

    .product-create-header::before {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 2px;
        content: "";
        background: linear-gradient(90deg, var(--red), var(--red-dark) 40%, transparent);
    }

    .product-create-header::after {
        position: absolute;
        top: 22px;
        bottom: 22px;
        left: 12px;
        width: 3px;
        border-radius: 3px;
        content: "";
        background: linear-gradient(180deg, var(--red), #7a0d12);
    }

    .product-create-header .page-title {
        padding-left: 14px;
    }

    .product-create-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 9px;
        padding: 4px 10px;
        border: 1px solid rgba(227, 27, 35, .25);
        border-radius: 999px;
        color: var(--red-light);
        background: var(--red-soft);
        font-size: 9.5px;
        font-weight: 800;
        letter-spacing: 1.4px;
        text-transform: uppercase;
    }

    .product-create-eyebrow::before {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        content: "";
        background: var(--red-light);
        box-shadow: 0 0 8px var(--red-glow);
    }

    .product-create-header h1 {
        margin: 0 0 8px;
        color: #f4f4f6;
        font-size: 30px;
        font-weight: 800;
        letter-spacing: -.5px;
        line-height: 1.15;
    }

    .product-create-header p {
        max-width: 580px;
        margin: 0;
        color: var(--muted);
        font-size: 12.5px;
        line-height: 1.65;
    }

    .product-create-back {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 16px;
        border: 1px solid var(--border);
        border-radius: 10px;
        color: #d2d3d8;
        background: #202126;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .2px;
        text-decoration: none;
        transition: color .2s ease, border-color .2s ease, background .2s ease,
                    transform .15s ease, box-shadow .2s ease;
    }

    .product-create-back:hover {
        transform: translateY(-1px);
        border-color: #474b58;
        color: #fff;
        background: #292a30;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .25);
    }

    .product-create-back:active {
        transform: translateY(0);
    }

    .product-create-back:focus-visible {
        outline: 2px solid #ef6068;
        outline-offset: 2px;
    }

    .product-create-back svg {
        width: 14px;
        height: 14px;
        transition: transform .2s ease;
    }

    .product-create-back:hover svg {
        transform: translateX(-2px);
    }

    .product-create-alert {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        padding: 14px 16px;
        border: 1px solid rgba(227, 27, 35, .28);
        border-radius: var(--radius-md);
        color: #ff9297;
        background: linear-gradient(180deg, rgba(227, 27, 35, .09), rgba(227, 27, 35, .05));
        font-size: 11.5px;
        line-height: 1.6;
    }

    .product-create-alert svg {
        flex-shrink: 0;
        width: 16px;
        height: 16px;
        margin-top: 1px;
        color: var(--red-light);
    }

    .product-create-alert strong {
        display: block;
        margin-bottom: 6px;
        color: #fff;
        font-size: 12px;
        font-weight: 750;
    }

    .product-create-alert ul {
        margin: 0;
        padding-left: 18px;
    }

    .product-create-alert li + li {
        margin-top: 3px;
    }

    .product-create-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    @media (max-width: 720px) {
        .product-create-header {
            padding: 18px 18px 18px 20px;
        }

        .product-create-header::after {
            top: 18px;
            bottom: 18px;
            left: 10px;
        }

        .product-create-header h1 {
            font-size: 25px;
        }
    }

    @media (max-width: 640px) {
        .product-create-header {
            flex-direction: column;
            align-items: stretch;
            padding: 16px 16px 16px 18px;
        }

        .product-create-header .page-title {
            padding-left: 12px;
        }

        .product-create-header h1 {
            font-size: 22px;
        }

        .product-create-header p {
            font-size: 12px;
        }

        .product-create-back {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
    <div class="product-create-page">
        <div class="product-create-header">
            <div class="page-title">
                <span class="product-create-eyebrow">Catálogo de productos</span>

                <h1>Registrar producto</h1>

                <p>
                    Registra un nuevo producto para controlar su inventario
                    y habilitarlo para la venta en el Minisúper NANOS.
                </p>
            </div>

            <a href="{{ route('admin.productos.index') }}" class="product-create-back">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
                Volver a productos
            </a>
        </div>

        @if (session('error'))
            <div class="product-create-alert" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="product-create-alert" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true">
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>

                <div>
                    <strong>Revisa los siguientes campos:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form
            action="{{ route('admin.productos.store') }}"
            method="POST"
            enctype="multipart/form-data"
            autocomplete="off"
            class="product-create-form"
        >
            @csrf

            @include('admin.productos._form')
        </form>
    </div>
@endsection