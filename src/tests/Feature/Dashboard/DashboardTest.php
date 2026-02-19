<?php

use App\Models\User;
use BADDIServices\ClnkGO\Domains\GoogleService;

describe('Dashboard Access Control', function () {
    it('redirects unauthenticated visitors to sign-in', function () {
        $this->get(route('dashboard'))
            ->assertRedirect(route('signin'));
    });

    it('redirects authenticated users without GMB credentials to the GMB error page', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });

    it('shows the GMB error page when GMB is not connected', function () {
        $user = User::factory()->create();

        $this->mock(GoogleService::class, fn ($mock) =>
            $mock->shouldReceive('generateAuthenticationURL')
                ->andReturn('https://accounts.google.com/o/oauth2/auth')
        );

        $this->actingAs($user)
            ->get(route('dashboard.errors.unauthenticated_gmb_access'))
            ->assertOk();
    });

    it('redirects authenticated users without GMB credentials from scheduled posts', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.scheduled.posts'))
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });

    it('redirects authenticated users without GMB credentials from reviews', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.reviews'))
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });

    it('redirects authenticated users without GMB credentials from media', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.media'))
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });
});
