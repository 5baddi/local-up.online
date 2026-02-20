<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts;

use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;
use BADDIServices\ClnkGO\Repositories\ScheduledPostRepository;
use BADDIServices\ClnkGO\Http\Filters\ScheduledPostQueryFilter;

class ScheduledPostsController extends DashboardController
{
    public function __invoke(
        ScheduledPostQueryFilter $queryFilter,
        ScheduledPostRepository $scheduledPostRepository
    ): View|Factory {
        $queryFilter->setUser($this->user->getId());

        $scheduledPosts = $scheduledPostRepository->paginate($queryFilter);

        return $this->render(
            'dashboard.posts.scheduled.index',
            [
                'title'             => trans('dashboard.scheduled_posts'),
                'scheduledPosts'    => $scheduledPosts,
            ]
        );
    }
}