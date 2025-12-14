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
        Schema::create('parent_student', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('student_id');
            $table->string('relationship')->default('parent');
            $table->boolean('is_primary')->default(false);
            $table->boolean('receive_notifications')->default(true);
            $table->timestamps();
            
            // Add indexes
            $table->index('parent_id');
            $table->index('student_id');
            $table->unique(['parent_id', 'student_id']);
            
            // Note: Foreign keys commented out due to constraint issues
            // The relationship is enforced at application level
            // If foreign keys are needed, they can be added manually:
            // ALTER TABLE parent_student ADD CONSTRAINT ps_parent_fk 
            // FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_student');
    }
};
