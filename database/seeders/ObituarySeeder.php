<?php

namespace Database\Seeders;

use App\Models\Obituary;
use Illuminate\Database\Seeder;

class ObituarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Obituary::factory()->count(5)->create();
    }
}
