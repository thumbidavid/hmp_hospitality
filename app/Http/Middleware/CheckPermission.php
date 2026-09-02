<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user()->can($permission)) {
            // Return 403 if it's an API/Inertia request
            if ($request->expectsJson() || $request->header('X-Inertia')) {
                abort(403, 'Unauthorized action.');
            }
            return redirect()->back()->with('error', 'You do not have permission.');
        }
        return $next($request);
    }
}
