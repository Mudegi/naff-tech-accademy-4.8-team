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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->foreignId('term_id')->constrained('terms')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // User who created the resource
            $table->enum('grade_level', ['O Level', 'A Level']); // Grade level (O Level or A Level)
            
            // Basic Information
            $table->string('title');
            $table->text('description')->nullable();
            
            // Video related fields
            $table->string('video_url')->nullable(); // URL for the video
            
            // Notes related fields
            $table->string('notes_file_path')->nullable(); // Path to the notes file (PDF/PPT/Excel)
            $table->string('notes_file_type')->nullable(); // Type of notes file (PDF/PPT/Excel)
            
            // Tags for searching (comma-separated)
            $table->string('tags')->nullable(); // Store tags as comma-separated values
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
