<?php

namespace Database\Seeders;

use App\Models\Distributor;
use App\Models\DistributorTicket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistributorTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DistributorTicket::factory()->count(25)->create();
    }
}
