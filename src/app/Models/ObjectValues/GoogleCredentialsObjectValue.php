<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\ClnkGO\Models\ObjectValues;

use BADDIServices\ClnkGO\Models\UserGoogleCredentials;

readonly class GoogleCredentialsObjectValue
{
    public function __construct(
        private string $id_token,
        private string $account_id,
        private string $access_token,
        private string $refresh_token,
        private string $scope,
        private string $token_type,
        private int $expires_in,
        private bool $is_expired,
        private int $created,
        private string $main_location_id
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function getId(): string
    {
        return $this->id_token;
    }

    public function getAccountId(): string
    {
        return $this->account_id;
    }

    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    public static function fromArray(array $attributes): self
    {
        return new self(
            $attributes[UserGoogleCredentials::ID_TOKEN_COLUMN] ?? '',
            $attributes[UserGoogleCredentials::ACCOUNT_ID_COLUMN] ?? '',
            $attributes[UserGoogleCredentials::ACCESS_TOKEN_COLUMN] ?? '',
            $attributes[UserGoogleCredentials::REFRESH_TOKEN_COLUMN] ?? '',
            $attributes[UserGoogleCredentials::SCOPE_COLUMN] ?? '',
            $attributes[UserGoogleCredentials::TOKEN_TYPE_COLUMN] ?? '',
            $attributes[UserGoogleCredentials::EXPIRES_IN_COLUMN] ?? 0,
            ($attributes[UserGoogleCredentials::IS_EXPIRED_COLUMN] ?? false) === true,
            $attributes[UserGoogleCredentials::CREATED_COLUMN] ?? 0,
            $attributes[UserGoogleCredentials::MAIN_LOCATION_ID_COLUMN] ?? ''
        );
    }
}