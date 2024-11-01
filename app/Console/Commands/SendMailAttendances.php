<?php

namespace App\Console\Commands;

use App\Http\Controllers\JobsController;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class SendMailAttendances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendMailAttendances:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SendMailAttendances';

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
        JobsController::sendMailAttendances();
        return "ok";
    }
    
}
