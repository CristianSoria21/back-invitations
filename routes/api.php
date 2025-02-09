<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

// Rutas públicas (No requieren autenticación)
Route::post('/register', [RegisteredUserController::class, 'store']); // Registro
Route::post('/login', [AuthenticatedSessionController::class, 'store']); // Inicio de sesión

// Rutas protegidas (Requieren autenticación con token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']); // Cierre de sesión

});
