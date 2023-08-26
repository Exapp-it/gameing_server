<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'amount'   => $this->faker->randomFloat(3, 0, 100),
            'currency' => $this->faker->randomElement(config('enums.currency')),
            'status'   => $this->faker->randomElement(array_values(config('enums.transaction_status'))),
        ];
    }
}
