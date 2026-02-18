<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name'         => $this->faker->firstName(),
            'last_name'          => $this->faker->lastName(),
            'email'              => $this->faker->unique()->safeEmail(),
            'password'           => bcrypt('password'),
            'phone'              => $this->faker->numerify('##########'),
            'role'               => User::DEFAULT_ROLE,
            'is_superadmin'      => false,
            'banned'             => false,
            'verified_at'        => now(),
            'confirmation_token' => null,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => [
            'verified_at'        => null,
            'confirmation_token' => $this->faker->uuid(),
        ]);
    }

    public function banned(): static
    {
        return $this->state(fn () => ['banned' => true]);
    }

    public function superAdmin(): static
    {
        return $this->state(fn () => [
            'is_superadmin' => true,
            'role'          => null,
        ]);
    }
}
