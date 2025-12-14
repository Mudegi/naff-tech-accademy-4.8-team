<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DebugRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->path() === 'student/projects/groups' || strpos($request->path(), 'groups') !== false) {
            file_put_contents(
                storage_path('logs/debug.log'),
                date('Y-m-d H:i:s') . " - Request: " . $request->path() . "\n",
                FILE_APPEND
            );
        }
        return $next($request);
    }
}
