<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand' => $this->faker->company,
            'model' => $this->faker->word,
            'vin' => $this->faker->unique()->lexify('???????'),
            'plate_number' => $this->faker->unique()->lexify('???-???'),
            'purchase_date' => $this->faker->date(),
            'cost' => $this->faker->randomFloat(2, 10000, 50000),
            'photo' => $this->faker->imageUrl(),
        ];
    }
}
