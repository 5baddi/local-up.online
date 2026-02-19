<?php

use App\Models\User;

describe('Media – Access Control', function () {
    it('redirects unauthenticated users to sign-in from media list', function () {
        $this->get(route('dashboard.media'))
            ->assertRedirect(route('signin'));
    });

    it('redirects unauthenticated users to sign-in from new media form', function () {
        $this->get(route('dashboard.media.new'))
            ->assertRedirect(route('signin'));
    });

    it('redirects users without GMB credentials to the GMB error page', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.media'))
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });

    it('redirects users without GMB credentials from the new media form', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.media.new'))
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });
});

describe('Scheduled Media – Access Control', function () {
    it('redirects unauthenticated users to sign-in', function () {
        $this->get(route('dashboard.scheduled.media'))
            ->assertRedirect(route('signin'));
    });

    it('redirects users without GMB credentials to the GMB error page', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.scheduled.media'))
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });
});
