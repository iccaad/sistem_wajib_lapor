<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the attendance_periods table — defines weekly or monthly
     * quota windows for each participant. The system auto-generates
     * these based on participant supervision dates and quota configuration.
     */
    public function up(): void
    {
        Schema::create('attendance_periods', function (Blueprint $table) {
            $table->id();

            // Link to participant — cascade delete removes periods when participant is deleted
            $table->foreignId('participant_id')->constrained('participants')->onDelete('cascade');

            // Period type matches participant's quota_type
            $table->string('period_type', 10);  // 'weekly' or 'monthly'

            // Date window for this period
            $table->date('period_start');
            $table->date('period_end');

            // Quota tracking
            $table->integer('target_count');          // Required attendance count
            $table->integer('attended_count')->default(0);  // Actual attendance count

            // Period status
            $table->string('status', 20)->default('active');

            $table->timestamps();

            // Prevent duplicate periods for the same participant and date range
            $table->unique(['participant_id', 'period_start', 'period_end'], 'attendance_periods_unique_range');
        });

        // Add PostgreSQL CHECK constraint for period_type values
        DB::statement("ALTER TABLE attendance_periods ADD CONSTRAINT attendance_periods_type_check CHECK (period_type IN ('weekly', 'monthly'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_periods');
    }
};
