<?php

namespace App\Console\Commands;

use App\Models\Sms;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class JobsSendSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobsSendSms:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'jobsSendSms';

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
        $sms =new Sms();
        $sms->processSms();
        return "ok";
    }
    
}
