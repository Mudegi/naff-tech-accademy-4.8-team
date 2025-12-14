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
        Schema::table('classes', function (Blueprint $table) {
            // Drop the existing unique constraint on slug
            $table->dropUnique(['slug']);
        });
        
        // Create a composite unique index on (school_id, slug)
        // This allows slugs to be unique per school
        // For classes with school_id=NULL (global classes), they remain globally unique
        DB::statement('CREATE UNIQUE INDEX classes_school_id_slug_unique ON classes (school_id, slug)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            // Drop the composite unique index
            DB::statement('DROP INDEX classes_school_id_slug_unique ON classes');
            
            // Restore the original unique constraint on slug
            $table->unique('slug');
        });
    }
};
