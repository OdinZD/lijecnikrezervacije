<?php

namespace Database\Seeders;

use App\Enums\SlotStatus;
use App\Models\City;
use App\Models\Clinic;
use App\Models\Slot;
use App\Models\Specialty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ClinicSeeder extends Seeder
{
    /** Latin-ish generic names — deliberately NOT real clinic brands. */
    private array $clinicNames = [
        'Vita', 'Salus', 'Sanus', 'Vitalis', 'Panacea', 'Medicus', 'Arsmedica',
        'Aesculap', 'Cor', 'Novamed', 'Medica', 'Hipokrat', 'Galen', 'Lumen',
        'Sanitas', 'Benessere', 'Alma', 'Nova Vita', 'Praxis', 'Remedia',
    ];

    private array $firstNames = [
        'Ivan', 'Marko', 'Luka', 'Josip', 'Ante', 'Tomislav', 'Nikola', 'Petar', 'Hrvoje',
        'Dario', 'Mate', 'Karlo', 'Ivana', 'Ana', 'Petra', 'Maja', 'Marija', 'Martina',
        'Sanja', 'Nikolina', 'Lucija', 'Dora', 'Iva', 'Tea',
    ];

    private array $lastNames = [
        'Horvat', 'Kovačević', 'Babić', 'Marić', 'Jurić', 'Novak', 'Kovač', 'Knežević',
        'Vuković', 'Marković', 'Petrović', 'Matić', 'Tomić', 'Pavlović', 'Blažević',
        'Grgić', 'Perić', 'Barišić', 'Lovrić', 'Vidović', 'Filipović', 'Bošnjak',
    ];

    /** Guaranteed coverage in the four biggest cities. */
    private array $coreSpecialties = [
        'Stomatolog', 'Ortoped', 'Kardiolog', 'Dermatolog', 'Ginekolog',
        'Oftalmolog', 'Otorinolaringolog', 'Pedijatar', 'Neurolog', 'Urolog',
    ];

    public function run(): void
    {
        /** @var Collection<string, int> $specialtyId */
        $specialtyId = Specialty::query()->pluck('id', 'name');
        /** @var Collection<string, City> $cities */
        $cities = City::query()->get(['id', 'name', 'lat', 'lng'])->keyBy('name');

        DB::transaction(function () use ($specialtyId, $cities) {
            $this->seedZadarRegion($specialtyId, $cities);
            $this->seedMajorCities($specialtyId, $cities);
        });
    }

    /** The original hand-curated demo: two orthopaedists (Zadar + Biograd). */
    private function seedZadarRegion(Collection $specialtyId, Collection $cities): void
    {
        $blueprint = [
            ['Poliklinika Jadran', 'Zadar', 44.1150, 15.2270, [
                ['dr. Ivan Horvat', ['Ortoped']],
                ['dr. Ana Kovač', ['Kardiolog']],
            ]],
            ['Poliklinika Sveti Donat', 'Zadar', 44.1080, 15.2320, [
                ['dr. Marko Perić', ['Dermatolog']],
                ['dr. Petra Novak', ['Ginekolog']],
            ]],
            ['Ordinacija dr. Marić', 'Zadar', 44.1205, 15.2255, [
                ['dr. Josip Marić', ['Ortoped']],
            ]],
            ['Poliklinika Biograd', 'Biograd na Moru', 43.9390, 15.4440, [
                ['dr. Luka Babić', ['Ortoped']],
                ['dr. Maja Vuković', ['Pedijatar']],
            ]],
            ['Poliklinika Benkovac', 'Benkovac', 44.0370, 15.6120, [
                ['dr. Nikola Jurić', ['Kardiolog']],
                ['dr. Ivana Matić', ['Neurolog']],
            ]],
            ['Ordinacija Nin', 'Nin', 44.2410, 15.1780, [
                ['dr. Tomislav Knežević', ['Dermatolog']],
            ]],
        ];

        foreach ($blueprint as [$name, $cityName, $lat, $lng, $doctors]) {
            $clinic = $this->makeClinic($name, $cities[$cityName]->id, $lat, $lng);

            foreach ($doctors as [$doctorName, $specialtyNames]) {
                $ids = array_map(fn ($n) => $specialtyId[$n], $specialtyNames);
                $this->addDoctor($clinic, $doctorName, $ids, $specialtyNames, $specialtyId);
            }
        }
    }

    private function seedMajorCities(Collection $specialtyId, Collection $cities): void
    {
        $skip = [
            'Zadar', 'Biograd na Moru', 'Benkovac', 'Nin', 'Pag', 'Obrovac', 'Sukošan',
            'Sveti Filip i Jakov', 'Privlaka', 'Posedarje', 'Starigrad', 'Pakoštane', 'Vir',
        ];
        $tier5 = ['Zagreb', 'Split', 'Rijeka', 'Osijek'];
        $tier3 = [
            'Pula', 'Dubrovnik', 'Varaždin', 'Karlovac', 'Slavonski Brod', 'Šibenik',
            'Vinkovci', 'Sisak', 'Velika Gorica', 'Vukovar', 'Bjelovar', 'Koprivnica', 'Čakovec',
        ];
        $allNames = $specialtyId->keys()->all();

        foreach ($cities as $name => $city) {
            if (in_array($name, $skip, true)) {
                continue;
            }

            $clinicCount = in_array($name, $tier5, true) ? 5 : (in_array($name, $tier3, true) ? 3 : 2);

            // Flagship clinic in the big four guarantees the common specialties exist.
            if (in_array($name, $tier5, true)) {
                $flagship = $this->makeClinic('Poliklinika Lumen', $city->id, (float) $city->lat, (float) $city->lng);
                foreach ($this->coreSpecialties as $specialtyName) {
                    $this->addDoctor($flagship, $this->randomDoctorName(), [$specialtyId[$specialtyName]], [$specialtyName], $specialtyId);
                }
                $clinicCount--;
            }

            for ($i = 0; $i < $clinicCount; $i++) {
                $clinic = $this->makeClinic(
                    $this->randomClinicName(),
                    $city->id,
                    (float) $city->lat + $this->jitter(),
                    (float) $city->lng + $this->jitter(),
                );

                $doctorCount = random_int(2, 5);
                for ($d = 0; $d < $doctorCount; $d++) {
                    $picked = collect($allNames)->random(random_int(1, 2))->all();
                    $ids = array_map(fn ($n) => $specialtyId[$n], $picked);
                    $this->addDoctor($clinic, $this->randomDoctorName(), $ids, $picked, $specialtyId);
                }
            }
        }
    }

    private function makeClinic(string $name, int $cityId, float $lat, float $lng): Clinic
    {
        return Clinic::query()->create([
            'name' => $name,
            'oib' => fake()->numerify('###########'),
            'city_id' => $cityId,
            'address' => fake()->streetAddress(),
            'lat' => $lat,
            'lng' => $lng,
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'is_active' => true,
        ]);
    }

    /**
     * @param  list<int>  $specialtyIds
     * @param  list<string>  $specialtyNames
     * @param  Collection<string, int>  $specialtyId
     */
    private function addDoctor(Clinic $clinic, string $name, array $specialtyIds, array $specialtyNames, Collection $specialtyId): void
    {
        $doctor = $clinic->doctors()->create(['name' => $name, 'title' => 'dr. med. spec.']);
        $doctor->specialties()->attach($specialtyIds);

        foreach ($specialtyNames as $specialtyName) {
            $clinic->services()->create([
                'doctor_id' => $doctor->id,
                'specialty_id' => $specialtyId[$specialtyName],
                'name' => "Pregled: {$specialtyName}",
                'duration_min' => 30,
                'price_eur' => fake()->randomFloat(2, 40, 120),
            ]);
        }

        $this->seedSlots($clinic->id, $doctor->id);
    }

    private function randomClinicName(): string
    {
        return random_int(1, 10) <= 7
            ? 'Poliklinika '.$this->clinicNames[array_rand($this->clinicNames)]
            : 'Ordinacija dr. '.$this->lastNames[array_rand($this->lastNames)];
    }

    private function randomDoctorName(): string
    {
        return 'dr. '.$this->firstNames[array_rand($this->firstNames)].' '.$this->lastNames[array_rand($this->lastNames)];
    }

    /** ~±1.5 km coordinate jitter so clinics scatter around the city centre. */
    private function jitter(): float
    {
        return random_int(-150, 150) / 10000;
    }

    /**
     * Weekday 08:00–14:30 half-hour slots for the next 14 days, ~45% free.
     */
    private function seedSlots(int $clinicId, int $doctorId): void
    {
        $now = Carbon::now();
        $rows = [];

        for ($day = 0; $day < 14; $day++) {
            $date = $now->copy()->addDays($day);
            if ($date->isWeekend()) {
                continue;
            }

            for ($hour = 8; $hour <= 14; $hour++) {
                $start = $date->copy()->setTime($hour, 0, 0);
                if ($start->isBefore($now)) {
                    continue;
                }

                $rows[] = [
                    'clinic_id' => $clinicId,
                    'doctor_id' => $doctorId,
                    'start_time' => $start->toDateTimeString(),
                    'end_time' => $start->copy()->addMinutes(30)->toDateTimeString(),
                    'status' => fake()->boolean(45) ? SlotStatus::Free->value : SlotStatus::Booked->value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        Slot::query()->insert($rows);
    }
}
