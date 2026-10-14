<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // "ORT" -> Ortoped; trigram handles both prefix and fuzzy matches.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX specialties_name_trgm_idx ON specialties USING gin (f_unaccent(name) gin_trgm_ops)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('specialties');
    }
};
