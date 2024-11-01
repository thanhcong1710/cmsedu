<?php

namespace App\Console\Commands;

use App\Http\Controllers\LMSAPIController;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class UpdateLMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateLMS:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update LMS';

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
        // u::query("DELETE FROM lms_students");
        // for($i=0; $i<40 ; $i++){
        //     $job = new LMSAPIController();
        //     $job->updateListStudent($i);
        //     echo $i."/";
        // }

        // u::query("DELETE FROM lms_classes");
        // for($i=0; $i<40 ; $i++){
        //     $job = new LMSAPIController();
        //     $job->updateListClass($i);
        //     echo $i."/";
        // }

        // u::query("DELETE FROM lms_teachers");
        // for($i=0; $i<40 ; $i++){
        //     $job = new LMSAPIController();
        //     $job->updateListTeacher($i);
        //     echo $i."/";
        // }

        $job = new LMSAPIController();
        // $job->updateInfoStudentLMSNoneCRMId();
        // $job->updateInfoStudentLMSWithDraw();
        // $job->updateInfoStudentLMSClass();
        $job->updateInfoStudentLMSInClass();
        return "ok";
    }
    
}
