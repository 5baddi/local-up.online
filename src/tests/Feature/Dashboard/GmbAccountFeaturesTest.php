<?php

use App\Models\User;
use BADDIServices\ClnkGO\Domains\GoogleService;
use BADDIServices\ClnkGO\Models\AccountLocation;
use BADDIServices\ClnkGO\Models\UserGoogleCredentials;

/**
 * Create a user with full GMB credentials and one AccountLocation record.
 */
function userWithGmbAndLocation(): array
{
    $user = User::factory()->create();

    UserGoogleCredentials::create([
        UserGoogleCredentials::USER_ID_COLUMN          => $user->getId(),
        UserGoogleCredentials::ID_TOKEN_COLUMN         => '',
        UserGoogleCredentials::ACCOUNT_ID_COLUMN       => 'acc-123',
        UserGoogleCredentials::MAIN_LOCATION_ID_COLUMN => 'loc-456',
        UserGoogleCredentials::ACCESS_TOKEN_COLUMN     => 'access-token',
        UserGoogleCredentials::REFRESH_TOKEN_COLUMN    => 'refresh-token',
        UserGoogleCredentials::EXPIRES_IN_COLUMN       => 3600,
        UserGoogleCredentials::CREATED_COLUMN          => time(),
        UserGoogleCredentials::IS_EXPIRED_COLUMN       => false,
        UserGoogleCredentials::SCOPE_COLUMN            => 'https://www.googleapis.com/auth/business.manage',
        UserGoogleCredentials::TOKEN_TYPE_COLUMN       => 'Bearer',
    ]);

    $location = AccountLocation::create([
        AccountLocation::USER_ID_COLUMN     => $user->getId(),
        AccountLocation::ACCOUNT_ID_COLUMN  => 'acc-123',
        AccountLocation::LOCATION_ID_COLUMN => 'loc-789',
        AccountLocation::TITLE_COLUMN       => 'My Shop',
    ]);

    $user->load(['googleCredentials']);

    return [$user, $location];
}

// ---------------------------------------------------------------------------
// GMB OAuth Callback
// ---------------------------------------------------------------------------

describe('GMB Callback – Access Control', function () {
    it('redirects unauthenticated users to sign-in', function () {
        $this->get(route('dashboard.account.gmb.callback'))
            ->assertRedirect(route('signin'));
    });
});

describe('GMB Callback – Behaviour', function () {
    it('redirects with error when no code is present', function () {
        $user = User::factory()->create();

        // Mock GoogleService so we don't need the auth config file
        $this->mock(GoogleService::class, fn ($m) =>
            $m->shouldReceive('generateAuthenticationURL')->andReturn('https://accounts.google.com')
              ->shouldReceive('exchangeAuthenticationCode')->never()
        );

        $this->actingAs($user)
            ->get(route('dashboard.account.gmb.callback'))
            ->assertRedirect()
            ->assertSessionHas('alert');
    });

    it('redirects with success when a valid code is exchanged', function () {
        $user = User::factory()->create();

        $this->mock(GoogleService::class, function ($mock) use ($user) {
            $mock->shouldReceive('generateAuthenticationURL')->andReturn('https://accounts.google.com');
            $mock->shouldReceive('exchangeAuthenticationCode')
                ->with('valid-code')
                ->andReturn([
                    UserGoogleCredentials::ACCOUNT_ID_COLUMN       => 'google-acc-id',
                    UserGoogleCredentials::ACCESS_TOKEN_COLUMN     => 'new-access-token',
                    UserGoogleCredentials::REFRESH_TOKEN_COLUMN    => 'new-refresh-token',
                    UserGoogleCredentials::EXPIRES_IN_COLUMN       => 3600,
                    UserGoogleCredentials::CREATED_COLUMN          => time(),
                    UserGoogleCredentials::ID_TOKEN_COLUMN         => 'id-token',
                    UserGoogleCredentials::SCOPE_COLUMN            => 'https://www.googleapis.com/auth/business.manage',
                    UserGoogleCredentials::TOKEN_TYPE_COLUMN       => 'Bearer',
                ]);
            $mock->shouldReceive('refreshAccessToken')->andReturn(null);
        });

        $this->actingAs($user)
            ->get(route('dashboard.account.gmb.callback', ['code' => 'valid-code']))
            ->assertRedirect()
            ->assertSessionHas('alert');

        $this->assertDatabaseHas('user_google_credentials', [
            'user_id'      => $user->getId(),
            'access_token' => 'new-access-token',
        ]);
    });

    it('redirects with error when the code exchange fails', function () {
        $user = User::factory()->create();

        $this->mock(GoogleService::class, function ($mock) {
            $mock->shouldReceive('generateAuthenticationURL')->andReturn('https://accounts.google.com');
            $mock->shouldReceive('exchangeAuthenticationCode')
                ->with('bad-code')
                ->andReturn(null);
            $mock->shouldReceive('refreshAccessToken')->andReturn(null);
        });

        $this->actingAs($user)
            ->get(route('dashboard.account.gmb.callback', ['code' => 'bad-code']))
            ->assertRedirect()
            ->assertSessionHas('alert');
    });
});

