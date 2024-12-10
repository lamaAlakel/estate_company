<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'nationality' => $this->faker->country(),
            'date_of_birth' => $this->faker->date(),
            'work_type' => $this->faker->randomElement(['on_site', 'remotely']),
            'health_insurance_expiration_date' => $this->faker->date(),
            'visa_start_date' => $this->faker->date(),
            'visa_expiration_date' => $this->faker->date(),
            'passport_expiration_date' => $this->faker->date(),
            'UAE_residency_number' => $this->faker->numerify('########'),
            'unified_number' => $this->faker->numerify('########'),
            'salary' => $this->faker->numberBetween(5000, 20000),
            'days_worked' => collect(range(1, 5))->map(fn () => $this->faker->dateTimeBetween('2024-01-01', '2024-12-31')->format('Y-m-d'))->toArray(),
            'position'=>$this->faker->jobTitle(),
        ];
    }
}
