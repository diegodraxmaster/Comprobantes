<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ComprobanteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
});
/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::controller(ComprobanteController::class)->prefix('comprobante')->group(function () {
        Route::get('/', 'index');
        Route::post('registrar', 'registrar');
        Route::get('detalles/{id}', 'getDetalles');
        Route::delete('/delete/{comprobante}', 'destroy');
        Route::get('/monto-total-articulos', 'getMontoTotalArticulos');
        Route::get('/monto-total-comprobantes', 'getMontoTotalComprobantes');
    });
});
//Route::post('/login', [AuthController::class, 'login']);
/* Route::controller(AuthController::class)->prefix('login')->group(function () {
    Route::post('/register', 'register');
    Route::post('/', 'login');
    //Route::post('/', 'update');
    //Route::delete('{absence_id}', 'destroy');
}); */