// ---------------------------------------------------------------------------
// GMB Disconnect
// ---------------------------------------------------------------------------

describe('GMB Disconnect – Access Control', function () {
    it('redirects unauthenticated users to sign-in', function () {
        $this->get(route('dashboard.account.gmb.disconnect'))
            ->assertRedirect(route('signin'));
    });
});

describe('GMB Disconnect – Behaviour', function () {
    it('revokes GMB credentials and deletes account locations, then redirects with success', function () {
        [$user, $location] = userWithGmbAndLocation();

        $this->mock(GoogleService::class, function ($mock) {
            $mock->shouldReceive('generateAuthenticationURL')->andReturn('https://accounts.google.com');
            $mock->shouldReceive('revokeAccessToken')->once()->andReturn(null);
            $mock->shouldReceive('refreshAccessToken')->andReturn(null);
        });

        $this->actingAs($user)
            ->get(route('dashboard.account.gmb.disconnect'))
            ->assertRedirect()
            ->assertSessionHas('alert');

        $this->assertDatabaseMissing('account_locations', [
            'id' => $location->getId(),
        ]);
    });
});

// ---------------------------------------------------------------------------
// Set Account Main Location
// ---------------------------------------------------------------------------

describe('Set Account Main Location – Access Control', function () {
    it('redirects unauthenticated users to sign-in', function () {
        $this->get(route('dashboard.account.locations.main'))
            ->assertRedirect(route('signin'));
    });
});

describe('Set Account Main Location – Behaviour', function () {
    it('returns 422 when the location name is missing', function () {
        $user = User::factory()->create();

        $this->mock(GoogleService::class, fn ($m) =>
            $m->shouldReceive('generateAuthenticationURL')->andReturn('https://accounts.google.com')
              ->shouldReceive('refreshAccessToken')->andReturn(null)
        );

        $this->actingAs($user)
            ->get(route('dashboard.account.locations.main'))
            ->assertStatus(422);
    });

    it('returns 404 when the location does not belong to the user', function () {
        [$user] = userWithGmbAndLocation();

        $this->mock(GoogleService::class, fn ($m) =>
            $m->shouldReceive('generateAuthenticationURL')->andReturn('https://accounts.google.com')
              ->shouldReceive('refreshAccessToken')->andReturn(null)
        );

        $otherUser = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.account.locations.main', ['name' => 'locations/loc-of-other-user']))
            ->assertNotFound();
    });

    it('updates main location and redirects with success when location belongs to the user', function () {
        [$user, $location] = userWithGmbAndLocation();

        $this->mock(GoogleService::class, fn ($m) =>
            $m->shouldReceive('generateAuthenticationURL')->andReturn('https://accounts.google.com')
              ->shouldReceive('refreshAccessToken')->andReturn(null)
        );

        $this->actingAs($user)
            ->get(route('dashboard.account.locations.main', ['name' => 'locations/' . $location->getLocationId()]))
            ->assertRedirect()
            ->assertSessionHas('alert');

        $this->assertDatabaseHas('user_google_credentials', [
            'user_id'          => $user->getId(),
            'main_location_id' => $location->getLocationId(),
        ]);
    });
});
