<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media;

use Illuminate\Http\RedirectResponse;
use BADDIServices\ClnkGO\Entities\Alert;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class DeleteMediaController extends DashboardController
{
    public function __invoke(string $id): RedirectResponse
    {
        if (! $this->googleMyBusinessService->deleteBusinessLocationMedia($id)) {
            return redirect()
                ->route('dashboard.media')
                ->with(
                    'alert',
                    new Alert('An error occurred while deleting this media!')
                );
        }

        return redirect()
            ->route('dashboard.media')
            ->with(
                'alert',
                new Alert(trans('The media has been deleted successfully!'), 'success')
            );
    }
}