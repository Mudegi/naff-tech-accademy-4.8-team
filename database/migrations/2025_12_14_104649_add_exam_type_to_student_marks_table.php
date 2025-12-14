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
            $table->string('exam_type')->nullable()->after('academic_year')->comment('Beginning of Term, Mid Term, End of Term, Mock, Other');
            $table->string('exam_type_other')->nullable()->after('exam_type')->comment('Specify if exam_type is Other');
            $table->index('exam_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_marks', function (Blueprint $table) {
            $table->dropIndex(['exam_type']);
            $table->dropColumn(['exam_type', 'exam_type_other']);
        });
    }
};
