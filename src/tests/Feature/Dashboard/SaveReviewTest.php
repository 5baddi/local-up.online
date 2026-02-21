<?php

use App\Models\User;
use BADDIServices\ClnkGO\Domains\OpenAIService;
use BADDIServices\ClnkGO\Models\UserGoogleCredentials;

function userWithGmbForReview(): User
{
    $user = User::factory()->create();

    UserGoogleCredentials::create([
        UserGoogleCredentials::USER_ID_COLUMN          => $user->getId(),
        UserGoogleCredentials::ID_TOKEN_COLUMN         => '',
        UserGoogleCredentials::ACCOUNT_ID_COLUMN       => 'acc-review-123',
        UserGoogleCredentials::MAIN_LOCATION_ID_COLUMN => 'loc-review-456',
        UserGoogleCredentials::ACCESS_TOKEN_COLUMN     => 'review-access-token',
        UserGoogleCredentials::REFRESH_TOKEN_COLUMN    => 'review-refresh-token',
        UserGoogleCredentials::EXPIRES_IN_COLUMN       => 3600,
        UserGoogleCredentials::CREATED_COLUMN          => time(),
        UserGoogleCredentials::IS_EXPIRED_COLUMN       => false,
        UserGoogleCredentials::SCOPE_COLUMN            => 'https://www.googleapis.com/auth/business.manage',
        UserGoogleCredentials::TOKEN_TYPE_COLUMN       => 'Bearer',
    ]);

    $user->load(['googleCredentials']);

    return $user;
}

// ---------------------------------------------------------------------------
// Access control
// ---------------------------------------------------------------------------

describe('Save Review Reply – Access Control', function () {
    it('redirects unauthenticated users to sign-in', function () {
        $this->post(route('dashboard.reviews.reply.update', ['id' => 'review-abc']))
            ->assertRedirect(route('signin'));
    });

    it('redirects users without GMB credentials to the GMB error page', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('dashboard.reviews.reply.update', ['id' => 'review-abc']), ['reply' => 'Thank you!'])
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });
});

// ---------------------------------------------------------------------------
// Manual reply update (GMB API call will fail in test env → error redirect)
// ---------------------------------------------------------------------------

describe('Save Review Reply – Manual Update', function () {
    it('redirects back with error when the GMB API call fails', function () {
        // The GoogleMyBusinessService is instantiated directly (not from the container)
        // so in test environments it will make a real HTTP request to Google that fails
        // (network not reachable → ConnectException → returns [] → error redirect).
        $user = userWithGmbForReview();

        $this->actingAs($user)
            ->post(
                route('dashboard.reviews.reply.update', ['id' => 'review-abc']),
                ['reply' => 'Thank you for your feedback!']
            )
            ->assertRedirect(route('dashboard.reviews.view', ['id' => 'review-abc']))
            ->assertSessionHas('alert');
    });
});

// ---------------------------------------------------------------------------
// AI-generated reply
// ---------------------------------------------------------------------------

describe('Save Review Reply – AI Generation', function () {
    it('redirects with error when review content is missing for AI generation', function () {
        $user = userWithGmbForReview();

        $this->actingAs($user)
            ->post(
                route('dashboard.reviews.reply.update', ['id' => 'review-abc']),
                ['action' => 'generate-reply']  // no 'review' field
            )
            ->assertRedirect(route('dashboard.reviews.view', ['id' => 'review-abc']))
            ->assertSessionHas('alert');
    });

    it('redirects with a generated reply when OpenAI succeeds', function () {
        $user = userWithGmbForReview();

        $this->mock(OpenAIService::class, function ($mock) {
            $mock->shouldReceive('generateTextCompletions')
                ->once()
                ->andReturn([
                    ['message' => ['content' => 'Thank you so much for your kind words!']],
                ]);
        });

        $this->actingAs($user)
            ->post(
                route('dashboard.reviews.reply.update', ['id' => 'review-abc']),
                [
                    'action' => 'generate-reply',
                    'review' => 'Great service, highly recommend!',
                ]
            )
            ->assertRedirect(route('dashboard.reviews.view', ['id' => 'review-abc']))
            ->assertSessionHas('alert');
    });

    it('redirects with error when OpenAI returns an empty response', function () {
        $user = userWithGmbForReview();

        $this->mock(OpenAIService::class, function ($mock) {
            $mock->shouldReceive('generateTextCompletions')
                ->once()
                ->andReturn([[]]);
        });

        $this->actingAs($user)
            ->post(
                route('dashboard.reviews.reply.update', ['id' => 'review-abc']),
                [
                    'action' => 'generate-reply',
                    'review' => 'Some review content',
                ]
            )
            ->assertRedirect(route('dashboard.reviews.view', ['id' => 'review-abc']))
            ->assertSessionHas('alert');
    });
});
