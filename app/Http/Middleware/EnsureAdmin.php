<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Ensure the authenticated user has the 'admin' role.
     *
     * If the user is a peserta trying to access admin routes,
     * redirect them to the peserta dashboard instead.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            if ($request->user() && $request->user()->isPeserta()) {
                return redirect()->route('peserta.dashboard');
            }

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
