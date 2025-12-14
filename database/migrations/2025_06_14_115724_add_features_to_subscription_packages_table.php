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
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->boolean('downloadable_content')->default(false);
            $table->boolean('practice_questions_bank')->default(false);
            $table->boolean('performance_analytics')->default(false);
            $table->boolean('parent_progress_reports')->default(false);
            $table->boolean('one_on_one_tutoring_sessions')->default(false);
            $table->boolean('shared_resources')->default(false);
            $table->boolean('priority_support')->default(false);
            $table->boolean('email_support')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->dropColumn([
                'downloadable_content',
                'practice_questions_bank',
                'performance_analytics',
                'parent_progress_reports',
                'one_on_one_tutoring_sessions',
                'shared_resources',
                'priority_support',
                'email_support'
            ]);
        });
    }
};
