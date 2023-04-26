<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Libraries\Import\ShipmentImporter;

/* 
 *  สร้าง command  -> php artisan make:command ImportShipment 
 *  เรียกใช้ command -> php artisan import_shipment
 *  
 */
class ImportShipment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import_shipment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Shipment';

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
         ShipmentImporter::start();
    }
}
