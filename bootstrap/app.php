<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'single.session' => \App\Http\Middleware\CheckSingleSession::class,
            'tenant' => \App\Http\Middleware\SetTenantContext::class,
        ]);
        
        // Add tenant middleware to web group
        $middleware->web(append: [
            \App\Http\Middleware\SetTenantContext::class,
        ]);
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        // Send weekly performance summaries every Sunday at 8 AM
        $schedule->command('parent:send-weekly-summaries')->weeklyOn(0, '8:00');
        
        // Check for low grades daily at 6 PM
        $schedule->command('parent:check-low-grades')->dailyAt('18:00');
        
        // Check for missing assignments every Monday and Thursday at 9 AM
        $schedule->command('parent:check-missing-assignments')->weeklyOn([1, 4], '9:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
