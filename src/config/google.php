<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

return [
    'client_id'     => env('GOOGLE_CLIENT_ID'),
    'project_id'    => env('GOOGLE_PROJECT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'auth_uri'      => 'https://accounts.google.com/o/oauth2/auth',
    'token_uri'     => 'https://oauth2.googleapis.com/token',
    'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
    'redirect_uris' => [
        env('APP_URL') . '/dashboard/account/gmb/callback',
    ],
];
