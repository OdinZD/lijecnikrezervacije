<?php

namespace App\Models;

use App\Enums\AppointmentRequestStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentRequest extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'clinic_id', 'slot_id', 'patient_id', 'status', 'note', 'requested_at', 'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AppointmentRequestStatus::class,
            'requested_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Slot, $this> */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    /** @return BelongsTo<Patient, $this> */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
