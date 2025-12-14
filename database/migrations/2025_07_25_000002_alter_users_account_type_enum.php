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
        // For MySQL, you need to use a raw statement to alter ENUM
        DB::statement("ALTER TABLE users MODIFY account_type ENUM('admin', 'staff', 'student', 'parent', 'teacher') COMMENT 'Type of user account'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY account_type ENUM('admin', 'staff', 'student', 'parent') COMMENT 'Type of user account'");
    }
}; 