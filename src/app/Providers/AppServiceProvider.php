<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\ServiceProvider;
use Creativeorange\Gravatar\Facades\Gravatar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::defaultMorphKeyType('uuid');
        Builder::defaultStringLength(191);

        Carbon::setLocale(config('app.locale', 'en'));

        if (app()->environment() !== 'local') {
	        URL::forceScheme('https');
        }

        view()->composer(['partials.dashboard.*', 'partials.admin.*'], function ($view) {
            $user = Auth::user();
            $avatar = $user instanceof User ? Gravatar::get($user->email) : null;

            $view->with('user', $user);
            $view->with('avatar', $avatar);
        });
    }
}
