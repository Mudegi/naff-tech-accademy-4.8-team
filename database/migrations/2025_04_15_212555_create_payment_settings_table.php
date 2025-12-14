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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            
            // Flutterwave Settings
            $table->string('flw_public_key')->nullable();
            $table->string('flw_secret_key')->nullable();
            $table->string('flw_encryption_key')->nullable();
            $table->boolean('flw_test_mode')->default(true);
            $table->string('flw_webhook_secret')->nullable();
            $table->string('flw_redirect_url')->nullable();
            
            // General Payment Settings
            $table->string('default_currency', 3)->default('UGX');
            $table->json('supported_currencies')->nullable();
            $table->json('supported_payment_methods')->nullable();
            $table->boolean('enable_test_mode')->default(true);
            $table->text('payment_success_message')->nullable();
            $table->text('payment_failure_message')->nullable();
            
            // Email Settings
            $table->boolean('send_payment_receipt')->default(true);
            $table->string('payment_receipt_template')->nullable();
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
