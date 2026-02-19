<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media;

use Throwable;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use BADDIServices\ClnkGO\AppLogger;
use Illuminate\Support\Facades\Session;
use BADDIServices\ClnkGO\Models\ScheduledMedia;
use BADDIServices\ClnkGO\Http\Requests\ScheduledMediaRequest;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class UploadMediaController extends DashboardController
{
    public function __invoke(ScheduledMediaRequest $request): void
    {
        $files = $request->file('file', []);
        $oldFiles = [];
            
        abort_if(empty($files), Response::HTTP_UNPROCESSABLE_ENTITY);

        abort_if(
            empty($this->user->googleCredentials?->getAccountId())
            || empty($this->user->googleCredentials?->getMainLocationId()),
            Response::HTTP_BAD_REQUEST
        );

        try {
            DB::beginTransaction();

            if (Str::isUuid($request->input('id'))) {
                ScheduledMedia::query()
                    ->find($request->input('id'))
                    ?->forceDelete();
            }

            $isInstantly = empty($request->input('scheduled_date'));

            $scheduledAt = Carbon::parse(
                    sprintf(
                        '%s %s',
                        $request->input('scheduled_date', date('Y-M-d')),
                        $request->input('scheduled_time', '00:00')
                    ),
                    Session::get('timezone', 'UTC')
                )
                ->setTimezone('UTC');

            $paths = [];

            foreach ($files as $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $fileName = sprintf('%d_%s', time(), $file->getClientOriginalName());
                $file->move(public_path('uploads'), $fileName);

                $type = explode('/', $file->getClientMimeType())[0] ?? null;

                $paths[] = [
                    ScheduledMedia::PATH  => sprintf('uploads/%s', $fileName),
                    ScheduledMedia::TYPE  => $type === 'image'
                        ? ScheduledMedia::PHOTO_TYPE
                        : ScheduledMedia::VIDEO_TYPE,
                ];
            }

            ScheduledMedia::query()
                ->create([
                    ScheduledMedia::USER_ID_COLUMN      => $this->user->getId(),
                    ScheduledMedia::ACCOUNT_ID_COLUMN   => $this->user->googleCredentials->getAccountId(),
                    ScheduledMedia::LOCATION_ID_COLUMN  => $this->user->googleCredentials->getMainLocationId(),
                    ScheduledMedia::FILES_COLUMN        => $paths,
                    ScheduledMedia::STATE_COLUMN        => ScheduledMedia::UNSPECIFIED_STATE,
                    ScheduledMedia::SCHEDULED_AT_COLUMN => $scheduledAt->toISOString(),
                    ScheduledMedia::SCHEDULED_FREQUENCY_COLUMN
                    => $isInstantly ? null : $request->input(ScheduledMedia::SCHEDULED_FREQUENCY_COLUMN),
                ]);

            DB::commit();
        } catch (Throwable $e){
            DB::rollBack();

            AppLogger::error(
                $e,
                'scheduled-media:upload-new-media',
                ['payload' => $request->toArray()]
            );

            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}