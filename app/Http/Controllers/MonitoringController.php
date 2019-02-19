<?php

namespace App\Http\Controllers;

use App\Models\Monitoring\Item;
use App\Models\Monitoring\Monitoring;
use App\Models\Monitoring\MonitoringViolation;
use App\Models\Monitoring\Tender;
use App\Models\Product;
use App\Settings;
use App\Traits\SearchTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Input;
use Request;

class MonitoringController extends BaseController
{
    use SearchTrait;

    public function medical(\Illuminate\Http\Request $request)
    {
        list($query_array, $preselected_values) = $this->parse_search_query(true);
        //dd($preselected_values);

        return $this->render('pages/medical_contracts', [
            'preselected_values' => !empty($preselected_values) ? json_encode($preselected_values, JSON_UNESCAPED_UNICODE) : null,
            'highlight' => json_encode($this->getSearchResultsHightlightArray(trim(Request::server('QUERY_STRING'), '&')), JSON_UNESCAPED_UNICODE),
            'default' => null,
        ]);
    }

    public function search(\Illuminate\Http\Request $request)
    {
        $params = [];

        foreach($request->get('query') as $param) {
            $t = explode("=", $param);

            if(starts_with($t[0], 'product_name')) {
                $params['products'][] = $t[1];
            }
            elseif($t[0] == 'region') {
                $params['regions'][] = $t[1];
            }
            elseif($t[0] == 'edrpou') {
                $params['edrpou'][] = $t[1];
            }
            elseif($t[0] == 'tid') {
                $params['tid'][] = $t[1];
            }
            elseif(starts_with($t[0], 'date')) {
                $params['date'] = explode('—', $t[1]);
            }
            elseif($t[0] == 'measure') {
                $params['measures'][] = $t[1];
            }
        }

        if(!empty($params['products'])) {
            $params['products'] = Product::whereIn('id', $params['products'])->get();
        } else {
            $params['products'] = new Collection([]);
        }

        $params['paginate'] = true;
        $tenders = Item::getData($params);

        if (!$tenders->isEmpty()) {

            $views = '';

            foreach ($tenders AS $item) {
                $views .= view('partials/_medical_tender', [
                    'item' => $item,
                ])->render();
            }
        } else {
            $views = '';
        }

        return response()->json(
            ['html' => $views], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function saveViolation(\Illuminate\Http\Request $request)
    {
        if(empty($request->all()) || !$this->user || !$this->user->monitoring || !$this->user->access_full || !$this->user->monitoring->is_enabled) {
            return redirect()->back();
        }

        $mviolation = new MonitoringViolation();
        $mviolation->violation_id = $request->get('violation');
        $mviolation->monitoring_id = $request->get('monitor');
        $mviolation->comment = $request->has('comment') ? $request->get('comment') : null;
        $mviolation->tid = $request->get('tid');
        $mviolation->save();

        return redirect()->back();
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchClassifier(\Illuminate\Http\Request $request, $type)
    {
        $query = trim($request->get('query'));
        $md5 = md5("search_{$type}_{$query}");

        if(in_array(env('APP_ENV'), ['local','dev','develop'])) {
            Cache::forget($md5);
        }

        $out = Cache::remember($md5, 60*24*365, function() use ($type, $query) {

            if(!$query) {
                return [];
            }

            if($data = @Settings::instance('perevorot.dozorro.classifier', 'Perevorot\Dozorro\Models\Classifier')->{$type}) {
                $data = trim($data)."\n";
                $data = explode("\n", $data);
            } else {
                return [];
            }

            $out = [];

            if(!empty($data)){
                foreach($data as $k => $one){
                    $one = trim($one);

                    if(strpos($one, '=')) {
                        $one = explode('=', $one);
                        $one = $one[1];
                    }

                    if(mb_stripos($one, $query) !== FALSE) {
                        array_push($out, [
                            'key' => $k,
                            'value' => $one
                        ]);
                    }
                }
            }

            return $out;
        });

        return new JsonResponse($out);
    }

    public function searchProducts(\Illuminate\Http\Request $request, $type)
    {
        $query = trim($request->get('query'));
        $name = $request->has('name') ? trim($request->get('name')) : '';
        $_query = $request->has('_query') ? trim($request->get('_query')) : '';
        $md5 = md5("search_{$type}_{$query}_{$name}");

        if($type == 'forms' && !$name) {
            return new JsonResponse([]);
        }

        if(in_array(env('APP_ENV'), ['local','dev','develop'])) {
            Cache::forget($md5);
        }

        $out = Cache::remember($md5, 60*24*365, function() use ($type, $query, $name, $_query) {

            if(!$query) {
                return [];
            }

            if ($type == 'other_names') {
                $field = 'description';

                if(!$_query) {
                    $data = Product::where($field, 'like', "%$query%")->get();
                } else {
                    $data = Product::where($field, 'like', "%$query%")->where('name', $_query)->get();
                }
            }
            elseif ($type == 'names') {
                $field = 'name';
                $data = Product::where($field, 'like', "%$query%")->get();
            } elseif ($type == 'forms') {
                $field = 'description';
                $data = Product::where($field, 'like', "%$query%")->where('name', $name)->get();
            } else {
                return [];
            }

            $out = [];

            if (!$data->isEmpty()) {
                foreach ($data as $k => $v) {

                    $_field = $v->{$field};
                    $isset = array_first($out, function($key, $item) use($_field) {
                        return $item['value'] == $_field;
                    });

                    if(!$isset) {
                        array_push($out, [
                            'key' => $v->id,
                            'value' => $_field
                        ]);
                    }
                }
            }

            return $out;
        });

        return new JsonResponse($out);
    }
}
