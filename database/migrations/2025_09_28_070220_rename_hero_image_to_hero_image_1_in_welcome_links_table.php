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
            $table->renameColumn('hero_image', 'hero_image_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('welcome_links', function (Blueprint $table) {
            $table->renameColumn('hero_image_1', 'hero_image');
        });
    }
};
