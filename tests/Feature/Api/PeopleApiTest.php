<?php

namespace Tests\Feature\Api;

use App\Models\Obituary;
use App\Models\Person;
use App\Models\Picture;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeopleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_people_index_is_paginated()
    {
        Obituary::factory()->count(20)->create();
        $this->assertDatabaseCount('people', 20);

        $response = $this->getJson(route('api.people.index'));

        $response->assertOk()
            ->assertJsonCount(15, 'people.data')
            ->assertJsonStructure([
                'people' => [
                    'data' => [
                        ['full_name', 'photo_url', 'obituary' => ['birth_date', 'death_date']],
                    ],
                    'links',
                ],
            ]);
    }

    public function test_people_can_be_searched()
    {
        Obituary::factory()->count(20)->create();

        $person = Person::first();
        $searchTerm = $person->first_name;

        $response = $this->getJson(route('api.people.index', ['search' => $searchTerm]));

        $response->assertOk()
            ->assertJsonCount(1, 'people.data')
            ->assertJson([
                'people' => [
                    'data' => [
                        ['full_name' => $person->full_name, 'photo_url' => $person->photo_url],
                    ],
                ],
            ]);
    }

    public function test_person_show_by_slug()
    {
        $obituary = Obituary::factory()->create();
        $story = Story::factory()->create();
        $picture = Picture::factory()->create();

        $obituary->person->stories()->attach($story);
        $obituary->person->pictures()->attach($picture);

        $response = $this->getJson(route('api.people.show', $obituary->person->slug));

        $response->assertOk()
            ->assertJsonStructure([
                'person' => [
                    'full_name', 'photo_url',
                    'obituary' => ['birth_date', 'death_date', 'content', 'background_photo_url'],
                    'pictures', 'stories', 'parents', 'children',
                ],
                'people',
            ])
            ->assertJsonMissingPath('person.last_name')
            ->assertJsonMissingPath('person.parent_ids');
    }

    public function test_person_show_by_slug_includes_auth_only_fields_when_authenticated()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $obituary = Obituary::factory()->create();

        $response = $this->getJson(route('api.people.show', $obituary->person->slug));

        $response->assertOk()
            ->assertJsonStructure([
                'person' => ['last_name', 'parent_ids'],
            ]);
    }

    public function test_person_show_hides_private_pictures_and_stories()
    {
        $obituary = Obituary::factory()->create();
        $picture = Picture::factory()->create(['private' => 1]);
        $story = Story::factory()->create(['private' => 1]);

        $obituary->person->pictures()->attach($picture);
        $obituary->person->stories()->attach($story);

        $response = $this->getJson(route('api.people.show', $obituary->person->slug));

        $response->assertOk()
            ->assertJsonCount(0, 'person.pictures')
            ->assertJsonCount(0, 'person.stories');
    }

    public function test_person_show_reveals_private_pictures_and_stories_when_authenticated()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $obituary = Obituary::factory()->create();
        $picture = Picture::factory()->create(['private' => 1]);
        $story = Story::factory()->create(['private' => 1]);

        $obituary->person->pictures()->attach($picture);
        $obituary->person->stories()->attach($story);

        $response = $this->getJson(route('api.people.show', $obituary->person->slug));

        $response->assertOk()
            ->assertJsonCount(1, 'person.pictures')
            ->assertJsonCount(1, 'person.stories');
    }
}
