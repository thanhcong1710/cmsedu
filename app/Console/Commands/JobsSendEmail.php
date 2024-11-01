<?php

namespace App\Console\Commands;

use App\Models\Mail;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class JobsSendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobsSendEmail:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'jobsSendEmail';

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
        $mail = new Mail();
        $mail->processMail();
       
        return "ok";
    }
    
}
