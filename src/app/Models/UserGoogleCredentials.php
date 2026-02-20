<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Models;

use BADDIServices\ClnkGO\Entities\ModelEntity;

class UserGoogleCredentials extends ModelEntity
{
    public const string USER_ID_COLUMN = 'user_id';
    public const string ACCOUNT_ID_COLUMN = 'account_id';
    public const string ID_TOKEN_COLUMN = 'id_token';
    public const string ACCESS_TOKEN_COLUMN = 'access_token';
    public const string REFRESH_TOKEN_COLUMN = 'refresh_token';
    public const string SCOPE_COLUMN = 'scope';
    public const string TOKEN_TYPE_COLUMN = 'token_type';
    public const string EXPIRES_IN_COLUMN = 'expires_in';
    public const string IS_EXPIRED_COLUMN = 'is_expired';
    public const string CREATED_COLUMN = 'created';
    public const string MAIN_LOCATION_ID_COLUMN = 'main_location_id';

    protected $casts = [
        self::IS_EXPIRED_COLUMN => 'boolean',
    ];

    public function getUserId(): string
    {
        return (string) $this->getAttribute(self::USER_ID_COLUMN);
    }

    public function getAccountId(): string
    {
        return (string) $this->getAttribute(self::ACCOUNT_ID_COLUMN);
    }

    public function getAccessToken(): string
    {
        return (string) $this->getAttribute(self::ACCESS_TOKEN_COLUMN);
    }

    public function getRefreshToken(): string
    {
        return (string) $this->getAttribute(self::REFRESH_TOKEN_COLUMN);
    }

    public function getMainLocationId(): string
    {
        return (string) $this->getAttribute(self::MAIN_LOCATION_ID_COLUMN);
    }

    public function getExpiresIn(): int
    {
        return (int) $this->getAttribute(self::EXPIRES_IN_COLUMN);
    }

    public function isExpired(): bool
    {
        return (bool) $this->getAttribute(self::IS_EXPIRED_COLUMN);
    }

    public function getCreated(): int
    {
        return (int) $this->getAttribute(self::CREATED_COLUMN);
    }
}