<?php

namespace App\Console\Commands;

use App\Http\Controllers\JobsController;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class UpdateContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateContract:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Contract';

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
        // $job = new JobsController();
        // $job->updateContractNewOpen();
        $list_district = u::query("SELECT * FROM districts");
        foreach($list_district AS $row){
            if($row->accounting_id) {
                $district_name = str_replace("'", " ", $row->name);
                u::query("UPDATE wards SET district = '$district_name', district_id='$row->id' WHERE accounting_id LIKE '$row->accounting_id.%'");
            }
            echo $row->id."/";
        }
        return "ok";
    }
    
}
