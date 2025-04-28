<?php

use App\Http\Controllers\RouteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\AssignmentController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/hello-world', function () {    
    return 'Hello world';
});
//primer Aproach de autentificado
Route::middleware('auth:sanctum')->prefix('vehicles')->group(function () {
    //Obtener todos los vehiculos
    Route::get('/', [VehicleController::class, 'index']);
    //Nuevo vehiculo
    Route::post('/', [VehicleController::class, 'store']);
    //Obtener un vehiculo por ID
    Route::get('{id}', [VehicleController::class, 'show']);
    //Actualizar un vehiculo
    Route::put('{id}', [VehicleController::class, 'update']);
    //Eliminar un vehiculo
    Route::delete('{id}', [VehicleController::class, 'destroy']);
});

// Route
Route::prefix('routes')->group(function () {
    Route::get('/', [RouteController::class, 'index']);
    Route::get('{id}', [RouteController::class, 'show']);
    Route::post('/', [RouteController::class, 'store']);
    Route::put('{id}', [RouteController::class, 'update']);
    Route::delete('{id}', [RouteController::class, 'destroy']);
});

// Assignment
Route::prefix('assignments')->group(function () {
    Route::get('/', [AssignmentController::class, 'index']);
    Route::get('{id}', [AssignmentController::class, 'show']);
    Route::post('/', [AssignmentController::class, 'store']);
    Route::put('{id}', [AssignmentController::class, 'update']);
    Route::delete('{id}', [AssignmentController::class, 'destroy']);
});