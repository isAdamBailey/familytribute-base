<?php

namespace Tests\Feature;

use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_component()
    {
        $this->actingAs(User::factory()->create());

        Person::factory()->count(5)->create();

        $this->get(route('dashboard'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Dashboard/Show')
                    ->url('/dashboard')
                    ->has('people', 5)
            );
    }
}
