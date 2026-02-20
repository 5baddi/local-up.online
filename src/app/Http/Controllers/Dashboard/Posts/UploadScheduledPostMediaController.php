<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts;

use Throwable;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use BADDIServices\ClnkGO\Models\ScheduledPost;
use BADDIServices\ClnkGO\Models\ScheduledPostMedia;
use BADDIServices\ClnkGO\Http\Requests\ScheduledMediaRequest;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class UploadScheduledPostMediaController extends DashboardController
{
    public function __invoke(string $id, ScheduledMediaRequest $request): void
    {
        try {
            $files = $request->file('file', []);

            abort_if(empty($files), Response::HTTP_UNPROCESSABLE_ENTITY);

            ScheduledPost::query()
                ->firstOrCreate(
                    [ScheduledPost::ID_COLUMN => $id],
                    [
                        ScheduledPost::ID_COLUMN        => $id,
                        ScheduledPost::USER_ID_COLUMN   => $this->user->getId(),
                        ScheduledPost::STATE_COLUMN     => ScheduledPost::UNSPECIFIED_STATE,
                    ]
                );

            foreach ($files as $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $fileName = sprintf('%d%d_%s', time(), rand(1,99), $file->getClientOriginalName());
                $file->move(public_path('uploads'), $fileName);

                ScheduledPostMedia::query()
                    ->firstOrCreate(
                        [ScheduledPostMedia::SCHEDULED_POST_ID_COLUMN => $id],
                        [
                            ScheduledPostMedia::SCHEDULED_POST_ID_COLUMN => $id,
                            ScheduledPostMedia::PATH_COLUMN => sprintf('uploads/%s', $fileName)
                        ]
                    );
            }
        } catch (Throwable){
            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}