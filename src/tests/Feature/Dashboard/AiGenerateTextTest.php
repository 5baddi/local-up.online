<?php

use App\Models\User;
use BADDIServices\ClnkGO\Domains\OpenAIService;
use BADDIServices\ClnkGO\Models\UserGoogleCredentials;

function userWithGmbForAi(): User
{
    $user = User::factory()->create();

    UserGoogleCredentials::create([
        UserGoogleCredentials::USER_ID_COLUMN          => $user->getId(),
        UserGoogleCredentials::ID_TOKEN_COLUMN         => '',
        UserGoogleCredentials::ACCOUNT_ID_COLUMN       => 'acc-ai-123',
        UserGoogleCredentials::MAIN_LOCATION_ID_COLUMN => 'loc-ai-456',
        UserGoogleCredentials::ACCESS_TOKEN_COLUMN     => 'ai-access-token',
        UserGoogleCredentials::REFRESH_TOKEN_COLUMN    => 'ai-refresh-token',
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

describe('AI Generate Text – Access Control', function () {
    it('redirects unauthenticated users to sign-in', function () {
        $this->post(route('dashboard.ai.generate.text'), ['topic' => 'coffee'])
            ->assertRedirect(route('signin'));
    });

    it('redirects users without GMB credentials to the GMB error page', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('dashboard.ai.generate.text'), ['topic' => 'coffee'])
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });
});

// ---------------------------------------------------------------------------
// Validation
// ---------------------------------------------------------------------------

describe('AI Generate Text – Validation', function () {
    it('fails validation when topic is not a string', function () {
        $user = userWithGmbForAi();

        $this->actingAs($user)
            ->post(route('dashboard.ai.generate.text'), ['topic' => []])
            ->assertSessionHasErrors('topic');
    });
});

// ---------------------------------------------------------------------------
// Successful generation
// ---------------------------------------------------------------------------

describe('AI Generate Text – Success', function () {
    it('returns JSON with generated text when OpenAI succeeds', function () {
        $user = userWithGmbForAi();

        $this->mock(OpenAIService::class, function ($mock) {
            $mock->shouldReceive('generateTextCompletions')
                ->once()
                ->andReturn([
                    ['message' => ['content' => 'Welcome to our amazing coffee shop!']],
                ]);
        });

        $response = $this->actingAs($user)
            ->postJson(route('dashboard.ai.generate.text'), [
                'prompt' => '',
                'topic'  => 'coffee shop',
            ]);

        $response->assertOk()
            ->assertJsonFragment(['text' => 'Welcome to our amazing coffee shop!']);
    });

    it('returns empty text when OpenAI response has no content', function () {
        $user = userWithGmbForAi();

        $this->mock(OpenAIService::class, function ($mock) {
            $mock->shouldReceive('generateTextCompletions')
                ->once()
                ->andReturn([[]]);
        });

        $response = $this->actingAs($user)
            ->postJson(route('dashboard.ai.generate.text'), ['topic' => 'coffee']);

        $response->assertOk()
            ->assertJsonFragment(['text' => '']);
    });
});

// ---------------------------------------------------------------------------
// Error handling
// ---------------------------------------------------------------------------

describe('AI Generate Text – Error Handling', function () {
    it('returns 500 when OpenAI throws an exception', function () {
        $user = userWithGmbForAi();

        $this->mock(OpenAIService::class, function ($mock) {
            $mock->shouldReceive('generateTextCompletions')
                ->once()
                ->andThrow(new Exception('OpenAI API error'));
        });

        $this->actingAs($user)
            ->postJson(route('dashboard.ai.generate.text'), ['topic' => 'coffee'])
            ->assertStatus(500);
    });
});
