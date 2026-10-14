<?php

namespace Tests\Feature;

use App\Enums\SlotStatus;
use App\Models\City;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Slot;
use App\Models\Specialty;
use App\Services\AppointmentSearch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AppointmentSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_nearby_clinics_ordered_by_earliest_free_slot(): void
    {
        $zadar = City::factory()->create(['name' => 'Zadar', 'lat' => 44.1194, 'lng' => 15.2314]);
        $ortoped = Specialty::factory()->create(['name' => 'Ortoped', 'slug' => 'ortoped']);
        $kardiolog = Specialty::factory()->create(['name' => 'Kardiolog', 'slug' => 'kardiolog']);

        $at8 = Carbon::tomorrow()->setTime(8, 0);
        $at9 = Carbon::tomorrow()->setTime(9, 0);

        // In radius, Zadar centre, free @09:00.
        $this->bookableDoctor($ortoped, 44.1194, 15.2314, $at9);
        // In radius, Biograd ~26 km, free @08:00 -> should sort first.
        $biograd = $this->bookableDoctor($ortoped, 43.9389, 15.4439, $at8);
        // Out of radius (Split ~110 km), even earlier -> excluded.
        $this->bookableDoctor($ortoped, 43.5081, 16.4402, Carbon::tomorrow()->setTime(7, 0));
        // Right place/specialty but slot is booked -> excluded.
        $this->bookableDoctor($ortoped, 44.1194, 15.2314, $at8, SlotStatus::Booked);
        // Right place but wrong specialty -> excluded.
        $this->bookableDoctor($kardiolog, 44.1194, 15.2314, $at8);
        // Right place/specialty but clinic inactive -> excluded.
        $this->bookableDoctor($ortoped, 44.1194, 15.2314, $at8, active: false);

        $results = app(AppointmentSearch::class)->results($zadar->id, $ortoped->id, days: 3, radiusKm: 30);

        $this->assertCount(2, $results);
        $this->assertSame($biograd->id, (int) $results->first()->doctor_id);
        $this->assertEqualsWithDelta(26.0, (float) $results->first()->distance_km, 2.0);
    }

    public function test_respects_the_day_window(): void
    {
        $zadar = City::factory()->create(['name' => 'Zadar', 'lat' => 44.1194, 'lng' => 15.2314]);
        $ortoped = Specialty::factory()->create(['name' => 'Ortoped', 'slug' => 'ortoped']);

        $this->bookableDoctor($ortoped, 44.1194, 15.2314, Carbon::now()->addDays(10)->setTime(9, 0));

        $results = app(AppointmentSearch::class)->results($zadar->id, $ortoped->id, days: 3, radiusKm: 30);

        $this->assertCount(0, $results);
    }

    private function bookableDoctor(
        Specialty $specialty,
        float $lat,
        float $lng,
        Carbon $slotStart,
        SlotStatus $status = SlotStatus::Free,
        bool $active = true,
    ): Doctor {
        $factory = Clinic::factory()->at($lat, $lng);
        $clinic = ($active ? $factory : $factory->inactive())->create();

        $doctor = Doctor::factory()->for($clinic)->create();
        $doctor->specialties()->attach($specialty);

        Slot::factory()->for($clinic)->for($doctor)->startingAt($slotStart)
            ->state(['status' => $status])->create();

        return $doctor;
    }
}
