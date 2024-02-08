<?php

namespace Database\Factories;

use App\Models\Theater;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'theater ' . fake()->sentence(3),
            'date' => fake()->date('Y-m-d'),
            'theater_id' => Theater::inRandomOrder()->first()->id,
            ];
    }
}
