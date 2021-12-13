<?php

namespace Tests\Feature;

use App\Models\Obituary;
use App\Models\Person;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\Assert;
use Tests\TestCase;

class StoriesTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_stories_component_is_rendered_with_paginated_data()
    {
        Story::factory()->count(20)->create();
        $this->assertDatabaseCount('stories', 20);

        $this->get(route('stories.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                ->component('Stories')
                ->url('/stories')
                ->has('stories.data', 15)
                ->has('stories.data.0.excerpt')
            );
    }

    public function test_stories_can_be_searched()
    {
        Story::factory()->count(20)->create();

        $searchTerm = 'qwerty';

        $story = Story::factory()->create([
            'title' => "title that has $searchTerm in the content",
        ]);

        $response = $this->get(route('stories.index', ['search' => $searchTerm]));

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Stories')
                ->url('/stories?search='.$searchTerm)
                ->has('stories.data', 1)
                ->has('stories.links')
                ->has('stories.data.0', fn (Assert $page) => $page
                    ->where('title', $story->title)
                    ->where('excerpt', $story->excerpt)
                    ->etc()
                )
        );
    }

    public function test_story_component_is_shown_by_slug()
    {
        $story = Story::factory()->create();

        $this->get(route('stories.show', $story))
            ->assertInertia(
                fn (Assert $page) => $page
                ->component('Story')
                ->url('/stories/'.$story->slug)
                ->has('story.title')
                ->has('story.content')
                ->has('story.excerpt')
                ->has('story.person_ids')
                ->has('people')
            );
    }

    public function test_new_story_is_stored()
    {
        $this->actingAs(User::factory()->create());

        $personCount = 5;
        $people = Person::factory()->count($personCount)->create();

        $request = [
            'title' => $this->faker->words(2, true),
            'excerpt' => $this->faker->sentence,
            'content' => $this->faker->sentences(4, true),
            'person_ids' => $people->modelKeys(),
        ];

        $this->post(route('stories.store'), $request)
            ->assertRedirect()
            ->assertSessionHas('flash.banner');

        $story = Story::first();
        $this->assertEquals(strtolower($story->title), $request['title']);
        $this->assertEquals($story->excerpt, $request['excerpt']);
        $this->assertEquals($story->content, $request['content']);
        $this->assertCount($personCount, $story->people->toArray());
    }

    public function test_all_properties_of_story_can_be_updated()
    {
        $this->actingAs(User::factory()->create());

        $story = Story::factory()->create();
        $personCount = 2;
        $people = Person::factory()->count($personCount)->create();

        $request = [
            'title' => $this->faker->words(2, true),
            'excerpt' => $this->faker->sentence,
            'content' => $this->faker->sentences(4, true),
            'person_ids' => $people->modelKeys(),
        ];

        $response = $this->put(route('stories.update', $story), $request);

        $story = $story->fresh();
        $response->assertRedirect(route('stories.show', $story))
            ->assertSessionHas('flash.banner');

        $this->assertEquals(strtolower($story->title), $request['title']);
        $this->assertEquals($story->excerpt, $request['excerpt']);
        $this->assertEquals($story->content, $request['content']);
        $this->assertCount($personCount, $story->people->toArray());
    }

    public function test_story_can_be_destroyed()
    {
        $this->actingAs(User::factory()->create());

        $obituary = Obituary::factory()->create();
        $story = Story::factory()->create();
        $obituary->person->stories()->attach($story);

        $response = $this->delete(route('stories.destroy', $story));
        $response->assertRedirect(route('stories.index'))
            ->assertSessionHas('flash.banner');

        $personHasStory = $obituary->person->stories()
            ->where('stories.id', $story->id)
            ->exists();
        $this->assertFalse($personHasStory);

        $this->assertDeleted($story);
    }
}
