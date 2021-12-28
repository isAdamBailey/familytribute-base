<?php

namespace App\Console\Commands;

use App\Mail\UpcomingDates;
use App\Models\Obituary;
use App\Models\SiteSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;

class SendUpcomingDatesEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:upcoming_dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command checks if any birth or death dates are coming up and sends and email if there are.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $upcomingDeathDates = $this->getObituariesInRange('death_date');
        $upcomingBirthDates = $this->getObituariesInRange('birth_date');

        if ($upcomingBirthDates->isNotEmpty() || $upcomingDeathDates->isNotEmpty()) {
            foreach (User::all() as $user) {
                Mail::to($user->email)->send(new UpcomingDates([
                    'siteName' => SiteSetting::first()->title,
                    'deathDates' => $upcomingDeathDates,
                    'birthDates' => $upcomingBirthDates,
                ]));
            }

            $this->info('The upcoming dates emails were sent!');

            return 0;
        }

        $this->info('There were no upcoming dates to email!');

        return 0;
    }

    private function getObituariesInRange($date): Collection|array
    {
        return Obituary::all()->load('person')->filter(function ($obit) use ($date) {
            $date = Carbon::parse($obit->$date);

            return $date->setYear(now()->year)->between(now(), now()->addWeek())
                || $date->setYear(now()->addWeek()->year)->between(now(), now()->addWeek());
        });
    }
}
