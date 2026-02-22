<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use BADDIServices\ClnkGO\Models\ScheduledMedia;
use BADDIServices\ClnkGO\Models\ScheduledPost;
use BADDIServices\ClnkGO\Models\ScheduledPostMedia;
use BADDIServices\ClnkGO\Models\UserGoogleCredentials;

function userWithGmbForMedia(): User
{
    $user = User::factory()->create();

    UserGoogleCredentials::create([
        UserGoogleCredentials::USER_ID_COLUMN          => $user->getId(),
        UserGoogleCredentials::ID_TOKEN_COLUMN         => '',
        UserGoogleCredentials::ACCOUNT_ID_COLUMN       => 'acc-media-123',
        UserGoogleCredentials::MAIN_LOCATION_ID_COLUMN => 'loc-media-456',
        UserGoogleCredentials::ACCESS_TOKEN_COLUMN     => 'media-access-token',
        UserGoogleCredentials::REFRESH_TOKEN_COLUMN    => 'media-refresh-token',
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
// Upload scheduled post media
// ---------------------------------------------------------------------------

describe('Upload Scheduled Post Media – Access Control', function () {
    it('redirects unauthenticated users to sign-in', function () {
        $uuid = \Illuminate\Support\Str::uuid()->toString();

        $this->post(route('dashboard.scheduled.posts.upload.media', ['id' => $uuid]))
            ->assertRedirect(route('signin'));
    });

    it('redirects users without GMB credentials to the GMB error page', function () {
        $uuid = \Illuminate\Support\Str::uuid()->toString();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('dashboard.scheduled.posts.upload.media', ['id' => $uuid]))
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });
});

describe('Upload Scheduled Post Media – Validation', function () {
    it('returns 422 when no file is uploaded', function () {
        $user = userWithGmbForMedia();
        $uuid = \Illuminate\Support\Str::uuid()->toString();

        $this->actingAs($user)
            ->post(route('dashboard.scheduled.posts.upload.media', ['id' => $uuid]))
            ->assertStatus(422);
    });
});

describe('Upload Scheduled Post Media – Success', function () {
    it('creates a ScheduledPostMedia record when a valid image is uploaded', function () {
        $user   = userWithGmbForMedia();
        $postId = \Illuminate\Support\Str::uuid()->toString();

        $file = UploadedFile::fake()->image('photo.jpg', 100, 100);

        $this->actingAs($user)
            ->post(
                route('dashboard.scheduled.posts.upload.media', ['id' => $postId]),
                ['file' => [$file]]
            );

        $this->assertDatabaseHas('scheduled_post_media', [
            'scheduled_post_id' => $postId,
        ]);
    });
});

// ---------------------------------------------------------------------------
// Delete scheduled post media
// ---------------------------------------------------------------------------

describe('Delete Scheduled Post Media – Access Control', function () {
    it('redirects unauthenticated users to sign-in', function () {
        $uuid = \Illuminate\Support\Str::uuid()->toString();

        $this->delete(route('dashboard.scheduled.posts.delete.media', ['id' => $uuid]))
            ->assertRedirect(route('signin'));
    });
});

describe('Delete Scheduled Post Media – Validation', function () {
    it('returns 422 when filename is missing', function () {
        $user = userWithGmbForMedia();
        $uuid = \Illuminate\Support\Str::uuid()->toString();

        $this->actingAs($user)
            ->delete(route('dashboard.scheduled.posts.delete.media', ['id' => $uuid]))
            ->assertStatus(422);
    });
});

describe('Delete Scheduled Post Media – Success', function () {
    it('deletes the scheduled post media record matching the given filename', function () {
        $user   = userWithGmbForMedia();
        $postId = \Illuminate\Support\Str::uuid()->toString();

        ScheduledPost::create([
            ScheduledPost::ID_COLUMN          => $postId,
            ScheduledPost::USER_ID_COLUMN     => $user->getId(),
            ScheduledPost::ACCOUNT_ID_COLUMN  => 'acc-media-123',
            ScheduledPost::LOCATION_ID_COLUMN => 'loc-media-456',
            ScheduledPost::STATE_COLUMN       => ScheduledPost::UNSPECIFIED_STATE,
        ]);

        ScheduledPostMedia::create([
            ScheduledPostMedia::SCHEDULED_POST_ID_COLUMN => $postId,
            ScheduledPostMedia::PATH_COLUMN              => 'uploads/test-photo.jpg',
        ]);

        $this->actingAs($user)
            ->delete(
                route('dashboard.scheduled.posts.delete.media', ['id' => $postId]),
                ['filename' => 'test-photo.jpg']
            );

        $this->assertDatabaseMissing('scheduled_post_media', [
            'scheduled_post_id' => $postId,
            'path'              => 'uploads/test-photo.jpg',
        ]);
    });
});

// ---------------------------------------------------------------------------
// Upload new scheduled media (standalone media, not post-attached)
// ---------------------------------------------------------------------------

describe('Upload Scheduled Media – Validation', function () {
    it('returns 422 when no file is provided', function () {
        $user = userWithGmbForMedia();

        $this->actingAs($user)
            ->post(route('dashboard.media.upload'))
            ->assertStatus(422);
    });

    it('returns 422 when file type is not an allowed image or video', function () {
        $user = userWithGmbForMedia();

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $this->actingAs($user)
            ->post(route('dashboard.media.upload'), ['file' => [$file]])
            ->assertStatus(422);
    });
});

describe('Upload Scheduled Media – Success', function () {
    it('creates a ScheduledMedia record when a valid image is uploaded', function () {
        $user = userWithGmbForMedia();

        $file = UploadedFile::fake()->image('shop-photo.jpg', 200, 200);

        $this->actingAs($user)
            ->post(route('dashboard.media.upload'), ['file' => [$file]]);

        $this->assertDatabaseHas('scheduled_media', [
            'user_id'     => $user->getId(),
            'account_id'  => 'acc-media-123',
            'location_id' => 'loc-media-456',
            'state'       => ScheduledMedia::UNSPECIFIED_STATE,
        ]);
    });

    it('stores a null scheduled_frequency when no date is provided (immediate post)', function () {
        $user = userWithGmbForMedia();

        $file = UploadedFile::fake()->image('immediate.jpg');

        $this->actingAs($user)
            ->post(route('dashboard.media.upload'), ['file' => [$file]]);

        $media = ScheduledMedia::where('user_id', $user->getId())->first();

        expect($media)->not->toBeNull();
        expect($media->getAttribute(ScheduledMedia::SCHEDULED_FREQUENCY_COLUMN))->toBeNull();
    });
});

// ---------------------------------------------------------------------------
// Delete scheduled media
// ---------------------------------------------------------------------------

describe('Delete Scheduled Media – Access Control', function () {
    it('redirects unauthenticated users to sign-in', function () {
        $uuid = \Illuminate\Support\Str::uuid()->toString();

        $this->delete(route('dashboard.scheduled.media.delete', ['id' => $uuid]))
            ->assertRedirect(route('signin'));
    });

    it('redirects users without GMB credentials to the GMB error page', function () {
        $uuid = \Illuminate\Support\Str::uuid()->toString();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete(route('dashboard.scheduled.media.delete', ['id' => $uuid]))
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });
});

describe('Delete Scheduled Media – Behaviour', function () {
    it('returns 404 for a non-existent scheduled media record', function () {
        $user = userWithGmbForMedia();
        $uuid = \Illuminate\Support\Str::uuid()->toString();

        $this->actingAs($user)
            ->delete(route('dashboard.scheduled.media.delete', ['id' => $uuid]))
            ->assertNotFound();
    });

    it('deletes the scheduled media and redirects with success', function () {
        $user = userWithGmbForMedia();

        $media = ScheduledMedia::create([
            ScheduledMedia::USER_ID_COLUMN      => $user->getId(),
            ScheduledMedia::ACCOUNT_ID_COLUMN   => 'acc-media-123',
            ScheduledMedia::LOCATION_ID_COLUMN  => 'loc-media-456',
            ScheduledMedia::FILES_COLUMN        => [],
            ScheduledMedia::STATE_COLUMN        => ScheduledMedia::UNSPECIFIED_STATE,
            ScheduledMedia::SCHEDULED_AT_COLUMN => now()->toISOString(),
        ]);

        $this->actingAs($user)
            ->delete(route('dashboard.scheduled.media.delete', ['id' => $media->getId()]))
            ->assertRedirect(route('dashboard.scheduled.media'))
            ->assertSessionHas('alert');

        $this->assertDatabaseMissing('scheduled_media', ['id' => $media->getId()]);
    });
});
