<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Models\ObjectValues;

use Carbon\Carbon;
use BADDIServices\ClnkGO\Models\ScheduledPost;

readonly class GoogleBusinessLocalPostObjectValue
{
    public function __construct(
        private string $summary,
        private array $callToAction = [],
        private array $event = [],
        private array $offer = [],
        private array $media = [],
        private string $topicType = 'STANDARD',
        private string $languageCode = 'en-US',
        private ?string $alertType = null
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this));
    }

    public static function fromArray(array $attributes): self
    {
        $attributes[ScheduledPost::TOPIC_TYPE_COLUMN] = $attributes[ScheduledPost::TOPIC_TYPE_COLUMN] ?? ScheduledPost::STANDARD_TYPE;
        $attributes[ScheduledPost::LANGUAGE_CODE_COLUMN] = $attributes[ScheduledPost::LANGUAGE_CODE_COLUMN] ?? ScheduledPost::DEFAULT_LANGUAGE_CODE;
        $attributes[ScheduledPost::SUMMARY_COLUMN] = $attributes[ScheduledPost::SUMMARY_COLUMN] ?? '';

        $callToAction = [
            'actionType' => $attributes[ScheduledPost::ACTION_TYPE_COLUMN]
                ?? ScheduledPost::ACTION_TYPES[ScheduledPost::LEARN_MORE_ACTION_TYPE],
        ];

        if ($callToAction['actionType'] !== ScheduledPost::ACTION_TYPES[ScheduledPost::CALL_ACTION_TYPE]) {
            $callToAction['url'] = $attributes[ScheduledPost::ACTION_URL_COLUMN] ?? '';
        }

        $alertType = null;
        if ($attributes[ScheduledPost::TOPIC_TYPE_COLUMN] === ScheduledPost::ALERT_TYPE) {
            $alertType = $attributes[ScheduledPost::ALERT_TYPE_COLUMN] ?? ScheduledPost::UNSPECIFIED_ALERT_TYPE;
        }

        $event = [];
        if (
            $attributes[ScheduledPost::TOPIC_TYPE_COLUMN] === ScheduledPost::EVENT_TYPE
            && ! empty($attributes[ScheduledPost::EVENT_START_DATETIME_COLUMN])
            && ! empty($attributes[ScheduledPost::EVENT_END_DATETIME_COLUMN])
        ) {
            $startDateTime = Carbon::parse($attributes[ScheduledPost::EVENT_START_DATETIME_COLUMN]);
            $endDateTime = Carbon::parse($attributes[ScheduledPost::EVENT_END_DATETIME_COLUMN]);

            $event = [
                'title'     => $attributes[ScheduledPost::EVENT_TITLE_COLUMN] ?? '',
                'schedule'  => [
                    'startDate' => [
                        'year'      => $startDateTime->year,
                        'month'     => $startDateTime->month,
                        'day'       => $startDateTime->day,
                    ],
                    'startTime' => [
                        'hours'         => $startDateTime->hour,
                        'minutes'       => $startDateTime->minute,
                        'seconds'       => 0,
                        'nanos'         => 0,
                    ],
                    'endDate' => [
                        'year'      => $endDateTime->year,
                        'month'     => $endDateTime->month,
                        'day'       => $endDateTime->day,
                    ],
                    'endTime' => [
                        'hours'         => $endDateTime->hour,
                        'minutes'       => $endDateTime->minute,
                        'seconds'       => 0,
                        'nanos'         => 0,
                    ],
                ]
            ];
        }

        $offer = [];
        if ($attributes[ScheduledPost::TOPIC_TYPE_COLUMN] === ScheduledPost::OFFER_TYPE) {
            $offer = [
                'couponCode'        => $attributes[ScheduledPost::OFFER_COUPON_CODE_COLUMN] ?? '',
                'redeemOnlineUrl'   => $attributes[ScheduledPost::OFFER_REDEEM_ONLINE_URL_COLUMN] ?? '',
                'termsConditions'   => $attributes[ScheduledPost::OFFER_TERMS_CONDITIONS_COLUMN] ?? '',
            ];
        }

        $media = $attributes['media'] ?? [];

        return new self(
            $attributes[ScheduledPost::SUMMARY_COLUMN],
            $callToAction,
            $event,
            $offer,
            $media,
            $attributes[ScheduledPost::TOPIC_TYPE_COLUMN],
            $attributes[ScheduledPost::LANGUAGE_CODE_COLUMN],
            $alertType
        );
    }
}