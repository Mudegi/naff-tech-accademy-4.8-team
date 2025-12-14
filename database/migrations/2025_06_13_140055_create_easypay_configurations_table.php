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
        Schema::create('easypay_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('client_id');
            $table->string('secret');
            $table->string('website_url');
            $table->string('ipn_url');
            $table->integer('hits')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('easypay_configurations');
    }
};
