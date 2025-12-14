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
            $table->text('content')->nullable()->after('description');
            $table->json('objectives')->nullable()->after('content');
            $table->json('prerequisites')->nullable()->after('objectives');
            $table->string('duration')->nullable()->after('prerequisites');
            $table->integer('total_topics')->default(0)->after('duration');
            $table->integer('total_resources')->default(0)->after('total_topics');
            $table->json('learning_outcomes')->nullable()->after('total_resources');
            $table->json('assessment_methods')->nullable()->after('learning_outcomes');
            $table->decimal('passing_score', 5, 2)->default(60.00)->after('assessment_methods');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn([
                'content',
                'objectives',
                'prerequisites',
                'duration',
                'total_topics',
                'total_resources',
                'learning_outcomes',
                'assessment_methods',
                'passing_score'
            ]);
        });
    }
};
