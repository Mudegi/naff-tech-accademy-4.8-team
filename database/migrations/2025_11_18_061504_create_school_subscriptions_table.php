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
        Schema::create('school_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('subscription_package_id')->nullable();
            $table->decimal('amount_paid', 10, 2);
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_method', ['flutterwave', 'easypay', 'manual', 'other'])->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_reference')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('school_id');
            $table->index('subscription_package_id');
            $table->index('payment_status');
            $table->index('is_active');
        });

        // Add foreign key for subscription_package_id separately to avoid issues
        try {
            Schema::table('school_subscriptions', function (Blueprint $table) {
                $table->foreign('subscription_package_id')
                    ->references('id')
                    ->on('subscription_packages')
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
        Schema::dropIfExists('school_subscriptions');
    }
};
