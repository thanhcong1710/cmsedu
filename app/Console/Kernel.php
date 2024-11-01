<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * ========================================================== *
 * --------------------- Apax ERP System --------------------
 * ========================================================== *
 *
 * @summary This file is section belong to ERP System
 * @package Apax ERP System
 * @author Hieu Trinh (Henry Harion)
 * @email hariondeveloper@gmail.com
 *
 * ========================================================== *
 */

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        '\App\Console\Commands\JobsSendEmail',
        '\App\Console\Commands\JobsSendSms',
        '\App\Console\Commands\CronPushContact',
        '\App\Console\Commands\GetCustomFields',
        '\App\Console\Commands\LogStudentActive',
        '\App\Console\Commands\ProcessData',
        '\App\Console\Commands\SendCheckin',
        '\App\Console\Commands\SendMailCreateContractSalehub',
        '\App\Console\Commands\SendMailCreateContractTransferCheckin',
        '\App\Console\Commands\SendMailRenew',
        '\App\Console\Commands\SendCheckinSaleHub',
        '\App\Console\Commands\SendMailAttendances'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('cronpushcontact:command')->cron('* * * * *');
        $schedule->command('getcustomfields:command')->cron('* * * * *'); 
        $schedule->command('logstudentactive:command')->cron('* * * * *');
        $schedule->command('jobsSendEmail:command')->cron('* * * * *');
        $schedule->command('jobsSendSms:command')->cron('* * * * *');
        $schedule->command('processdata:command')->dailyAt('2:00'); 
        $schedule->command('sendcheckin:command')->dailyAt('14:00'); 
        $schedule->command('sendMailAttendances:command')->dailyAt('10:00'); 
        $schedule->command('sendMailCreateContractSalehub:command')->dailyAt('8:00'); 
        $schedule->command('sendMailCreateContractTransferCheckin:command')->dailyAt('22:00'); 
        $schedule->command('sendMailRenew:command')->cron('0 8 1 * *');
        $schedule->command('sendCheckinSaleHub:command')->cron('0 * * * *');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
