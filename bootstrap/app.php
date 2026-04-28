<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsurePeserta;
use App\Http\Middleware\LogActivityMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Smart guest redirect: peserta routes → /login, admin routes → /admin/login
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('peserta/*')) {
                return route('peserta.login');
            }
            return route('admin.login');
        });

        // Register custom middleware aliases
        $middleware->alias([
            'admin'        => EnsureAdmin::class,
            'peserta'      => EnsurePeserta::class,
            'log.activity' => LogActivityMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
