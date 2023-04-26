<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Libraries\Alarm\AlarmSender;

/* 
 *  เรียกใช้ command -> php artisan send_alarm
 */

class SendAlarm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "send_alarm {option=no}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Alarm';

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
     * @return mixed
     */
    public function handle()
    {
       // $date = $this->argument('date');
        $option = $this->argument('option');
        
        //echo "OPTION: $option";
        
        if ($option == "test") {
            AlarmSender::startTest();
        }
        else {
            AlarmSender::start();
        }
        
        
    }
}
