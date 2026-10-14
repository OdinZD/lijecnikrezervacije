<?php

namespace App\Models\Concerns;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Latent multi-tenancy: the tenant IS the clinic. In this MVP (no clinic-staff
 * auth yet) this only exposes the clinic relation and an explicit scope. The
 * automatic global scope + request middleware land in Phase 4, once staff can
 * log in and every write must be pinned to their clinic.
 */
trait BelongsToTenant
{
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /** @param  Builder<covariant static>  $query */
    public function scopeForTenant(Builder $query, int $clinicId): void
    {
        $query->where($this->qualifyColumn('clinic_id'), $clinicId);
    }
}
