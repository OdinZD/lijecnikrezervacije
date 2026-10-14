<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'doctor_id' => Doctor::factory(),
            'specialty_id' => Specialty::factory(),
            'name' => 'Specijalistički pregled',
            'duration_min' => 30,
            'price_eur' => fake()->randomFloat(2, 30, 150),
        ];
    }
}
