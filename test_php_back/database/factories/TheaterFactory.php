<?php

namespace Database\Factories;

use App\Models\District;
use Database\Seeders\DistrictSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Theater>
 */
class TheaterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word,
            'address' => fake()->address,
            'district_id' => District::inRandomOrder()->first(),
        ];
    }
}
