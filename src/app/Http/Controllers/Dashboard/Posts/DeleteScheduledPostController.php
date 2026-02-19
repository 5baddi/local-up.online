<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts;

use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use BADDIServices\ClnkGO\Entities\Alert;
use BADDIServices\ClnkGO\Models\ScheduledPost;
use BADDIServices\ClnkGO\Models\ScheduledPostMedia;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class DeleteScheduledPostController extends DashboardController
{
    public function __invoke(string $id): RedirectResponse
    {
        $scheduledPost = ScheduledPost::query()
            ->find($id);

        abort_unless($scheduledPost instanceof ScheduledPost, Response::HTTP_NOT_FOUND);

        ScheduledPostMedia::query()
            ->where([ScheduledPostMedia::SCHEDULED_POST_ID_COLUMN => $scheduledPost->id])
            ->forceDelete();

        $scheduledPost->forceDelete();

        return redirect()->route('dashboard.scheduled.posts')
            ->with(
                'alert',
                new Alert(trans('global.scheduled_post_deleted'), 'success')
            );
    }
}