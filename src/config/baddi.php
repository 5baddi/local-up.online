<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

return [
    'support_email'     => env('SUPPORT_EMAIL'),
    'version'           => env('APP_VERSION', '1.0.0'),
    'news_api_key'      => env('NEWS_API_KEY'),
    'hcaptcha_verify_endpoint'           => env('HCAPTCHA_VERIFY_ENDPOINT'),
    'hcaptcha_js_endpoint'               => sprintf(
        '%s?hl=%s',
        env('HCAPTCHA_JS_ENDPOINT', 'https://js.hcaptcha.com/1/api.js'),
        app()->getLocale()
    ),
    'hcaptcha_secret'                    => env('HCAPTCHA_SECRET'),
    'hcaptcha_site_key'                  => env('HCAPTCHA_SITE_KEY'),
    'hcaptcha_enabled'                   => env('HCAPTCHA_FEATURE_ENABLED', false),
];