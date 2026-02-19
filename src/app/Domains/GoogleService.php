<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Domains;

use Exception;
use Throwable;
use Google\client;
use Carbon\Carbon;
use Google\Service\Oauth2;
use Google_Service_Oauth2;
use Illuminate\Support\Arr;
use BADDIServices\ClnkGO\AppLogger;
use BADDIServices\ClnkGO\Services\Service;
use BADDIServices\ClnkGO\Repositories\UserRepository;
use BADDIServices\ClnkGO\Models\UserGoogleCredentials;
use BADDIServices\ClnkGO\Models\ObjectValues\GoogleCredentialsObjectValue;

class GoogleService extends Service
{
    public const string MANAGE_BUSINESS_SCOPE = 'https://www.googleapis.com/auth/business.manage';
    public const string MANAGE_BUSINESS_PLUS_SCOPE = 'https://www.googleapis.com/auth/plus.business.manage';

    public Client $client;

    public function __construct(private readonly UserRepository $userRepository)
    {
        parent::__construct();

        $this->configure();
    }

    public function generateAuthenticationURL(): string
    {
        return $this->client->createAuthUrl();
    }

    /**
     * @throws \Google\Service\Exception
     */
    public function exchangeAuthenticationCode(string $code): ?array
    {
        $userCredentials = $this->client->fetchAccessTokenWithAuthCode($code);
        if (Arr::has($userCredentials, 'error')) {
            return null;
        }

        $oauth = new Google_Service_Oauth2($this->client);
        $userInfo = $oauth->userinfo->get();
        $userCredentials[UserGoogleCredentials::ACCOUNT_ID_COLUMN] = $userInfo->getId();

        return GoogleCredentialsObjectValue::fromArray($userCredentials)->toArray();
    }

    public function refreshAccessToken(?UserGoogleCredentials $userCredentials = null): void
    {
        if (
            ! $userCredentials instanceof UserGoogleCredentials
            || empty($userCredentials->getUserId())
            || $userCredentials->isExpired()
        ) {
            return;
        }

        try {
            $expiresAt = Carbon::parse($userCredentials->getCreated())->addSeconds($userCredentials->getExpiresIn());
            if ($expiresAt->isFuture()) {
                return;
            }

            $this->client->setAccessToken(json_encode([
                'access_token'  => $userCredentials->getAccessToken(),
                'refresh_token' => $userCredentials->getRefreshToken(),
                'expires_in'    => $userCredentials->getExpiresIn(),
                'created'       => $userCredentials->getCreated(),
                'id_token'      => $userCredentials->getAttribute(UserGoogleCredentials::ID_TOKEN_COLUMN),
                'scope'         => $userCredentials->getAttribute(UserGoogleCredentials::SCOPE_COLUMN),
                'token_type'    => $userCredentials->getAttribute(UserGoogleCredentials::TOKEN_TYPE_COLUMN),
            ]));

            if (! $this->client->isAccessTokenExpired()) {
                return;
            }

            $response = $this->client->fetchAccessTokenWithRefreshToken($userCredentials->getRefreshToken());

            if (Arr::has($response, 'error')) {
                AppLogger::error(
                    new Exception('Error while refreshing google access token'),
                    'google:refresh-access-token',
                    array_merge($response, ['payload' => $userCredentials?->toArray() ?? []])
                );

                $this->userRepository->markGoogleCredentialsAsExpired($userCredentials->getUserId());

                return;
            }

            $attributes = GoogleCredentialsObjectValue::fromArray(array_merge(
                    $response,
                    [
                        UserGoogleCredentials::REFRESH_TOKEN_COLUMN
                        => blank($response[UserGoogleCredentials::REFRESH_TOKEN_COLUMN] ?? null)
                            ? $userCredentials->getRefreshToken()
                            : $response[UserGoogleCredentials::REFRESH_TOKEN_COLUMN],
                        UserGoogleCredentials::ACCOUNT_ID_COLUMN        => $userCredentials->getAccountId(),
                        UserGoogleCredentials::MAIN_LOCATION_ID_COLUMN  => $userCredentials->getMainLocationId(),
                    ]
                ));

            $this->userRepository->saveGoogleCredentials($userCredentials->getUserId(), $attributes->toArray());
        } catch (Throwable $e) {
            AppLogger::error(
                $e,
                'google:refresh-access-token',
                $userCredentials?->toArray() ?? []
            );

            $this->userRepository->markGoogleCredentialsAsExpired($userCredentials->getUserId());
        }
    }

    public function revokeAccessToken(?UserGoogleCredentials $userCredentials = null): void
    {
        if (
            ! $userCredentials instanceof UserGoogleCredentials
            || empty($userCredentials->getUserId())
        ) {
            return;
        }

        try {
            if (! empty($userCredentials->getAccessToken())) {
                $this->client->revokeToken($userCredentials->getAccessToken());
            }
        } catch (Throwable) {}

        $this->userRepository->deleteGoogleCredentials($userCredentials->getUserId());
    }

    private function configure(): void
    {
        $this->client = new client();

        try {
            $this->client->setAuthConfig(config_path('google.json'));
            $this->client->setApprovalPrompt('force');
            $this->client->setAccessType('offline');
            $this->client->setIncludeGrantedScopes(true);
            $this->client->setRedirectUri(route('dashboard.account.gmb.callback'));

            $this->client->addScope([
                Oauth2::USERINFO_PROFILE,
                Oauth2::USERINFO_EMAIL,
                Oauth2::OPENID,
                self::MANAGE_BUSINESS_SCOPE,
                self::MANAGE_BUSINESS_PLUS_SCOPE,
            ]);
        } catch (Exception) {}
    }
}