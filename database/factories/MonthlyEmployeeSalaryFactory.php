<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class MonthlyEmployeeSalaryFactory extends Factory
{
    public function definition()
    {
        return [
            'employee_id' => Employee::factory(),
            'main_salary' => $this->faker->numberBetween(5000, 15000),
            'bonus' => $this->faker->numberBetween(100, 500),
            'daily_amount' => $this->faker->numberBetween(200, 1000),
            'notice' => $this->faker->sentence(),
            'date_should_translate_to_month' => $this->faker->date(),
        ];
    }
}
