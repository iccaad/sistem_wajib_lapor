<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the attendance_attempts table — records every failed or
     * rejected attendance check-in attempt. Used for rate limiting,
     * security auditing, and identifying patterns of misuse.
     *
     * Examples: outside radius, already checked in, quota full,
     * inactive participant, invalid geolocation, photo missing.
     */
    public function up(): void
    {
        Schema::create('attendance_attempts', function (Blueprint $table) {
            $table->id();

            // Link to participant — nullable, set null if participant deleted
            $table->unsignedBigInteger('participant_id')->nullable();
            $table->foreign('participant_id')
                  ->references('id')
                  ->on('participants')
                  ->onDelete('set null');

            // Link to location they attempted to check in at
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')
                  ->references('id')
                  ->on('locations')
                  ->onDelete('set null');

            // When the attempt was made
            $table->timestamp('attempted_at');

            // GPS coordinates at time of attempt
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Calculated distance from location center
            $table->decimal('distance_meters', 8, 2)->nullable();

            // Why the attempt was rejected
            $table->string('failure_reason', 255);

            // Additional structured data (e.g., validation errors, device info)
            $table->json('metadata')->nullable();

            // Security tracking
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Indexes for efficient querying
            $table->index('participant_id');
            $table->index('attempted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_attempts');
    }
};
