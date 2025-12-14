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
        Schema::table('subjects', function (Blueprint $table) {
            $table->enum('level', ['O Level', 'A Level', 'Both'])->default('Both')->after('name')->comment('Academic level: O Level, A Level, or Both');
            $table->integer('paper_count')->default(1)->after('level')->comment('Number of papers for this subject (e.g., Mathematics has 2 papers)');
            $table->json('papers')->nullable()->after('paper_count')->comment('Paper details: [{"name": "Paper 1", "description": "..."}, {"name": "Paper 2", ...}]');
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropIndex(['level']);
            $table->dropColumn(['level', 'paper_count', 'papers']);
        });
    }
};
