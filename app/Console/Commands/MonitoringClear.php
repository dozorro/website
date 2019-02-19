<?php

namespace App\Console\Commands;

use App\Classes\Cron;
use App\Models\Monitoring\Item;
use App\Models\Monitoring\Tender;
use Illuminate\Console\Command;

class MonitoringClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitoring:clear';

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
        Tender::truncate();
        Item::truncate();
        $this->info('Monitoring successfully cleared!');
    }
}
