<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Account;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class AccountController extends DashboardController
{
    public function __invoke(Request $request): Factory|View|Response
    {
        return $this->render(
            'dashboard.account.index',
            [
                'title'         => trans('global.account'),
                'tab'           => $request->query('tab', 'gmb'),
                'user'          => $this->user,
                'callbackURL'   => $this->googleService->generateAuthenticationURL(),
            ]
        );
    }
}