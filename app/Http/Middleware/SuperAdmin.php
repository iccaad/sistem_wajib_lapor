<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdmin
{
    /**
     * Only allow the super-admin account (pccpolrestabessemarang@gmail.com)
     * to access routes protected by this middleware.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! ($request->user()->is_root_super_admin || $request->user()->role === 'super_admin')) {
            abort(403, 'Hanya akun utama yang dapat mengakses fitur ini.');
        }

        return $next($request);
    }
}
