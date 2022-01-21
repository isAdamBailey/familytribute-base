<?php

namespace Database\Seeders;

use App\Models\Obituary;
use App\Models\Person;
use App\Models\Picture;
use App\Models\Story;
use App\Models\Team;
use App\Models\User;
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
        $user = User::factory()->withPersonalTeam()->create([
            'email' => 'test@test.com',
        ]);
        $team = Team::first();

        $user2 = User::factory()->create([
            'email' => 'test2@test.com',
        ]);
        $user2->switchTeam($team);

        if (is_null($user2->current_team_id)) {
            $user2->update(['current_team_id' => $team->id]);
        }

        $team->users()->attach($user, ['role' => 'admin']);
        $team->users()->attach($user2, ['role' => 'editor']);

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
