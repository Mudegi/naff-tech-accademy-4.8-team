<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('uploaded_by'); // Student who uploaded
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_type', 10); // pdf, png, jpg, etc
            $table->enum('status', ['pending', 'reviewed', 'graded'])->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index('group_id');
            $table->index('subject_id');
            $table->index('class_id');
            $table->index('uploaded_by');
            $table->foreign('group_id')->references('id')->on('groups')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_submissions');
    }
};
