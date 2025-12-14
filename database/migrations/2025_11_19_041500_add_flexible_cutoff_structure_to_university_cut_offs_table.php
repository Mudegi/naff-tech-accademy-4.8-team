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
            // Add a JSON field to store flexible cut-off structure for different university formats
            $table->json('cut_off_structure')->nullable()->after('cut_off_points_female')
                ->comment('Flexible JSON structure for university-specific cut-off formats (e.g., Kyambogo format, custom categories)');
            
            // Add cut-off format type to identify how to interpret the cut-offs
            $table->enum('cut_off_format', ['standard', 'makerere', 'kyambogo', 'custom'])->default('standard')->after('program_category')
                ->comment('Format type: standard (single), makerere (STEM/Other with gender), kyambogo (custom structure), custom (JSON-based)');
            
            // Add index for format type
            $table->index('cut_off_format');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('university_cut_offs', function (Blueprint $table) {
            $table->dropColumn(['cut_off_structure', 'cut_off_format']);
        });
    }
};

