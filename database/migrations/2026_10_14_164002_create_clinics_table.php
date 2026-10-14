<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('oib', 11)->nullable(); // Croatian tax/business ID
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->string('address')->nullable();
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['lat', 'lng']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX clinics_name_trgm_idx ON clinics USING gin (f_unaccent(name) gin_trgm_ops)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};
