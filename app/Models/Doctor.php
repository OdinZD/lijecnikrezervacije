<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\DoctorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    /** @use HasFactory<DoctorFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = ['clinic_id', 'name', 'title'];

    /** @return BelongsToMany<Specialty, $this> */
    public function specialties(): BelongsToMany
    {
        return $this->belongsToMany(Specialty::class);
    }

    /** @return HasMany<Slot, $this> */
    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class);
    }
}
