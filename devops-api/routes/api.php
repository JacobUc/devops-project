<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\RouteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvitationCodeController;
use App\Http\Controllers\StatisticsController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


//Routes without authentication
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin', [AdminController::class, 'store']);

Route::get('/hello-world', function () {
    return 'Hello world';
});

//primer Aproach de autentificado
Route::prefix('vehicles')->group(function () {
    //Obtener todos los vehiculos
    Route::get('/', [VehicleController::class, 'index']);
    Route::post('/', [VehicleController::class, 'store']);
    Route::get('{id}', [VehicleController::class, 'show']);
    Route::put('{id}', [VehicleController::class, 'update']);
    Route::delete('{id}', [VehicleController::class, 'destroy']);
    Route::patch('{id}', [VehicleController::class, 'update']);
});


// Route
Route::prefix('routes')->group(function () {
    Route::get('/', [RouteController::class, 'index']);
    Route::get('{id}', [RouteController::class, 'show']);
    Route::post('/', [RouteController::class, 'store']);
    Route::put('{id}', [RouteController::class, 'update']);
    Route::delete('{id}', [RouteController::class, 'destroy']);
});

//Invitation code
Route::middleware('auth:sanctum')->prefix('invitationCode')->group(function () {
    Route::get('/', [InvitationCodeController::class, 'index']);
    Route::get('{id}', [InvitationCodeController::class, 'show']);
    Route::post('/', [InvitationCodeController::class, 'store']);
    Route::put('{id}', [InvitationCodeController::class, 'update']);
    Route::delete('{id}', [InvitationCodeController::class, 'destroy']);
});

//Admin
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index']);
    Route::get('{id}', [AdminController::class, 'show']);
    Route::put('{id}', [AdminController::class, 'update']);
    Route::delete('{id}', [AdminController::class, 'destroy']);
});

// Assignment
Route::prefix('assignments')->group(function () {
    Route::get('/', [AssignmentController::class, 'index']);
    Route::get('{id}', [AssignmentController::class, 'show']);
    Route::post('/', [AssignmentController::class, 'store']);
    Route::put('{id}', [AssignmentController::class, 'update']);
    Route::delete('{id}', [AssignmentController::class, 'destroy']);
});

Route::get('/drivers', [DriverController::class, 'index']);
Route::get('/drivers/{id}', [DriverController::class, 'show']);
Route::post('/drivers', [DriverController::class, 'store']);
Route::put('/drivers/{id}', [DriverController::class, 'update']);
Route::patch('/drivers/{id}', [DriverController::class, 'updatePartial']);
Route::delete('/drivers/{id}', [DriverController::class, 'destroy']);

//Statistics
Route::middleware('auth:sanctum')->prefix('statistics')->group(function () {
Route::get('/users', [StatisticsController::class, 'listUsers']);
Route::get('/drivers', [StatisticsController::class, 'listDrivers']);
Route::get('/vehicles', [StatisticsController::class, 'listVehicles']);
Route::get('/routes', [StatisticsController::class, 'listRoutesToday']);
});
