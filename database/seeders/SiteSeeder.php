<?php

namespace Database\Seeders;

use App\Models\Obituary;
use App\Models\Person;
use App\Models\Picture;
use App\Models\SiteSetting;
use App\Models\Story;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Deterministic seed used by Playwright e2e and local demos.
 *
 * Contract (do not rename without updating e2e/constants.ts):
 * - Users: test@test.com (admin), test2@test.com (editor); password "password"
 * - Site: title "Family Tribute", registration on, secret "familytribute"
 * - People: /ada-lovelace, /alan-turing
 * - Pictures: /pictures/public-picnic (public+featured), /pictures/private-portrait (private)
 * - Stories: /stories/public-garden-story (public), /stories/private-letter (private)
 */
class SiteSeeder extends Seeder
{
    public function run(): void
    {
        $settings = SiteSetting::first();
        $settings->update([
            'title' => 'Family Tribute',
            'description' => '<p>Welcome to Family Tribute. We hope you will enjoy the history of our family.</p>',
            'registration' => true,
            'registration_secret' => 'familytribute',
        ]);

        $user = User::factory()->withPersonalTeam()->create([
            'name' => 'Test Admin',
            'email' => 'test@test.com',
        ]);
        $team = Team::first();

        $user2 = User::factory()->create([
            'name' => 'Test Editor',
            'email' => 'test2@test.com',
        ]);
        $user2->switchTeam($team);

        if (is_null($user2->current_team_id)) {
            $user2->update(['current_team_id' => $team->id]);
        }

        $team->users()->attach($user, ['role' => 'admin']);
        $team->users()->attach($user2, ['role' => 'editor']);

        $ada = Person::factory()->create([
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'photo_url' => 'https://picsum.photos/seed/ada/400/400',
        ]);
        Obituary::factory()->create([
            'person_id' => $ada->id,
            'birth_date' => '1815-12-10',
            'death_date' => '1852-11-27',
            'content' => '<p>Ada Lovelace was a mathematician and writer, known for her work on Charles Babbage\'s Analytical Engine.</p>',
            'background_photo_url' => 'https://picsum.photos/seed/ada-bg/1200/630',
        ]);

        $alan = Person::factory()->create([
            'first_name' => 'Alan',
            'last_name' => 'Turing',
            'photo_url' => 'https://picsum.photos/seed/alan/400/400',
        ]);
        Obituary::factory()->create([
            'person_id' => $alan->id,
            'birth_date' => '1912-06-23',
            'death_date' => '1954-06-07',
            'content' => '<p>Alan Turing was a mathematician, computer scientist, and cryptanalyst.</p>',
            'background_photo_url' => 'https://picsum.photos/seed/alan-bg/1200/630',
        ]);

        $publicPicture = Picture::factory()->create([
            'title' => 'Public Picnic',
            'description' => '<p>A sunny afternoon picnic with the family.</p>',
            'url' => 'https://picsum.photos/seed/picnic/800/600',
            'year' => 1950,
            'featured' => true,
            'private' => false,
        ]);

        $privatePicture = Picture::factory()->create([
            'title' => 'Private Portrait',
            'description' => '<p>A private family portrait.</p>',
            'url' => 'https://picsum.photos/seed/portrait/800/600',
            'year' => 1960,
            'featured' => false,
            'private' => true,
        ]);

        $publicStory = Story::factory()->create([
            'title' => 'Public Garden Story',
            'excerpt' => '<p>Remembering summers in the garden.</p>',
            'content' => '<p>Every summer we gathered in the garden to share stories and lemonade.</p>',
            'year' => 1945,
            'private' => false,
        ]);

        $privateStory = Story::factory()->create([
            'title' => 'Private Letter',
            'excerpt' => '<p>A letter kept in the family trunk.</p>',
            'content' => '<p>This letter was never meant for public eyes, but it tells who we were.</p>',
            'year' => 1938,
            'private' => true,
        ]);

        $ada->pictures()->attach([$publicPicture->id, $privatePicture->id]);
        $ada->stories()->attach([$publicStory->id, $privateStory->id]);
        $alan->pictures()->attach([$publicPicture->id]);
        $alan->stories()->attach([$publicStory->id]);
    }
}
