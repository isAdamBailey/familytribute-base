<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $command = $schedule->command('email:upcoming_dates')->weekly();

        // No admin/role distinction in this app (every verified user has equal
        // access — issue #19 Phase 6 dropped the team/role model), so failure
        // notifications go to a dedicated ops address rather than every family
        // member's inbox. Skipped entirely if that address isn't configured.
        if ($adminEmail = config('mail.admin_email')) {
            $command->emailOutputOnFailure($adminEmail);
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
