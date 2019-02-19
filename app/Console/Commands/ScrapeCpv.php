<?php

namespace App\Console\Commands;

use App\Classes\Cron;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ScrapeCpv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crape:cpv';

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
        $this->info('Scrape cpv...');

        $json = file_get_contents('http://standards.openprocurement.org/classifiers/cpv/custom-2016-04-01/uk.json');
        $json = json_decode($json);

        $this->info('Scrape found '.count((array)$json).' items');

        $values = [];

        foreach($json as $code => $name) {
            $values[] = "('{$code}', '".addslashes($name)."')";
        }

        DB::statement("INSERT INTO dozorro_cpv (code, description) VALUES ".implode(',',$values).";");

        $this->info('Scrape was end.');
    }
}
