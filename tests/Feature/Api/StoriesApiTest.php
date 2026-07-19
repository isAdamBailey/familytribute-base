<?php

namespace Tests\Feature\Api;

use App\Models\Obituary;
use App\Models\Person;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoriesApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_stories_index_hides_private_field_for_guests()
    {
        Story::factory()->count(20)->create();

        $response = $this->getJson(route('api.stories.index'));

        $response->assertOk()
            ->assertJsonCount(15, 'stories.data')
            ->assertJsonMissingPath('stories.data.0.private');
    }

    public function test_stories_index_includes_private_field_for_authenticated_users()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        Story::factory()->count(5)->create();

        $this->getJson(route('api.stories.index'))
            ->assertOk()
            ->assertJsonStructure(['stories' => ['data' => [['private', 'person_ids']]]]);
    }

    public function test_private_stories_are_not_shown_publicly()
    {
        Story::factory()->create();
        Story::factory()->count(20)->create(['private' => 1]);

        $this->getJson(route('api.stories.index'))
            ->assertOk()
            ->assertJsonCount(1, 'stories.data');
    }

    public function test_private_stories_are_shown_to_authenticated_users()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        Story::factory()->create();
        Story::factory()->count(10)->create(['private' => 1]);

        $this->getJson(route('api.stories.index'))
            ->assertOk()
            ->assertJsonCount(11, 'stories.data');
    }

    public function test_story_show_by_slug()
    {
        $story = Story::factory()->create();

        $this->getJson(route('api.stories.show', $story))
            ->assertOk()
            ->assertJsonStructure(['story' => ['title', 'content', 'excerpt', 'year', 'media_url', 'people'], 'people'])
            ->assertJsonMissingPath('story.private');
    }

    public function test_private_story_show_is_404_for_guests()
    {
        $story = Story::factory()->create(['private' => 1]);

        $this->getJson(route('api.stories.show', $story))->assertNotFound();
    }

    public function test_private_story_show_is_visible_when_authenticated()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $story = Story::factory()->create(['private' => 1]);

        $this->getJson(route('api.stories.show', $story))
            ->assertOk()
            ->assertJsonStructure(['story' => ['private', 'person_ids']]);
    }

    public function test_new_story_is_stored()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $people = Person::factory()->count(5)->create();

        $request = [
            'title' => $this->faker->words(2, true),
            'excerpt' => $this->faker->sentence,
            'content' => $this->faker->sentences(4, true),
            'year' => $this->faker->numberBetween(1900, 2000),
            'private' => $this->faker->numberBetween(0, 1),
            'person_ids' => $people->modelKeys(),
        ];

        $response = $this->postJson(route('api.stories.store'), $request);

        $response->assertCreated()
            ->assertJsonStructure(['story' => ['slug', 'title', 'excerpt']]);

        $story = Story::first();
        $this->assertEquals(strtolower($story->title), $request['title']);
        $this->assertCount(5, $story->people->toArray());
    }

    public function test_new_story_store_requires_authentication()
    {
        $this->postJson(route('api.stories.store'), [])->assertUnauthorized();
    }

    public function test_all_properties_of_story_can_be_updated()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $story = Story::factory()->create();
        $people = Person::factory()->count(2)->create();

        $request = [
            'title' => $this->faker->words(2, true),
            'excerpt' => $this->faker->sentence,
            'content' => $this->faker->sentences(4, true),
            'year' => $this->faker->numberBetween(1900, 2000),
            'private' => $this->faker->numberBetween(0, 1),
            'person_ids' => $people->modelKeys(),
        ];

        $response = $this->putJson(route('api.stories.update', $story), $request);

        $response->assertOk()
            ->assertJsonStructure(['story' => ['slug', 'title']]);

        $story = $story->fresh();
        $this->assertEquals(strtolower($story->title), $request['title']);
        $this->assertEquals($story->year, $request['year']);
        $this->assertCount(2, $story->people->toArray());
    }

    public function test_story_can_be_destroyed()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $obituary = Obituary::factory()->create();
        $story = Story::factory()->create();
        $obituary->person->stories()->attach($story);

        $response = $this->deleteJson(route('api.stories.destroy', $story));

        $response->assertNoContent();
        $this->assertModelMissing($story);
    }

    public function test_story_media_is_stored_on_create()
    {
        Storage::fake('s3');
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $file = UploadedFile::fake()->create('recording.mp3', 500, 'audio/mpeg');

        $this->postJson(route('api.stories.store'), [
            'title' => $this->faker->words(2, true),
            'excerpt' => $this->faker->sentence,
            'content' => $this->faker->sentences(4, true),
            'year' => $this->faker->numberBetween(1900, 2000),
            'private' => false,
            'media' => $file,
        ])->assertCreated();

        $story = Story::first();
        $this->assertNotNull($story->media_path);
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

        $this->putJson(route('api.stories.update', $story), ['media' => $newFile])
            ->assertOk();

        Storage::disk('s3')->assertMissing($oldPath);
        $newPath = $story->fresh()->media_path;
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

        $this->putJson(route('api.stories.update', $story), ['remove_media' => true])
            ->assertOk();

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

        $this->deleteJson(route('api.stories.destroy', $story))->assertNoContent();

        Storage::disk('s3')->assertMissing($path);
    }
}
