<?php

namespace App\Providers;

use App\Models\FooterContent;
use App\Models\WhyChooseUs;
use App\Models\WelcomeLink;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

class ViewServiceProvider extends ServiceProvider
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
        View::composer('*', function ($view) {
            // Share footer content if the table exists
            if (Schema::hasTable('footer_contents')) {
                $view->with('footerContent', FooterContent::first());
            }
            
            // Share WhyChooseUs data if the table exists
            if (Schema::hasTable('why_choose_us')) {
                $view->with('whyChooseUs', WhyChooseUs::with('activeFeatures')->first());
            }
            
            // Share WelcomeLinks data if the table exists
            if (Schema::hasTable('welcome_links')) {
                $welcomeLinks = WelcomeLink::first();
                if (!$welcomeLinks) {
                    // Create a default welcome links record if none exists
                    $welcomeLinks = new WelcomeLink();
                }
                $view->with('welcomeLinks', $welcomeLinks);
            }
        });
    }
} 