<?php

namespace Tests\Feature;

use App\Models\Picture;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\Assert;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_has_pictures()
    {
        Picture::factory()->count(6)->create([
            'featured' => 1,
        ]);

        $this->get(route('home'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Home')
                    ->url('/')
                    ->has('pictures', 5)
                    ->has('pictures.0.description')
                    ->has('pictures.0.url')
                    ->has('pictures.0.title')
                    ->has('meta.meta')
                    ->has('meta.title')
            );
    }
}
