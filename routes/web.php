<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UniversidadController;
use App\Http\Controllers\TemperaturaController;

// Route::get('/', [UniversidadController::class, 'showForm']);
// Route::post('/temperaturas', [TemperaturaController::class, 'store'])
//      ->name('temperaturas.store');

Route::middleware('web')->group(function () {

     // Ruta para MOSTRAR el formulario
     Route::get('/', [UniversidadController::class, 'showForm']);

     // Ruta para RECIBIR el formulario
     Route::post('/temperaturas', [TemperaturaController::class, 'store'])
          ->name('temperaturas.store');

});
