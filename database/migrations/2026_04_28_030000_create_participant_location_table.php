<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the participant_location pivot table.
     *
     * Each participant is assigned specific locations where they must report.
     * The number of assigned locations must match the participant's quota_amount.
     * Each location is tied to a specific check-in order within the period:
     *   - check_in_order=1 → location for the 1st attendance of the period
     *   - check_in_order=2 → location for the 2nd attendance of the period
     *   - etc.
     */
    public function up(): void
    {
        Schema::create('participant_location', function (Blueprint $table) {
            $table->id();

            $table->foreignId('participant_id')
                ->constrained('participants')
                ->onDelete('cascade');

            $table->foreignId('location_id')
                ->constrained('locations')
                ->onDelete('cascade');

            $table->unsignedSmallInteger('check_in_order')
                ->comment('Sequence number: 1st check-in, 2nd check-in, etc.');

            $table->timestamps();

            // Each participant has exactly one location per check-in order
            $table->unique(['participant_id', 'check_in_order'], 'participant_checkin_order_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_location');
    }
};
