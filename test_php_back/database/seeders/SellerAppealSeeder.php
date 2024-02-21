<?php

namespace Database\Seeders;

use App\Models\SellerAppeal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SellerAppealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SellerAppeal::factory()->count(3)->create();
    }
}
