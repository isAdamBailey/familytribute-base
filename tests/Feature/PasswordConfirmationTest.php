<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_confirmed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/user/confirm-password', [
            'password' => 'password',
        ]);

        $response->assertCreated();
    }

    public function test_password_is_not_confirmed_with_invalid_password()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/user/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertJsonValidationErrors('password');
    }
}
