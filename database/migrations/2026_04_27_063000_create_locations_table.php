<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the locations table — official check-in points where
     * participants must physically be present to complete attendance.
     * Each location defines a GPS center point and a radius (meters)
     * used for Haversine distance validation.
     */
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();

            $table->string('name', 255);
            $table->text('address')->nullable();

            // GPS coordinates — decimal(10,7) supports ±999.9999999
            // which covers all valid lat (-90 to 90) and lng (-180 to 180)
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // Geofence radius in meters — used for Haversine validation
            $table->integer('radius_meters')->default(100);

            // Soft toggle to disable locations without deleting
            $table->boolean('is_active')->default(true);

            // Track which admin created this location
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->timestamps();

            // Composite index for geospatial queries
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
