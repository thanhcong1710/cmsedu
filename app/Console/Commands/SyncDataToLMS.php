<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\LMSAPIController;
use App\Providers\UtilityServiceProvider as u;

class SyncDataToLMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lms:sync {--type=all : The type of data to sync (branch, teacher, class, student, all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data from CRM to LMS manually';

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
    public function handle()
    {
        $type = $this->option('type');
        $lmsApi = new LMSAPIController();

        if ($type === 'all' || $type === 'branch') {
            $this->info('Syncing branches...');
            $branches = u::query("SELECT id FROM branches WHERE id IN (1,2,4,5,6,7,9,14,19)");
            foreach ($branches as $branch) {
                try {
                    $lmsApi->updateBranchLMS($branch->id);
                    $this->line("Branch ID {$branch->id} synced.");
                } catch (\Exception $e) {
                    $this->error("Failed to sync Branch ID {$branch->id}: " . $e->getMessage());
                }
            }
        }

        if ($type === 'all' || $type === 'teacher') {
            $this->info('Syncing teachers...');
            $teachers = u::query("SELECT t.id,t.user_id FROM teachers As t WHERE t.user_id IN (
                SELECT DISTINCT
                    teacher_id 
                FROM
                    classes 
                WHERE
                    product_id IN (1, 2, 3, 100) 
                    AND branch_id IN (1, 2, 4, 5, 6, 7, 9, 14, 19) 
                    AND cls_iscancelled = 'no' 
                    AND cls_enddate >= CURRENT_DATE )");
            foreach ($teachers as $teacher) {
                try {
                    $lmsApi->updateTeacherLMS($teacher->id);
                    $this->line("Teacher ID {$teacher->id} synced.");
                } catch (\Exception $e) {
                    $this->error("Failed to sync Teacher ID {$teacher->id}: " . $e->getMessage());
                }
            }
        }

        if ($type === 'all' || $type === 'class') {
            $this->info('Syncing classes...');
            $classes = u::query("SELECT id, id_lms 
                FROM
                    classes 
                WHERE
                    product_id IN (1, 2, 3, 100) 
                    AND branch_id IN (1, 2, 4, 5, 6, 7, 9, 14, 19) 
                    AND cls_iscancelled = 'no' 
                    AND cls_enddate >= CURRENT_DATE");
            foreach ($classes as $class) {
                try {
                    if ($class->id_lms) {
                        $lmsApi->updateClassLMS($class->id);
                    } else {
                        $lmsApi->createClassLMS($class->id);
                    }
                    $this->line("Class ID {$class->id} synced.");
                } catch (\Exception $e) {
                    $this->error("Failed to sync Class ID {$class->id}: " . $e->getMessage());
                }
            }
        }

        if ($type === 'all' || $type === 'student') {
            $this->info('Syncing students...');
            $students = u::query("SELECT DISTINCT student_id AS id FROM contracts 
                WHERE product_id IN (1,2,3,100) AND class_id IS NOT NULL 
                AND branch_id IN (1, 2, 4, 5, 6, 7, 9, 14, 19) AND status=6");
            foreach ($students as $student) {
                try {
                    $lmsApi->updateStudentLMS($student->id);
                    $this->line("Student ID {$student->id} synced.");
                } catch (\Exception $e) {
                    $this->error("Failed to sync Student ID {$student->id}: " . $e->getMessage());
                }
            }
        }

        $this->info('Sync complete!');
    }
}
