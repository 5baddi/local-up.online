<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts;

use Throwable;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use BADDIServices\ClnkGO\Entities\Alert;
use BADDIServices\ClnkGO\Models\ScheduledPost;
use BADDIServices\ClnkGO\Http\Requests\ScheduledPostRequest;
use BADDIServices\ClnkGO\Http\Controllers\DashboardController;

class SaveScheduledPostController extends DashboardController
{
    public function __invoke(string $type, ScheduledPostRequest $request)
    {
        abort_unless(Arr::has(ScheduledPost::TYPES, $type), Response::HTTP_NOT_FOUND);

        if (
            empty($this->user->googleCredentials?->getAccountId())
            || empty($this->user->googleCredentials?->getMainLocationId())
        ) {
            return redirect()
                ->route('dashboard.scheduled.posts.edit', ['type' => $type])
                ->with(
                    'alert',
                    new Alert(trans('global.main_location_not_set'))
                )
                ->withInput();
        }

        try {
            $isInstantly = empty($request->input('scheduled_date'));

            $scheduledAt = Carbon::parse(
                    sprintf(
                        '%s %s',
                        $request->input('scheduled_date', date('Y-M-d')),
                        $request->input('scheduled_time', '00:00')
                    ),
                    Session::get('timezone', 'UTC')
                )
                ->setTimezone('UTC')
                ->toISOString();

            $eventStartDateTime = sprintf(
                '%s %s',
                $request->input('event_start_date'),
                $request->input('event_start_time')
            );

            $eventEndDateTime = sprintf(
                '%s %s',
                $request->input('event_end_date'),
                $request->input('event_end_time')
            );

            ScheduledPost::query()
                ->updateOrCreate(
                    [ScheduledPost::ID_COLUMN => $request->input('id')],
                    [
                        ScheduledPost::USER_ID_COLUMN       => $this->user->getId(),
                        ScheduledPost::ACCOUNT_ID_COLUMN    => $this->user->googleCredentials->getAccountId(),
                        ScheduledPost::LOCATION_ID_COLUMN   => $this->user->googleCredentials->getMainLocationId(),
                        ScheduledPost::SCHEDULED_AT_COLUMN  => $scheduledAt,
                        ScheduledPost::TOPIC_TYPE_COLUMN    => ScheduledPost::TYPES[$type] ?? ScheduledPost::STANDARD_TYPE,
                        ScheduledPost::STATE_COLUMN         => ScheduledPost::UNSPECIFIED_STATE,
                        ScheduledPost::LANGUAGE_CODE_COLUMN => ScheduledPost::DEFAULT_LANGUAGE_CODE ?? 'fr-FR', // TODO: make dynamic
                        ScheduledPost::SUMMARY_COLUMN       => $request->input(ScheduledPost::SUMMARY_COLUMN, ''),
                        ScheduledPost::ACTION_TYPE_COLUMN   => ScheduledPost::ACTION_TYPES[
                            $request->input(ScheduledPost::ACTION_TYPE_COLUMN, ScheduledPost::LEARN_MORE_ACTION_TYPE)
                        ],
                        ScheduledPost::ACTION_URL_COLUMN    => $request->input(ScheduledPost::ACTION_URL_COLUMN, ''),
                        ScheduledPost::EVENT_TITLE_COLUMN   => $request->input(ScheduledPost::EVENT_TITLE_COLUMN, ''),
                        ScheduledPost::ALERT_TYPE_COLUMN    => ScheduledPost::ALERT_TYPES[
                            $request->input(ScheduledPost::ALERT_TYPE_COLUMN, 'none')
                        ] ?? null,
                        ScheduledPost::OFFER_COUPON_CODE_COLUMN
                        => $request->input(ScheduledPost::OFFER_COUPON_CODE_COLUMN),
                        ScheduledPost::OFFER_REDEEM_ONLINE_URL_COLUMN
                        => $request->input(ScheduledPost::OFFER_REDEEM_ONLINE_URL_COLUMN),
                        ScheduledPost::OFFER_TERMS_CONDITIONS_COLUMN
                        => $request->input(ScheduledPost::OFFER_TERMS_CONDITIONS_COLUMN),
                        ScheduledPost::EVENT_START_DATETIME_COLUMN
                        => blank($eventStartDateTime)
                            ? null
                            : Carbon::parse(
                                $eventStartDateTime,
                                Session::get('timezone', 'UTC')
                            )
                            ->setTimezone('UTC')
                            ->getTimestamp(),
                        ScheduledPost::EVENT_END_DATETIME_COLUMN
                        => blank($eventEndDateTime)
                            ? null
                            : Carbon::parse(
                                $eventEndDateTime,
                                Session::get('timezone', 'UTC')
                            )
                            ->setTimezone('UTC')
                            ->getTimestamp(),
                    ]
                );

            return redirect()->route('dashboard.scheduled.posts')
                ->with(
                    'alert',
                    new Alert(
                        $isInstantly
                            ? trans('global.scheduled_post_posted')
                            : trans('global.scheduled_post_saved'),
                        'success'
                    )
                );
        } catch (Throwable $exception){
            $validationErrors = $exception?->errors ?? [];

            $response = redirect()
                ->route('dashboard.scheduled.posts.edit', ['type' => $type])
                ->withErrors($validationErrors)
                ->withInput();

            if (empty($validationErrors)) {
                $response->with(
                    'alert',
                    new Alert(trans('global.saving_scheduled_post_error'))
                );
            }

            return $response;
        }
    }
}