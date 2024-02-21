<?php

namespace Database\Factories;

use App\Models\Appeal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Applicant>
 */
class ApplicantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Имя заявителя ' . fake()->name(),
            'email' => 'Email заявителя '. fake()->unique()->safeEmail,
            'phone' => 'Телефон заявителя '. fake()->phoneNumber,
            'appeal_id' => Appeal::inRandomOrder()->first()->id,
        ];
    }
}
