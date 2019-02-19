<?php

namespace App\Console\Commands;

use App\Classes\Cron;
use Illuminate\Console\Command;

class FormsSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:sync';

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
        if(!is_writable(storage_path('app'))) {
            $this->info('Operation failed! Permissions denied: ' . storage_path('app'));
        }
        elseif(!file_exists(storage_path('app/syncForms.lock'))) {
            Cron::syncForms();
            $this->info('Operation success!');
        } else {
            $this->info('Operation failed! Please, remove this file: ' . storage_path('app/syncForms.lock'));
        }
    }
}
