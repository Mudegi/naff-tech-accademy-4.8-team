<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Drop the existing unique constraint on slug
            $table->dropUnique(['slug']);
        });
        
        // Create a composite unique index on (school_id, slug)
        // This allows slugs to be unique per school
        // For subjects with school_id=NULL (global subjects), they remain globally unique
        DB::statement('CREATE UNIQUE INDEX subjects_school_id_slug_unique ON subjects (school_id, slug)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Drop the composite unique index
            DB::statement('DROP INDEX subjects_school_id_slug_unique ON subjects');
            
            // Restore the original unique constraint on slug
            $table->unique('slug');
        });
    }
};
