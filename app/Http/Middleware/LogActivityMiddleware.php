<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * LogActivityMiddleware — Auto-log all admin state-changing HTTP actions.
 *
 * Runs AFTER the response is sent (terminate middleware) to avoid slowing
 * down the request.  Captures POST, PUT, PATCH, and DELETE methods made by
 * authenticated admin users and writes them to the activity_logs table.
 */
class LogActivityMiddleware
{
    /**
     * Handle the incoming request (pass-through — logging happens in terminate()).
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Perform logging after the response has been sent to the browser.
     * Only logs write methods by authenticated admin users.
     */
    public function terminate(Request $request, Response $response): void
    {
        // Only log state-changing methods
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return;
        }

        // Only log authenticated admin users
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return;
        }

        // Build action string from HTTP method + route name (or path fallback)
        $routeName = $request->route()?->getName() ?? $request->path();
        $action    = $request->method() . ' ' . $routeName;

        // Try to extract model type and ID from route parameters
        $targetType = null;
        $targetId   = null;

        $routeParams = $request->route()?->parameters() ?? [];

        foreach ($routeParams as $key => $value) {
            if (is_object($value) && method_exists($value, 'getKey')) {
                // Route model binding — get the model class name and PK
                $targetType = class_basename($value);
                $targetId   = $value->getKey();
                break;
            } elseif (is_numeric($value)) {
                $targetType = ucfirst($key);
                $targetId   = (int) $value;
                break;
            }
        }

        // Persist to activity_logs
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'description' => null,
            'ip_address'  => $request->ip(),
        ]);
    }
}
