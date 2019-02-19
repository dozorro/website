<?php

namespace App\Classes\Blocks;

use App\Helpers;
use App\JsonForm;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Log;

class DynamicText extends IBlock
{
    public function get()
    {
        $request = app(Request::class);
        $scheme = explode('/', $request->path());

        if(count($scheme) > 1) {
            $scheme = $scheme[1];
            $id = explode('-', $scheme)[2];
        } else {
            $scheme = '';
            $id = '';
        }

        $data = $this->block->value;

        if(empty($data->table_name)) {
            return null;
        }

        try {
            $where = '';

            if (!empty($data->table_sql)) {
                $where = stripos($data->table_sql,
                    'where') !== false ? $data->table_sql : "where {$data->table_sql}";
                $where = strtr($where, ['{id-short}' => "'{$id}'", '{id}' => "'{$scheme}'"]);
            }

            $fields = [];

            if(!empty($data->field_title)) {
                $fields[] = "`{$data->field_title}`";
            }
            if(!empty($data->field_subtitle)) {
                $fields[] = "`{$data->field_subtitle}`";
            }
            if(!empty($data->field_text)) {
                $fields[] = "`{$data->field_text}`";
            }

            if(empty($fields)) {
                return null;
            }

            if($data->table_name == 'dozorro_profile_ratings_detailed') {
                $fields[] = "`object_id`";
            }

            $fields = implode(', ', $fields);
            $query = "select $fields from {$data->table_name} {$where}";

            Log::info($query);

            $results = DB::select("$query");

            $error = false;

        } catch(\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $error = $e->getMessage();
            $results = [];
        }

        $tenders = [];

        if(!empty($results)) {
            foreach($results as &$fields) {
                foreach($fields as $f => $field) {
                   $field = trim(strip_tags($field));

                    if($f == 'object_id') {
                        $tenders[] = $field;
                    }

                   if(strlen($field) == 22 && strpos($field, 'UA-') === 0) {
                       $fields->$f = "tender/$field";
                       $fields->tender = strtr($field, ['tender/'=>'']);
                   }
                }
            }
        }

        if(!empty($tenders)) {
            $forms = JsonForm::
                select(
                    '*',
                    DB::raw("(Select COUNT(forms.id) from perevorot_dozorro_json_forms as forms where forms.tender = perevorot_dozorro_json_forms.tender and forms.schema not in ('".implode("','", JsonForm::$ngoForms)."')) as reviews"),
                    DB::raw('(Select COUNT(forms.id) from perevorot_dozorro_json_forms as forms where forms.tender = perevorot_dozorro_json_forms.tender and forms.schema = "F201") as f201_count')
                )
                ->selectRaw('(tender in
                    (SELECT forms.tender from perevorot_dozorro_json_forms as forms where forms.is_customer = 1)) as reaction')
                ->whereIn('object_id', $tenders)
                ->get();

            foreach($results as &$fields) {
                if(!empty($fields->tender)) {
                    foreach($forms as $tender) {
                        if($tender->tender_id == $fields->tender) {
                            $fields->tender = $tender;
                        }
                    }
                }
            }
        }

        return [
            'error' => $error,
            'data' => $data,
            'results' => $results,
        ];
    }
}
