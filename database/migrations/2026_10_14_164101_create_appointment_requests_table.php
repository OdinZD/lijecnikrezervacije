<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('slot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('requested'); // requested|confirmed|declined|cancelled
            $table->text('note')->nullable();
            $table->timestampTz('requested_at');
            $table->timestampTz('responded_at')->nullable();
            $table->timestamps();

            // 4b dashboard lists pending requests per clinic.
            $table->index(['clinic_id', 'status']);
            $table->index('slot_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_requests');
    }
};
