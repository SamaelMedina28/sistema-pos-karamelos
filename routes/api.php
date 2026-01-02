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
    /**
     * @group Lotes
     * Rutas relacionadas con la gestión de lotes
     */
    // ? Lotes
    Route::get('/lots/{lot}', [LotController::class, 'show']);
    /**
     * @group Cortes
     * Rutas para la gestión de cortes de caja
     */
    // ? Cortes
    Route::post('/cuts', [CutController::class, 'store']); // Crear un corte

    /**
     * @group Ventas
     * Rutas para el registro y consulta de ventas
     */
    // ? Ventas
    Route::post('/sales', [SaleController::class, 'store']); // Crear una venta
    // Route::get('/sales/{sale}', [SaleController::class, 'show']); // Ver una venta
    /**
     * @group Ventas
     * Rutas adicionales de ventas
     */
    // ? Ventas
    Route::get('/sales', [SaleController::class, 'index']); // Ver todas las ventas de una fecha

    /**
     * @group Productos
     * Rutas de consulta pública de productos
     */
    // ? Productos
    Route::get('/products', [ProductController::class, 'index']);


    Route::middleware('admin')->group(function () {
        /**
         * @group Lotes (Admin)
         */
        // ? Lotes
        Route::get('/lots', [LotController::class, 'index']); // Ver todos los lotes o de una fecha
        /**
         * @group Cortes (Admin)
         */
        // ? Cortes
        Route::get('/cuts/{lot_id}', [CutController::class, 'index']); // Ver todos los cortes de un lote
        Route::get('/cut/{id}', [CutController::class, 'show']); // Ver un corte
        /**
         * @group Productos (Admin)
         * Gestión completa de productos
         */
        // ? Productos
        Route::post('/products/{product}', [ProductController::class, 'update']);
        Route::apiResource('products', ProductController::class)->except(['index', 'update']);
    });
});
