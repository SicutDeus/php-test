<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\DistributorTicket;
use App\Models\House;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            HouseSeeder::class,
            UsersTableSeeder::class,
            DistrictSeeder::class,
            TheaterSeeder::class,
            EventsTableSeeder::class,
            DistributorSeeder::class,
            TicketTableSeeder::class,
            DistributorTicketSeeder::class,
            SellerSeeder::class,
            AppealsSeeder::class,
            ApplicantSeeder::class,
            ProductSeeder::class,
            SellerAppealSeeder::class,
        ]);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
