<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CutController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\LotController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth')->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
    // ? Lotes
    Route::get('/lots/{lot}', [LotController::class, 'show']);
    // ? Cortes
    Route::post('/cuts', [CutController::class, 'store']); // Crear un corte
    
    // ? Ventas
    Route::post('/sales', [SaleController::class, 'store']); // Crear una venta
    // Route::get('/sales/{sale}', [SaleController::class, 'show']); // Ver una venta
    // ? Ventas
    Route::get('/sales', [SaleController::class, 'index']); // Ver todas las ventas de una fecha
    
    // ? Productos
    Route::get('/products',[ProductController::class, 'index']);
    
    
    Route::middleware('admin')->group(function () {
        // ? Lotes
        Route::get('/lots', [LotController::class, 'index']); // Ver todos los lotes o de una fecha
        // ? Cortes
        Route::get('/cuts/{lot_id}', [CutController::class, 'index']); // Ver todos los cortes de un lote
        Route::get('/cut/{id}', [CutController::class, 'show']); // Ver un corte
        // ? Productos
        Route::post('/products/{product}', [ProductController::class, 'update']);
        Route::apiResource('products', ProductController::class)->except(['index', 'update']);
    });
});