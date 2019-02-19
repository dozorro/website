<?php

namespace App\Classes\Blocks;

use App\Helpers;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Log;

class Table extends IBlock
{
    public function get()
    {
        $request = app(Request::class);
        $scheme = explode('/', $request->path());

        if(count($scheme) > 1) {
            $scheme = $scheme[1];
            $id = explode('-', $scheme)[2];
        } else {
            $scheme = null;
            $id = null;
        }

        $cutStr = 75;

        if(!$this->block->value->is_enabled) {
            return null;
        }

        if(strpos($request->path(), 'table')) {
            $code = explode('table/', $request->path())[1];
            $code = explode('/', $code)[0];
            $cutStr = 95;

            if($code != $this->block->value->code) {
                return null;
            }
        }

        if($this->block->value->position == 'full') {
            $cutStr = 95;
        }

        $data = $this->block->value;
        $data->table_fields = (array)$data->table_fields;
        $data->table_fields = array_filter($data->table_fields, function ($v) {
            return !empty($v);
        });

        if(!empty($data->table_fields)) {
            $data->table_fields = array_map(function ($v) use($id, $scheme) {

                if(empty($v->link)) {
                    return $v;
                }

                //$v->link = stripos($v->link, 'profile/UA-EDR-') !== false ? ($v->link) : $v->link;
                $v->link_field = Helpers::cut_str($v->link, '{', '}');

                if(stripos($v->link, 'table') !== false && $scheme) {
                    $v->link = '/profile/' . $scheme . $v->link;
                }
                elseif(stripos($v->link, '{') !== false) {
                    $v->link = '/' . $v->link;
                }

                return $v;
            }, $data->table_fields);
        }

        if(!empty($data->table_subtitles)) {
            $data->table_subtitles = (array)$data->table_subtitles;
            $data->table_subtitles = array_filter($data->table_subtitles, function ($v) {
                return !empty($v->table_subtitle) && !empty($v->table_sql2);
            });
        }

        if(!isset($data->table_name) && !isset($data->table_fields)) {
            return null;
        }

       // Cache::forget(md5(json_encode($this->block->value)));
       // $results = Cache::remember(md5(json_encode($this->block->value)), 60, function () use($data, $request) {

        try {
            if (!empty($data->table_name)) {

                $where = '';

                if (!empty($data->table_sql)) {
                    $where = stripos($data->table_sql,
                        'where') !== false ? $data->table_sql : "where {$data->table_sql}";
                }

                if (!empty($data->table_subtitles) && !empty($request->all()) && $data->code == $request->get('table')) {

                    $subquery = array_first($data->table_subtitles, function ($k, $item) use ($request) {
                        return $k == $request->get('q');
                    });

                    if (!empty($subquery->table_sql2)) {
                        $where = !empty($where) ? "$where and {$subquery->table_sql2}" : (stripos($subquery->table_sql2,
                            'where') !== false ? $subquery->table_sql2 : "where {$subquery->table_sql2}");
                    }
                }

                $limit = '';

                if (!empty($data->limit)) {
                    $limit = "LIMIT 0, {$data->limit}";
                }

                if($id) {
                    $where = strtr($where, ['{id-short}' => "'{$id}'", '{id}' => "'{$scheme}'"]);
                }

                if (strpos($request->path(), 'table')) {

                    $query = "select * from {$data->table_name} {$where}";

                    Log::info($query);

                    $results = DB::select($query);
                    $total = 0;
                } else {

                    $query = "select * from {$data->table_name} {$where} {$limit}";

                    Log::info($query);

                    $results = DB::select($query);
                    $total = DB::select("select count(*) as total from {$data->table_name} {$where}")[0]->total;
                }
            } else {
                $results = [];
                $total = 0;
            }

            $error = false;

        } catch(\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $error = $e->getMessage();
            $results = [];
            $total = 0;
        }

        if(!empty($results)) {

            foreach ($results as $fields) {
                foreach ($fields as $field => $value) {
                    if (mb_strlen($value) > $cutStr) {
                        $fields->$field = mb_substr($value, 0, $cutStr) . '...';
                    }
                }
            }

            $fields = DB::select("SHOW FIELDS FROM {$data->table_name}");

            foreach($data->table_fields as &$field) {
                $_field = array_first($fields, function($key, $item) use($field) {
                    return $item->Field == $field->field_name;
                });

                if($_field) {
                    $formatted = stripos($_field->Type, 'int') !== false || stripos($_field->Type, 'decimal') !== false ||
                                stripos($_field->Type, 'float') !== false || stripos($_field->Type, 'double') !== false;
                    $field->is_formatted = $formatted;
                    $tmp = explode(',',$_field->Type);
                    $field->decimal = intval(end($tmp));
                } else {
                    $field->is_formatted = false;
                    $field->decimal = 0;
                }
            }

            foreach ($results AS $k => $item) {
                $results[$k] = (array)$item;
            }
        }

        if(!$id) {
            $data->single = true;
            $data->results = $results;
            $data->total = $total;
        }

        return [
            'error' => $error,
            'total' => $total,
            'data' => $data,
            'results' => $results,
        ];
    }
}
