<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemografiaController;
use App\Http\Controllers\TasaParoController;
use App\Http\Controllers\NumeroEmpresaSectorController;
use App\Http\Controllers\RentaController;

Route::get('/', function () {
    return view('inicio');
})->name('home');

/// === DEMOGRAFÍA ===
Route::prefix('demografia')->group(function () {
    Route::get('/', [DemografiaController::class, 'index'])->name('demografia.index');
    Route::get('/tasa_paro', [TasaParoController::class, 'index'])->name('tasa_paro');
    Route::get('/municipios/{provincia_id}', [DemografiaController::class, 'getMunicipios']);
});

/// === ECONOMÍA ===
Route::prefix('economia')->group(function () {
    Route::get('/rentas', [RentaController::class, 'index'])->name('economia.rentas');
    Route::get('/empresas_sectores', [NumeroEmpresaSectorController::class, 'index'])->name('empresas_sectores');
});
?>