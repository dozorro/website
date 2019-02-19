<?php

namespace App\Classes\Blocks;

use App\Helpers;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Log;

class ProfileRating extends IBlock
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
        $data->table_fields = (array)$data->table_fields;
        $data->table_fields = array_filter($data->table_fields, function ($v) {
            return !empty($v);
        });

        if(empty($data->table_name) && empty($data->table_fields)) {
            return null;
        }

        try {
            if (!empty($data->table_name)) {

                $where = '';

                if (!empty($data->table_sql)) {
                    $where = stripos($data->table_sql,
                        'where') !== false ? $data->table_sql : "where {$data->table_sql}";
                }

                $limit = '';

                if (!empty($data->limit)) {
                    $limit = "LIMIT 0, {$data->limit}";
                }

                $where = strtr($where, ['{id-short}' => "'{$id}'", '{id}' => "'{$scheme}'"]);

                    $query = "select * from {$data->table_name} {$where}";

                    Log::info($query);

                    $results = DB::select("$query {$limit}");
                    //$total = DB::select("select count(*) as total from {$data->table_name} {$where}")[0]->total;

                if(sizeof($results)) {
                    $fields = array_pluck($data->table_fields, 'field_name');
                    $data->value = new \stdClass();
                    $data->value->ratings = $this->array_column_multi($results, $fields);
                    $data->value->ratings_total = array_sum($data->value->ratings);

                    $total = 0;

                    foreach($data->value->ratings as $field => $rating) {
                        $f = explode('_', $field)[1];
                        $total += ($f * $rating);
                    }

                    $data->value->ratings_avg = round($total / $data->value->ratings_total, 1);
                    $data->value->__ratings = $data->value->ratings;
                    arsort($data->value->__ratings);

                    $i = 100;
                    $j = 0;

                    foreach($data->value->__ratings as &$rating) {
                        if($j == 0) {
                            $i /= $rating;
                            $rating = 100;
                        } else {
                            $rating *= $i;
                        }
                        
                        $j++;
                    }
                }
            } else {
                $results = [];
            }

            $error = false;

        } catch(\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $error = $e->getMessage();
            $results = [];
            $total = 0;
        }

        return [
            'error' => $error,
            //'total' => $total,
            'data' => $data,
            'results' => $results,
        ];
    }

    function array_column_multi(array $input, array $column_keys) {
        $result = array();
        $column_keys = array_flip($column_keys);
        foreach($input as $key => $el) {
            return array_intersect_key((array)$el, $column_keys);
        }
    }
}
