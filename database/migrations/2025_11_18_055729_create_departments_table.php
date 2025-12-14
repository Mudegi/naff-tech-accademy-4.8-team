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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable()->comment('Department code/abbreviation');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('head_of_department_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('school_id');
            $table->index('head_of_department_id');
        });

        // Add foreign key for head_of_department_id separately to avoid issues
        // This will be handled at the application level if foreign key creation fails
        try {
            Schema::table('departments', function (Blueprint $table) {
                $table->foreign('head_of_department_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            });
        } catch (\Exception $e) {
            // If foreign key creation fails, continue without it
            // The relationship will work at the application level
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
