<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('title')->nullable(); // e.g. "dr. med. spec."
            $table->timestamps();

            // Postgres does not auto-index FK columns; the radius query joins on it.
            $table->index('clinic_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
