<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media;

use Illuminate\Support\Str;
use BADDIServices\ClnkGO\Models\ScheduledMedia;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class NewMediaController extends DashboardController
{
    public function __invoke(?string $id = null)
    {
        if (Str::isUuid($id)) {
            $scheduledMedia = ScheduledMedia::query()
                ->find($id);
        }

        return $this->render(
            'dashboard.media.create',
            [
                'title'             => trans('global.upload_new_media'),
                'scheduledMedia'    => $scheduledMedia ?? null,
            ]
        );
    }
}