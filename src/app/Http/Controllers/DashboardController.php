<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Foundation\Bus\DispatchesJobs;
use BADDIServices\ClnkGO\Services\UserService;
use BADDIServices\ClnkGO\Domains\GoogleService;
use BADDIServices\ClnkGO\Models\AccountLocation;
use Illuminate\Routing\Controller as BaseController;
use BADDIServices\ClnkGO\Models\UserGoogleCredentials;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use BADDIServices\ClnkGO\Domains\GoogleMyBusinessService;

class DashboardController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    protected UserService $userService;

    protected GoogleService $googleService;

    protected GoogleMyBusinessService $googleMyBusinessService;

    protected User $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->userService = app(UserService::class);
            $this->googleService = app(GoogleService::class);

            $this->user = Auth::id() !== null ? $this->userService->findById(Auth::id()) : null;

            $this->googleMyBusinessService = new GoogleMyBusinessService(
                $this->user->googleCredentials?->getAccessToken(),
                $this->user->googleCredentials?->getAccountId(),
                $this->user->googleCredentials?->getMainLocationId()
            );

            if (
                ! Route::is(['dashboard.account', 'dashboard.account.*'])
                && ! Route::is('dashboard.errors.*')
                && Route::is(['dashboard', 'dashboard.*'])
                && (
                    ! $this->user->googleCredentials instanceof UserGoogleCredentials
                    || $this->user->googleCredentials->isExpired()
                    || empty($this->user->googleCredentials->getAccountId())
                    || empty($this->user->googleCredentials->getMainLocationId())
                )
            ) {
                return Redirect::route('dashboard.errors.unauthenticated_gmb_access');
            }

            return $next($request);
        });
    }

    public function render(string $name, array $data = []): View|Factory
    {
        return view($name, array_merge($this->defaultData(), $data));
    }

    private function defaultData(): array
    {
        $accountLocations = AccountLocation::query()
            ->where(AccountLocation::USER_ID_COLUMN, $this->user->getId())
            ->get()
            ->toArray();

        return [
            'user'                  => $this->user,
            'userAccountLocations'  => $accountLocations,
        ];
    }
}