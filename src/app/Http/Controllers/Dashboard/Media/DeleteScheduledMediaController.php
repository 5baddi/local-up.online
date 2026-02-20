<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media;

use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use BADDIServices\ClnkGO\Entities\Alert;
use BADDIServices\ClnkGO\Models\ScheduledMedia;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class DeleteScheduledMediaController extends DashboardController
{
    public function __invoke(string $id): RedirectResponse
    {
        $scheduledMedia = ScheduledMedia::query()
            ->find($id);

        abort_unless($scheduledMedia instanceof ScheduledMedia, Response::HTTP_NOT_FOUND);

        $scheduledMedia->forceDelete();

        return redirect()->route('dashboard.scheduled.media')
            ->with(
                'alert',
                new Alert(trans('global.scheduled_media_deleted'), 'success')
            );
    }
}