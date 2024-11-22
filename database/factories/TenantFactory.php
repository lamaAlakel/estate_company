<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name'=>fake()->text,
            'id_number'=>fake()->numerify,
            'phone_number'=>fake()->phoneNumber ,
            'address' =>fake()->address ,
            'id_image'=>fake()->imageUrl ,
        ];
    }
}
