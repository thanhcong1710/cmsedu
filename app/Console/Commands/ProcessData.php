<?php

namespace App\Console\Commands;

use App\Http\Controllers\JobsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\ReportStudentsController;
use Illuminate\Console\Command;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;

class ProcessData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'processdata:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Data';

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
        $report = new ReportsController();
        //update report_full_fee_active
        $report->collectFullFeeActive( $request,'_','_');
        //update report_get_users
        $report->collectReportGetUser( $request,'_');
        $report->collectReportReserve( $request,'_');
        $report->collectReportPending( $request,'_');

        $job = new JobsController();
        //update renew_report
        $job->updateCompletedDate();
        $job->updateAllRenewedDates();
        $job->updateRenewReport();
        $job->updateEnrolmentLastDate();
        $job->updateSourceDetail();

        $tool = new ToolsController();
        $tool->processWithdrawAll(0);

        // $report_student = new ReportStudentsController();
        // $report_student->insertStudentReport();

        u::query("INSERT INTO log_jobs (`action`, created_at) VALUES ('ProcessData','".date('Y-m-d H:i:s')."')");
        return "ok";
    }
    
}
