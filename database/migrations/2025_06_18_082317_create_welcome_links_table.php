<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('welcome_links', function (Blueprint $table) {
            $table->id();
            $table->string('hero_title')->nullable();
            $table->string('hero_subtitle')->nullable();
            $table->text('hero_description')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('hero_image_2')->nullable();
            $table->string('hero_image_3')->nullable();
            $table->string('hero_image_4')->nullable();
            $table->string('hero_image_5')->nullable();
            $table->string('hero_image_6')->nullable();
            $table->string('hero_image_7')->nullable();
            $table->string('hero_image_8')->nullable();
            $table->string('hero_image_9')->nullable();
            $table->string('hero_image_10')->nullable();
            $table->string('about_title')->nullable();
            $table->text('about_description')->nullable();
            $table->string('about_image')->nullable();
            $table->string('features_title')->nullable();
            $table->text('features_description')->nullable();
            $table->string('features_image')->nullable();
            $table->string('testimonials_title')->nullable();
            $table->text('testimonials_description')->nullable();
            $table->string('testimonials_image')->nullable();
            $table->string('cta_title')->nullable();
            $table->text('cta_description')->nullable();
            $table->string('cta_image')->nullable();
            $table->string('login_image')->nullable();
            $table->string('register_image')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('welcome_links');
    }
};