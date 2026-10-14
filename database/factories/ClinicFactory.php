<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Clinic>
 */
class ClinicFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Poliklinika '.fake()->lastName(),
            'oib' => fake()->numerify('###########'),
            'city_id' => City::factory(),
            'address' => fake()->streetAddress(),
            'lat' => fake()->randomFloat(7, 42.5, 46.5),
            'lng' => fake()->randomFloat(7, 13.5, 19.0),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    /** Place the clinic at an explicit coordinate (for radius tests). */
    public function at(float $lat, float $lng): static
    {
        return $this->state(fn () => ['lat' => $lat, 'lng' => $lng]);
    }
}
