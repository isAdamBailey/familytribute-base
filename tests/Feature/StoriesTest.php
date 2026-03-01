<?php

namespace Tests\Feature;

use App\Models\Obituary;
use App\Models\Person;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
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
                    ->has('stories.data.0.year')
                    ->missing('stories.data.0.private')
                    ->has('meta.meta')
                    ->has('meta.title')
            );
    }

    public function test_stories_component_is_rendered_with_paginated_data_for_auth()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        Story::factory()->count(20)->create();
        $this->assertDatabaseCount('stories', 20);

        $this->get(route('stories.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Stories')
                    ->url('/stories')
                    ->has('stories.data', 15)
                    ->has('stories.data.0.excerpt')
                    ->has('stories.data.0.year')
                    ->has('stories.data.0.private')
                    ->has('meta.meta')
                    ->has('meta.title')
            );
    }

    public function test_private_stories_are_not_shown_publicly()
    {
        Story::factory()->create();
        Story::factory()->count(20)->create(['private' => 1]);
        $this->assertDatabaseCount('stories', 21);

        $this->get(route('stories.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Stories')
                    ->url('/stories')
                    ->has('stories.data', 1)
                    ->missing('stories.data.0.private')
            );
    }

    public function test_private_stories_are_shown_to_authenticated_users()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        Story::factory()->create();
        Story::factory()->count(10)->create(['private' => 1]);
        $this->assertDatabaseCount('stories', 11);

        $this->get(route('stories.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Stories')
                    ->url('/stories')
                    ->has('stories.data', 11)
                    ->has('stories.data.0.private')
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
                    ->where('year', $story->year)
                    ->etc()
                )
                ->has('meta.meta')
                ->has('meta.title')
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
                    ->has('story.year')
                    ->has('story.media_url')
                    ->missing('story.media_path')
                    ->missing('story.private')
                    ->missing('story.person_ids')
                    ->has('people')
                    ->has('meta.meta')
                    ->has('meta.title')
            );
    }

    public function test_private_story_is_not_shown_publicly()
    {
        $story = Story::factory()->create(['private' => 1]);

        $this->get(route('stories.show', $story))->assertRedirect(route('stories.index'));
    }

    public function test_new_story_is_stored()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $personCount = 5;
        $people = Person::factory()->count($personCount)->create();

        $request = [
            'title' => $this->faker->words(2, true),
            'excerpt' => $this->faker->sentence,
            'content' => $this->faker->sentences(4, true),
            'year' => $this->faker->numberBetween(1900, 2000),
            'private' => $this->faker->numberBetween(0, 1),
            'person_ids' => $people->modelKeys(),
        ];

        $this->post(route('stories.store'), $request)
            ->assertRedirect()
            ->assertSessionHas('flash.banner');

        $story = Story::first();
        $this->assertEquals(strtolower($story->title), $request['title']);
        $this->assertEquals($story->excerpt, $request['excerpt']);
        $this->assertEquals($story->content, $request['content']);
        $this->assertEquals($story->private, $request['private']);
        $this->assertEquals($story->year, $request['year']);
        $this->assertCount($personCount, $story->people->toArray());
    }

    public function test_all_properties_of_story_can_be_updated()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $story = Story::factory()->create();
        $personCount = 2;
        $people = Person::factory()->count($personCount)->create();

        $request = [
            'title' => $this->faker->words(2, true),
            'excerpt' => $this->faker->sentence,
            'content' => $this->faker->sentences(4, true),
            'year' => $this->faker->numberBetween(1900, 2000),
            'private' => $this->faker->numberBetween(0, 1),
            'person_ids' => $people->modelKeys(),
        ];

        $response = $this->put(route('stories.update', $story), $request);

        $story = $story->fresh();
        $response->assertRedirect(route('stories.show', $story))
            ->assertSessionHas('flash.banner');

        $this->assertEquals(strtolower($story->title), $request['title']);
        $this->assertEquals($story->excerpt, $request['excerpt']);
        $this->assertEquals($story->content, $request['content']);
        $this->assertEquals($story->private, $request['private']);
        $this->assertEquals($story->year, $request['year']);
        $this->assertCount($personCount, $story->people->toArray());
    }

    public function test_story_can_be_destroyed()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

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

        $this->assertModelMissing($story);
    }

    public function test_story_media_is_stored_on_create()
    {
        Storage::fake('s3');
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $file = UploadedFile::fake()->create('recording.mp3', 500, 'audio/mpeg');

        $this->post(route('stories.store'), [
            'title' => $this->faker->words(2, true),
            'excerpt' => $this->faker->sentence,
            'content' => $this->faker->sentences(4, true),
            'year' => $this->faker->numberBetween(1900, 2000),
            'private' => false,
            'media' => $file,
        ])->assertRedirect()->assertSessionHas('flash.banner');

        $story = Story::first();
        $this->assertNotNull($story->media_path);
        $this->assertStringStartsWith('story-media/', $story->media_path);
        Storage::disk('s3')->assertExists($story->media_path);
    }

    public function test_story_media_is_replaced_on_update()
    {
        Storage::fake('s3');
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $oldPath = 'story-media/old-recording.mp3';
        Storage::disk('s3')->put($oldPath, 'old content');
        $story = Story::factory()->create(['media_path' => $oldPath]);

        $newFile = UploadedFile::fake()->create('new-recording.mp3', 500, 'audio/mpeg');

        $this->put(route('stories.update', $story), ['media' => $newFile])
            ->assertRedirect();

        Storage::disk('s3')->assertMissing($oldPath);
        $newPath = $story->fresh()->media_path;
        $this->assertNotNull($newPath);
        $this->assertNotEquals($oldPath, $newPath);
        Storage::disk('s3')->assertExists($newPath);
    }

    public function test_story_media_can_be_removed()
    {
        Storage::fake('s3');
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $path = 'story-media/recording.mp3';
        Storage::disk('s3')->put($path, 'content');
        $story = Story::factory()->create(['media_path' => $path]);

        $this->put(route('stories.update', $story), ['remove_media' => true])
            ->assertRedirect();

        Storage::disk('s3')->assertMissing($path);
        $this->assertNull($story->fresh()->media_path);
    }

    public function test_story_media_is_deleted_from_s3_on_destroy()
    {
        Storage::fake('s3');
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $path = 'story-media/recording.mp3';
        Storage::disk('s3')->put($path, 'content');
        $story = Story::factory()->create(['media_path' => $path]);

        $this->delete(route('stories.destroy', $story))
            ->assertRedirect(route('stories.index'));

        Storage::disk('s3')->assertMissing($path);
        $this->assertModelMissing($story);
    }
}
