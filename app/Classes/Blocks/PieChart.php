<?php

namespace App\Classes\Blocks;

use App\Helpers;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Log;

class PieChart extends IBlock
{
    public function get()
    {
        $request = app(Request::class);
        $scheme = explode('/', $request->path())[1];
        $id = explode('-', $scheme)[2];

        if(empty($this->block->value->is_enabled)) {
            return null;
        }

        $data = $this->block->value;

        if(empty($data->table_name)) {
            return null;
        }

        try {

            $where = '';
            $limit = '';
            $order = '';

            if (!empty($data->table_sql)) {
                $where = stripos($data->table_sql,
                    'where') !== false ? $data->table_sql : "where {$data->table_sql}";
            }
            if (!empty($data->limit)) {
                $limit = "LIMIT 0, {$data->limit}";
            }
            if (!empty($data->order)) {
                $order = "ORDER BY {$data->order}";
            }

            $where = strtr($where, ['{id-short}' => "'{$id}'", '{id}' => "'{$scheme}'"]);
            $query = "select * from {$data->table_name} {$where} {$order} {$limit}";

            Log::info($query);

            $results = DB::select($query);
            $error = false;
            $i = 0;

            foreach($data->datasets as &$dataset) {

                $item = array_first($results, function($key, $item) use($dataset) {
                    return $item->{$dataset->fieldLabel} == $dataset->fieldValue;
                });

                if(!empty($item)) {
                    $data->values[] = $item->{$data->fieldValue};
                    $data->colors[] = $dataset->color;
                    $data->labels[] = $dataset->label;
                }

                $i++;
            }
            //dd($data);
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $error = $e->getMessage();
            $results = [];
        }

        return [
            'error' => $error,
            'data' => $data,
            'results' => $results,
        ];
    }
}
