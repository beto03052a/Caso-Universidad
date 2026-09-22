<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Campus Connect — Web Routes
|--------------------------------------------------------------------------
*/

// ─── Ruta raíz: redirigir a login o dashboard ──────────────────────────────
Route::get('/', function () {
    return auth()->check() && auth()->user()->isAdminOrTecnico()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// ─── CU01: Autenticación ──────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ─── Panel Administrativo (auth + admin/tecnico) ──────────────────────────
Route::middleware(['auth', \App\Http\Middleware\AdminOrTecnicoMiddleware::class])
    ->group(function () {

    // CU02 — Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CU03 — Gestión de Solicitudes
    Route::get('/solicitudes',                    [RequestController::class, 'index'])->name('requests.index');
    Route::get('/solicitudes/{id}',               [RequestController::class, 'show'])->name('requests.show');
    Route::post('/solicitudes/{id}/asignar',      [RequestController::class, 'assign'])->name('requests.assign');
    Route::post('/solicitudes/{id}/estado',       [RequestController::class, 'updateStatus'])->name('requests.updateStatus');

    // CU04 — Gestión de Recursos (CRUD)
    Route::get('/recursos',                       [ResourceController::class, 'index'])->name('resources.index');
    Route::get('/recursos/crear',                 [ResourceController::class, 'create'])->name('resources.create');
    Route::post('/recursos',                      [ResourceController::class, 'store'])->name('resources.store');
    Route::get('/recursos/{resource}/editar',     [ResourceController::class, 'edit'])->name('resources.edit');
    Route::put('/recursos/{resource}',            [ResourceController::class, 'update'])->name('resources.update');
    Route::delete('/recursos/{resource}',         [ResourceController::class, 'destroy'])->name('resources.destroy');

    // CU05 — Reportes
    Route::get('/reportes',          [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reportes/exportar', [ReportController::class, 'export'])->name('reports.export');
});
