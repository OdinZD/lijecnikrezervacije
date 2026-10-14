<?php

namespace Tests\Feature;

use App\Enums\SlotStatus;
use App\Models\City;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Slot;
use App\Models\Specialty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class SearchWizardTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_search_flow_surfaces_a_nearby_free_slot(): void
    {
        $zadar = City::factory()->create(['name' => 'Zadar', 'lat' => 44.1194, 'lng' => 15.2314]);
        $ortoped = Specialty::factory()->create(['name' => 'Ortoped', 'slug' => 'ortoped']);

        $clinic = Clinic::factory()->at(44.1150, 15.2270)->create(['name' => 'Poliklinika Jadran']);
        $doctor = Doctor::factory()->for($clinic)->create(['name' => 'dr. Ivan Horvat']);
        $doctor->specialties()->attach($ortoped);
        Slot::factory()->for($clinic)->for($doctor)
            ->startingAt(Carbon::tomorrow()->setTime(8, 0))->create();

        Livewire::test('search-wizard')
            ->set('cityQuery', 'Zad')
            ->assertSee('Zadar')
            ->call('selectCity', $zadar->id)
            ->assertSet('cityId', $zadar->id)
            ->set('specialtyQuery', 'ort')
            ->assertSee('Ortoped')
            ->call('selectSpecialty', $ortoped->id)
            ->assertSet('specialtyId', $ortoped->id)
            ->call('chooseWindow', 3)
            ->assertSee('Poliklinika Jadran')
            ->assertSee('dr. Ivan Horvat');
    }

    public function test_specialty_bar_stays_locked_until_a_city_is_chosen(): void
    {
        Livewire::test('search-wizard')
            ->assertSet('cityId', null)
            ->set('specialtyQuery', 'ort')
            ->assertCount('specialtySuggestions', 0);
    }

    public function test_guest_can_request_a_slot_through_the_wizard(): void
    {
        $zadar = City::factory()->create(['name' => 'Zadar', 'lat' => 44.1194, 'lng' => 15.2314]);
        $ortoped = Specialty::factory()->create(['name' => 'Ortoped', 'slug' => 'ortoped']);
        $clinic = Clinic::factory()->at(44.1150, 15.2270)->create(['name' => 'Poliklinika Jadran']);
        $doctor = Doctor::factory()->for($clinic)->create(['name' => 'dr. Ivan Horvat']);
        $doctor->specialties()->attach($ortoped);
        $slot = Slot::factory()->for($clinic)->for($doctor)
            ->startingAt(Carbon::tomorrow()->setTime(8, 0))->create();

        Livewire::test('search-wizard')
            ->call('selectCity', $zadar->id)
            ->call('selectSpecialty', $ortoped->id)
            ->call('chooseWindow', 3)
            ->call('showSlots', $doctor->id)
            ->call('chooseSlot', $slot->id)
            ->set('patientName', 'Ana Anić')
            ->set('patientPhone', '0915556677')
            ->call('submitBooking')
            ->assertHasNoErrors()
            ->assertSee('Zahtjev poslan');

        $this->assertSame(SlotStatus::Held, $slot->fresh()->status);
        $this->assertDatabaseHas('appointment_requests', [
            'slot_id' => $slot->id,
            'status' => 'requested',
        ]);
    }
}
