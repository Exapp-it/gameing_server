<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'login'             => $this->faker->word(),
            'first_name'        => $this->faker->name(),
            'last_name'         => $this->faker->name(),
            'patronymic'        => $this->faker->name(),
            'address'           => $this->faker->address(),
            'birth_date'        => $this->faker->date(),
            'fingerprint'       => $this->faker->uuid(),
            'balance'           => $this->faker->randomFloat(),
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'role'              => $this->faker->randomElement(['client', 'analyst', 'manager']),
            'city'              => $this->faker->city(),
            'gender'            => $this->faker->randomElement(['M','F']),
            'phone'             => $this->faker->tollFreePhoneNumber(),
            'country'           => $this->faker->country(),
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
            'currency'          => $this->faker->randomElement(config('enums.user_currency')),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
