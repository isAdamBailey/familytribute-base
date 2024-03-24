<?php

namespace Tests\Feature;

use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_component_redirects_if_no_team()
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('dashboard'))
            ->assertRedirect(route('teams.create'));
    }

    public function test_dashboard_component()
    {
        $this->actingAs(User::factory()->withPersonalTeam()->create());

        Person::factory()->count(5)->create();

        $this->get(route('dashboard'))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Dashboard/Show')
                    ->url('/dashboard')
                    ->has('people', 5)
                    ->has('settings')
                    ->has('meta.meta')
                    ->has('meta.title')
            );
    }
}
