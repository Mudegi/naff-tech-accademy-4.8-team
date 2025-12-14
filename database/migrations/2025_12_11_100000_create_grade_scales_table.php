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
        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->string('grade')->comment('A, B, C, D, E, O, F');
            $table->decimal('min_percentage', 5, 2)->comment('Minimum percentage for this grade');
            $table->decimal('max_percentage', 5, 2)->comment('Maximum percentage for this grade');
            $table->integer('points')->comment('Points awarded for this grade');
            $table->string('academic_level')->default('O-Level')->comment('O-Level or A-Level');
            $table->unsignedBigInteger('school_id')->nullable()->comment('NULL for default system-wide scale, or specific school ID for custom scale');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index(['academic_level', 'school_id']);
            $table->index(['grade', 'academic_level']);
            
            // Foreign key
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_scales');
    }
};
