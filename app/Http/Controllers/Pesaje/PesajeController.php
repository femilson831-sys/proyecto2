<?php

namespace App\Http\Controllers\Pesaje;

use App\Http\Controllers\Admin\PesajeController as AdminPesajeController;
use Illuminate\Support\Facades\DB;

class PesajeController extends AdminPesajeController
{
    public function dashboard()
    {
        $pesajesHoy = DB::table('pesajes')->whereDate('fecha', today())->count();
        $pesoHoy = DB::table('pesajes')->whereDate('fecha', today())->sum('peso_total');
        $totalPesajes = DB::table('pesajes')->count();

        return view('pesaje.dashboard', compact('pesajesHoy', 'pesoHoy', 'totalPesajes'));
    }
}
