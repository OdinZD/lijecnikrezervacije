<?php

namespace App\Models;

use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email'];

    /** @return HasMany<AppointmentRequest, $this> */
    public function appointmentRequests(): HasMany
    {
        return $this->hasMany(AppointmentRequest::class);
    }
}
