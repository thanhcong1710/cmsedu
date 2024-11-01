<?php

namespace App\Console\Commands;

use App\Http\Controllers\JobsController;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class SendCheckinSaleHub extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendCheckinSaleHub:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Checkin SaleHub';

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
    public function handle(Request $request)
    {
        if(date('H')>14){
            $branches = u::query("SELECT * FROM branches WHERE id NOT IN (100,1001)");
            foreach($branches AS $row){
                JobsController::sendMailSalehubOver($row->id);
                echo $row->id."/";
            }
        }
        return "ok";
    }
    
}
