<?php

namespace Tests\Feature;

use App\Mail\UpcomingDates;
use App\Models\Obituary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendUpcomingDatesEmailCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-07-15 12:00:00', 'UTC'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_send_upcoming_dates_if_no_dates_exist()
    {
        Mail::fake();

        User::factory()->count(5)->create();

        $this->artisan('email:upcoming_dates')
            ->assertExitCode(0)
            ->expectsOutput('There were no upcoming dates to email!');

        Mail::assertNotSent(UpcomingDates::class);
    }

    public function test_send_upcoming_dates_if_birth_dates_exist()
    {
        Mail::fake();

        User::factory()->count(5)->create();

        Obituary::factory()->create([
            'birth_date' => '2026-07-18',
            'death_date' => '1990-01-01',
        ]);

        $this->artisan('email:upcoming_dates')
            ->assertExitCode(0)
            ->expectsOutput('The upcoming dates emails were sent!');

        Mail::assertSent(UpcomingDates::class, 5);
    }

    public function test_send_upcoming_dates_if_death_dates_exist()
    {
        Mail::fake();

        User::factory()->count(5)->create();

        Obituary::factory()->create([
            'birth_date' => '1990-01-01',
            'death_date' => '2026-07-20',
        ]);

        $this->artisan('email:upcoming_dates')
            ->assertExitCode(0)
            ->expectsOutput('The upcoming dates emails were sent!');

        Mail::assertSent(UpcomingDates::class, 5);
    }

    public function test_send_upcoming_dates_if_date_out_of_range()
    {
        Mail::fake();

        User::factory()->count(5)->create();

        Obituary::factory()->create([
            'birth_date' => '1990-01-01',
            'death_date' => '2026-07-13',
        ]);

        Obituary::factory()->create([
            'birth_date' => '2026-07-23',
            'death_date' => '1990-01-01',
        ]);

        $this->artisan('email:upcoming_dates')
            ->assertExitCode(0)
            ->expectsOutput('There were no upcoming dates to email!');

        Mail::assertNotSent(UpcomingDates::class);
    }
}
