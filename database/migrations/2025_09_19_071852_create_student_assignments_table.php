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
        Schema::create('student_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // References users.id (students have account_type = 'student')
            $table->unsignedBigInteger('resource_id'); // References resources.id
            $table->string('assignment_file_path');
            $table->string('assignment_file_type');
            $table->string('status')->default('submitted'); // submitted, reviewed, graded
            $table->text('teacher_feedback')->nullable();
            $table->integer('grade')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            // Remove foreign key constraints to avoid migration issues
            // $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
            $table->unique(['student_id', 'resource_id']); // One assignment per student per resource
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_assignments');
    }
};
