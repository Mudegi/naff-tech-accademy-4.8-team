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
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->comment('Short code for the university (e.g., MAK, KyU)');
            $table->text('url_pattern')->nullable()->comment('URL pattern or template for cut-off points (supports {year} and {nextYear} placeholders)');
            $table->text('base_url')->nullable()->comment('Base URL of the university website');
            $table->enum('scraper_type', ['pdf', 'html_table', 'html_custom', 'auto'])->default('auto')
                ->comment('Type of scraper: pdf, html_table, html_custom, or auto-detect');
            $table->enum('cut_off_format', ['standard', 'makerere', 'kyambogo', 'custom'])->default('standard')
                ->comment('Cut-off format type for this university');
            $table->json('scraper_config')->nullable()->comment('Additional scraper configuration (selectors, patterns, etc.)');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable()->comment('Admin notes about this university');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('code');
            $table->index('is_active');
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('universities');
    }
};
