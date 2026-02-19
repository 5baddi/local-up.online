<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Account;

use Throwable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use BADDIServices\ClnkGO\Entities\Alert;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;
use BADDIServices\ClnkGO\Models\ObjectValues\GoogleCredentialsObjectValue;

class GoogleMyBusinessCallbackController extends DashboardController
{
    public function __invoke(Request $request): RedirectResponse
    {
        try {
            $code = $request->query('code');
            if (empty($code)) {
                throw new Exception(trans('global.missing_google_auth_code'));
            }

            $googleCredentials = $this->googleService->exchangeAuthenticationCode($code);
            if (empty($googleCredentials)) {
                throw new Exception(trans('global.google_auth_failed'));
            }

            $this->userService->saveGoogleCredentials(
                $this->user,
                GoogleCredentialsObjectValue::fromArray($googleCredentials)
            );

            return redirect()->route('dashboard.account', ['tab' => $request->query('tab', 'gmb')])
                ->with(
                    'alert',
                    new Alert(trans('global.gmb_connected'), 'success')
                );
        } catch (Throwable){
            return redirect()->route('dashboard.account', ['tab' => $request->query('tab', 'gmb')])
                ->with(
                    'alert',
                    new Alert(trans('global.error_connect_gmb'))
                );
        }
    }
}