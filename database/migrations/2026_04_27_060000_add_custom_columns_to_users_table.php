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
     * Adds custom columns to the users table for the dual-auth system:
     * - nik: 16-digit national ID for peserta login (nullable for admin accounts)
     * - role: 'admin' or 'peserta' with database-level CHECK constraint
     * - is_active: soft toggle to disable accounts without deleting
     *
     * Also makes email and password nullable to support peserta accounts
     * that authenticate via NIK only (no email, no password).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add role column — determines login flow and access control
            $table->string('role', 10)->default('peserta')->after('remember_token');

            // Add NIK column — unique identifier for peserta login
            // Nullable because admin accounts don't have NIK
            // PostgreSQL: NULL values are NOT considered duplicates in UNIQUE indexes
            $table->char('nik', 16)->nullable()->unique()->after('role');

            // Add is_active flag — allows admin to disable accounts
            $table->boolean('is_active')->default(true)->after('nik');
        });

        // Make email nullable — peserta accounts won't have email
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });

        // Make password nullable — peserta accounts authenticate via NIK only
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });

        // Add PostgreSQL CHECK constraint for role values
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'peserta'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove CHECK constraint first
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');

        // Revert email to NOT NULL
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });

        // Revert password to NOT NULL
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
        });

        // Drop custom columns
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nik']);
            $table->dropColumn(['role', 'nik', 'is_active']);
        });
    }
};
