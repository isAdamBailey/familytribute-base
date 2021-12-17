<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_all_settings_can_be_updated()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        $settings = SiteSetting::first();

        $request = [
            'registration_secret' => $this->faker->word(),
            'registration' => $this->faker->boolean(),
        ];

        $response = $this->put(route('site-settings.update', $settings), $request);

        $response->assertRedirect()
            ->assertSessionHas('flash.banner');

        $settings = $settings->fresh();
        $this->assertEquals($settings->registration_secret, $request['registration_secret']);
        $this->assertEquals($settings->registration, (bool) $request['registration']);
    }
}
