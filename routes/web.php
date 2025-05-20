<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\LockerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RenewalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    // Formulario de filtro de reportes
    Route::get('reportes/clientes', [ReportController::class, 'index'])
        ->name('reportes.clientes.index');

    // Procesar filtro y mostrar resultados
    Route::post('reportes/clientes', [ReportController::class, 'filter'])
        ->name('reportes.clientes.filter');
    Route::get('reportes/clientes/export', [ReportController::class, 'export'])
        ->name('reportes.clientes.export');
    Route::resource('/client', ClientController::class)->names('cliente');
    Route::resource('/Locker', LockerController::class)->names('locker');
    Route::get('/lockers', [LockerController::class, 'index'])->name('sistema.listLocker');
    Route::get('renovaciones', [RenewalController::class, 'index'])
        ->name('renovaciones.index');

    Route::post('renovaciones', [RenewalController::class, 'renew'])
        ->name('renovaciones.renew');


});