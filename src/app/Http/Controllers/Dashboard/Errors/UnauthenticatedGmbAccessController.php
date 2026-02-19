<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Errors;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class UnauthenticatedGmbAccessController extends DashboardController
{
    public function __invoke(Request $request): Factory|View|Response
    {
        return $this->render(
            'dashboard.errors.unauthenticated_gmb_access',
            [
                'title'         => trans('global.unauthenticated_gmb_title'),
                'user'          => $this->user,
                'callbackURL'   => $this->googleService->generateAuthenticationURL(),
            ]
        );
    }
}