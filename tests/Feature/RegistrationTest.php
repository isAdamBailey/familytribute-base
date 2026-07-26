<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register()
    {
        $settings = SiteSetting::first();

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'registration_secret' => $settings->registration_secret,
        ]);

        $this->assertAuthenticated();
        $response->assertCreated();
    }

    public function test_new_users_cannot_register_if_registration_is_disabled()
    {
        $settings = SiteSetting::first();
        $settings->update(['registration' => false]);

        $request = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'registration_secret' => $settings->registration_secret,
        ];

        $this->postJson('/api/register', $request)
            ->assertJsonValidationErrors(['registration']);

        $this->assertDatabaseMissing('users', [
            'name' => $request['name'],
            'email' => $request['email'],
        ]);
    }
}
