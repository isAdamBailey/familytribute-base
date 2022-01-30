<?php

namespace Tests\Feature;

use App\Models\Obituary;
use App\Models\Person;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\Assert;
use Tests\TestCase;

class PicturesTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_pictures_component_is_rendered_with_paginated_data()
    {
        Picture::factory()->count(20)->create();
        $this->assertDatabaseCount('pictures', 20);

        $this->get(route('pictures.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                ->component('Pictures')
                ->url('/pictures')
                ->has('pictures.data', 15)
                ->has('pictures.links')
                ->has('pictures.data.0.description')
                ->has('pictures.data.0.url')
                ->has('pictures.data.0.title')
                ->has('pictures.data.0.year')
                ->missing('pictures.data.0.featured')
                ->missing('pictures.data.0.private')
                ->has('meta.meta')
                ->has('meta.title')
            );
    }

    public function test_pictures_component_is_rendered_with_paginated_data_and_extra_for_auth()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        Picture::factory()->count(20)->create();
        $this->assertDatabaseCount('pictures', 20);

        $this->get(route('pictures.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Pictures')
                    ->url('/pictures')
                    ->has('pictures.data', 15)
                    ->has('pictures.links')
                    ->has('pictures.data.0.featured')
                    ->has('pictures.data.0.private')
            );
    }

    public function test_private_pictures_are_not_shown_publicly()
    {
        Picture::factory()->create();
        Picture::factory()->count(20)->create(['private' => 1]);
        $this->assertDatabaseCount('pictures', 21);

        $this->get(route('pictures.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Pictures')
                    ->url('/pictures')
                    ->has('pictures.data', 1)
                    ->missing('pictures.data.0.featured')
                    ->missing('pictures.data.0.private')
            );
    }

    public function test_private_pictures_are_shown_to_authenticated_users()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        Picture::factory()->create();
        Picture::factory()->count(10)->create(['private' => 1]);
        $this->assertDatabaseCount('pictures', 11);

        $this->get(route('pictures.index'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Pictures')
                    ->url('/pictures')
                    ->has('pictures.data', 11)
                    ->has('pictures.data.0.featured')
                    ->has('pictures.data.0.private')
            );
    }

    public function test_pictures_can_be_searched()
    {
        Picture::factory()->count(20)->create();

        $searchTerm = 'qwerty';

        $picture = Picture::factory()->create([
            'title' => "title that has $searchTerm in the content",
        ]);

        $response = $this->get(route('pictures.index', ['search' => $searchTerm]));

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Pictures')
                ->url('/pictures?search='.$searchTerm)
                ->has('pictures.data', 1)
                ->has('pictures.links')
                ->has('pictures.data.0', fn (Assert $page) => $page
                    ->where('title', $picture->title)
                    ->where('description', $picture->description)
                    ->where('url', $picture->url)
                    ->where('year', (int) $picture->year)
                    ->etc()
                )
                ->has('meta.meta')
                ->has('meta.title')
        );
    }

    public function test_picture_component_is_shown_by_slug()
    {
        $picture = Picture::factory()->create();

        $this->get(route('pictures.show', $picture))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Picture')
                    ->url('/pictures/'.$picture->slug)
                    ->has('picture.title')
                    ->has('picture.description')
                    ->has('picture.url')
                    ->has('picture.year')
                    ->has('picture.people')
                    ->missing('picture.featured')
                    ->missing('picture.private')
                    ->missing('picture.person_ids')
                    ->has('people')
                    ->has('meta.meta')
                    ->has('meta.title')
            );
    }

    public function test_private_picture_is_not_shown_publicly()
    {
        $picture = Picture::factory()->create(['private' => 1]);

        $this->get(route('pictures.show', $picture))->assertRedirect(route('pictures.index'));
    }

    public function test_private_picture_component_is_shown_by_slug_when_authenticated()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $picture = Picture::factory()->create(['private' => 1]);

        $this->get(route('pictures.show', $picture))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Picture')
                    ->url('/pictures/'.$picture->slug)
                    ->has('picture.title')
                    ->has('picture.description')
                    ->has('picture.url')
                    ->has('picture.year')
                    ->has('picture.featured')
                    ->has('picture.people')
                    ->has('picture.person_ids')
                    ->has('people')
                    ->has('meta.meta')
                    ->has('meta.title')
            );
    }

    public function test_new_picture_is_stored_and_uploaded()
    {
        Storage::fake('s3');

        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $personCount = 5;
        $people = Person::factory()->count($personCount)->create();

        $request = [
            'photo' => UploadedFile::fake()->image('photo1.jpg'),
            'title' => $this->faker->words(2, true),
            'description' => $this->faker->sentences(4, true),
            'year' => $this->faker->year(),
            'featured' => $this->faker->numberBetween(0, 1),
            'private' => $this->faker->numberBetween(0, 1),
            'person_ids' => $people->modelKeys(),
        ];

        $this->post(route('pictures.store'), $request)
            ->assertRedirect()
            ->assertSessionHas('flash.banner');

        $filePath = 'pictures/'.$request['photo']->hashName();
        Storage::disk('s3')->assertExists($filePath);

        $picture = Picture::first();
        $this->assertEquals($picture->url, Storage::url($filePath));
        $this->assertEquals(strtolower($picture->title), $request['title']);
        $this->assertEquals($picture->description, $request['description']);
        $this->assertEquals($picture->year, $request['year']);
        $this->assertEquals($picture->featured, $request['featured']);
        $this->assertEquals($picture->private, $request['private']);
        $this->assertCount($personCount, $picture->people->toArray());
    }

    public function test_all_properties_of_picture_can_be_updated()
    {
        Storage::fake('s3');

        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $picture = Picture::factory()->create();
        $personCount = 2;
        $people = Person::factory()->count($personCount)->create();

        $request = [
            'photo' => UploadedFile::fake()->image('photo1.jpg'),
            'title' => $this->faker->words(2, true),
            'description' => $this->faker->sentences(4, true),
            'year' => $this->faker->year(),
            'featured' => $this->faker->numberBetween(0, 1),
            'private' => $this->faker->numberBetween(0, 1),
            'person_ids' => $people->modelKeys(),
        ];

        $response = $this->put(route('pictures.update', $picture), $request);

        $picture = $picture->fresh();
        $response->assertRedirect(route('pictures.show', $picture))
            ->assertSessionHas('flash.banner');

        $filePath = 'pictures/'.$request['photo']->hashName();
        Storage::disk('s3')->assertExists($filePath);

        $this->assertEquals($picture->url, Storage::url($filePath));
        $this->assertEquals(strtolower($picture->title), $request['title']);
        $this->assertEquals($picture->description, $request['description']);
        $this->assertEquals($picture->year, $request['year']);
        $this->assertEquals($picture->featured, $request['featured']);
        $this->assertEquals($picture->private, $request['private']);
        $this->assertCount($personCount, $picture->people->toArray());
    }

    public function test_picture_can_be_destroyed()
    {
        Storage::fake('s3');

        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $obituary = Obituary::factory()->create();

        $photo = UploadedFile::fake()->image('photo1.jpg');
        $picture = Picture::factory()->create([
            'url' => $photo,
        ]);

        $obituary->person->pictures()->attach($picture);

        $response = $this->delete(route('pictures.destroy', $picture));

        $response->assertRedirect(route('pictures.index'))
            ->assertSessionHas('flash.banner');

        $filePath = 'pictures/'.$photo->hashName();
        Storage::disk('s3')->assertMissing($filePath);

        $personHasPicture = $obituary->person->pictures()
            ->where('pictures.id', $picture->id)
            ->exists();
        $this->assertFalse($personHasPicture);

        $this->assertDeleted($picture);
    }
}
