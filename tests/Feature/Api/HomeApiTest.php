<?php

namespace Tests\Feature\Api;

use App\Models\Picture;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_endpoint_returns_featured_pictures()
    {
        Picture::factory()->count(6)->create(['featured' => 1]);

        $response = $this->getJson(route('api.home'));

        $response->assertOk()
            ->assertJsonCount(5, 'pictures')
            ->assertJsonStructure([
                'pictures' => [
                    ['url', 'title', 'description'],
                ],
            ]);
    }
}
