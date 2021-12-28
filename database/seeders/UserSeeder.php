<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
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
        Team::first()->users()->attach($user, ['role' => 'admin']);
    }
}
