<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UniversidadController;
use App\Http\Controllers\TemperaturaController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\CheckAdminLogin;
use App\Http\Controllers\AulasController;

// Route::get('/', [UniversidadController::class, 'showForm']);
// Route::post('/temperaturas', [TemperaturaController::class, 'store'])
//      ->name('temperaturas.store');

Route::prefix('admin')->group(function () {

     // 1. Muestra el formulario de login (GET /admin)
     Route::get('/', [AdminAuthController::class, 'showLogin'])->name('admin.login.form');

     // 2. Procesa el intento de login (POST /admin/login)
     Route::post('/login', [AdminAuthController::class, 'doLogin'])->name('admin.login.attempt');

     // 3. Ruta para Salir (Logout)
     Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

     // 4. Grupo de rutas protegidas
     Route::middleware(CheckAdminLogin::class)->group(function () {

          Route::get('/temperaturas', [DashboardController::class, 'showTemperaturas'])->name('admin.dashboard');
          Route::get('/temperaturas-analiticas', [DashboardController::class, 'showAnaliticas'])
               ->name('admin.analiticas');
          Route::resource('aulas', AulasController::class)->except(['show']);

     });
});

Route::middleware('web')->group(function () {

     // Ruta para MOSTRAR el formulario
     Route::get('/', [UniversidadController::class, 'showForm']);

     // Ruta para RECIBIR el formulario
     Route::post('/temperaturas', [TemperaturaController::class, 'store'])
          ->name('temperaturas.store');

});
