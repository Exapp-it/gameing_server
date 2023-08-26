<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WithdrawFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'status'       => $this->faker->randomElement(array_values(config('enums.transaction_status'))),
            'internal_id'  => 'some_id_'.$this->faker->word(),
            'amount'       => $this->faker->randomFloat(3, 0, 100),
            'fiat_address' => $this->faker->creditCardNumber(),
            'confirmed'    => $this->faker->randomElement([true, false]),
            'currency'     => $this->faker->randomElement(config('enums.currency')),
        ];
    }
}
