<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Reference data first, then clinics/doctors/slots that depend on it.
        $this->call([
            CitySeeder::class,
            SpecialtySeeder::class,
            ClinicSeeder::class,
        ]);
    }
}
