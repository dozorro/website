<?php

namespace App\Console\Commands;

use App\Classes\Cron;
use Illuminate\Console\Command;
use DB;

class ProcuringEntities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'procuring:entities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate rating by procuring entities';

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
        $db=DB::table('perevorot_dozorro_json_forms')->select('payload', 'entity_id')->where('schema', '=', 'F101')->get();
        $data=[];

        foreach($db as $one)
        {
            $json=json_decode($one->payload);

            if(!empty($json->userForm->overallScore))
                $data[$one->entity_id][]=(int) $json->userForm->overallScore;
        }

        $out=[];

        foreach($data as $entity_id=>$one)
        {
            $out[]=(object)[
                'entity_id'=>$entity_id,
                'rating'=>(int) round(array_sum($one)/sizeof($one))
            ];
        }

        file_put_contents(storage_path('api').'/procuring-entites.json', json_encode($out));
    }
}
