@extends('pesaje.layouts.app')

@section('title', 'Panel de pesaje')

@section('content')
<style>
    .welcome { padding: 25px; background: #1a1c22; border: 1px solid #323640; border-radius: 16px; }
    .welcome h1 { margin-top: 0; }
    .stats { display: grid; grid-template-columns: repeat(auto-fit,minmax(200px,1fr)); gap: 14px; margin-top: 22px; }
    .stat { padding: 20px; border-radius: 14px; background: #21232a; border: 1px solid #3b3d47; }
    .stat strong { display: block; font-size: 28px; margin-top: 10px; }
    .actions { display:flex; flex-wrap:wrap; gap: 12px; margin-top: 24px; }
    .action { background: #e31b23; border-radius: 10px; padding: 13px 18px; text-decoration: none; color: white; font-weight: 700; }
    .action.alt { background: #353944; }
</style>
<section class="welcome">
    <h1>Panel de personal de pesaje</h1>
    <p>Registra el peso de los productos, consulta el historial y genera etiquetas de código de barras.</p>
    <div class="stats">
        <div class="stat">Pesajes registrados hoy<strong>{{ number_format($pesajesHoy) }}</strong></div>
        <div class="stat">Peso registrado hoy<strong>{{ number_format($pesoHoy, 3) }}</strong></div>
        <div class="stat">Total de pesajes<strong>{{ number_format($totalPesajes) }}</strong></div>
    </div>
    <div class="actions">
        <a class="action" href="{{ route('pesaje.create') }}">Registrar pesaje</a>
        <a class="action alt" href="{{ route('pesaje.index') }}">Consultar registros</a>
    </div>
</section>
@endsection
