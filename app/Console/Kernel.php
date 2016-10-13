<?php

namespace App\Console;

use App\Console\Commands\DirectoryMonitorCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        DirectoryMonitorCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(DirectoryMonitorCommand::class)
            ->everyMinute()
            ->before(function(){
                Log::info(sprintf("[%s] before id: %s", __CLASS__, getmygid()));
            })->after(function(){
                Log::info(sprintf("[%s] after id: %s", __CLASS__, getmygid()));
            });
    }
}
