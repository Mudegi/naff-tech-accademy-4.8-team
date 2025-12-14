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
            $table->string('academic_level')->default('A-Level')->after('degree_type')->comment('O-Level or A-Level');
            $table->index('academic_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('university_cut_offs', function (Blueprint $table) {
            $table->dropIndex(['academic_level']);
            $table->dropColumn('academic_level');
        });
    }
};
