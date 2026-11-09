<?php

use App\Http\Controllers\Pesaje\PesajeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'rol:personal-de-pesaje'])
    ->prefix('pesaje')
    ->name('pesaje.')
    ->controller(PesajeController::class)
    ->group(function (): void {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/', 'index')->name('index');
        Route::get('/crear', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{pesaje}/etiqueta', 'etiqueta')
            ->whereNumber('pesaje')->name('etiqueta');
    });
