<?php

use App\Models\User;
use BADDIServices\ClnkGO\Models\ScheduledPost;
use BADDIServices\ClnkGO\Models\UserGoogleCredentials;

// ---------------------------------------------------------------------------
// Helper: user with full GMB credentials (accountId + mainLocationId set)
// ---------------------------------------------------------------------------

function userWithGmb(): User
{
    $user = User::factory()->create();

    UserGoogleCredentials::create([
        UserGoogleCredentials::USER_ID_COLUMN          => $user->getId(),
        UserGoogleCredentials::ID_TOKEN_COLUMN         => '',
        UserGoogleCredentials::ACCOUNT_ID_COLUMN       => 'acc-test-123',
        UserGoogleCredentials::MAIN_LOCATION_ID_COLUMN => 'loc-test-456',
        UserGoogleCredentials::ACCESS_TOKEN_COLUMN     => 'test-access-token',
        UserGoogleCredentials::REFRESH_TOKEN_COLUMN    => 'test-refresh-token',
        UserGoogleCredentials::EXPIRES_IN_COLUMN       => 3600,
        UserGoogleCredentials::CREATED_COLUMN          => time(),
        UserGoogleCredentials::IS_EXPIRED_COLUMN       => false,
        UserGoogleCredentials::SCOPE_COLUMN            => 'https://www.googleapis.com/auth/business.manage',
        UserGoogleCredentials::TOKEN_TYPE_COLUMN       => 'Bearer',
    ]);

    $user->load(['googleCredentials']);

    return $user;
}

// Minimal valid STANDARD post payload
function standardPostPayload(array $overrides = []): array
{
    return array_merge([
        ScheduledPost::SUMMARY_COLUMN     => 'Test post summary',
        ScheduledPost::ACTION_TYPE_COLUMN => ScheduledPost::LEARN_MORE_ACTION_TYPE,
        ScheduledPost::ACTION_URL_COLUMN  => 'https://example.com',
    ], $overrides);
}

// ---------------------------------------------------------------------------
// Access control
// ---------------------------------------------------------------------------

describe('SaveScheduledPost – Access Control', function () {
    it('redirects unauthenticated users to sign-in', function () {
        $this->post(route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::STANDARD_TYPE]))
            ->assertRedirect(route('signin'));
    });

    it('redirects users without GMB credentials to the GMB error page', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::STANDARD_TYPE]))
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });

    it('returns 404 for an unknown post type', function () {
        $user = userWithGmb();

        $this->actingAs($user)
            ->post(route('dashboard.scheduled.posts.save', ['type' => 'invalid-type']), standardPostPayload())
            ->assertNotFound();
    });
});

// ---------------------------------------------------------------------------
// Validation
// ---------------------------------------------------------------------------

