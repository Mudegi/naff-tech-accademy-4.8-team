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
        Schema::table('students', function (Blueprint $table) {
            // Add class_id foreign key column after the 'class' column
            $table->unsignedBigInteger('class_id')->nullable()->after('class');
            
            // Add foreign key constraint
            $table->foreign('class_id')
                  ->references('id')
                  ->on('classes')
                  ->onDelete('set null');
            
            // Add index for better query performance
            $table->index('class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop foreign key and index before dropping column
            $table->dropForeign(['class_id']);
            $table->dropIndex(['class_id']);
            $table->dropColumn('class_id');
        });
    }
};
