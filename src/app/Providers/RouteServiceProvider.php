<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->namespace($this->namespace)
                ->group(function () {
                    $apiFiles = glob(__DIR__ . '/../../routes/api/**/*.php');
                    $signleApiFiles = glob(__DIR__ . '/../../routes/api/*.php');

                    foreach (array_merge($apiFiles, $signleApiFiles) as $routeFile) {
                        require $routeFile;
                    }
                });

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(function () {
                    $webFiles = glob(__DIR__ . '/../../routes/web/**/*.php');
                    $signleWebFiles = glob(__DIR__ . '/../../routes/web/*.php');

                    foreach (array_merge($webFiles, $signleWebFiles) as $routeFile) {
                        require $routeFile;
                    }
                });
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
