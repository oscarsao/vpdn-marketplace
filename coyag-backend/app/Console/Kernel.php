<?php

namespace App\Console;
use App\Jobs\RunToolSubcriptions;
use App\Jobs\NoActivityImmigration;
use App\Jobs\BusinessCheckerCaller;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void {
        $schedule->command('business:order')->everySixHours();

        $schedule->command('notification:plan-expiration')->dailyAt('15:00');

        $schedule->command('notification:new-business-video')->dailyAt('16:00');

        $schedule->command('maintenance:delete-old-business-images')->monthlyOn(1, '2:00');

        $schedule->job(new NoActivityImmigration)->weekly()->thursdays()->at('14:00');

        if (app()->environment('production')) {
            $schedule->job(new RunToolSubcriptions  )->weekly()->sundays()->at('1:00');
            $schedule->job(new BusinessCheckerCaller)->weekly()->mondays()->at('1:00');
            $schedule->job(new BusinessCheckerCaller)->weekly()->fridays()->at('1:00');
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

