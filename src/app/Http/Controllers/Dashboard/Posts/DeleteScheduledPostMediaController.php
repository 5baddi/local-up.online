<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use BADDIServices\ClnkGO\Models\ScheduledPostMedia;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class DeleteScheduledPostMediaController extends DashboardController
{
    public function __invoke(string $id, Request $request): void
    {
        try {
            abort_if(empty($request->input('filename')), Response::HTTP_UNPROCESSABLE_ENTITY);

            $media = ScheduledPostMedia::query()
                ->where(ScheduledPostMedia::SCHEDULED_POST_ID_COLUMN, $id)
                ->where(
                    ScheduledPostMedia::PATH_COLUMN,
                    sprintf('uploads/%s', $request->input('filename'))
                )
                ->forceDelete();
        } catch (Throwable){
            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}