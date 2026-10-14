<?php

namespace Tests\Feature;

use App\Enums\AppointmentRequestStatus;
use App\Enums\SlotStatus;
use App\Exceptions\SlotUnavailableException;
use App\Models\Slot;
use App\Services\RequestAppointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestAppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_requesting_a_free_slot_holds_it_and_records_the_request(): void
    {
        $slot = Slot::factory()->create(); // free by default; also creates clinic + doctor

        $appointment = app(RequestAppointment::class)(
            $slot->id, 'Ivana Horvat', '0911234567', 'ivana@example.com', 'Bol u koljenu',
        );

        $this->assertSame(SlotStatus::Held, $slot->fresh()->status);
        $this->assertSame(AppointmentRequestStatus::Requested, $appointment->status);
        $this->assertSame($slot->clinic_id, $appointment->clinic_id);
        $this->assertDatabaseHas('patients', ['phone' => '0911234567', 'name' => 'Ivana Horvat']);
        $this->assertDatabaseCount('appointment_requests', 1);
    }

    public function test_requesting_an_already_booked_slot_is_rejected(): void
    {
        $slot = Slot::factory()->booked()->create();

        $this->expectException(SlotUnavailableException::class);

        app(RequestAppointment::class)($slot->id, 'Tko God', '0917654321');
    }

    public function test_second_request_for_the_same_slot_loses(): void
    {
        $slot = Slot::factory()->create();
        $service = app(RequestAppointment::class);

        $service($slot->id, 'Prvi Pacijent', '0911111111');

        $this->expectException(SlotUnavailableException::class);
        $service($slot->id, 'Drugi Pacijent', '0922222222');
    }
}
