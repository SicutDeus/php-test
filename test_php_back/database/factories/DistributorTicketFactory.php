<?php

namespace Database\Factories;

use App\Models\Distributor;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DistributorTicket>
 */
class DistributorTicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::inRandomOrder()->first()->id,
            'distributor_id' => Distributor::inRandomOrder()->first()->id,
        ];
    }
}