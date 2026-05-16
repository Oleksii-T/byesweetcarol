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
        // update ip-to-country database
        $schedule->command('location:update')->weekly();

        // update calendar games
        $schedule->command('calendar:games')->days([1, 4]);

        // generate sitemap
        // $schedule->command('sitemap:generate')->everySixHours();

        // clear spatie activity logs
        // $schedule->command('activitylog:clean')->daily();
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
