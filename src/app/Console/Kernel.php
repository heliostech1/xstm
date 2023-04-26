<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ImportShipment;
use App\Http\Models\AppSetting\AppSetting;
use App\Http\Libraries\Alarm\AlarmSender;
use App\Http\Libraries\DateHelper;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
         Commands\SendAlarm::class,         
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        
        
        $nowSql = DateHelper::nowSql();

        //=======================================================
        // Send Alarm  |  minute hour day(month) month day(week)
        
        $schedule->call(function () use ($nowSql)  {
            AlarmSender::start($nowSql);
        })->cron( "* * * * *" ); 
        
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
