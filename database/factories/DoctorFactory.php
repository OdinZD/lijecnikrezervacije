<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'name' => 'dr. '.fake()->name(),
            'title' => 'dr. med. spec.',
        ];
    }
}
