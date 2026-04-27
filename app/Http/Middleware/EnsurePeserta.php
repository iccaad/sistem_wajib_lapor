<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePeserta
{
    /**
     * Ensure the authenticated user has the 'peserta' role.
     *
     * If the user is an admin trying to access peserta routes,
     * redirect them to the admin dashboard instead.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isPeserta()) {
            if ($request->user() && $request->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect('/login');
        }

        return $next($request);
    }
}
