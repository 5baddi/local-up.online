<?php

use App\Models\User;
use BADDIServices\ClnkGO\Domains\GoogleService;

beforeEach(function () {
    $this->mock(GoogleService::class, fn ($mock) =>
        $mock->shouldReceive('generateAuthenticationURL')
            ->andReturn('https://accounts.google.com/o/oauth2/auth')
    );
});

describe('Account Page', function () {
    it('is accessible without GMB credentials', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard.account'))
            ->assertOk();
    });

    it('redirects unauthenticated users to sign-in', function () {
        $this->get(route('dashboard.account'))
            ->assertRedirect(route('signin'));
    });
});

describe('Update Account Info', function () {
    it('updates first name, last name and phone', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('dashboard.account.save'), [
                'first_name' => 'Jane',
                'last_name'  => 'Smith',
                'phone'      => '0600000001',
            ])
            ->assertRedirect();

        expect(
            User::find($user->id)->first_name
        )->toBe('Jane');
    });

    it('fails validation when first name is missing', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('dashboard.account.save'), [
                'last_name' => 'Smith',
            ])
            ->assertSessionHasErrors('first_name');
    });

    it('fails validation when last name is missing', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('dashboard.account.save'), [
                'first_name' => 'Jane',
            ])
            ->assertSessionHasErrors('last_name');
    });
});

describe('Update Account Password', function () {
    it('rejects an incorrect current password', function () {
        $user = User::factory()->create(['password' => bcrypt('oldpassword')]);

        $this->actingAs($user)
            ->post(route('dashboard.account.save') . '?tab=password', [
                'current_password' => 'wrongpassword',
                'password'         => 'newpassword1',
                'confirm_password' => 'newpassword1',
            ])
            ->assertSessionHasErrors('current_password');
    });
});
