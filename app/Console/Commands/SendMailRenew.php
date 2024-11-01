<?php

namespace App\Console\Commands;

use App\Http\Controllers\JobsController;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class SendMailRenew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendMailRenew:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SendMailRenew';

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
        $tmp_job = new JobsController();
        $report_month = date('Y-m');
        $tmp_job->sendMailRenew($report_month);
        $next_month = strtotime ( '+1 month' , strtotime ( date('Y-m-d') ) );
        $next_month = date ( 'Y-m' , $next_month ); 
        $tmp_job->sendMailRenewNextMonth($next_month);
        return "ok";
    }
    
}
