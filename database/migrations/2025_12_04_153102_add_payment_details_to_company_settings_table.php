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
        Schema::table('company_settings', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('company_website');
            $table->string('account_name')->nullable()->after('bank_name');
            $table->string('account_number')->nullable()->after('account_name');
            $table->string('mtn_mobile_number')->nullable()->after('account_number');
            $table->string('airtel_mobile_number')->nullable()->after('mtn_mobile_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'account_name', 'account_number', 'mtn_mobile_number', 'airtel_mobile_number']);
        });
    }
};
