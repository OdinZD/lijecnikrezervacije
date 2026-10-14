<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Specialty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutocompleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_city_prefix_match_is_ordered_and_scoped(): void
    {
        City::factory()->create(['name' => 'Zadar']);
        City::factory()->create(['name' => 'Zagreb']);
        City::factory()->create(['name' => 'Split']);

        $names = City::startingWith('Za')->pluck('name')->all();

        $this->assertSame(['Zadar', 'Zagreb'], $names);
    }

    public function test_city_match_is_diacritic_insensitive(): void
    {
        City::factory()->create(['name' => 'Đakovo']);

        $this->assertSame(['Đakovo'], City::startingWith('Dako')->pluck('name')->all());
        $this->assertSame(['Đakovo'], City::startingWith('Đako')->pluck('name')->all());
    }

    public function test_empty_or_unmatched_city_term_yields_nothing(): void
    {
        City::factory()->create(['name' => 'Zadar']);

        $this->assertEmpty(City::startingWith('')->get());
        $this->assertEmpty(City::startingWith('   ')->get());
        $this->assertEmpty(City::startingWith('xyzzy')->get());
    }

    public function test_specialty_matches_prefix_and_substring(): void
    {
        Specialty::factory()->create(['name' => 'Ortoped', 'slug' => 'ortoped']);
        Specialty::factory()->create(['name' => 'Kardiolog', 'slug' => 'kardiolog']);

        $this->assertSame(['Ortoped'], Specialty::matching('ort')->pluck('name')->all());
        $this->assertSame(['Ortoped'], Specialty::matching('oped')->pluck('name')->all());
        $this->assertEmpty(Specialty::matching('xyz')->get());
    }
}
