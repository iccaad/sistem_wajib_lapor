<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the attendance_logs table — stores successful check-ins.
     * Each log records the GPS coordinates, distance from location,
     * selfie photo path, and links to the participant, period, and location.
     */
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();

            // Link to participant — cascade delete
            $table->foreignId('participant_id')->constrained('participants')->onDelete('cascade');

            // Link to attendance period — nullable, set null if period deleted
            $table->unsignedBigInteger('attendance_period_id')->nullable();
            $table->foreign('attendance_period_id')
                  ->references('id')
                  ->on('attendance_periods')
                  ->onDelete('set null');

            // Link to location — nullable, set null if location deleted
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')
                  ->references('id')
                  ->on('locations')
                  ->onDelete('set null');

            // Attendance date and time — separate for easy date-based queries
            $table->date('attendance_date');
            $table->time('attendance_time');

            // GPS coordinates captured at check-in time
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // Calculated distance from the location center (Haversine result)
            $table->decimal('distance_meters', 8, 2)->nullable();

            // Selfie photo path (stored in private disk)
            $table->string('photo_path', 500)->nullable();

            // Optional notes from participant or admin
            $table->text('notes')->nullable();

            // Attendance validity status
            $table->string('status', 20)->default('valid');

            $table->timestamps();

            // Prevent duplicate same-day attendance per participant
            $table->unique(['participant_id', 'attendance_date'], 'attendance_logs_unique_daily');

            // Index for date-range queries
            $table->index('attendance_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
