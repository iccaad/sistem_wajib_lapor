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
     * Creates the participants table — stores supervision details for each
     * peserta. Every participant is linked to a user account (1:1) and
     * optionally assigned to an admin for oversight.
     */
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();

            // Link to user account — cascade delete removes participant when user is deleted
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Admin responsible for this participant — nullable, set null if admin deleted
            $table->unsignedBigInteger('assigned_admin_id')->nullable();
            $table->foreign('assigned_admin_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            // Personal information
            $table->string('full_name', 255);
            $table->char('nik', 16)->index();  // Indexed for fast lookup, not unique here (unique is on users.nik)
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();

            // Violation and case details
            $table->string('violation_type', 255);
            $table->text('case_notes')->nullable();

            // Supervision period
            $table->date('supervision_start');
            $table->date('supervision_end');

            // Attendance quota configuration
            $table->string('quota_type', 10);  // 'weekly' or 'monthly'
            $table->integer('quota_amount');     // Required attendance count per period

            // Participant status
            $table->string('status', 20)->default('active');

            $table->timestamps();
        });

        // Add PostgreSQL CHECK constraint for quota_type values
        DB::statement("ALTER TABLE participants ADD CONSTRAINT participants_quota_type_check CHECK (quota_type IN ('weekly', 'monthly'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
