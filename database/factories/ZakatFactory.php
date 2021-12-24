<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ZakatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->numberBetween(),
            'transaction_no' => $this->faker->postcode(),
            'receive_from' => 1,
            'zakat_pic' => 1,
            'transaction_date' => $this->faker->date(),
            'hijri_year' => 1443,
            'family_head' => $this->faker->name(),

        ];
    }
}
