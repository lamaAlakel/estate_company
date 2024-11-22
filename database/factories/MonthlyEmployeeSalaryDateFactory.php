<?php

namespace Database\Factories;

use App\Models\MonthlyEmployeeSalary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MonthlyEmployeeSalaryDate>
 */
class MonthlyEmployeeSalaryDateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'monthly_employee_salary_id'=>MonthlyEmployeeSalary::factory(),
            'date'=>fake()->date,
            'amount'=>fake()->numberBetween('1000','10000'),
        ];
    }
}
