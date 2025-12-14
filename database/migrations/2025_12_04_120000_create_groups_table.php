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
        Schema::dropIfExists('groups');

        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('class_id');
            $table->enum('status', ['open', 'closed', 'full'])->default('open');
            $table->integer('max_members')->default(5);
            $table->timestamps();

            $table->unique(['school_id', 'class_id', 'name']);

            // Indexes for foreign keys (without constraints for compatibility)
            $table->index('created_by');
            $table->index('school_id');
            $table->index('class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
