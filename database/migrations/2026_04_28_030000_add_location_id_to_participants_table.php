<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->after('status')->nullable()->comment('Mandatory reporting location');
            
            $table->foreign('location_id')
                  ->references('id')
                  ->on('locations')
                  ->onDelete('restrict'); // Prevent deleting a location that is assigned to a participant
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn('location_id');
        });
    }
};
