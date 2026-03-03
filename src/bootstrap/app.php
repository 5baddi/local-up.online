<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(function () {
                    $webFiles = array_merge(
                        glob(__DIR__.'/../routes/web/**/*.php') ?: [],
                        glob(__DIR__.'/../routes/web/*.php') ?: []
                    );
                    foreach ($webFiles as $routeFile) {
                        require $routeFile;
                    }
                });

            Route::middleware('api')
                ->prefix('api')
                ->group(function () {
                    $apiFiles = array_merge(
                        glob(__DIR__.'/../routes/api/**/*.php') ?: [],
                        glob(__DIR__.'/../routes/api/*.php') ?: []
                    );
                    foreach ($apiFiles as $routeFile) {
                        require $routeFile;
                    }
                });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware stack
        $middleware->use([
            \Illuminate\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Http\Middleware\ValidatePostSize::class,
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \BADDIServices\ClnkGO\Http\Middleware\BlockRobotsMiddleware::class,
        ]);

        // Web middleware group
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // API middleware group
        $middleware->group('api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Adminer middleware group
        $middleware->group('adminer', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
        ]);

        // Named middleware aliases
        $middleware->alias([
            'auth'          => \App\Http\Middleware\Authenticate::class,
            'auth.basic'    => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can'           => \Illuminate\Auth\Middleware\Authorize::class,
            'guest'         => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed'        => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle'      => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified'      => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'is.super-admin' => \BADDIServices\ClnkGO\Http\Middleware\IsSuperAdmin::class,
        ]);

        // Trust proxies configuration
        $middleware->trustProxies(
            at: '*',
            headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB
        );

        // CSRF token exclusions
        $middleware->validateCsrfTokens(except: [
            '/webceo/callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->command('auto-post:scheduled-posts')->everyMinute()->withoutOverlapping();
        $schedule->command('auto-post:scheduled-media')->everyMinute()->withoutOverlapping();
        $schedule->command('remove:outdated-draft-scheduled-posts')->dailyAt('00:00:00')->withoutOverlapping();
        $schedule->command('user:refresh-google-access-token')->everyMinute()->withoutOverlapping();
    })
    ->create();
