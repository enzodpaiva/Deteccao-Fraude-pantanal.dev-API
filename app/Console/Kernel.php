<?php

namespace App\Console;

use App\Console\Commands\PartialOpportunity;
use App\Console\Commands\VerifyDeviceInsuranceActive;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        PartialOpportunity::class,
        VerifyDeviceInsuranceActive::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('partial')->everyFiveMinutes()->withoutOverlapping()->environments(['production']);
        $schedule->command('VerifyDeviceInsuranceActive')->everyThirtyMinutes()->withoutOverlapping()->environments(['production']);

    }
}
