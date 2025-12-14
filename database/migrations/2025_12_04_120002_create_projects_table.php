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
        Schema::dropIfExists('projects');

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('class_id');
            $table->enum('status', ['planning', 'implementation', 'completed', 'cancelled'])->default('planning');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('metadata')->nullable(); // For additional project data
            $table->timestamps();

            $table->index(['group_id', 'status']);
            $table->index(['school_id', 'class_id']);

            // Indexes for foreign keys
            $table->index('group_id');
            $table->index('created_by');
            $table->index('school_id');
            $table->index('class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
