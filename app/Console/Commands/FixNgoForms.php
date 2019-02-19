<?php

namespace App\Console\Commands;

use App\JsonForm;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixNgoForms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:ngo_forms';

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
        $keys = [];
        $total = 0;

        foreach(['F203', 'F202'] as $schema) {

            $entities = JsonForm::
                where('schema', $schema)
                ->groupBy('entity_id')
                ->get();

            $this->info('Found ' . $entities->count() . ' entities.');

            foreach($entities as $entity) {

                $forms = JsonForm::
                    where('entity_id', $entity->entity_id)
                    ->where('schema', $schema)
                    ->orderBy('id', 'desc')
                    ->get();

                $this->info('Found ' . $forms->count() . ' forms by '.$entity->entity_id.'.');

                foreach ($forms as $form) {
                    if ($form->JsonParentForm) {

                        $_form = [];

                        if(!empty($form->json->actionCode)) {
                            $_form[] = 'actionCode';
                            $_form[] = $form->json->actionCode;
                        }
                        elseif(!empty($form->json->resultCode)) {
                            $_form[] = 'resultCode';
                            $_form[] = $form->json->resultCode;
                        } else {
                            continue;
                        }

                        $key = $form->tender.'-'.$entity->entity_id.'-'.$form->JsonParentForm.'-'.$form->schema.'-'.implode('-', $_form);

                        if (!in_array($key, $keys)) {

                            $count = JsonForm::
                                where('tender', $form->tender)
                                ->where('entity_id', $entity->entity_id)
                                ->where('schema', $form->schema)
                                ->where('payload', 'like', '%parentForm":"' . $form->JsonParentForm . '%')
                                ->where('payload', 'like', "%\"{$_form[0]}\":\"{$_form[1]}\"%")
                                ->where('id', '<', $form->id)
                                ->count();

                            if($count > 0) {
                                DB::table('perevorot_dozorro_json_forms')
                                    ->where('tender', $form->tender)
                                    ->where('entity_id', $entity->entity_id)
                                    ->where('schema', $form->schema)
                                    ->where('payload', 'like', '%parentForm":"' . $form->JsonParentForm . '%')
                                    ->where('payload', 'like', "%\"{$_form[0]}\":\"{$_form[1]}\"%")
                                    ->where('id', '<', $form->id)
                                    ->update(['is_duplicate' => 1]);

                                $total += $count;

                                echo "Updated " . $count . " forms by $key\n";
                            } else {
                                echo "Nothing to update by $key\n";
                            }

                            $keys[] = $key;
                        }
                    }
                }
            }
        }

        $this->info("Total updated: $total.");
    }
}
