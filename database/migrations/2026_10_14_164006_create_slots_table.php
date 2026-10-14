<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->timestampTz('start_time');
            $table->timestampTz('end_time');
            $table->string('status')->default('free'); // free | held | booked
            $table->timestamps();

            $table->index('clinic_id');
        });

        // The hot path: "earliest free slot per doctor within N days". Partial
        // index keeps it tiny and index-only as booked/held slots accumulate.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("CREATE INDEX slots_free_idx ON slots (doctor_id, start_time) WHERE status = 'free'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
