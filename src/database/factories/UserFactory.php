<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'firstname'         => $this->faker->firstName(),
            'surname'           => $this->faker->lastName(),
            'username'          => $this->faker->unique()->userName(),
            'username_nice'     => Str::slug($this->faker->unique()->words(2, true)),
            'email'             => $this->faker->unique()->safeEmail(),
            'password'          => bcrypt('password'),
            'remember_token'    => Str::random(10),
            'admin'             => false,
            'email_verified_at' => now(),
            'credit_total'      => 0,
        ];
    }

    /** State: admin user */
    public function admin(): static
    {
        return $this->state(fn() => ['admin' => true]);
    }

    /** State: unverified email */
    public function unverified(): static
    {
        return $this->state(fn() => ['email_verified_at' => null]);
    }

    /** State: banned user */
    public function banned(): static
    {
        return $this->state(fn() => ['banned' => true]);
    }
}