describe('SaveScheduledPost – Validation', function () {
    it('fails when summary is missing', function () {
        $user = userWithGmb();

        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::STANDARD_TYPE]),
                standardPostPayload([ScheduledPost::SUMMARY_COLUMN => ''])
            )
            ->assertSessionHasErrors(ScheduledPost::SUMMARY_COLUMN);
    });

    it('fails when summary exceeds 1500 characters', function () {
        $user = userWithGmb();

        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::STANDARD_TYPE]),
                standardPostPayload([ScheduledPost::SUMMARY_COLUMN => str_repeat('a', 1501)])
            )
            ->assertSessionHasErrors(ScheduledPost::SUMMARY_COLUMN);
    });

    it('fails when action_url is missing for a non-CALL action type', function () {
        $user = userWithGmb();

        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::STANDARD_TYPE]),
                standardPostPayload([
                    ScheduledPost::ACTION_TYPE_COLUMN => ScheduledPost::LEARN_MORE_ACTION_TYPE,
                    ScheduledPost::ACTION_URL_COLUMN  => '',
                ])
            )
            ->assertSessionHasErrors(ScheduledPost::ACTION_URL_COLUMN);
    });

    it('does not require action_url for the CALL action type', function () {
        $user = userWithGmb();

        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::STANDARD_TYPE]),
                standardPostPayload([
                    ScheduledPost::ACTION_TYPE_COLUMN => ScheduledPost::CALL_ACTION_TYPE,
                    ScheduledPost::ACTION_URL_COLUMN  => '',
                ])
            )
            ->assertSessionMissing('errors');
    });

    it('fails event post when event_title is missing', function () {
        $user = userWithGmb();

        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::EVENT_TYPE]),
                array_merge(standardPostPayload(), [
                    'event_start_date' => '2025-12-25',
                    'event_end_date'   => '2025-12-26',
                ])
            )
            ->assertSessionHasErrors(ScheduledPost::EVENT_TITLE_COLUMN);
    });

    it('fails event post when event_start_date is missing', function () {
        $user = userWithGmb();

        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::EVENT_TYPE]),
                array_merge(standardPostPayload(), [
                    ScheduledPost::EVENT_TITLE_COLUMN => 'Grand Opening',
                    'event_end_date'                  => '2025-12-26',
                ])
            )
            ->assertSessionHasErrors('event_start_date');
    });

    it('fails event post when event_end_date is missing', function () {
        $user = userWithGmb();

        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::EVENT_TYPE]),
                array_merge(standardPostPayload(), [
                    ScheduledPost::EVENT_TITLE_COLUMN => 'Grand Opening',
                    'event_start_date'                => '2025-12-25',
                ])
            )
            ->assertSessionHasErrors('event_end_date');
    });

    it('fails alert post when alert_type is missing', function () {
        $user = userWithGmb();

        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::ALERT_TYPE]),
                standardPostPayload()
            )
            ->assertSessionHasErrors(ScheduledPost::ALERT_TYPE_COLUMN);
    });

    it('fails when scheduled_date is not a valid date', function () {
        $user = userWithGmb();

        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::STANDARD_TYPE]),
                standardPostPayload(['scheduled_date' => 'not-a-date'])
            )
            ->assertSessionHasErrors('scheduled_date');
    });
});

// ---------------------------------------------------------------------------
// Successful saves
// ---------------------------------------------------------------------------

