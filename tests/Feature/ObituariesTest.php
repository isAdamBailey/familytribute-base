<?php

namespace Tests\Feature;

use App\Models\Obituary;
use App\Models\Picture;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ObituariesTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_new_obituary_is_stored_and_uploaded()
    {
        Storage::fake('s3');

        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $request = [
            'photo' => UploadedFile::fake()->image('photo1.jpg'),
            'background_photo' => UploadedFile::fake()->image('photo2.jpg'),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'content' => $this->faker->sentences(4, true),
            'birth_date' => $this->faker->date(),
            'death_date' => $this->faker->date(),
        ];

        $this->post(route('obituaries.store'), $request)
            ->assertRedirect()
            ->assertSessionHas('flash.banner');

        $mainPhotoFilePath = 'obituaries/'.$request['photo']->hashName();
        Storage::disk('s3')->assertExists($mainPhotoFilePath);

        $backgroundPhotoFilePath = 'obituaries/'.$request['background_photo']->hashName();
        Storage::disk('s3')->assertExists($backgroundPhotoFilePath);

        $obituary = Obituary::first();
        $this->assertEquals($obituary->main_photo_url, Storage::url($mainPhotoFilePath));
        $this->assertEquals($obituary->background_photo_url, Storage::url($backgroundPhotoFilePath));
        $this->assertEquals($obituary->person->first_name, strtolower($request['first_name']));
        $this->assertEquals($obituary->person->last_name, strtolower($request['last_name']));
        $this->assertEquals($obituary->content, $request['content']);
        $this->assertEquals($obituary->birth_date, $request['birth_date']);
        $this->assertEquals($obituary->death_date, $request['death_date']);
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

        $response = $this->put(route('obituaries.update', $obituary), $request);

        $obituary = $obituary->fresh();
        $response->assertRedirect(route('people.show', $obituary->person->slug))
            ->assertSessionHas('flash.banner');

        $mainPhotoFilePath = 'obituaries/'.$request['photo']->hashName();
        Storage::disk('s3')->assertExists($mainPhotoFilePath);

        $backgroundPhotoFilePath = 'obituaries/'.$request['background_photo']->hashName();
        Storage::disk('s3')->assertExists($backgroundPhotoFilePath);

        $this->assertEquals($obituary->main_photo_url, Storage::url($mainPhotoFilePath));
        $this->assertEquals($obituary->background_photo_url, Storage::url($backgroundPhotoFilePath));
        $this->assertEquals($obituary->person->first_name, strtolower($request['first_name']));
        $this->assertEquals($obituary->person->last_name, strtolower($request['last_name']));
        $this->assertEquals($obituary->content, $request['content']);
        $this->assertEquals($obituary->birth_date, $request['birth_date']);
        $this->assertEquals($obituary->death_date, $request['death_date']);
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

        $response = $this->delete(route('obituaries.destroy', $obituary));
        $response->assertRedirect(route('people.index'))
            ->assertSessionHas('flash.banner');

        $personHasStory = $obituary->person->stories()
            ->where('stories.id', $story->id)
            ->exists();
        $this->assertFalse($personHasStory);

        $personHasPicture = $obituary->person->pictures()
            ->where('pictures.id', $picture->id)
            ->exists();
        $this->assertFalse($personHasPicture);

        $this->assertDeleted($obituary->person);
        $this->assertDeleted($obituary);
    }
}
