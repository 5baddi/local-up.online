<?php

use App\Models\User;
use Illuminate\Support\Str;
use BADDIServices\ClnkGO\Models\ScheduledPost;

describe('Scheduled Posts – Access Control', function () {
    it('redirects unauthenticated users to sign-in', function () {
        $this->get(route('dashboard.scheduled.posts'))
            ->assertRedirect(route('signin'));
    });

    it('redirects users without GMB credentials to the GMB error page', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.scheduled.posts'))
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });
});

describe('Scheduled Posts – Deletion', function () {
    it('redirects unauthenticated users to sign-in when deleting a post', function () {
        // The route requires a valid UUID (whereUuid constraint)
        $uuid = Str::uuid()->toString();

        $this->delete(route('dashboard.scheduled.posts.delete', ['id' => $uuid]))
            ->assertRedirect(route('signin'));
    });

    it('redirects users without GMB credentials when deleting another user\'s post', function () {
        $owner  = User::factory()->create();
        $viewer = User::factory()->create();

        $post = ScheduledPost::create([
            'user_id'     => $owner->getId(),
            'account_id'  => 'acc-123',
            'location_id' => 'loc-456',
        ]);

        $this->actingAs($viewer)
            ->delete(route('dashboard.scheduled.posts.delete', ['id' => $post->getId()]))
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });
});

describe('Scheduled Posts – Edit Form', function () {
    it('redirects unauthenticated users to sign-in when accessing the edit form', function () {
        $this->get(route('dashboard.scheduled.posts.edit', ['type' => ScheduledPost::STANDARD_TYPE]))
            ->assertRedirect(route('signin'));
    });
});
