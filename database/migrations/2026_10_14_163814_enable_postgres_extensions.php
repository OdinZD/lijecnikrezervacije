<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * pg_trgm powers the diacritic-insensitive autocomplete (GIN trigram
     * indexes on city/specialty/clinic names); unaccent normalises č/ć/đ/š/ž
     * so "Dakovo" matches "Đakovo". Guarded so the suite stays no-op on
     * non-Postgres drivers (e.g. the sqlite test fallback).
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        DB::statement('CREATE EXTENSION IF NOT EXISTS unaccent');

        // unaccent() is only STABLE, so Postgres refuses it inside a functional
        // (trigram) index. This IMMUTABLE wrapper pins the dictionary by name and
        // lets Phase 1 build GIN indexes on f_unaccent(name).
        DB::statement(<<<'SQL'
            CREATE OR REPLACE FUNCTION f_unaccent(text)
            RETURNS text
            LANGUAGE sql
            IMMUTABLE PARALLEL SAFE STRICT
            AS $$ SELECT public.unaccent('public.unaccent', $1) $$
        SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP FUNCTION IF EXISTS f_unaccent(text)');
        DB::statement('DROP EXTENSION IF EXISTS unaccent');
        DB::statement('DROP EXTENSION IF EXISTS pg_trgm');
    }
};
