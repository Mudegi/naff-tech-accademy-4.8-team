<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\FooterContent;

class FrontendViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('frontend.layouts.app', function ($view) {
            $footerContent = FooterContent::first();
            
            if (!$footerContent) {
                // If no footer content exists, run the seeder
                $seeder = new \Database\Seeders\FooterContentSeeder();
                $seeder->run();
                $footerContent = FooterContent::first();
            }
            
            $view->with('footerContent', $footerContent);
        });
    }
}
