<?php

namespace App\Services;

use App\Enums\AppointmentRequestStatus;
use App\Enums\SlotStatus;
use App\Exceptions\SlotUnavailableException;
use App\Models\AppointmentRequest;
use App\Models\Patient;
use App\Models\Slot;
use Illuminate\Support\Facades\DB;

/**
 * Turns a guest's slot pick into a held appointment request. The hold is the
 * concurrency-critical bit: we lock the slot row (SELECT ... FOR UPDATE) and
 * re-check its status inside the transaction, so two patients racing for the
 * same slot can't both hold it — the loser gets a SlotUnavailableException.
 */
class RequestAppointment
{
    public function __invoke(
        int $slotId,
        string $name,
        string $phone,
        ?string $email = null,
        ?string $note = null,
    ): AppointmentRequest {
        return DB::transaction(function () use ($slotId, $name, $phone, $email, $note) {
            $slot = Slot::query()->whereKey($slotId)->lockForUpdate()->first();

            if ($slot === null || $slot->status !== SlotStatus::Free) {
                throw SlotUnavailableException::make();
            }

            $patient = Patient::query()->updateOrCreate(
                ['phone' => $phone],
                ['name' => $name, 'email' => $email],
            );

            $slot->update(['status' => SlotStatus::Held]);

            return AppointmentRequest::query()->create([
                'clinic_id' => $slot->clinic_id,
                'slot_id' => $slot->id,
                'patient_id' => $patient->id,
                'status' => AppointmentRequestStatus::Requested,
                'note' => $note,
                'requested_at' => now(),
            ]);
        });
    }
}
