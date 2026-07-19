<?php

namespace Tests\Feature\Api;

use App\Models\Obituary;
use App\Models\Person;
use App\Models\Picture;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ObituariesApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_new_obituary_is_stored_and_uploaded()
    {
        Storage::fake('s3');
        $this->actingAs(User::factory()->withPersonalTeam()->create());
        $parents = Person::factory()->count(2)->create();

        $request = [
            'photo' => UploadedFile::fake()->image('photo1.jpg'),
            'background_photo' => UploadedFile::fake()->image('photo2.jpg'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'content' => $this->faker->sentences(4, true),
            'birth_date' => $this->faker->date(),
            'death_date' => $this->faker->date(),
            'parent_ids' => $parents->pluck('id')->toArray(),
        ];

        $response = $this->postJson(route('api.obituaries.store'), $request);

        $response->assertCreated()
            ->assertJsonStructure(['person' => ['slug', 'full_name', 'obituary']]);

        $obituary = Obituary::first();
        $this->assertEquals($obituary->content, $request['content']);

        $person = $obituary->person;
        $this->assertEquals($person->first_name, $request['first_name']);
        $this->assertEquals($person->last_name, $request['last_name']);
        $this->assertSame($request['parent_ids'], $person->parents->pluck('id')->toArray());
    }

    public function test_new_obituary_store_requires_authentication()
    {
        $this->postJson(route('api.obituaries.store'), [])->assertUnauthorized();
    }

    public function test_all_properties_of_obituary_can_be_updated()
    {
        Storage::fake('s3');
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $obituary = Obituary::factory()->create();

        $request = [
            'photo' => UploadedFile::fake()->image('photo1.jpg'),
            'background_photo' => UploadedFile::fake()->image('photo2.jpg'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'content' => $this->faker->sentences(4, true),
            'birth_date' => $this->faker->date(),
            'death_date' => $this->faker->date(),
        ];

        $response = $this->putJson(route('api.obituaries.update', $obituary), $request);

        $response->assertOk()
            ->assertJsonStructure(['person' => ['slug', 'full_name', 'obituary']]);

        $obituary = $obituary->fresh();
        $this->assertEquals($obituary->content, $request['content']);

        $person = $obituary->person;
        $this->assertEquals($person->first_name, $request['first_name']);
        $this->assertEquals($person->last_name, $request['last_name']);
    }

    public function test_obituary_can_be_destroyed()
    {
        Storage::fake('s3');
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $obituary = Obituary::factory()->create();
        $story = Story::factory()->create();
        $picture = Picture::factory()->create();

        $obituary->person->stories()->attach($story);
        $obituary->person->pictures()->attach($picture);

        $response = $this->deleteJson(route('api.obituaries.destroy', $obituary));

        $response->assertNoContent();
        $this->assertModelMissing($obituary->person);
        $this->assertModelMissing($obituary);
    }
}
