<?php

namespace App\Http\Controllers;

use App\Page;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Models\MvpTemplate;
use App\JsonForm;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class SearchController extends BaseController
{
    use App\Traits\SearchTrait;

    public function index(Request $request)
    {
        $risks = App\Models\Risk::where('risk_code', 'like', 'R%')->orderBy('risk_code')->get();
        $FormController = app('App\Http\Controllers\FormController');
        $FormController->search_type = 'tender';
        $procedures = $FormController->get_procedure_t_data();
        $statuses = $FormController->get_status_data();
        $regions = $FormController->get_region_data();

        $filters = $this->parse_search_query()[1];

        setlocale(LC_ALL, "en_US.UTF-8");
        $data = array_column($regions, 'name');
        array_multisort($data, SORT_LOCALE_STRING, $regions);

        if(!empty($filters['region'])) {
            foreach($filters['region'] as $k => &$region) {
                if (is_numeric($region) && strlen($region) == 5) {
                    $start = substr($region, 0, 2);

                    foreach($regions as $_region) {
                        $range = explode('-', $_region['id']);

                        if(empty($range[1])) {
                            $range[1] = $range[0];
                        }

                        for($i = $range[0]; $i <= $range[1]; $i++) {
                            if($i === $start) {
                                unset($filters['region'][$k]);
                                $filters['region'][$region] = $_region['name'];
                                break 2;
                            }
                        }
                    }
                }
            }
        }

        foreach($filters as $k => &$filter) {
            if($k == 'proc_type') {
                foreach($procedures as &$procedure) {
                    foreach($filter as $f) {
                        if($f) {
                            if (in_array($f, explode(',', $procedure['id']))) {
                                $procedure['selected'] = true;
                                break;
                            } else {
                                $procedure['selected'] = false;
                            }
                        }
                    }
                }
            }
            elseif($k == 'value') {
                if($filter[0]) {
                    $filter[0] = explode('-', $filter[0]);
                }
            }
            elseif(in_array($k, ['form_code_all', 'risk_code_all'])) {
                $filter = explode(' ', $filter[0]);
            }
            elseif(in_array($k, ['tenderer_edrpou_all', 'supplier_edrpou_all', 'tenderer_edrpou', 'supplier_edrpou'])) {

                $filter = explode(' ', $filter[0]);

                foreach($filter as $fk => $f) {
                    if($f) {
                        $filter[$fk] = current($FormController->get_edrpou_data($f, true));
                    }
                }
            }
            elseif(in_array($k, ['contract_active', 'supplier_active'])) {
                foreach($filter as $fk => $f) {
                    if($f) {
                        $filter[$fk] = current($FormController->get_edrpou_data($f, true));
                    }
                }
            }
        }

        //dd($filters);

        /*$maxPrice = Cache::remember('getTenderPrice', 60*24, function() {
            $json = $this->getSearchResults('sort=value&order=desc', true);

            if(!empty($json)) {
                $data = json_decode($json);
            } else {
                $data = null;
            }

            if(!empty($data->items)) {
                return $data->items[0]->value->amount;
            }

            return 3000;
        });*/

        $agent = new Agent();
        $isMobile = $agent->isMobile();
        $riskFeedback = !empty($this->user->ngo) && @App\Settings::instance('perevorot.dozorro.custom_setting', 'Perevorot\Dozorro\Models\CustomSetting')->risk_feedback;
        $riskAccess = @App\Settings::instance('perevorot.dozorro.custom_setting', 'Perevorot\Dozorro\Models\CustomSetting')->risk_in_search;

        if(!$riskAccess && !empty($this->user->ngo)) {
            $riskAccess = true;
        }

        $examples = App\Models\SearchRequest::all();

        if(!$examples->isEmpty()) {
            $examples->each(function($v, $k) {
                $v->font_size = rand(13, 24);
            });
        }

        return $this->render('pages.search.index', [
            'risk_code_like' => !empty($filters['risk_code_like']) && in_array('R', $filters['risk_code_like']),
            'examples' => $examples,
            'checkedAnyRisks' => !empty($filters['risk_code']) && count($filters['risk_code']) >= 2,
            'riskAccess' => $riskAccess,
            'riskFeedback' => $riskFeedback,
            //'maxPrice' => $maxPrice,
            'isMobile' => $isMobile,
            'risks' => $risks,
            'filters' => $filters,
            'procedures' => $procedures,
            'statuses' => $statuses,
            'regions' => $regions,
            'forms' => $this->forms
        ]);
    }

    public function search(Request $request)
    {        
        $query = $request->get('query');
        $json = $this->getSearchResults($query);

        if(!empty($json)) {
            $data = json_decode($json);
        } else {
            $data = null;
        }

        if(!empty($data->items)) {

            $tender_ids=array_pluck($data->items, 'id');
            $ngo_forms=DB::table('perevorot_dozorro_json_forms')->select('schema', 'tender')->whereIn('tender', $tender_ids)->where('schema', 'F201')->lists('schema', 'tender');

            foreach($data->items as $ik => $item) {

                if(str_contains($query, 'form_code=F201') && !empty($item->dozorro->forms) && $item->dozorro->formsCount == 1) {
                    $formData = array_first($item->dozorro->forms, function($k, $v) {
                        return @$v->payload->formData->abuseCode == 'A028';
                    });
                    if($formData) {
                        unset($data->items[$ik]);
                        continue;
                    }
                }

                $item->__is_F201=array_key_exists($item->id, $ngo_forms);
            }

            $tenders = [];

            $tpls = MvpTemplate::orderBy('is_default', 'DESC')->groupBy('role')->get();
            $role = [1 => '', 2 => ''];

            if (!$tpls->IsEmpty()) {
                if (!empty($tpls[0])) {
                    $role[$tpls[0]->role] = $tpls[0]->id;
                }
                if (!empty($tpls[1])) {
                    $role[$tpls[1]->role] = $tpls[1]->id;
                }
            }

            $parser = app('App\Http\Controllers\PageController');

            $parser->modifiers = [
                'get_signed_contracts',
                'get_yaml_documents',
                'get_tender_documents',
                'get_awards',
                'get_contracts',
                'get_lots_base',
            ];

            $reviews = JsonForm::
                whereIn('tender', array_column($data->items, 'id'))
                ->where('is_hide', 0)
                ->where('model', '=', 'form')
                ->whereIn('schema', ['F101', 'F102', 'F111', 'F114', 'F115', 'F116'])
                ->get();

            foreach ($data->items AS $item) {
                $tender = $this->getTender($parser, $item, $reviews);
                $tender->__is_F201 = $item->__is_F201;
                $tenders[$tender->id]=$tender;
            }
        } else {
            $tenders = [];
        }

        //dd($tenders);

        return response()->json([
            'tenders' => array_values($tenders),
            'total' => number_format(@$data->total, 0, null, ' ')
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function getSearchResults($query = '', $skip = false)
    {
        if (env('API_PRETEND')) {
            return file_get_contents('./sources/pretend/results.json');
        }

        if(empty($query)) {
            return [];
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if(!empty(Input::get('start'))) {
            $query .= '&start='.Input::get('start');
        }

        $request = head(\Symfony\Component\HttpFoundation\Request::createFromGlobals()->server);
        $page = $request['REQUEST_SCHEME'].'://'.$request['HTTP_HOST'].$request['REQUEST_URI'].$request['QUERY_STRING'];
        $path = env('API_TENDER') .(!empty($query) ? ('?' . $query) : '').'&__url='.$page;

        if(isset($_GET['api']) && getenv('APP_ENV')=='local')
            dd($path);

        curl_setopt($ch, CURLOPT_URL, $path);

        if(env('API_LOGIN') && env('API_PASSWORD')){
            curl_setopt($ch, CURLOPT_USERPWD, env('API_LOGIN') . ":" . env('API_PASSWORD'));
        }

        $headers = [
            'X-Forwarded-For: '.@$request['REMOTE_ADDR'],
            'Accept-Encoding: gzip'
        ];

        curl_setopt($ch,CURLOPT_ENCODING , "gzip");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result=curl_exec($ch);

        curl_close($ch);

        Log::info(__FUNCTION__.': '.$path);

        return $result;
    }
}
