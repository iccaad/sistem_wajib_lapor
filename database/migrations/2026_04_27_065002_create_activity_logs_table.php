<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the activity_logs table — admin audit trail.
     * Records every significant admin action for accountability
     * and compliance reporting.
     *
     * Examples: created participant, edited participant, created warning,
     * deleted location, manual attendance override.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // The admin who performed the action — nullable, set null if deleted
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            // Action identifier (e.g., 'created_participant', 'deleted_location')
            $table->string('action', 100);

            // Polymorphic-style target reference (not using Laravel morphs for simplicity)
            $table->string('target_type', 100)->nullable();  // e.g., 'participant', 'location'
            $table->unsignedBigInteger('target_id')->nullable();

            // Human-readable description of what happened
            $table->text('description')->nullable();

            // Additional structured data (e.g., before/after values)
            $table->json('metadata')->nullable();

            // Security tracking
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            // Indexes for efficient querying
            $table->index('user_id');
            $table->index('action');
            $table->index(['target_type', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
