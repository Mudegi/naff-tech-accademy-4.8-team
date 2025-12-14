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
        Schema::create('university_cut_offs', function (Blueprint $table) {
            $table->id();
            $table->string('university_name');
            $table->string('university_code')->nullable()->comment('Short code for the university');
            $table->string('course_name');
            $table->string('course_code')->nullable();
            $table->text('course_description')->nullable();
            $table->string('faculty')->nullable();
            $table->string('department')->nullable();
            $table->integer('minimum_principal_passes')->default(2)->comment('Minimum number of principal passes required');
            $table->decimal('minimum_aggregate_points', 5, 2)->nullable()->comment('Minimum aggregate points required');
            $table->decimal('cut_off_points', 5, 2)->comment('Cut-off points for the last academic year');
            $table->year('academic_year')->comment('Academic year for which this cut-off applies');
            $table->json('essential_subjects')->nullable()->comment('List of essential subjects required');
            $table->json('relevant_subjects')->nullable()->comment('List of relevant subjects');
            $table->json('desirable_subjects')->nullable()->comment('List of desirable subjects');
            $table->text('additional_requirements')->nullable()->comment('Any additional requirements');
            $table->integer('duration_years')->nullable()->comment('Course duration in years');
            $table->enum('degree_type', ['bachelor', 'diploma', 'certificate', 'masters', 'phd'])->default('bachelor');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better query performance
            $table->index('university_name');
            $table->index('course_name');
            $table->index(['cut_off_points', 'is_active']);
            $table->index('academic_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('university_cut_offs');
    }
};
