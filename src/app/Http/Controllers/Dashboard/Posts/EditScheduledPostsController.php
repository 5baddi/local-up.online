<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use BADDIServices\ClnkGO\Models\ScheduledPost;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class EditScheduledPostsController extends DashboardController
{
    public function __invoke(string $type, ?string $id = null): View|Factory
    {
        abort_unless(Arr::has(ScheduledPost::TYPES, $type), Response::HTTP_NOT_FOUND);

        $scheduledPost = ScheduledPost::query()
            ->with(['media'])
            ->find($id);

        if ($scheduledPost instanceof ScheduledPost) {
            $type = strtolower($scheduledPost->getAttribute(ScheduledPost::TOPIC_TYPE_COLUMN) ?? ScheduledPost::STANDARD_TYPE);
        }

        return $this->render(
            sprintf('dashboard.posts.scheduled.%s', $type),
            [
                'title' => ucfirst(strtolower(sprintf(
                    '%s %s',
                    trans('dashboard.scheduled_post'),
                    trans(sprintf('global.%s', $type))
                ))),
                'cardTitle' => ucfirst(strtolower(sprintf(
                    '%s <b>%s</b>',
                    trans('dashboard.scheduled_post'),
                    trans(sprintf('global.%s', $type))
                ))),
                'type'          => $type,
                'id'            => $id ?? Uuid::uuid4()->toString(),
                'scheduledPost' => $scheduledPost,
            ]
        );
    }
}