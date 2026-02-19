<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Models;

use App\Models\User;
use BADDIServices\ClnkGO\Entities\ModelEntity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledMedia extends ModelEntity
{
    public const string USER_ID_COLUMN = 'user_id';
    public const string ACCOUNT_ID_COLUMN = 'account_id';
    public const string LOCATION_ID_COLUMN = 'location_id';
    public const string FILES_COLUMN = 'files';
    public const string STATE_COLUMN = 'state';
    public const string REASON_COLUMN = 'reason';
    public const string SCHEDULED_AT_COLUMN = 'scheduled_at';
    public const string SCHEDULED_FREQUENCY_COLUMN = 'scheduled_frequency';

    public const string TYPE = 'type';
    public const string PATH = 'path';

    public const string PHOTO_TYPE = 'photo';
    public const string VIDEO_TYPE = 'video';

    public const array TYPES = [
        self::PHOTO_TYPE    => 'PHOTO',
        self::VIDEO_TYPE    => 'VIDEO',
    ];

    public const string UNSPECIFIED_STATE = 'unspecified';
    public const string REJECTED_STATE = 'rejected';

    public const array STATES = [
        self::UNSPECIFIED_STATE => 'UNSPECIFIED',
        self::REJECTED_STATE    => 'REJECTED',
    ];

    public const string DAILY_SCHEDULED_FREQUENCY = 'daily';
    public const string EVERY_3_DAYS_SCHEDULED_FREQUENCY = '3_days';
    public const string WEEKLY_SCHEDULED_FREQUENCY = 'weekly';

    public const array SCHEDULED_FREQUENCIES = [
        self::DAILY_SCHEDULED_FREQUENCY,
        self::EVERY_3_DAYS_SCHEDULED_FREQUENCY,
        self::WEEKLY_SCHEDULED_FREQUENCY,
    ];

    protected $dates = [
        self::SCHEDULED_AT_COLUMN,
    ];

    protected $casts = [
        self::FILES_COLUMN => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, User::ID_COLUMN);
    }
}