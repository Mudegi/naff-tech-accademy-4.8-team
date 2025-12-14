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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_package_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_subscription_id')->constrained()->onDelete('cascade');
            
            // Payment Details
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('UGX');
            $table->string('payment_method', 50);
            $table->string('payment_provider', 50)->default('flutterwave'); // flutterwave, paystack, etc.
            $table->string('payment_status', 20); // pending, successful, failed, cancelled
            $table->string('transaction_id', 100)->unique();
            $table->string('reference', 100)->unique();
            
            // Flutterwave Specific Fields
            $table->string('flw_ref', 100)->nullable();
            $table->string('flw_tx_ref', 100)->nullable();
            $table->string('flw_order_id', 100)->nullable();
            $table->json('flw_meta')->nullable();
            $table->json('flw_response')->nullable();
            
            // Payment Verification
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_response')->nullable();
            
            // Error Handling
            $table->text('error_message')->nullable();
            $table->text('error_code')->nullable();
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Indexes
            $table->index(['transaction_id', 'reference']);
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
