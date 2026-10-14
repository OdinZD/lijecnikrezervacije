<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city(),
            'postal_code' => fake()->numerify('#####'),
            'county' => 'Zadarska',
            // Roughly within Croatia's bounding box; precise coords come from seeders.
            'lat' => fake()->randomFloat(7, 42.5, 46.5),
            'lng' => fake()->randomFloat(7, 13.5, 19.0),
        ];
    }
}
