<?php

namespace App\Console\Commands;

use App\Mail\UpcomingDates;
use App\Models\Obituary;
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
        $upcomingDeathDates = $this->checkBetweenDates('death_date');
        $upcomingBirthDates = $this->checkBetweenDates('birth_date');

        if ($upcomingBirthDates->isNotEmpty() || $upcomingDeathDates->isNotEmpty()) {
            foreach (User::all() as $user) {
                Mail::to($user->email)->send(new UpcomingDates([
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

    private function checkBetweenDates($date): Collection|array
    {
        return Obituary::all()->filter(function ($obit) use ($date) {
            $date = Carbon::parse($obit->$date);

            return $date->setYear(now()->year)->between(now(), now()->addWeek())
                || $date->setYear(now()->addWeek()->year)->between(now(), now()->addWeek());
        });
    }
}
