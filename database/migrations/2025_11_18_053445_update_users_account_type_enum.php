<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update account_type enum to include new school roles
        DB::statement("ALTER TABLE users MODIFY COLUMN account_type ENUM('admin', 'staff', 'student', 'parent', 'teacher', 'school_admin', 'director_of_studies', 'head_of_department', 'subject_teacher') COMMENT 'Type of user account'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE users MODIFY COLUMN account_type ENUM('admin', 'staff', 'student', 'parent', 'teacher') COMMENT 'Type of user account'");
    }
};
