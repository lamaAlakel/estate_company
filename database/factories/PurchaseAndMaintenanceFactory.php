<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseAndMaintenance>
 */
class PurchaseAndMaintenanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'=>fake()->name,
            'date'=>fake()->date,
            'quantity'=>fake()->numberBetween('2','10'),
            'unit_cost'=>fake()->numberBetween('300' , '1000'),
            'total_paid'=>fake()->numberBetween('10','5000'),
        ];
    }
}
