<?php

namespace Database\Factories;

use App\Models\Appeal;
use App\Models\House;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product' => 'Название продукта' . fake()->name,
            'appeal_id' => Appeal::inRandomOrder()->first()->id,
            'house_id' => House::inRandomOrder()->first()->id,
        ];
    }
}
