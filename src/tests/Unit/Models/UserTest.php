<?php

use App\Models\User;

describe('User::isEmailConfirmed()', function () {
    it('returns true when verified_at is set and confirmation_token is null', function () {
        $user = User::factory()->make([
            'verified_at'        => now(),
            'confirmation_token' => null,
        ]);

        expect($user->isEmailConfirmed())->toBeTrue();
    });

    it('returns false when verified_at is null', function () {
        $user = User::factory()->make([
            'verified_at'        => null,
            'confirmation_token' => null,
        ]);

        expect($user->isEmailConfirmed())->toBeFalse();
    });

    it('returns false when confirmation_token is present', function () {
        $user = User::factory()->make([
            'verified_at'        => now(),
            'confirmation_token' => 'pending-token',
        ]);

        expect($user->isEmailConfirmed())->toBeFalse();
    });

    it('always returns true for super-admins', function () {
        $user = User::factory()->superAdmin()->make([
            'verified_at'        => null,
            'confirmation_token' => 'pending-token',
        ]);

        expect($user->isEmailConfirmed())->toBeTrue();
    });
});

describe('User::isBanned()', function () {
    it('returns true when the user is banned', function () {
        $user = User::factory()->banned()->make();

        expect($user->isBanned())->toBeTrue();
    });

    it('returns false for a normal user', function () {
        $user = User::factory()->make();

        expect($user->isBanned())->toBeFalse();
    });
});

describe('User::isSuperAdmin()', function () {
    it('returns true for a super-admin', function () {
        $user = User::factory()->superAdmin()->make();

        expect($user->isSuperAdmin())->toBeTrue();
    });

    it('returns false for a regular user', function () {
        $user = User::factory()->make();

        expect($user->isSuperAdmin())->toBeFalse();
    });
});

describe('User email normalisation', function () {
    it('stores the email in lower case', function () {
        $user = User::factory()->make(['email' => 'User@EXAMPLE.COM']);

        expect($user->email)->toBe('user@example.com');
    });
});

describe('User helpers', function () {
    it('returns the full name', function () {
        $user = User::factory()->make([
            'first_name' => 'John',
            'last_name'  => 'Doe',
        ]);

        expect($user->getFullName())->toContain('John')->toContain('Doe');
    });
});
