<?php

use App\Models\User;

describe('Sign Out', function () {
    it('signs out an authenticated user', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('signout'))
            ->assertRedirect();

        $this->assertGuest();
    });

    it('redirects unauthenticated users to sign-in from protected routes', function () {
        $this->get(route('dashboard'))
            ->assertRedirect(route('signin'));
    });

    it('redirects unauthenticated users to sign-in from account route', function () {
        $this->get(route('dashboard.account'))
            ->assertRedirect(route('signin'));
    });

    it('redirects unauthenticated users to sign-in from scheduled posts', function () {
        $this->get(route('dashboard.scheduled.posts'))
            ->assertRedirect(route('signin'));
    });
});
