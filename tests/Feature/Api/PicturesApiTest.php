<?php

namespace Tests\Feature\Api;

use App\Models\Obituary;
use App\Models\Person;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PicturesApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_pictures_index_hides_private_fields_for_guests()
    {
        Picture::factory()->count(20)->create();

        $response = $this->getJson(route('api.pictures.index'));

        $response->assertOk()
            ->assertJsonCount(15, 'pictures.data')
            ->assertJsonMissingPath('pictures.data.0.featured')
            ->assertJsonMissingPath('pictures.data.0.private');
    }

    public function test_pictures_index_includes_auth_only_fields_for_authenticated_users()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        Picture::factory()->count(5)->create();

        $response = $this->getJson(route('api.pictures.index'));

        $response->assertOk()
            ->assertJsonStructure([
                'pictures' => ['data' => [['featured', 'private', 'person_ids']]],
            ]);
    }

    public function test_private_pictures_are_not_shown_publicly()
    {
        Picture::factory()->create();
        Picture::factory()->count(20)->create(['private' => 1]);

        $response = $this->getJson(route('api.pictures.index'));

        $response->assertOk()->assertJsonCount(1, 'pictures.data');
    }

    public function test_private_pictures_are_shown_to_authenticated_users()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        Picture::factory()->create();
        Picture::factory()->count(10)->create(['private' => 1]);

        $response = $this->getJson(route('api.pictures.index'));

        $response->assertOk()->assertJsonCount(11, 'pictures.data');
    }

    public function test_picture_show_by_slug()
    {
        $picture = Picture::factory()->create();

        $response = $this->getJson(route('api.pictures.show', $picture));

        $response->assertOk()
            ->assertJsonStructure(['picture' => ['title', 'description', 'url', 'year', 'people'], 'people'])
            ->assertJsonMissingPath('picture.featured')
            ->assertJsonMissingPath('picture.private');
    }

    public function test_private_picture_show_is_404_for_guests()
    {
        $picture = Picture::factory()->create(['private' => 1]);

        $this->getJson(route('api.pictures.show', $picture))->assertNotFound();
    }

    public function test_private_picture_show_is_visible_when_authenticated()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $picture = Picture::factory()->create(['private' => 1]);

        $this->getJson(route('api.pictures.show', $picture))
            ->assertOk()
            ->assertJsonStructure(['picture' => ['featured', 'private', 'person_ids']]);
    }

    public function test_new_picture_is_stored_and_uploaded()
    {
        Storage::fake('s3');
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $people = Person::factory()->count(5)->create();

        $request = [
            'photo' => UploadedFile::fake()->image('photo1.jpg'),
            'title' => $this->faker->words(2, true),
            'description' => $this->faker->sentences(4, true),
            'year' => $this->faker->year(),
            'featured' => $this->faker->numberBetween(0, 1),
            'private' => $this->faker->numberBetween(0, 1),
            'person_ids' => $people->modelKeys(),
        ];

        $response = $this->postJson(route('api.pictures.store'), $request);

        $response->assertCreated()
            ->assertJsonStructure(['picture' => ['slug', 'title', 'url']]);

        $filePath = 'pictures/'.$request['photo']->hashName();
        Storage::disk('s3')->assertExists($filePath);

        $picture = Picture::first();
        $this->assertEquals(strtolower($picture->title), $request['title']);
        $this->assertCount(5, $picture->people->toArray());
    }

    public function test_new_picture_store_requires_authentication()
    {
        $this->postJson(route('api.pictures.store'), [])->assertUnauthorized();
    }

    public function test_all_properties_of_picture_can_be_updated()
    {
        Storage::fake('s3');
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $picture = Picture::factory()->create();
        $people = Person::factory()->count(2)->create();

        $request = [
            'photo' => UploadedFile::fake()->image('photo1.jpg'),
            'title' => $this->faker->words(2, true),
            'description' => $this->faker->sentences(4, true),
            'year' => $this->faker->year(),
            'featured' => $this->faker->numberBetween(0, 1),
            'private' => $this->faker->numberBetween(0, 1),
            'person_ids' => $people->modelKeys(),
        ];

        $response = $this->putJson(route('api.pictures.update', $picture), $request);

        $response->assertOk()
            ->assertJsonStructure(['picture' => ['slug', 'title']]);

        $picture = $picture->fresh();
        $this->assertEquals(strtolower($picture->title), $request['title']);
        $this->assertEquals($picture->year, $request['year']);
        $this->assertCount(2, $picture->people->toArray());
    }

    public function test_picture_can_be_destroyed()
    {
        Storage::fake('s3');
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $obituary = Obituary::factory()->create();
        $photo = UploadedFile::fake()->image('photo1.jpg');
        $picture = Picture::factory()->create(['url' => $photo]);
        $obituary->person->pictures()->attach($picture);

        $response = $this->deleteJson(route('api.pictures.destroy', $picture));

        $response->assertNoContent();

        $filePath = 'pictures/'.$photo->hashName();
        Storage::disk('s3')->assertMissing($filePath);
        $this->assertModelMissing($picture);
    }
}
