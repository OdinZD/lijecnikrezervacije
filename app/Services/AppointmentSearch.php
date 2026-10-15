<?php

namespace App\Services;

use App\Enums\SlotStatus;
use App\Models\City;
use App\Support\Haversine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The third search bar: for a chosen city + specialty, find every nearby clinic
 * that still has a free slot within N days, with each doctor's earliest one.
 *
 * Shape of each row: {clinic_id, clinic, doctor_id, doctor, distance_km, earliest}.
 */
class AppointmentSearch
{
    public function __construct(
        private readonly int $defaultDays = 3,
        private readonly float $defaultRadiusKm = 30.0,
    ) {}

    public function results(int $cityId, int $specialtyId, ?int $days = null, ?float $radiusKm = null): Collection
    {
        $days = max(1, $days ?? $this->defaultDays);
        $radiusKm = $radiusKm ?? $this->defaultRadiusKm;

        $origin = City::query()->findOrFail($cityId);

        $distance = Haversine::expression($origin->lat, $origin->lng, 'clinics.lat', 'clinics.lng');
        $dLat = Haversine::latDelta($radiusKm);
        $dLng = Haversine::lngDelta($radiusKm, $origin->lat);

        // Bind the window as datetimes (portable) instead of Postgres `now() + interval`.
        $from = now();
        $to = now()->copy()->addDays($days);

        return DB::table('clinics')
            ->join('doctors', 'doctors.clinic_id', '=', 'clinics.id')
            ->join('doctor_specialty', 'doctor_specialty.doctor_id', '=', 'doctors.id')
            ->join('slots', function ($join) use ($from, $to) {
                $join->on('slots.doctor_id', '=', 'doctors.id')
                    ->where('slots.status', SlotStatus::Free->value)
                    ->whereBetween('slots.start_time', [$from, $to]);
            })
            ->where('doctor_specialty.specialty_id', $specialtyId)
            ->where('clinics.is_active', true)
            // Bounding-box prefilter (uses the B-tree index) ...
            ->whereBetween('clinics.lat', [$origin->lat - $dLat, $origin->lat + $dLat])
            ->whereBetween('clinics.lng', [$origin->lng - $dLng, $origin->lng + $dLng])
            // ... then the exact great-circle cut.
            ->whereRaw("({$distance}) <= ?", [$radiusKm])
            ->groupBy('clinics.id', 'doctors.id')
            // No round()/::numeric here — the Blade formats distance_km to 1 decimal,
            // and 2-arg round + ::numeric are Postgres-only.
            ->selectRaw(
                'clinics.id as clinic_id, clinics.name as clinic, '
                .'doctors.id as doctor_id, doctors.name as doctor, '
                ."({$distance}) as distance_km, "
                .'min(slots.start_time) as earliest'
            )
            ->orderBy('earliest')
            ->orderBy('distance_km')
            ->get();
    }
}
