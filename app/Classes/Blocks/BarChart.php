<?php

namespace App\Classes\Blocks;

use App\Helpers;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Log;

class BarChart extends IBlock
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

       // Cache::forget(md5(json_encode($this->block->value)));
       // $results = Cache::remember(md5(json_encode($this->block->value)), 60, function () use($data, $request) {

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
            $data->labels = [];

            foreach($results as $item) {
                foreach($data->datasets as &$dataset) {
                    //$value = is_numeric($item->{$dataset->fieldValue}) ? number_format($item->{$dataset->fieldValue}, 0, '.', ' ') : $item->{$dataset->fieldValue};
                    //$dataset->array[] = $value;
                    $dataset->array[] = $item->{$dataset->fieldValue};

                    if(!in_array($item->{$dataset->fieldLabel}, $data->labels)) {
                        $data->labels[] = $item->{$dataset->fieldLabel};
                    }
                }
            }

            foreach($data->datasets as &$dataset) {
                $dataset->min = min($dataset->array);
                $dataset->max = max($dataset->array);
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
