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
        Schema::table('welcome_links', function (Blueprint $table) {
            // Meta Tags
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();

            // Mission Section
            $table->string('mission_title')->nullable();
            $table->text('mission_description')->nullable();
            $table->string('mission_image')->nullable();

            // Team Section
            $table->string('team_title')->nullable();
            $table->text('team_description')->nullable();
            $table->string('team_image')->nullable();

            // Values Section
            $table->string('values_title')->nullable();
            $table->text('values_description')->nullable();
            $table->string('values_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('welcome_links', function (Blueprint $table) {
            // Meta Tags
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'meta_keywords',
                'og_title',
                'og_description',
                'og_image',
                'twitter_title',
                'twitter_description',
                'twitter_image',
            ]);

            // Mission Section
            $table->dropColumn([
                'mission_title',
                'mission_description',
                'mission_image',
            ]);

            // Team Section
            $table->dropColumn([
                'team_title',
                'team_description',
                'team_image',
            ]);

            // Values Section
            $table->dropColumn([
                'values_title',
                'values_description',
                'values_image',
            ]);
        });
    }
};