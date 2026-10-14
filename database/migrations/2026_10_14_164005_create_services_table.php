<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('specialty_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. "Prvi pregled ortopeda"
            $table->unsignedSmallInteger('duration_min')->default(30);
            $table->decimal('price_eur', 8, 2)->nullable();
            $table->timestamps();

            $table->index('clinic_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
