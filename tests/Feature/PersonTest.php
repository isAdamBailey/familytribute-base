<?php

namespace Tests\Feature;

use App\Models\Obituary;
use App\Models\Person;
use App\Models\Picture;
use App\Models\Story;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\Assert;
use Tests\TestCase;

class PersonTest extends TestCase
{
    use RefreshDatabase;

    public function test_people_component_is_rendered_with_paginated_data()
    {
        Obituary::factory()->count(20)->create();
        $this->assertDatabaseCount('obituaries', 20);
        $this->assertDatabaseCount('people', 20);

        $response = $this->get(route('people.index'));

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('People')
                ->url('/people')
                ->has('people.data', 15)
                ->has('people.links')
                ->has('people.data.0.full_name')
                ->has('people.data.0.obituary')
                ->has('people.data.0.obituary.birth_date')
                ->has('people.data.0.obituary.death_date')
                ->has('people.data.0.obituary.main_photo_url')
                ->has('meta.meta')
                ->has('meta.title')
        );
    }

    public function test_people_component_can_be_searched()
    {
        Obituary::factory()->count(20)->create();

        $person = Person::first();
        $searchTerm = $person->first_name;

        $response = $this->get(route('people.index', ['search' => $searchTerm]));

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('People')
                ->url('/people?search='.$searchTerm)
                ->has('people.data', 1)
                ->has('people.links')
                ->has('people.data.0', fn (Assert $page) => $page
                    ->where('full_name', $person->full_name)
                    ->etc()
                )
                ->has('people.data.0.obituary')
                ->has('people.data.0.obituary.birth_date')
                ->has('people.data.0.obituary.death_date')
                ->has('people.data.0.obituary.main_photo_url')
                ->has('meta.meta')
                ->has('meta.title')
        );
    }

    public function test_person_component_is_shown_by_slug()
    {
        $obituary = Obituary::factory()->create();
        $story = Story::factory()->create();
        $picture = Picture::factory()->create();

        $obituary->person->stories()->attach($story);
        $obituary->person->pictures()->attach($picture);

        $this->get(route('people.show', $obituary->person->slug))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Person')
                    ->url('/'.$obituary->person->slug)
                    ->has('person.full_name')
                    ->has('person.first_name')
                    ->has('person.last_name')
                    ->has('person.parent_ids')
                    ->has('person.parents')
                    ->has('person.children')
                    ->has('person.obituary.birth_date')
                    ->has('person.obituary.death_date')
                    ->has('person.obituary.content')
                    ->has('person.obituary.main_photo_url')
                    ->has('person.obituary.background_photo_url')
                    ->has('person.pictures.0', fn (Assert $page) => $page
                        ->where('slug', $picture->slug)
                        ->where('title', $picture->title)
                        ->where('url', $picture->url)
                        ->where('year', (int) $picture->year)
                        ->etc()
                    )
                    ->has('person.stories.0', fn (Assert $page) => $page
                        ->where('slug', $story->slug)
                        ->where('title', $story->title)
                        ->where('excerpt', $story->excerpt)
                        ->etc()
                    )
                    ->has('meta.meta')
                    ->has('meta.title')
            );
    }

    public function test_person_component_has_no_private_model_relations()
    {
        $obituary = Obituary::factory()->create();
        $picture = Picture::factory()->create(['private' => 1]);
        $story = Story::factory()->create(['private' => 1]);

        $obituary->person->pictures()->attach($picture);
        $obituary->person->stories()->attach($story);

        $this->get(route('people.show', $obituary->person->slug))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Person')
                    ->url('/'.$obituary->person->slug)
                    ->missing('person.pictures.0')
                    ->missing('person.stories.0')
            );
    }
}
