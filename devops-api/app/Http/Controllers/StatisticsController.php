<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LoggerService;
use Illuminate\Support\Carbon;
use App\Models\Assignment;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\User;

class StatisticsController extends Controller
{
    public function listUsers()
    {
        try {
            $users = User::select('id', 'name', 'email', 'created_at')
                        ->orderByDesc('created_at')
                        ->get();
                        
            LoggerService::info('User list retrieved successfully', [
                'total_users' => $users->count(),
                'timestamp' => now()->toDateTimeString(),
            ]);

            return response()->json([
                'message' => 'Success',
                'data' => $users,
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            LoggerService::error('Error getting users: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error getting information',
                'data' => null,
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function listDrivers()
    {
        try {
            $drivers = Driver::orderByDesc('created_at')->get();

            LoggerService::info('Driver list retrieved successfully', [
                'total_drivers' => $drivers->count(),
                'timestamp' => now()->toDateTimeString(),
            ]);

            return response()->json([
                'message' => 'Success',
                'data' => $drivers,
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            LoggerService::error('Error getting drivers: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error getting information',
                'data' => null,
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

        public function listVehicles()
    {
        try {
            $vehicles = Vehicle::orderByDesc('created_at')->get();

            LoggerService::info('Vehicle list retrieved successfully', [
                'total_vehicles' => $vehicles->count(),
                'timestamp' => now()->toDateTimeString(),
            ]);

            return response()->json([
                'message' => 'Success',
                'data' => $vehicles,
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            LoggerService::error('Error getting vehicles: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error getting information',
                'data' => null,
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function listRoutesToday()
    {
        try {
            $today = Carbon::today()->toDateString();
            $routes = Route::whereDate('route_date', $today)
               ->orderByDesc('created_at')
               ->get();

            LoggerService::info('Routes retrieved for today', [
                'date' => $today,
                'total_routes' => $routes->count(),
                'timestamp' => now()->toDateTimeString()
            ]);

            $data = [
                'message' => 'Success',
                'data' => $routes,
                'status' => 200
            ];

            return response()->json($data, 200);

        } catch (\Exception $e) {
            LoggerService::error('Error retrieving today\'s routes', [
                'error' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ]);

            $data = [
                'message' => 'Error getting information',
                'data' => null,
                'error' => $e->getMessage(),
                'status' => 500
            ];

            return response()->json($data, 500);
        }
    }

}
