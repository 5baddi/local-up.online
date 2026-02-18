<?php

use App\Models\User;

describe('Reviews – Access Control', function () {
    it('redirects unauthenticated users to sign-in from reviews list', function () {
        $this->get(route('dashboard.reviews'))
            ->assertRedirect(route('signin'));
    });

    it('redirects unauthenticated users to sign-in from a review detail', function () {
        $this->get(route('dashboard.reviews.view', ['id' => 'fake-review-id']))
            ->assertRedirect(route('signin'));
    });

    it('redirects users without GMB credentials to the GMB error page', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.reviews'))
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });

    it('redirects users without GMB credentials from review detail', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.reviews.view', ['id' => 'fake-review-id']))
            ->assertRedirect(route('dashboard.errors.unauthenticated_gmb_access'));
    });
});
