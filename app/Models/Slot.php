<?php

namespace App\Models;

use App\Enums\SlotStatus;
use App\Models\Concerns\BelongsToTenant;
use Database\Factories\SlotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slot extends Model
{
    /** @use HasFactory<SlotFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = ['clinic_id', 'doctor_id', 'start_time', 'end_time', 'status'];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'status' => SlotStatus::class,
        ];
    }

    /** @return BelongsTo<Doctor, $this> */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
