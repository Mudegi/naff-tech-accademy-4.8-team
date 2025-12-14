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
        Schema::create('contact_pages', function (Blueprint $table) {
            $table->id();
            
            // Meta Tags
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('meta_author')->nullable();
            $table->string('meta_robots')->nullable();
            $table->string('meta_language')->nullable();
            $table->string('meta_revisit_after')->nullable();
            
            // Open Graph / Facebook
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            
            // Twitter
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            
            // Schema.org
            $table->string('schema_name')->nullable();
            $table->text('schema_description')->nullable();
            $table->string('schema_street_address')->nullable();
            $table->string('schema_address_locality')->nullable();
            $table->string('schema_address_region')->nullable();
            $table->string('schema_postal_code')->nullable();
            $table->string('schema_address_country')->nullable();
            $table->string('schema_telephone')->nullable();
            $table->string('schema_email')->nullable();
            $table->string('schema_opening_hours')->nullable();
            
            // Contact Information
            $table->string('contact_title')->nullable();
            $table->text('contact_description')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_phone_hours')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('contact_address')->nullable();
            
            // Map Section
            $table->string('map_title')->nullable();
            $table->text('map_description')->nullable();
            $table->text('map_embed_url')->nullable();
            $table->string('map_opening_hours_monday_friday')->nullable();
            $table->string('map_opening_hours_saturday')->nullable();
            $table->string('map_opening_hours_sunday')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_pages');
    }
};
