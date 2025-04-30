<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/hello-world', function () {    
    return 'Hello world';
});
//primer Aproach de autentificado
Route::prefix('vehicles')->group(function () {
    Route::get('/', [VehicleController::class, 'index']);
    Route::post('/', [VehicleController::class, 'store']);
    Route::get('{id}', [VehicleController::class, 'show']);
    Route::put('{id}', [VehicleController::class, 'update']);
    Route::delete('{id}', [VehicleController::class, 'destroy']);
});
