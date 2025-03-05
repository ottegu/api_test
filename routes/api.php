<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuscripcionController;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="API de Suscripciones",
 *         version="1.0.0"
 *     ),
 *     @OA\Server(
 *         url="http://localhost:8000/api",
 *         description="Servidor de desarrollo"
 *     )
 * )
 */

Route::middleware('api')->group(function () {
    Route::get('/suscripciones', [SuscripcionController::class, 'getSuscripciones'])->name('api.suscripciones');
});
