<?php

use App\Models\User;

describe('Sign In Page', function () {
    it('shows the sign-in page to guests', function () {
        $this->get(route('signin'))
            ->assertOk();
    });

    it('redirects authenticated users away from sign-in page', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('signin'))
            ->assertRedirect(route('dashboard'));
    });
});

describe('Authentication', function () {
    it('signs in a user with valid credentials', function () {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $this->post(route('auth.signin'), [
            'email'    => $user->email,
            'password' => 'secret123',
        ])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    });

    it('fails when email is not registered', function () {
        $this->post(route('auth.signin'), [
            'email'    => 'nobody@example.com',
            'password' => 'password',
        ])
            ->assertRedirect(route('signin'))
            ->assertSessionHas('error');

        $this->assertGuest();
    });

    it('fails with a wrong password', function () {
        $user = User::factory()->create(['password' => bcrypt('correct')]);

        $this->post(route('auth.signin'), [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ])
            ->assertRedirect(route('signin'))
            ->assertSessionHas('error');

        $this->assertGuest();
    });

    it('fails when the email field is missing', function () {
        $this->post(route('auth.signin'), ['password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    });

    it('fails when the password field is missing', function () {
        $this->post(route('auth.signin'), ['email' => 'user@example.com'])
            ->assertSessionHasErrors('password');

        $this->assertGuest();
    });

    it('fails when the email is not confirmed', function () {
        $user = User::factory()->unverified()->create(['password' => bcrypt('password')]);

        $this->post(route('auth.signin'), [
            'email'    => $user->email,
            'password' => 'password',
        ])
            ->assertRedirect(route('signin'))
            ->assertSessionHas('error');

        $this->assertGuest();
    });

    it('fails when the account is banned', function () {
        $user = User::factory()->banned()->create(['password' => bcrypt('password')]);

        $this->post(route('auth.signin'), [
            'email'    => $user->email,
            'password' => 'password',
        ])
            ->assertRedirect(route('signin'))
            ->assertSessionHas('error');

        $this->assertGuest();
    });
});
