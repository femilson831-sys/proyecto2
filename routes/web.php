<?php

use App\Http\Controllers\Admin\AlertaController;
use App\Http\Controllers\Admin\AlmacenController;
use App\Http\Controllers\Admin\AlmacenUbicacionController;
use App\Http\Controllers\Admin\AreaCargoController;
use App\Http\Controllers\Admin\CatalogoProductoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmpleadoController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\MovimientoInventarioController;
use App\Http\Controllers\Admin\PesajeController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\PromocionController;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\RolController;
use App\Http\Controllers\Admin\TareaController;
use App\Http\Controllers\Admin\UnidadMedidaController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\VentaReporteController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Inicio y autenticación
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'mostrarLogin'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.procesar');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Administración
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Áreas y cargos
        |--------------------------------------------------------------------------
        */

        Route::get('/areas-cargos', [AreaCargoController::class, 'index'])
            ->name('areas-cargos.index');

        Route::post('/areas', [AreaCargoController::class, 'storeArea'])
            ->name('areas.store');

        Route::put('/areas/{area}', [AreaCargoController::class, 'updateArea'])
            ->name('areas.update');

        Route::patch('/areas/{area}/estado', [AreaCargoController::class, 'cambiarEstadoArea'])
            ->name('areas.estado');

        Route::post('/cargos', [AreaCargoController::class, 'storeCargo'])
            ->name('cargos.store');

        Route::put('/cargos/{cargo}', [AreaCargoController::class, 'updateCargo'])
            ->name('cargos.update');

        Route::patch('/cargos/{cargo}/estado', [AreaCargoController::class, 'cambiarEstadoCargo'])
            ->name('cargos.estado');

        /*
        |--------------------------------------------------------------------------
        | Catálogos de productos
        |--------------------------------------------------------------------------
        */

        Route::prefix('catalogos-productos')
            ->name('catalogos-productos.')
            ->controller(CatalogoProductoController::class)
            ->group(function (): void {
                Route::get('/', 'index')
                    ->name('index');

                // Categorías.

                Route::post('/categorias', 'storeCategoria')
                    ->name('categorias.store');

                Route::put('/categorias/{categoria}', 'updateCategoria')
                    ->name('categorias.update');

                Route::patch('/categorias/{categoria}/estado', 'estadoCategoria')
                    ->name('categorias.estado');

                Route::delete('/categorias/{categoria}', 'destroyCategoria')
                    ->name('categorias.destroy');

                // Subcategorías.

                Route::post('/subcategorias', 'storeSubcategoria')
                    ->name('subcategorias.store');

                Route::put('/subcategorias/{subcategoria}', 'updateSubcategoria')
                    ->name('subcategorias.update');

                Route::patch('/subcategorias/{subcategoria}/estado', 'estadoSubcategoria')
                    ->name('subcategorias.estado');

                Route::delete('/subcategorias/{subcategoria}', 'destroySubcategoria')
                    ->name('subcategorias.destroy');

                // Marcas.

                Route::post('/marcas', 'storeMarca')
                    ->name('marcas.store');

                Route::put('/marcas/{marca}', 'updateMarca')
                    ->name('marcas.update');

                Route::patch('/marcas/{marca}/estado', 'estadoMarca')
                    ->name('marcas.estado');

                Route::delete('/marcas/{marca}', 'destroyMarca')
                    ->name('marcas.destroy');
            });

        /*
        |--------------------------------------------------------------------------
        | Unidades de medida
        |--------------------------------------------------------------------------
        */

        Route::prefix('unidades-medida')
            ->name('unidades-medida.')
            ->controller(UnidadMedidaController::class)
            ->group(function (): void {
                Route::get('/', 'index')
                    ->name('index');

                Route::post('/', 'store')
                    ->name('store');

                Route::put('/{unidadMedida}', 'update')
                    ->name('update');

                Route::patch('/{unidadMedida}/estado', 'cambiarEstado')
                    ->name('estado');

                Route::delete('/{unidadMedida}', 'destroy')
                    ->name('destroy');
            });

        /*
        |--------------------------------------------------------------------------
        | Productos
        |--------------------------------------------------------------------------
        */

        Route::prefix('productos')
            ->name('productos.')
            ->controller(ProductoController::class)
            ->group(function (): void {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/crear', 'create')
                    ->name('create');

                Route::post('/', 'store')
                    ->name('store');

                Route::get('/categoria/{categoria}/subcategorias', 'subcategorias')
                    ->name('subcategorias');

                Route::get('/{producto}/editar', 'edit')
                    ->name('edit');

                Route::put('/{producto}', 'update')
                    ->name('update');

                Route::patch('/{producto}/estado', 'cambiarEstado')
                    ->name('estado');

                Route::delete('/{producto}', 'destroy')
                    ->name('destroy');

                Route::get('/{producto}', 'show')
                    ->name('show');
            });

        /*
        |--------------------------------------------------------------------------
        | Pesajes
        |--------------------------------------------------------------------------
        */

        Route::prefix('pesajes')
            ->name('pesajes.')
            ->controller(PesajeController::class)
            ->group(function (): void {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/crear', 'create')
                    ->name('create');

                Route::post('/', 'store')
                    ->name('store');

                Route::get('/{pesaje}/etiqueta', 'etiqueta')
                    ->whereNumber('pesaje')
                    ->name('etiqueta');
            });

        /*
        |--------------------------------------------------------------------------
        | Promociones y combos
        |--------------------------------------------------------------------------
        */

        Route::prefix('promociones')
            ->name('promociones.')
            ->controller(PromocionController::class)
            ->group(function (): void {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/crear', 'create')
                    ->name('create');

                Route::post('/', 'store')
                    ->name('store');

                Route::get('/{promocion}/editar', 'edit')
                    ->whereNumber('promocion')
                    ->name('edit');

                Route::put('/{promocion}', 'update')
                    ->whereNumber('promocion')
                    ->name('update');

                Route::patch('/{promocion}/estado', 'cambiarEstado')
                    ->whereNumber('promocion')
                    ->name('estado');

                Route::delete('/{promocion}', 'destroy')
                    ->whereNumber('promocion')
                    ->name('destroy');
            });

        /*
        |--------------------------------------------------------------------------
        | Inventario
        |--------------------------------------------------------------------------
        */

        Route::prefix('inventario')
            ->name('inventario.')
            ->controller(InventarioController::class)
            ->group(function (): void {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/producto/{producto}/datos', 'datosProducto')
                    ->name('producto-datos');

                Route::get('/crear', 'create')
                    ->name('create');

                Route::post('/', 'store')
                    ->name('store');

                Route::get('/salida', 'createSalida')
                    ->name('salida.create');

                Route::post('/salida', 'storeSalida')
                    ->name('salida.store');

                Route::get('/productos/{producto}/historial', 'historialProducto')
                    ->name('historial');
            });

        /*
        |--------------------------------------------------------------------------
        | Proveedores
        |--------------------------------------------------------------------------
        */

        Route::prefix('proveedores')
            ->name('proveedores.')
            ->controller(ProveedorController::class)
            ->group(function (): void {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/crear', 'create')
                    ->name('create');

                Route::post('/', 'store')
                    ->name('store');

                Route::get('/{proveedor}/editar', 'edit')
                    ->name('edit');

                Route::put('/{proveedor}', 'update')
                    ->name('update');

                Route::patch('/{proveedor}/estado', 'cambiarEstado')
                    ->name('estado');

                Route::delete('/{proveedor}', 'destroy')
                    ->name('destroy');
            });

        /*
        |--------------------------------------------------------------------------
        | Historial de empleados
        |--------------------------------------------------------------------------
        |
        | Estas rutas se registran antes de /empleados/{empleado}.
        |
        */

        Route::get('/empleados/historial', [EmpleadoController::class, 'historial'])
            ->name('empleados.historial');

        Route::get('/empleados/historial/excel', [EmpleadoController::class, 'exportarExcel'])
            ->name('empleados.historial.excel');

        Route::get('/empleados/historial/pdf', [EmpleadoController::class, 'exportarPdf'])
            ->name('empleados.historial.pdf');

        Route::get('/empleados/historial/empleado/{id}', [EmpleadoController::class, 'historialEmpleado'])
            ->name('empleados.historial.individual');

        Route::get('/empleados/historial/empleado/{id}/excel', [EmpleadoController::class, 'exportarHistorialEmpleadoExcel'])
            ->name('empleados.historial.individual.excel');

        Route::get('/empleados/historial/empleado/{id}/pdf', [EmpleadoController::class, 'exportarHistorialEmpleadoPdf'])
            ->name('empleados.historial.individual.pdf');

        Route::get('/empleados/historial/evento/{id}', [EmpleadoController::class, 'verHistorial'])
            ->name('empleados.historial.ver');

        Route::patch('/empleados/{id}/restaurar', [EmpleadoController::class, 'restaurar'])
            ->name('empleados.restaurar');

        /*
        |--------------------------------------------------------------------------
        | Empleados
        |--------------------------------------------------------------------------
        */

        Route::prefix('empleados')
            ->name('empleados.')
            ->controller(EmpleadoController::class)
            ->group(function (): void {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/crear', 'create')
                    ->name('create');

                Route::post('/', 'store')
                    ->name('store');

                Route::get('/{empleado}/editar', 'edit')
                    ->name('edit');

                Route::put('/{empleado}', 'update')
                    ->name('update');

                Route::patch('/{empleado}/estado', 'cambiarEstado')
                    ->name('estado');

                Route::delete('/{empleado}', 'destroy')
                    ->name('destroy');

                Route::get('/{empleado}', 'show')
                    ->name('show');
            });

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        Route::prefix('roles')
            ->name('roles.')
            ->controller(RolController::class)
            ->group(function (): void {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/crear', 'create')
                    ->name('create');

                Route::post('/', 'store')
                    ->name('store');

                Route::get('/{rol}/editar', 'edit')
                    ->name('edit');

                Route::put('/{rol}', 'update')
                    ->name('update');

                Route::patch('/{rol}/estado', 'cambiarEstado')
                    ->name('estado');
            });

        /*
        |--------------------------------------------------------------------------
        | Usuarios
        |--------------------------------------------------------------------------
        */

        Route::prefix('usuarios')
            ->name('usuarios.')
            ->controller(UsuarioController::class)
            ->group(function (): void {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/crear', 'create')
                    ->name('create');

                Route::post('/', 'store')
                    ->name('store');

                Route::get('/{usuario}/editar', 'edit')
                    ->name('edit');

                Route::put('/{usuario}', 'update')
                    ->name('update');

                Route::patch('/{usuario}/estado', 'cambiarEstado')
                    ->name('estado');

                Route::get('/{usuario}', 'show')
                    ->name('show');
            });

        /*
        |--------------------------------------------------------------------------
        | Almacenes
        |--------------------------------------------------------------------------
        */

        Route::get('/almacenes', [AlmacenController::class, 'index'])
            ->name('almacenes.index');

        Route::get('/almacenes/crear', [AlmacenController::class, 'create'])
            ->name('almacenes.create');

        Route::post('/almacenes', [AlmacenController::class, 'store'])
            ->name('almacenes.store');

        Route::get('/almacenes/{almacen}/editar', [AlmacenController::class, 'edit'])
            ->whereNumber('almacen')
            ->name('almacenes.edit');

        Route::put('/almacenes/{almacen}', [AlmacenController::class, 'update'])
            ->whereNumber('almacen')
            ->name('almacenes.update');

        Route::patch('/almacenes/{almacen}/estado', [AlmacenController::class, 'cambiarEstado'])
            ->whereNumber('almacen')
            ->name('almacenes.estado');

        /*
        |--------------------------------------------------------------------------
        | Ubicaciones y traslados
        |--------------------------------------------------------------------------
        */

        Route::prefix('almacenes/{almacenId}/ubicaciones')
            ->name('almacenes.ubicaciones.')
            ->controller(AlmacenUbicacionController::class)
            ->group(function (): void {
                Route::get('/', 'index')
                    ->name('index');

                Route::post('/', 'store')
                    ->name('store');

                Route::post('/trasladar', 'trasladar')
                    ->name('trasladar');

                Route::post('/{ubicacionId}/lotes', 'asignarLote')
                    ->name('lotes.store');

                Route::delete('/{ubicacionId}/lotes/{asignacionId}', 'quitarAsignacion')
                    ->name('lotes.destroy');

                Route::delete('/{ubicacionId}', 'destroy')
                    ->name('destroy');
            });

        /*
        |--------------------------------------------------------------------------
        | Alertas
        |--------------------------------------------------------------------------
        */

        Route::prefix('alertas')
            ->name('alertas.')
            ->controller(AlertaController::class)
            ->group(function (): void {
                Route::get('/', 'index')
                    ->name('index');

                // Sonido de notificación de alertas.
                Route::get('/sonido', 'sonido')
                    ->name('sonido');

                // Generar y actualizar alertas automáticas.
                Route::post('/actualizar', 'actualizar')
                    ->name('actualizar');

                // Lectura de todas las alertas pendientes del usuario.
                Route::patch('/leidas', 'marcarTodasLeidas')
                    ->name('leidas');

                // Lectura individual.
                Route::patch('/{alerta}/leida', 'marcarLeida')
                    ->whereNumber('alerta')
                    ->name('leida');
            });

        /*
        |--------------------------------------------------------------------------
        | Movimientos de inventario
        |--------------------------------------------------------------------------
        */

        Route::prefix('movimientos')
            ->name('movimientos.')
            ->controller(MovimientoInventarioController::class)
            ->group(function (): void {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/{movimiento}', 'show')
                    ->whereNumber('movimiento')
                    ->name('show');
            });

        /*
        |--------------------------------------------------------------------------
        | Tareas
        |--------------------------------------------------------------------------
        */

        Route::prefix('tareas')
            ->name('tareas.')
            ->controller(TareaController::class)
            ->group(function (): void {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/crear', 'create')
                    ->name('create');

                Route::post('/', 'store')
                    ->name('store');

                Route::get('/{tarea}', 'show')
                    ->whereNumber('tarea')
                    ->name('show');

                Route::patch('/{tarea}/estado', 'cambiarEstado')
                    ->whereNumber('tarea')
                    ->name('estado');

                Route::post('/{tarea}/evidencias', 'guardarEvidencia')
                    ->whereNumber('tarea')
                    ->name('evidencias.store');
            });

        /*
        |--------------------------------------------------------------------------
        | Reportes
        |--------------------------------------------------------------------------
        */

        Route::prefix('reportes')
            ->name('reportes.')
            ->controller(ReporteController::class)
            ->group(function (): void {
                Route::get('/', 'index')
                    ->name('index');

                Route::get('/exportar', 'exportar')
                    ->name('exportar');

                Route::get('/exportar-csv', 'exportarCsv')
                    ->name('exportar-csv');

                Route::get('/inventario/exportar', 'exportarInventario')
                    ->name('inventario.exportar');

                Route::get('/inventario', 'inventario')
                    ->name('inventario');
            });

        /*
        |--------------------------------------------------------------------------
        | Reporte administrativo de cobros
        |--------------------------------------------------------------------------
        */

        Route::get('/ventas/reportes/cobros', [VentaReporteController::class, 'cobros'])
            ->name('ventas.reportes.cobros');

        // Compatibilidad con los enlaces existentes del menú.
        Route::get('/ventas/pagos', [VentaReporteController::class, 'cobros'])
            ->name('ventas.pagos');
    });
// Panel independiente del personal de pesaje.
require __DIR__ . '/pesaje.php';

Route::middleware('auth')->get('/sin-panel', function () {
    return response()->view('auth.sin-panel', [], 403);
})->name('acceso.sin-panel');
