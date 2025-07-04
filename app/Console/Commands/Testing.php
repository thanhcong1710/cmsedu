<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Http\Controllers\TestingController as t;
use App\Providers\UtilityServiceProvider as u;

class Testing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ada:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is command testing';

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
        $data = u::query("SELECT * FROM  students WHERe crm_id LIKE 'LGL%' AND id >103840");
        foreach ($data AS $row){
            $lastInsertedId = $row->id;
            $lastCode = u::first("SELECT cms_id FROM students WHERE id < $lastInsertedId ORDER BY id DESC LIMIT 1");
            $cms_id = (int)data_get($lastCode, 'cms_id')+1;
            $crm_id = "LGL".str_pad($cms_id, 8, '0', STR_PAD_LEFT);
            u::query("UPDATE students SET cms_id = '$cms_id', crm_id = '$crm_id' WHERE id = $lastInsertedId");
        }
    }

    private function note($content) {
        $log = ROOT."tests".DS."testing.log";
        $exc = file_exists($log) ? file_get_contents($log)."\n" : '';
        file_put_contents($log, $exc.$content.' ('.time().')');
        echo(file_get_contents($log));
    }
}
