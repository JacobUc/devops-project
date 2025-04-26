<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\UploadedFile; 
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VehicleControllerTest extends TestCase
{
    use RefreshDatabase;

    // Test para la creacin de un vehiculo
    public function test_create_vehicle()
    {
        // Crear un usuario y obtener el token (suguridad amigos)
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Craar el token de autenticacion para el usuario
        $token = $user->createToken('API Token')->plainTextToken;
        $image = UploadedFile::fake()->image('vehicle.jpg', 600, 400);

        // Los datos para crear un vehiculo, 
        $vehicleData = [
            'brand' => 'NISSAN',
            'model' => 'Versa',
            'vin' => '12345678910ZSAWFF',
            'plate_number' => 'XYZ123',
            'purchase_date' => '2024-01-02',
            'cost' => 20000,
            'photo' => $image,
            'registration_date' => '2025-02-02',
        ];

        //solicitud POST para crear el vehiculo
        $response = $this->postJson('/api/vehicles', $vehicleData, [
            'Authorization' => 'Bearer ' . $token,
        ]);
        
        // Verificar la respuesta
        $response->dump(); //lo imprimo ya que al final de la prueba no guarda la transaccion
        $response->assertStatus(201); // 201 Created
        $response->assertJsonStructure([
            'id',
            'brand',
            'model',
            'vin',
            'plate_number',
            'purchase_date',
            'cost',
            'photo',
            'registration_date'
        ]);
    }

    // Test para obtener todos los vehículos
    public function test_get_vehicles()
    {
        // Crear un usuario y obtener el token (seguridad amigos)
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Crear el token de autenticacin para el usuario
        $token = $user->createToken('API Token')->plainTextToken;

        // Crear un vehículo para la prueba
        Vehicle::factory()->create([
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'vin' => '1234567890ABCDEF',
            'plate_number' => 'XYZ123',
            'purchase_date' => '2021-01-01',
            'cost' => 20000,
            'photo' => 'image.jpg',
            'registration_date' => '2021-01-01',
        ]);

        // Hacer la solicitud GET para obtener todos los vehículos
        $response = $this->getJson('/api/vehicles', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Verificar la respuesta
        $response->assertStatus(200); // 200 OK
        $response->assertJsonCount(1); // Deberia devolver 1 vehículo
        $response->dump(); //Pero no lo hizo porque no se xd se imprime
    }
}