describe('SaveScheduledPost – Successful Saves', function () {
    it('saves a STANDARD post and redirects to the posts list', function () {
        $user = userWithGmb();

        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::STANDARD_TYPE]),
                standardPostPayload()
            )
            ->assertRedirect(route('dashboard.scheduled.posts'));

        $this->assertDatabaseHas('scheduled_posts', [
            'user_id'    => $user->getId(),
            'topic_type' => 'STANDARD',
            'summary'    => 'Test post summary',
            'state'      => ScheduledPost::UNSPECIFIED_STATE,
        ]);
    });

    it('saves an EVENT post and stores the event title', function () {
        $user = userWithGmb();

        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::EVENT_TYPE]),
                array_merge(standardPostPayload(), [
                    ScheduledPost::EVENT_TITLE_COLUMN => 'Grand Opening',
                    'event_start_date'                => '2025-12-25',
                    'event_start_time'                => '10:00',
                    'event_end_date'                  => '2025-12-25',
                    'event_end_time'                  => '18:00',
                ])
            )
            ->assertRedirect(route('dashboard.scheduled.posts'));

        $this->assertDatabaseHas('scheduled_posts', [
            'user_id'     => $user->getId(),
            'topic_type'  => 'EVENT',
            'event_title' => 'Grand Opening',
        ]);
    });

    it('saves an OFFER post and stores coupon details', function () {
        $user = userWithGmb();

        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::OFFER_TYPE]),
                array_merge(standardPostPayload(), [
                    ScheduledPost::OFFER_COUPON_CODE_COLUMN => 'SAVE20',
                ])
            )
            ->assertRedirect(route('dashboard.scheduled.posts'));

        $this->assertDatabaseHas('scheduled_posts', [
            'user_id'      => $user->getId(),
            'topic_type'   => 'OFFER',
            'offer_coupon_code' => 'SAVE20',
        ]);
    });

    it('saves an ALERT post and stores the alert type', function () {
        $user = userWithGmb();

        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::ALERT_TYPE]),
                array_merge(standardPostPayload(), [
                    ScheduledPost::ALERT_TYPE_COLUMN => ScheduledPost::UNSPECIFIED_ALERT_TYPE,
                ])
            )
            ->assertRedirect(route('dashboard.scheduled.posts'));

        $this->assertDatabaseHas('scheduled_posts', [
            'user_id'    => $user->getId(),
            'topic_type' => 'ALERT',
        ]);
    });

    it('updates an existing post when an id is provided', function () {
        $user = userWithGmb();

        $post = ScheduledPost::create([
            ScheduledPost::USER_ID_COLUMN     => $user->getId(),
            ScheduledPost::ACCOUNT_ID_COLUMN  => 'acc-test-123',
            ScheduledPost::LOCATION_ID_COLUMN => 'loc-test-456',
            ScheduledPost::TOPIC_TYPE_COLUMN  => 'STANDARD',
            ScheduledPost::STATE_COLUMN       => ScheduledPost::UNSPECIFIED_STATE,
        ]);

        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::STANDARD_TYPE]),
                standardPostPayload([
                    'id'                              => $post->getId(),
                    ScheduledPost::SUMMARY_COLUMN     => 'Updated summary',
                ])
            )
            ->assertRedirect(route('dashboard.scheduled.posts'));

        $this->assertDatabaseHas('scheduled_posts', [
            'id'      => $post->getId(),
            'summary' => 'Updated summary',
        ]);
    });

    it('stores the action_type as the GMB API uppercase enum', function () {
        $user = userWithGmb();

        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::STANDARD_TYPE]),
                standardPostPayload([
                    ScheduledPost::ACTION_TYPE_COLUMN => ScheduledPost::BOOK_ACTION_TYPE,
                    ScheduledPost::ACTION_URL_COLUMN  => 'https://booking.example.com',
                ])
            )
            ->assertRedirect(route('dashboard.scheduled.posts'));

        $this->assertDatabaseHas('scheduled_posts', [
            'user_id'     => $user->getId(),
            'action_type' => 'BOOK',
        ]);
    });
});

// ---------------------------------------------------------------------------
// Guard: main location not configured
// ---------------------------------------------------------------------------

describe('SaveScheduledPost – No Main Location Guard', function () {
    it('redirects back with an alert when the main location is not set', function () {
        $user = User::factory()->create();

        UserGoogleCredentials::create([
            UserGoogleCredentials::USER_ID_COLUMN          => $user->getId(),
            UserGoogleCredentials::ID_TOKEN_COLUMN         => '',
            UserGoogleCredentials::ACCOUNT_ID_COLUMN       => '',
            UserGoogleCredentials::MAIN_LOCATION_ID_COLUMN => '',
            UserGoogleCredentials::ACCESS_TOKEN_COLUMN     => 'token',
            UserGoogleCredentials::REFRESH_TOKEN_COLUMN    => 'refresh',
            UserGoogleCredentials::EXPIRES_IN_COLUMN       => 3600,
            UserGoogleCredentials::CREATED_COLUMN          => time(),
            UserGoogleCredentials::IS_EXPIRED_COLUMN       => false,
            UserGoogleCredentials::SCOPE_COLUMN            => '',
            UserGoogleCredentials::TOKEN_TYPE_COLUMN       => 'Bearer',
        ]);

        $user->load(['googleCredentials']);

        // Middleware redirects to GMB error page before even reaching the controller
        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.save', ['type' => ScheduledPost::STANDARD_TYPE]),
                standardPostPayload()
            )
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });
});
