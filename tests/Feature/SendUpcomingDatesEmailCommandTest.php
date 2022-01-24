<?php

namespace Tests\Feature;

use App\Mail\UpcomingDates;
use App\Models\Obituary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendUpcomingDatesEmailCommandTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_send_upcoming_dates_if_no_dates_exist()
    {
        Mail::fake();

        User::factory()->count(5)->create();

        $this->artisan('email:upcoming_dates')
            ->expectsOutput('There were no upcoming dates to email!')
            ->assertExitCode(0);

        Mail::assertNotSent(UpcomingDates::class);
    }

    public function test_send_upcoming_dates_if_birth_dates_exist()
    {
        Mail::fake();

        User::factory()->count(5)->create();

        Obituary::factory()->create([
            'birth_date' => $this->faker->dateTimeBetween('now', '+1 week'),
        ]);

        $this->artisan('email:upcoming_dates')
            ->expectsOutput('The upcoming dates emails were sent!')
            ->assertExitCode(0);

        Mail::assertSent(UpcomingDates::class, 5);
    }

    public function test_send_upcoming_dates_if_death_dates_exist()
    {
        Mail::fake();

        User::factory()->count(5)->create();

        Obituary::factory()->create([
            'death_date' => $this->faker->dateTimeBetween('now', '+1 week'),
        ]);

        $this->artisan('email:upcoming_dates')
            ->expectsOutput('The upcoming dates emails were sent!')
            ->assertExitCode(0);

        Mail::assertSent(UpcomingDates::class, 5);
    }

    public function test_send_upcoming_dates_if_date_out_of_range()
    {
        Mail::fake();

        User::factory()->count(5)->create();

        Obituary::factory()->create([
            'death_date' => $this->faker->dateTimeBetween('-8 days', '-1 day'),
        ]);

        Obituary::factory()->create([
            'birth_date' => $this->faker->dateTimeBetween('+8 days', '+3 weeks'),
        ]);

        $this->artisan('email:upcoming_dates')
            ->expectsOutput('There were no upcoming dates to email!')
            ->assertExitCode(0);

        Mail::assertNotSent(UpcomingDates::class);
    }
}
