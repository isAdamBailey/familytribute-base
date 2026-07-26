<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_endpoint_includes_two_factor_enabled_flag()
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->getJson('/api/user');

        $response->assertOk()->assertJson(['two_factor_enabled' => false]);

        $user->forceFill([
            'two_factor_secret' => encrypt('secret'),
        ])->save();

        $response = $this->getJson('/api/user');

        $response->assertOk()->assertJson(['two_factor_enabled' => true]);
    }

    public function test_sessions_endpoint_requires_authentication()
    {
        $this->getJson(route('api.user.sessions'))->assertUnauthorized();
    }

    public function test_sessions_endpoint_returns_shape()
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->getJson(route('api.user.sessions'));

        $response->assertOk()->assertJsonStructure(['sessions']);
    }

    public function test_other_browser_sessions_can_be_logged_out()
    {
        $this->actingAs(User::factory()->create());

        $response = $this->deleteJson(route('api.user.other-browser-sessions.destroy'), [
            'password' => 'password',
        ]);

        $response->assertOk()->assertJson(['status' => 'ok']);
    }

    public function test_other_browser_sessions_requires_correct_password()
    {
        $this->actingAs(User::factory()->create());

        $response = $this->deleteJson(route('api.user.other-browser-sessions.destroy'), [
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('password');
    }

    public function test_user_account_can_be_deleted()
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->deleteJson(route('api.user.destroy'), [
            'password' => 'password',
        ]);

        $response->assertOk()->assertJson(['status' => 'ok']);
        $this->assertNull($user->fresh());
    }

    public function test_account_deletion_requires_correct_password()
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->deleteJson(route('api.user.destroy'), [
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('password');
        $this->assertNotNull($user->fresh());
    }
}
