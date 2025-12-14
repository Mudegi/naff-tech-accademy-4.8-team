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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); // For group conversations
            $table->text('description')->nullable(); // For group conversations
            $table->enum('type', ['private', 'group'])->default('private');
            $table->unsignedBigInteger('created_by'); // User who created the conversation
            $table->timestamps();
            
            $table->index(['type', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};