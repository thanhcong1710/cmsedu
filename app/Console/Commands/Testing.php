<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Http\Controllers\TestingController as t;

class Testing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ada:test {action} {--params=}';

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
    {   $this->output('OK FINE');
        $action = $this->argument('action');
        $options = $this->options();
        $data = (Object)[];
        $data->action = $action;
        $data->options = $options;        
        if (method_exists($this, $action)) {
            self::$action($options['params']);
        }
    }

    private function note($content) {
        $log = ROOT."tests".DS."testing.log";
        $exc = file_exists($log) ? file_get_contents($log)."\n" : '';
        file_put_contents($log, $exc.$content.' ('.time().')');
        echo(file_get_contents($log));
    }
}
