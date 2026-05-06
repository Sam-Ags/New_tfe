<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorityController;
use App\Http\Controllers\IncidentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IncidentController::class, 'publicCreate'])->name('incidents.public.create');
Route::post('/incidents', [IncidentController::class, 'storePublic'])->middleware('throttle:10,1')->name('incidents.store');
Route::get('/admin', [AuthController::class, 'showAdminLogin'])->name('admin.login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1')->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [IncidentController::class, 'index'])->name('incidents.index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/authorities/create', [AuthorityController::class, 'create'])->name('authorities.create');
    Route::post('/authorities', [AuthorityController::class, 'store'])->middleware('throttle:5,1')->name('authorities.store');
    Route::post('/incidents/{incident}/dashboard', [IncidentController::class, 'storeInDashboard'])->name('incidents.dashboard.store');
    Route::patch('/incidents/{incident}', [IncidentController::class, 'update'])->name('incidents.update');
});
