<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! auth()->check()) {
            // Not logged in
            abort(401);
        }

        $roles = explode(',', $role);
        if (! ($request->user()->roles()->whereIn('name', $roles)->exists() || $request->user()->roles()->value('name') == 'admin')) {
            abort(403);
            // Not authorised

        }

        return $next($request);

    }
}
