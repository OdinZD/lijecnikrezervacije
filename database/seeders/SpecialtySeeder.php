<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpecialtySeeder extends Seeder
{
    /**
     * The specialties patients most commonly seek in Croatian private care.
     * Dentistry and physiotherapy are deliberately included — they are among
     * the highest-volume private categories.
     *
     * @var list<string>
     */
    private array $specialties = [
        'Stomatolog',
        'Ortoped',
        'Kardiolog',
        'Dermatolog',
        'Ginekolog',
        'Neurolog',
        'Oftalmolog',
        'Otorinolaringolog',
        'Urolog',
        'Pedijatar',
        'Psihijatar',
        'Psiholog',
        'Radiolog',
        'Fizijatar',
        'Fizioterapeut',
        'Endokrinolog',
        'Gastroenterolog',
        'Internist',
        'Reumatolog',
        'Pulmolog',
        'Alergolog',
        'Nefrolog',
        'Plastični kirurg',
        'Opći kirurg',
        'Nutricionist',
        'Logoped',
    ];

    public function run(): void
    {
        foreach ($this->specialties as $name) {
            Specialty::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }
    }
}
