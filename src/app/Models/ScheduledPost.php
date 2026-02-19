<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Models;

use BADDIServices\ClnkGO\Entities\ModelEntity;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduledPost extends ModelEntity
{
    public const string USER_ID_COLUMN = 'user_id';
    public const string ACCOUNT_ID_COLUMN = 'account_id';
    public const string LOCATION_ID_COLUMN = 'location_id';
    public const string SUMMARY_COLUMN = 'summary';
    public const string ACTION_TYPE_COLUMN = 'action_type';
    public const string ACTION_URL_COLUMN = 'action_url';
    public const string TOPIC_TYPE_COLUMN = 'topic_type';
    public const string ALERT_TYPE_COLUMN = 'alert_type';
    public const string LANGUAGE_CODE_COLUMN = 'language_code';
    public const string STATE_COLUMN = 'state';
    public const string OFFER_COUPON_CODE_COLUMN = 'offer_coupon_code';
    public const string OFFER_REDEEM_ONLINE_URL_COLUMN = 'offer_redeem_online_url';
    public const string OFFER_TERMS_CONDITIONS_COLUMN = 'offer_terms_conditions';
    public const string EVENT_TITLE_COLUMN = 'event_title';
    public const string EVENT_START_DATETIME_COLUMN = 'event_start_datetime';
    public const string EVENT_END_DATETIME_COLUMN = 'event_end_datetime';
    public const string SCHEDULED_AT_COLUMN = 'scheduled_at';
    public const string REASON_COLUMN = 'reason';
    public const string ONLINE_ID_COLUMN = 'online_id';

    public const string STANDARD_TYPE = 'standard';
    public const string EVENT_TYPE = 'event';
    public const string OFFER_TYPE = 'offer';
    public const string ALERT_TYPE = 'alert';

    public const array TYPES = [
        self::STANDARD_TYPE => 'STANDARD',
        self::EVENT_TYPE    => 'EVENT',
        self::OFFER_TYPE    => 'OFFER',
        self::ALERT_TYPE    => 'ALERT',
    ];

    public const string UNSPECIFIED_STATE = 'unspecified';
    public const string REJECTED_STATE = 'rejected';
    public const string LIVE_STATE = 'live';
    public const string PROCESSING_STATE = 'processing';

    public const array STATES = [
        self::UNSPECIFIED_STATE => 'LOCAL_POST_STATE_UNSPECIFIED',
        self::REJECTED_STATE    => 'REJECTED',
        self::LIVE_STATE        => 'LIVE',
        self::PROCESSING_STATE  => 'PROCESSING',
    ];

    public const string UNSPECIFIED_ALERT_TYPE = 'unspecified';
    public const string COVID_19_ALERT_TYPE = 'covid_19';

    public const array ALERT_TYPES = [
        self::UNSPECIFIED_ALERT_TYPE    => 'ALERT_TYPE_UNSPECIFIED',
        self::COVID_19_ALERT_TYPE       => 'COVID_19',
    ];

    public const string UNSPECIFIED_ACTION_TYPE = 'unspecified';
    public const string BOOK_ACTION_TYPE = 'book';
    public const string ORDER_ACTION_TYPE = 'order';
    public const string SHOP_ACTION_TYPE = 'shop';
    public const string LEARN_MORE_ACTION_TYPE = 'learn_more';
    public const string SIGN_UP_ACTION_TYPE = 'sign_up';
    public const string GET_OFFER_ACTION_TYPE = 'get_offer';
    public const string CALL_ACTION_TYPE = 'call';

    public const array ACTION_TYPES = [
        self::UNSPECIFIED_ACTION_TYPE   => 'ACTION_TYPE_UNSPECIFIED',
        self::BOOK_ACTION_TYPE          => 'BOOK',
        self::ORDER_ACTION_TYPE         => 'ORDER',
        self::SHOP_ACTION_TYPE          => 'SHOP',
        self::LEARN_MORE_ACTION_TYPE    => 'LEARN_MORE',
        self::SIGN_UP_ACTION_TYPE       => 'SIGN_UP',
        self::GET_OFFER_ACTION_TYPE     => 'GET_OFFER',
        self::CALL_ACTION_TYPE          => 'CALL',
    ];

    protected $dates = [
        self::SCHEDULED_AT_COLUMN,
    ];

    public const string DEFAULT_LANGUAGE_CODE = 'en-US';

    public function media(): HasMany
    {
        return $this->hasMany(ScheduledPostMedia::class, ScheduledPostMedia::SCHEDULED_POST_ID_COLUMN);
    }
}