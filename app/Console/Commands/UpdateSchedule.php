<?php

namespace App\Console\Commands;

use App\Http\Controllers\SessionsController;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class UpdateSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateSchedule:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'UpdateSchedule';

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
        $list_class = "
3667,
3678,
3679,
3684";
        $job = new SessionsController();
        $job->addScheduleTool($list_class);
        return "ok";
    }
    
}
