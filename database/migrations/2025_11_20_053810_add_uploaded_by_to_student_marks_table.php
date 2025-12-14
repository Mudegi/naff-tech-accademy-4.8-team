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
        Schema::table('student_marks', function (Blueprint $table) {
            $table->unsignedBigInteger('uploaded_by')->nullable()->after('school_id')->comment('User ID of who uploaded this mark (teacher or student)');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_marks', function (Blueprint $table) {
            $table->dropIndex(['uploaded_by']);
            $table->dropColumn('uploaded_by');
        });
    }
};
