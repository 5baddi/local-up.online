<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Account;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use BADDIServices\ClnkGO\Entities\Alert;
use BADDIServices\ClnkGO\Models\AccountLocation;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class GoogleMyBusinessDisconnectController extends DashboardController
{
    public function __invoke(Request $request): RedirectResponse
    {
        try {
            $this->googleService->revokeAccessToken($this->user->googleCredentials);

            AccountLocation::query()
                ->where(AccountLocation::USER_ID_COLUMN, $this->user->getId())
                ->forceDelete();

            return redirect()->route('dashboard.account', ['tab' => $request->query('tab', 'gmb')])
                ->with(
                    'alert',
                    new Alert(trans('dashboard.successfully_gmb_disconnected'), 'success')
                );
        } catch (Throwable){
            return redirect()->route('dashboard.account', ['tab' => $request->query('tab', 'gmb')])
                ->with(
                    'alert',
                    new Alert(trans('dashboard.error_gmb_disconnect'))
                );
        }
    }
}