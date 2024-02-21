<?php

namespace Database\Seeders;

use App\Models\Appeal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppealsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Appeal::factory()->count(5)->create();
    }
}
