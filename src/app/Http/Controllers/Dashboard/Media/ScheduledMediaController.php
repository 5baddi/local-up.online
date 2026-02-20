<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media;

use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;
use BADDIServices\ClnkGO\Repositories\ScheduledMediaRepository;
use BADDIServices\ClnkGO\Http\Filters\ScheduledMediaQueryFilter;

class ScheduledMediaController extends DashboardController
{
    public function __invoke(
        ScheduledMediaQueryFilter $queryFilter,
        ScheduledMediaRepository $scheduledMediaRepository
    ): View|Factory {
        $queryFilter->setUser($this->user->getId());

        $scheduledMedia = $scheduledMediaRepository->paginate($queryFilter);

        return $this->render(
            'dashboard.media.scheduled.index',
            [
                'title'             => trans('global.scheduled_media'),
                'scheduledMedia'    => $scheduledMedia,
            ]
        );
    }
}