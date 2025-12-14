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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('account_type', ['student', 'parent'])->comment('Type of account: student or parent');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('school_name')->nullable();
            $table->json('academic_levels')->nullable()->comment('For parents to select multiple levels: O Level, A Level, or both');
            $table->string('registration_number')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('phone_number')->nullable()->unique();
            $table->boolean('email_verified')->default(false);
            $table->boolean('phone_verified')->default(false);
            $table->json('classes')->nullable()->comment('For parents to select multiple classes: S.1 to S.6');
            $table->string('class')->nullable()->comment('For students: S.1 to S.6');
            $table->boolean('is_referral')->default(false);
            $table->string('referee_name')->nullable();
            $table->string('referee_contact')->nullable();
            $table->string('how_you_know_us')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->foreignId('subscription_package_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('subscription_start_date')->nullable();
            $table->timestamp('subscription_end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
