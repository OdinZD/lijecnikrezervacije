<?php

namespace App\Models;

use Database\Factories\SpecialtyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Specialty extends Model
{
    /** @use HasFactory<SpecialtyFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    /** @return BelongsToMany<Doctor, $this> */
    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class);
    }

    /**
     * Diacritic-insensitive substring match for the specialty autocomplete
     * ("ort" -> Ortoped, "oped" -> Ortoped). Backed by the f_unaccent GIN
     * trigram index for terms of 3+ characters.
     *
     * @param  Builder<Specialty>  $query
     */
    public function scopeMatching(Builder $query, string $term): void
    {
        $term = trim($term);

        if ($term === '') {
            $query->whereRaw('1 = 0'); // empty term -> no suggestions

            return;
        }

        $operator = $query->getConnection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';

        $query->whereRaw("f_unaccent(name) {$operator} '%' || f_unaccent(?) || '%'", [$term])
            ->orderBy('name');
    }
}
