<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ZakatLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'muzakki_id',
            'fitrah_rp',
            'fitrah_kg',
            'fitrah_lt',
            'maal_rp',
            'profesi_rp',
            'infaq_rp',
            'wakaf_rp',
            'fidyah_kg',
            'fidyah_rp',
            'kafarat_rp',
        ];
    }
}
