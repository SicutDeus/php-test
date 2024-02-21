<?php

namespace Database\Factories;

use App\Models\Appeal;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SellerAppeal>
 */
class SellerAppealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'appeal_id' => 1,
            'seller_id' => Seller::inRandomOrder()->first()->id
        ];
    }
}
