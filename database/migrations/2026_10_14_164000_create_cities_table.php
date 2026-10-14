<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('postal_code', 10)->nullable();
            $table->string('county')->nullable(); // županija
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->timestamps();

            // Bounding-box prefilter for the haversine radius query.
            $table->index(['lat', 'lng']);
        });

        // Diacritic-insensitive prefix autocomplete ("Za" -> Zadar, "Dakovo" -> Đakovo).
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX cities_name_trgm_idx ON cities USING gin (f_unaccent(name) gin_trgm_ops)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
