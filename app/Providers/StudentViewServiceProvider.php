<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSubscription;

class StudentViewServiceProvider extends ServiceProvider
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
        View::composer('layouts.student-dashboard', function ($view) {
            $user = Auth::user();
            $hasActiveSubscription = false;
            
            if ($user) {
                $hasActiveSubscription = UserSubscription::where('user_id', $user->id)
                    ->where('end_date', '>', now())
                    ->where('is_active', true)
                    ->exists();
            }
            
            $view->with('hasActiveSubscription', $hasActiveSubscription);
        });
    }
} 