<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Vehicle;
use Illuminate\Http\UploadedFile; 
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VehicleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_vehicle()
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('vehicle.jpg', 600, 400);

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

        // Enviar solicitud como multipart para incluir archivo
        $response = $this->post('/api/vehicles', $vehicleData);

        $response->dump();
        $response->assertStatus(201);
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

        // Verifica que se haya guardado la imagen
        Storage::disk('public')->assertExists($response['photo']);
    }

    public function test_get_vehicles()
    {
        Vehicle::factory()->create([
            'brand' => 'NISSAN',
            'model' => 'Versa',
            'vin' => '12345678910sdfqw',
            'plate_number' => 'XYZ123',
            'purchase_date' => '2021-01-01',
            'cost' => 20000,
            'photo' => 'photos/fake.jpg',
            'registration_date' => '2025-02-02',
        ]);

        $response = $this->get('/api/vehicles');

        $response->dump();
        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }
}
