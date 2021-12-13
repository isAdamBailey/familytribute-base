<?php

namespace Database\Seeders;

use App\Models\Obituary;
use App\Models\Person;
use App\Models\Picture;
use App\Models\Story;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Obituary::factory()->count(20)->create();
        $pictures = Picture::factory()->count(100)->create();
        $stories = Story::factory()->count(20)->create();

        Person::all()->each(function ($person) use ($pictures, $stories) {
            $person->pictures()->attach(
                $pictures->random(rand(1, 10))->pluck('id')->toArray()
            );

            $person->stories()->attach(
                $stories->random(rand(1, 10))->pluck('id')->toArray()
            );
        });
    }
}
