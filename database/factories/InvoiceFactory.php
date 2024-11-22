<?php

namespace Database\Factories;

use App\Models\Estate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'estate_id'=> Estate::factory(),
            'meter_number'=> fake()->numerify,
            'account_number'=> fake()->numerify,
            'total_invoice_amount'=> fake()->numberBetween('1000','2000'),
            'type'=> fake()->randomElement(['water','electric']),
            'date'=> fake()->date,
        ];
    }
}
