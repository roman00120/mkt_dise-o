<?php

use App\Http\Middleware\EnsurePasswordChanged;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\RequestCorrelationId;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$application = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'active' => EnsureUserIsActive::class,
            'role' => EnsureUserHasRole::class,
            'password.changed' => EnsurePasswordChanged::class,
        ]);
        $middleware->append(SecurityHeaders::class);
        $middleware->append(RequestCorrelationId::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->is('logout') || $request->isMethod('POST')) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect()->route('login')->with('status', 'Tu sesión ha finalizado. Por favor ingresa de nuevo.');
        });
    })->create();

date_default_timezone_set((string) ($_ENV['APP_TIMEZONE'] ?? $_SERVER['APP_TIMEZONE'] ?? 'America/Mexico_City'));

return $application;
