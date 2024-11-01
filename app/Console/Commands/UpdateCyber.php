<?php

namespace App\Console\Commands;

use App\Http\Controllers\JobsController;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class UpdateCyber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateCyber:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Cyber';

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
        $job = new JobsController();
        // $job->updateCyberBranch();
        // $job->updateCyberSale();
        // $job->updateCyberStudent();
        // $job->updateCyberContract();
        $job->retryCallCyber();
        return "ok";
    }
    
}
