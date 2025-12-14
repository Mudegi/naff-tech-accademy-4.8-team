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
        Schema::dropIfExists('project_implementations');

        Schema::create('project_implementations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->text('gathering_resources')->nullable();
            $table->text('activity_execution')->nullable();
            $table->text('stakeholder_engagement')->nullable();
            $table->text('producing_product_service')->nullable();
            $table->text('documentation_report')->nullable();
            $table->text('dissemination_presentation')->nullable();
            $table->string('documentation_file_path')->nullable(); // File path for documentation
            $table->string('presentation_file_path')->nullable(); // File path for presentation
            $table->enum('status', ['in_progress', 'completed', 'submitted'])->default('in_progress');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_implementations');
    }
};
