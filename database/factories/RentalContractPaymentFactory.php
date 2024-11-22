<?php

namespace Database\Factories;

use App\Models\RentalContract;
use Illuminate\Database\Eloquent\Factories\Factory;

class RentalContractPaymentFactory extends Factory
{
    public function definition()
    {
        return [
            'date' => $this->faker->date(),
            'amount' => $this->faker->numberBetween(500, 2000),
            'rental_contract_id' => RentalContract::factory(),
        ];
    }
}
