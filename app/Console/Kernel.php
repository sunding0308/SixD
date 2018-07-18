<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         * push signal to machine
         */
        $schedule->call('\App\Http\Controllers\Api\PushController@pushAlarmsSignal')
                 ->hourly();
        $schedule->call('\App\Http\Controllers\Api\PushController@pushOverageSignal')
                 ->hourly();
        $schedule->call('\App\Http\Controllers\Api\PushController@pushHardwareStatusSignal')
                 ->hourly();
        $schedule->call('\App\Http\Controllers\Api\PushController@pushRecordsSignal')
                 ->daily();
        $schedule->call('\App\Http\Controllers\Api\PushController@pushEnvironmentSignal')
                 ->hourly();
        $schedule->call('\App\Http\Controllers\Api\PushController@pushWaterQualityStatisticsSignal')
                 ->daily();
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
