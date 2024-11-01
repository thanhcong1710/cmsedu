<?php

namespace App\Console\Commands;

use App\Http\Controllers\APAXAPIController;
use App\Http\Controllers\JobsController;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class ImportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Data';

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
        $apax = new APAXAPIController();
        //update renew_report
        $apax->getAllData();

        u::query("INSERT INTO log_jobs (`action`, created_at) VALUES ('ImportData','".date('Y-m-d H:i:s')."')");
        return "ok";
    }
    
}
