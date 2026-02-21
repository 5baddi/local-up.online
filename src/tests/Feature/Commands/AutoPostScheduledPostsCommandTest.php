<?php

use App\Models\User;
use Carbon\Carbon;
use BADDIServices\ClnkGO\Domains\GoogleService;
use BADDIServices\ClnkGO\Models\ScheduledPost;
use BADDIServices\ClnkGO\Models\UserGoogleCredentials;

function userWithGmbForCommand(): User
{
    $user = User::factory()->create();

    UserGoogleCredentials::create([
        UserGoogleCredentials::USER_ID_COLUMN          => $user->getId(),
        UserGoogleCredentials::ID_TOKEN_COLUMN         => '',
        UserGoogleCredentials::ACCOUNT_ID_COLUMN       => 'acc-cmd-123',
        UserGoogleCredentials::MAIN_LOCATION_ID_COLUMN => 'loc-cmd-456',
        UserGoogleCredentials::ACCESS_TOKEN_COLUMN     => 'cmd-access-token',
        UserGoogleCredentials::REFRESH_TOKEN_COLUMN    => 'cmd-refresh-token',
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
// auto-post:scheduled-posts
// ---------------------------------------------------------------------------

describe('AutoPostScheduledPostsCommand', function () {
    it('exits cleanly when there are no posts due', function () {
        $this->artisan('auto-post:scheduled-posts')
            ->assertExitCode(0);
    });

    it('does not process posts scheduled in the future', function () {
        $user = userWithGmbForCommand();

        $post = ScheduledPost::create([
            ScheduledPost::USER_ID_COLUMN     => $user->getId(),
            ScheduledPost::ACCOUNT_ID_COLUMN  => 'acc-cmd-123',
            ScheduledPost::LOCATION_ID_COLUMN => 'loc-cmd-456',
            ScheduledPost::SUMMARY_COLUMN     => 'Future post',
            ScheduledPost::TOPIC_TYPE_COLUMN  => 'STANDARD',
            ScheduledPost::STATE_COLUMN       => ScheduledPost::UNSPECIFIED_STATE,
            ScheduledPost::SCHEDULED_AT_COLUMN => Carbon::now()->addDay()->toISOString(),
        ]);

        $this->artisan('auto-post:scheduled-posts')->assertExitCode(0);

        $this->assertDatabaseHas('scheduled_posts', [
            'id'    => $post->getId(),
            'state' => ScheduledPost::UNSPECIFIED_STATE, // unchanged
        ]);
    });

    it('skips posts whose user has no Google credentials', function () {
        $user = User::factory()->create(); // NO credentials

        $post = ScheduledPost::create([
            ScheduledPost::USER_ID_COLUMN      => $user->getId(),
            ScheduledPost::ACCOUNT_ID_COLUMN   => 'acc-cmd-123',
            ScheduledPost::LOCATION_ID_COLUMN  => 'loc-cmd-456',
            ScheduledPost::SUMMARY_COLUMN      => 'No-creds post',
            ScheduledPost::TOPIC_TYPE_COLUMN   => 'STANDARD',
            ScheduledPost::STATE_COLUMN        => ScheduledPost::UNSPECIFIED_STATE,
            ScheduledPost::SCHEDULED_AT_COLUMN => Carbon::now()->subMinute()->toISOString(),
        ]);

        $this->artisan('auto-post:scheduled-posts')->assertExitCode(0);

        $this->assertDatabaseHas('scheduled_posts', [
            'id'    => $post->getId(),
            'state' => ScheduledPost::UNSPECIFIED_STATE, // still unprocessed
        ]);
    });

    it('marks the post as rejected when the GMB API call fails (network not reachable in tests)', function () {
        // Mock GoogleService so the token refresh does not throw;
        // the inner GoogleMyBusinessService is created with `new` (not from the container)
        // so it will attempt a real HTTP call that fails with a ConnectException →
        // caught by the command → post state is set to REJECTED.
        $this->mock(GoogleService::class, function ($mock) {
            $mock->shouldReceive('refreshAccessToken')->andReturn(null);
        });

        $user = userWithGmbForCommand();

        $post = ScheduledPost::create([
            ScheduledPost::USER_ID_COLUMN      => $user->getId(),
            ScheduledPost::ACCOUNT_ID_COLUMN   => 'acc-cmd-123',
            ScheduledPost::LOCATION_ID_COLUMN  => 'loc-cmd-456',
            ScheduledPost::SUMMARY_COLUMN      => 'Due post',
            ScheduledPost::TOPIC_TYPE_COLUMN   => 'STANDARD',
            ScheduledPost::STATE_COLUMN        => ScheduledPost::UNSPECIFIED_STATE,
            ScheduledPost::ACTION_TYPE_COLUMN  => 'LEARN_MORE',
            ScheduledPost::ACTION_URL_COLUMN   => 'https://example.com',
            ScheduledPost::LANGUAGE_CODE_COLUMN => 'en-US',
            ScheduledPost::SCHEDULED_AT_COLUMN => Carbon::now()->subMinute()->toISOString(),
        ]);

        $this->artisan('auto-post:scheduled-posts')->assertExitCode(0);

        $this->assertDatabaseHas('scheduled_posts', [
            'id'    => $post->getId(),
            'state' => ScheduledPost::REJECTED_STATE,
        ]);
    });

    it('skips already-processed (non-unspecified) posts', function () {
        $user = userWithGmbForCommand();

        $post = ScheduledPost::create([
            ScheduledPost::USER_ID_COLUMN      => $user->getId(),
            ScheduledPost::ACCOUNT_ID_COLUMN   => 'acc-cmd-123',
            ScheduledPost::LOCATION_ID_COLUMN  => 'loc-cmd-456',
            ScheduledPost::SUMMARY_COLUMN      => 'Already live post',
            ScheduledPost::TOPIC_TYPE_COLUMN   => 'STANDARD',
            ScheduledPost::STATE_COLUMN        => ScheduledPost::LIVE_STATE, // already posted
            ScheduledPost::SCHEDULED_AT_COLUMN => Carbon::now()->subMinute()->toISOString(),
        ]);

        $this->artisan('auto-post:scheduled-posts')->assertExitCode(0);

        $this->assertDatabaseHas('scheduled_posts', [
            'id'    => $post->getId(),
            'state' => ScheduledPost::LIVE_STATE, // unchanged
        ]);
    });
});

// ---------------------------------------------------------------------------
// auto-post:scheduled-media
// ---------------------------------------------------------------------------

describe('AutoPostScheduledMediaCommand', function () {
    it('exits cleanly when there are no media due', function () {
        $this->artisan('auto-post:scheduled-media')
            ->assertExitCode(0);
    });
});
