<?php

namespace Database\Factories;

use App\Enums\SlotStatus;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Slot;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Slot>
 */
class SlotFactory extends Factory
{
    public function definition(): array
    {
        $start = Carbon::instance(fake()->dateTimeBetween('now', '+14 days'))
            ->setTime(fake()->numberBetween(8, 14), 0);

        return [
            'clinic_id' => Clinic::factory(),
            'doctor_id' => Doctor::factory(),
            'start_time' => $start,
            'end_time' => $start->copy()->addMinutes(30),
            'status' => SlotStatus::Free,
        ];
    }

    public function booked(): static
    {
        return $this->state(fn () => ['status' => SlotStatus::Booked]);
    }

    /** Pin the slot to a specific start instant (for earliest-slot tests). */
    public function startingAt(Carbon $start): static
    {
        return $this->state(fn () => [
            'start_time' => $start,
            'end_time' => $start->copy()->addMinutes(30),
        ]);
    }
}
