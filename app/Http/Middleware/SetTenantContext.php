<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->school_id) {
            // Set the school context in the request
            $request->merge(['school_id' => Auth::user()->school_id]);
            
            $user = Auth::user();
            $school = $user->school;

            // Check if school exists (it might have been soft deleted or deleted)
            if (!$school) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Your school account is no longer active. Please contact support.');
            }
            
            // Allow school admins, directors of studies, and teachers to access even if subscription is not active
            // They need to be able to complete payment or view their dashboard, and teachers need to see student projects
            if ($school && !$school->isSubscriptionActive()) {
                // If user is school admin, director of studies, or teacher allow access to specific pages
                if (in_array($user->account_type, ['school_admin', 'director_of_studies', 'teacher', 'subject_teacher'])) {
                    // Allow school admin, director of studies, and teachers to access payment and dashboard pages
                    $allowedRoutes = [
                        'school.subscription.payment',
                        'admin.school.subscriptions.payment',
                        'admin.school.subscriptions.index',
                        'admin.school.subscriptions.create',
                        'admin.school.subscriptions.store',
                        'admin.school.subscriptions.process-payment',
                        'admin.school.settings',
                        'admin.school.settings.update',
                        'admin.school.dashboard',
                        'admin.director-of-studies.dashboard',
                        'teacher.dashboard',
                        'teacher.projects.index',
                        'teacher.projects.show',
                        'teacher.projects.grade.form',
                        'teacher.projects.grade.submit',
                        'teacher.projects.groups.index',
                        'teacher.projects.groups.show',
                        // Allow canonical groups routes as well
                        'teacher.groups.index',
                        'teacher.groups.submissions',
                        'dashboard',
                        'logout',
                        'login', // Allow staying on login page if redirected
                    ];
                    
                    // Check if current route is in allowed list
                    $currentRoute = $request->route()?->getName();
                    // Allow any teacher.groups.* route prefix as well
                    if ($currentRoute && str_starts_with($currentRoute, 'teacher.groups.')) {
                        // allowed
                    } elseif (!in_array($currentRoute, $allowedRoutes)) {
                        // Redirect to subscription payment page
                        $pendingSubscription = $school->subscriptions()
                            ->whereIn('payment_status', ['pending', 'pending_approval'])
                            ->latest()
                            ->first();
                        
                        if ($pendingSubscription) {
                            // If payment is pending approval, redirect to subscriptions page to show status
                            if ($pendingSubscription->payment_status === 'pending_approval') {
                                return redirect()->route('admin.school.subscriptions.index')
                                    ->with('info', 'Your subscription payment is pending admin approval. You will be notified once it is approved.');
                            }
                            // Otherwise redirect to payment page
                            return redirect()->route('school.subscription.payment', $pendingSubscription->id)
                                ->with('warning', 'Please complete your subscription payment to access all features.');
                        } else {
                            return redirect()->route('admin.school.subscriptions.create')
                                ->with('warning', 'Please create and activate a subscription to access all features.');
                        }
                    }
                } else {
                    // For other school staff, log them out if subscription is not active
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('error', 'Your school subscription has expired. Please contact your administrator.');
                }
            }
        }

        return $next($request);
    }
}
