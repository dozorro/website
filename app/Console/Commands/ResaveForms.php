<?php

namespace App\Console\Commands;

use App\Classes\Cron;
use Illuminate\Console\Command;

class ResaveForms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:resave';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Cron::reSaveForms();
        $this->info('Forms updated.');
    }
}
