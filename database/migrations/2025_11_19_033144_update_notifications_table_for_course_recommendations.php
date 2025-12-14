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
        Schema::table('notifications', function (Blueprint $table) {
            // Make resource_id nullable to support course recommendation notifications
            $table->unsignedBigInteger('resource_id')->nullable()->change();
            
            // Add link field for course recommendations
            $table->string('link')->nullable()->after('message');
            
            // Add university_cut_off_id for course recommendation notifications
            $table->unsignedBigInteger('university_cut_off_id')->nullable()->after('comment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('resource_id')->nullable(false)->change();
            $table->dropColumn(['link', 'university_cut_off_id']);
        });
    }
};
