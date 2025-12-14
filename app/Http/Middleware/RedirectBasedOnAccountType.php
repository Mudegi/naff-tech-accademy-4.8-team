<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnAccountType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user belongs to a school and if that school is active
            if ($user->school_id) {
                $school = $user->school;
                if (!$school) {
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('error', 'Your school account is no longer active. Please contact support.');
                }
            }

            $accountType = $user->account_type;

            if ($request->is('login') || $request->is('/')) {
                switch ($accountType) {
                    case 'admin':
                        if (!Auth::user()->school_id) {
                            // Super admin
                            return redirect()->route('admin.dashboard');
                        }
                        // Admin with school_id falls through to school staff
                    case 'director_of_studies':
                        return redirect()->route('admin.director-of-studies.dashboard');
                    case 'school_admin':
                    case 'head_of_department':
                        return redirect()->route('admin.school.dashboard');
                    case 'student':
                    case 'parent':
                        return redirect()->route('student.dashboard');
                    case 'teacher':
                    case 'subject_teacher':
                        return redirect()->route('teacher.dashboard');
                    default:
                        return $next($request);
                }
            }
        }

        return $next($request);
    }
} 