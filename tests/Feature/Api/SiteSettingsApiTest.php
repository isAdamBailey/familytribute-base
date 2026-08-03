<?php

namespace Tests\Feature\Api;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SiteSettingsApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_site_settings_endpoint_returns_shape()
    {
        $response = $this->getJson(route('api.site-settings.show'));

        $response->assertOk()
            ->assertJsonStructure([
                'settings' => ['id', 'title', 'description', 'registration'],
                'google_site_tag',
            ]);
    }

    public function test_google_site_tag_is_served_in_production()
    {
        $this->app['env'] = 'production';
        config(['services.google.site_tag' => 'G-ABC1234567']);

        $this->getJson(route('api.site-settings.show'))
            ->assertOk()
            ->assertJsonPath('google_site_tag', 'G-ABC1234567');
    }

    public function test_google_site_tag_is_withheld_outside_production()
    {
        config(['services.google.site_tag' => 'G-ABC1234567']);

        $this->getJson(route('api.site-settings.show'))
            ->assertOk()
            ->assertJsonPath('google_site_tag', null);
    }

    public function test_google_site_tag_is_null_when_unconfigured()
    {
        $this->app['env'] = 'production';
        config(['services.google.site_tag' => '']);

        $this->getJson(route('api.site-settings.show'))
            ->assertOk()
            ->assertJsonPath('google_site_tag', null);
    }

    public function test_all_settings_can_be_updated()
    {
        $this->actingAs(User::factory()->create());

        $settings = SiteSetting::first();

        $request = [
            'title' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'registration_secret' => $this->faker->word(),
            'registration' => $this->faker->boolean(),
        ];

        $response = $this->putJson(route('api.site-settings.update', $settings), $request);

        $response->assertOk()
            ->assertJson([
                'settings' => [
                    'title' => $request['title'],
                    'description' => $request['description'],
                    'registration' => $request['registration'],
                ],
            ]);

        $settings = $settings->fresh();
        $this->assertEquals($settings->title, $request['title']);
        $this->assertEquals($settings->description, $request['description']);
        $this->assertEquals($settings->registration_secret, $request['registration_secret']);
        $this->assertEquals($settings->registration, (bool) $request['registration']);
    }

    public function test_update_requires_authentication()
    {
        $settings = SiteSetting::first();

        $this->putJson(route('api.site-settings.update', $settings), ['title' => 'New Title'])
            ->assertUnauthorized();
    }
}
