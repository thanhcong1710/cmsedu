<?php

namespace App\Console\Commands;

use App\Http\Controllers\CsController;
use App\Models\StudentTemp;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class GetCustomFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getcustomfields:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'getcustomfields';

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
        $stTemp = new CsController();
        $stTemp->getCustomFields();
        return "ok";
    }
    
}
