<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

return [
    'extract_due_date'  =>  [
        'enabled'       => env('EXTRACT_DUE_DATE_FEATURE_ENABLED', false),
        'for'           => env('EXTRACT_DUE_DATE_FEATURE_ENABLED_FOR'),
    ],
    'fetch_tweets'      =>  [
        'enabled'       => env('FETCH_TWEETS_FEATURE_ENABLED', false),
        'for'           => env('FETCH_TWEETS_FEATURE_ENABLED_FOR'),
    ],
    'mark_as_answered'  =>  [
        'enabled'       => env('MARK_AS_ANSWERED_FEATURE_ENABLED', false),
        'for'           => env('MARK_AS_ANSWERED_FEATURE_ENABLED_FOR'),
    ],
    'journalist_area'   =>  [
        'enabled'       => env('JOURNALIST_AREA_FEATURE_ENABLED', false),
        'for'           => env('JOURNALIST_AREA_FEATURE_ENABLED_FOR'),
    ],
    'report_bugs_with_gleap'    =>  [
        'enabled'               => env('REPORT_BUGS_WITH_GLEAP_FEATURE_ENABLED', false),
        'for'                   => env('REPORT_BUGS_WITH_GLEAP_FEATURE_ENABLED_FOR'),
    ],
    'fetch_cpalead_offers'      =>  [
        'enabled'               => env('FETCH_CPALEAD_OFFERS_FEATURE_ENABLED', false),
        'for'                   => env('FETCH_CPALEAD_OFFERS_FEATURE_ENABLED_FOR'),
    ],
    'fetch_news'                =>  [
        'enabled'               => env('FETCH_NEWS_FEATURE_ENABLED', false),
        'for'                   => env('FETCH_NEWS_FEATURE_ENABLED_FOR'),
    ],
];