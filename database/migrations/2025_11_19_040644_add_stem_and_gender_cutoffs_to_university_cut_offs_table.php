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
        Schema::table('university_cut_offs', function (Blueprint $table) {
            // Add program category (STEM or Other)
            $table->enum('program_category', ['stem', 'other', 'both'])->default('other')->after('degree_type')
                ->comment('STEM programs have gender-specific cut-offs, Other programs have single cut-off');
            
            // Add gender-specific cut-off points for STEM programs
            $table->decimal('cut_off_points_male', 5, 2)->nullable()->after('cut_off_points')
                ->comment('Cut-off points for male applicants (STEM programs)');
            $table->decimal('cut_off_points_female', 5, 2)->nullable()->after('cut_off_points_male')
                ->comment('Cut-off points for female applicants (STEM programs)');
            
            // Make the original cut_off_points nullable since STEM programs use gender-specific fields
            $table->decimal('cut_off_points', 5, 2)->nullable()->change()
                ->comment('Cut-off points for all applicants (Other programs) or fallback');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('university_cut_offs', function (Blueprint $table) {
            $table->dropColumn(['program_category', 'cut_off_points_male', 'cut_off_points_female']);
            $table->decimal('cut_off_points', 5, 2)->nullable(false)->change();
        });
    }
};
