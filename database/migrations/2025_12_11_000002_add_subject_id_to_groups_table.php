<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // Add subject_id to link group to a specific subject
            $table->unsignedBigInteger('subject_id')->nullable()->after('class_id');
            $table->index('subject_id');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropIndex(['subject_id']);
            $table->dropColumn('subject_id');
        });
    }
};
