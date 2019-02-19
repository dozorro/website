<?php

namespace App\Console\Commands;

use App\JsonForm;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckNgoForms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:ngo_forms';

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
        $this->info('Starting...');
        $total = 0;

        foreach(['F203', 'F202'] as $schema) {

            $entities = JsonForm::
                where('schema', $schema)
                ->where('is_duplicate', 0)
                ->get();

            $this->info('Found ' . $entities->count() . ' forms.');

            foreach($entities as $entity) {

                $forms = JsonForm::
                    where('ngo_profile_id', $entity->ngo_profile_id)
                    ->whereIn('schema', ['F201', 'F202'])
                    ->where('object_id', $entity->JsonParentForm)
                    ->where('tender_id', $entity->tender_id)
                    ->get();

                if(!$forms->IsEmpty()) {
                    echo '.';
                    continue;
                }

                $total += 1;
                $this->info('Found wrong parent form for '.$entity->object_id);

                JsonForm::
                    where('id', $entity->id)
                    ->update(['is_duplicate' => 1]);
            }
        }

        $this->info("Total updated: $total.");
    }
}
