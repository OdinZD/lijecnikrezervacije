<?php

namespace App\Models;

use Database\Factories\CityFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    /** @use HasFactory<CityFactory> */
    use HasFactory;

    protected $fillable = ['name', 'postal_code', 'county', 'lat', 'lng'];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
        ];
    }

    /** @return HasMany<Clinic, $this> */
    public function clinics(): HasMany
    {
        return $this->hasMany(Clinic::class);
    }

    /**
     * Diacritic-insensitive prefix match for the city autocomplete ("Za" ->
     * Zadar, "Dako" -> Đakovo). Backed by the f_unaccent GIN trigram index
     * for terms of 3+ characters.
     *
     * @param  Builder<City>  $query
     */
    public function scopeStartingWith(Builder $query, string $term): void
    {
        $term = trim($term);

        if ($term === '') {
            $query->whereRaw('1 = 0'); // empty term -> no suggestions

            return;
        }

        $query->whereRaw("f_unaccent(name) ILIKE f_unaccent(?) || '%'", [$term])
            ->orderBy('name');
    }
}
