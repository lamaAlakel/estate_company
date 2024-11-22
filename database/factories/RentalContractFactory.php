<?php

namespace Database\Factories;

use App\Models\Estate;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class RentalContractFactory extends Factory
{
    public function definition()
    {
        return [
            'rent_start_date' => $this->faker->date(),
            'rent_end_date' => $this->faker->date(),
            'type' => $this->faker->randomElement(['cash', 'installment', 'visaCard']),
            'monthly_rent' => $this->faker->numberBetween(1000, 5000),
            'estate_id' => Estate::factory(),
            'tenant_id' => Tenant::factory(),
        ];
    }
}
