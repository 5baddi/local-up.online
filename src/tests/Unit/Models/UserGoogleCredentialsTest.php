<?php

use BADDIServices\ClnkGO\Models\UserGoogleCredentials;

describe('UserGoogleCredentials::isExpired()', function () {
    it('returns true when is_expired is true', function () {
        $creds = new UserGoogleCredentials([
            UserGoogleCredentials::IS_EXPIRED_COLUMN => true,
        ]);

        expect($creds->isExpired())->toBeTrue();
    });

    it('returns false when is_expired is false', function () {
        $creds = new UserGoogleCredentials([
            UserGoogleCredentials::IS_EXPIRED_COLUMN => false,
        ]);

        expect($creds->isExpired())->toBeFalse();
    });

    it('returns false when is_expired is not set', function () {
        $creds = new UserGoogleCredentials([]);

        expect($creds->isExpired())->toBeFalse();
    });
});

describe('UserGoogleCredentials::getAccessToken()', function () {
    it('returns the stored access token as a string', function () {
        $creds = new UserGoogleCredentials([
            UserGoogleCredentials::ACCESS_TOKEN_COLUMN => 'my-access-token',
        ]);

        expect($creds->getAccessToken())->toBe('my-access-token');
    });

    it('returns an empty string when access_token is null', function () {
        $creds = new UserGoogleCredentials([
            UserGoogleCredentials::ACCESS_TOKEN_COLUMN => null,
        ]);

        expect($creds->getAccessToken())->toBe('');
    });
});

describe('UserGoogleCredentials::getRefreshToken()', function () {
    it('returns the stored refresh token as a string', function () {
        $creds = new UserGoogleCredentials([
            UserGoogleCredentials::REFRESH_TOKEN_COLUMN => 'my-refresh-token',
        ]);

        expect($creds->getRefreshToken())->toBe('my-refresh-token');
    });
});

describe('UserGoogleCredentials::getAccountId()', function () {
    it('returns the account ID as a string', function () {
        $creds = new UserGoogleCredentials([
            UserGoogleCredentials::ACCOUNT_ID_COLUMN => '1234567890',
        ]);

        expect($creds->getAccountId())->toBe('1234567890');
    });

    it('returns an empty string when account_id is null', function () {
        $creds = new UserGoogleCredentials([]);

        expect($creds->getAccountId())->toBe('');
    });
});

describe('UserGoogleCredentials::getMainLocationId()', function () {
    it('returns the main location ID as a string', function () {
        $creds = new UserGoogleCredentials([
            UserGoogleCredentials::MAIN_LOCATION_ID_COLUMN => 'loc-987',
        ]);

        expect($creds->getMainLocationId())->toBe('loc-987');
    });

    it('returns an empty string when main_location_id is null', function () {
        $creds = new UserGoogleCredentials([]);

        expect($creds->getMainLocationId())->toBe('');
    });
});

describe('UserGoogleCredentials::getExpiresIn()', function () {
    it('returns the expires_in value as an integer', function () {
        $creds = new UserGoogleCredentials([
            UserGoogleCredentials::EXPIRES_IN_COLUMN => 3600,
        ]);

        expect($creds->getExpiresIn())->toBe(3600);
    });

    it('returns 0 when expires_in is not set', function () {
        $creds = new UserGoogleCredentials([]);

        expect($creds->getExpiresIn())->toBe(0);
    });
});

describe('UserGoogleCredentials::getCreated()', function () {
    it('returns the created timestamp as an integer', function () {
        $timestamp = time();

        $creds = new UserGoogleCredentials([
            UserGoogleCredentials::CREATED_COLUMN => $timestamp,
        ]);

        expect($creds->getCreated())->toBe($timestamp);
    });

    it('returns 0 when created is not set', function () {
        $creds = new UserGoogleCredentials([]);

        expect($creds->getCreated())->toBe(0);
    });
});

describe('UserGoogleCredentials::getUserId()', function () {
    it('returns the user ID as a string', function () {
        $creds = new UserGoogleCredentials([
            UserGoogleCredentials::USER_ID_COLUMN => 'uuid-abc-123',
        ]);

        expect($creds->getUserId())->toBe('uuid-abc-123');
    });
});
