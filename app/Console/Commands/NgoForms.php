<?php

namespace App\Console\Commands;

use App\Classes\Cron;
use Illuminate\Console\Command;

class NgoForms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reviews:ngo';

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
        $r = Cron::reviewsNgo();
        $this->info('Reviews updated: '.$r);
    }
}
