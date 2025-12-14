<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountType
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
            $routeName = $request->route() ? $request->route()->getName() : null;
            $uri = $request->path();
            
            \Log::info('CheckAccountType middleware', [
                'user_id' => $user->id,
                'account_type' => $user->account_type,
                'route_name' => $routeName,
                'uri' => $uri,
            ]);
            
            // Define the routes that should be accessible to all authenticated users
            $publicRoutes = [
                'profile.edit',
                'profile.update',
                'logout'
            ];

            // If the current route is in public routes, allow access
            $routeName = $request->route() ? $request->route()->getName() : null;
            if ($routeName && in_array($routeName, $publicRoutes)) {
                return $next($request);
            }

            // Redirect based on account type
            switch ($user->account_type) {
                case 'student':
                    $routeName = $request->route() ? $request->route()->getName() : null;
                    if ($routeName && !str_starts_with($routeName, 'student.')) {
                        return redirect()->route('student.dashboard');
                    }
                    
                    // Check if user has set preferences when accessing my-videos
                    // School students don't need preferences - they get free access to school resources
                    $isSchoolStudent = !is_null($user->school_id) && $user->school_id > 0;
                    if ($request->route()->getName() === 'student.my-videos' && !$isSchoolStudent && !$user->preference) {
                        return redirect()->route('student.preferences.index')
                            ->with('error', 'Please set your learning preferences before accessing your videos.');
                    }
                    break;
                    
                case 'parent':
                    $routeName = $request->route() ? $request->route()->getName() : null;
                    if ($routeName && !str_starts_with($routeName, 'student.')) {
                        return redirect()->route('student.dashboard');
                    }
                    break;
                    
                case 'teacher':
                case 'subject_teacher':
                    // Teachers and subject teachers use teacher routes
                    // But also allow access to student routes for projects and groups
                    $route = $request->route();
                    $routeName = $route ? $route->getName() : null;
                    $uri = $request->path();
                    
                    // Allowed student routes for teachers
                    $allowedStudentRoutes = [
                        'student.projects.index',
                        'student.projects.create',
                        'student.projects.store',
                        'student.projects.show',
                        'student.projects.edit-planning',
                        'student.projects.update-planning',
                        'student.projects.submit-planning',
                        'student.projects.edit-implementation',
                        'student.projects.update-implementation',
                        'student.projects.submit-project',
                        'student.projects.groups.index',
                        'student.projects.groups.create',
                        'student.projects.groups.store',
                        'student.projects.groups.show',
                        'student.projects.groups.join',
                        'student.projects.groups.leave',
                        'student.projects.groups.remove-member',
                    ];
                    
                    // Check if route name is in allowed routes
                    $isAllowedRoute = $routeName && (str_starts_with($routeName, 'teacher.') || in_array($routeName, $allowedStudentRoutes));
                    
                    // Also check URI path as fallback (in case route name isn't available yet)
                    $isProjectsGroupsPath = str_starts_with($uri, 'student/projects/groups') || str_starts_with($uri, 'student/projects');
                    
                    if (!$isAllowedRoute && !$isProjectsGroupsPath && str_starts_with($uri, 'student/')) {
                        return redirect()->route('teacher.dashboard');
                    }
                    break;
                    
                case 'admin':
                    $routeName = $request->route() ? $request->route()->getName() : null;
                    if ($routeName && !str_starts_with($routeName, 'admin.')) {
                        return redirect()->route('admin.dashboard');
                    }
                    break;
                    
                case 'director_of_studies':
                case 'school_admin':
                case 'head_of_department':
                    // School staff use admin routes
                    $routeName = $request->route() ? $request->route()->getName() : null;
                    if ($routeName && !str_starts_with($routeName, 'admin.')) {
                        return redirect()->route('admin.dashboard');
                    }
                    break;
                    
                case 'staff':
                    // Staff members - allow access to staff routes if they exist
                    // For now, redirect to admin dashboard
                    $routeName = $request->route() ? $request->route()->getName() : null;
                    if ($routeName && !str_starts_with($routeName, 'admin.') && !str_starts_with($routeName, 'staff.')) {
                        return redirect()->route('admin.dashboard');
                    }
                    break;
                    
                default:
                    // Handle unknown account types
                    Auth::logout();
                    return redirect()->route('login')->with('error', 'Invalid account type.');
            }
        }

        return $next($request);
    }
}
