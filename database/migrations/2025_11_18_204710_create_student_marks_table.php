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
        Schema::create('student_marks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('academic_level')->comment('UACE, UCE, etc.');
            $table->string('subject_name');
            $table->string('paper_name')->nullable()->comment('e.g., Mathematics Paper 1, Physics Paper 2');
            $table->string('grade')->comment('A, B, C, D, E, O, F or Distinction 1, Credit 3, Pass 7, or numeric marks');
            $table->decimal('numeric_mark', 5, 2)->nullable()->comment('Numeric mark if applicable (e.g., 85.5/100)');
            $table->string('grade_type')->default('letter')->comment('letter, distinction_credit_pass, numeric');
            $table->integer('points')->nullable()->comment('Calculated points based on grade');
            $table->boolean('is_principal_pass')->default(false)->comment('Whether this is a principal pass subject');
            $table->boolean('is_essential')->default(false)->comment('Whether this is an essential subject for the course');
            $table->boolean('is_relevant')->default(false)->comment('Whether this is a relevant subject');
            $table->boolean('is_desirable')->default(false)->comment('Whether this is a desirable subject');
            $table->year('academic_year')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better query performance
            $table->index(['user_id', 'academic_level']);
            $table->index(['user_id', 'is_principal_pass']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_marks');
    }
};
