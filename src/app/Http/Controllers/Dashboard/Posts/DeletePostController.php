<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts;

use Illuminate\Http\RedirectResponse;
use BADDIServices\ClnkGO\Entities\Alert;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class DeletePostController extends DashboardController
{
    public function __invoke(string $id): RedirectResponse
    {
        if (! $this->googleMyBusinessService->deleteBusinessLocationPost($id)) {
            return redirect()
                ->route(
                    'dashboard.posts.view',
                    [
                        'accountId'     => $this->user->googleCredentials->getAccountId(),
                        'locationId'    => $this->user->googleCredentials->getMainLocationId(),
                        'postId'        => $id,
                    ]
                )
                ->with(
                    'alert',
                    new Alert(trans('global.deleting_post_error'))
                );
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'alert',
                new Alert(trans('global.post_deleted'), 'success')
            );
    }
}