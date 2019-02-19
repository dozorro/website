<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Models\MvpTemplate;
use App\Models\NgoProfile;
use App\Models\NgoRisk;
use App\Models\Risk;
use App\Models\RiskValue;
use App\Settings;
use App\Traits\SearchTrait;
use Carbon\Carbon;
use Config;
use App\JsonForm;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class IndicatorsController extends BaseController
{
    use SearchTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(\Illuminate\Http\Request $request, $id = null)
    {
        if(empty($this->user->ngo)) {
            abort(404);
        }

        list($query_array, $preselected_values) = $this->parse_search_query(true);

        /*
        if(!empty($id)) {
            $tids[] = 'tid=' . $id;
            $default = $this->search($request, $tids, $preselected_values);
        }
        elseif(empty($preselected_values)) {
            $risks = RiskValue::
                orderBy('risk_value', 'DESC')
                ->take(10)
                ->get();

            $tids = array_map(function ($risk) {
                return 'id=' . $risk['tender_id'];
            }, $risks->toArray());

            $default = $this->search($request, $tids, $preselected_values);
        } else {
            $default = false;
        }
        */

        $agent = new Agent();
        $isMobile = $agent->isMobile();
        $riskFeedback = @Settings::instance('perevorot.dozorro.custom_setting', 'Perevorot\Dozorro\Models\CustomSetting')->risk_feedback;

        return $this->render('pages/indicators/index', [
            'riskFeedback' => $riskFeedback,
            'isMobile' => $isMobile,
            'preselected_values' => !empty($preselected_values) ? json_encode($preselected_values, JSON_UNESCAPED_UNICODE) : null,
            //'highlight' => json_encode($this->getSearchResultsHightlightArray(trim(Request::server('QUERY_STRING'), '&')), JSON_UNESCAPED_UNICODE),
            //'default' => $default,
            //'id' => $id,
        ]);
    }

    public function search(\Illuminate\Http\Request $request)
    {
        $start = $request->has('start') ? $request->get('start') : 0;
        $tids = [];

        if($request->has('tender_id')) {
            $tids = ['tid=' . $request->get('tender_id')];
        } elseif(!empty($this->user->ngo)) {

            $forms = JsonForm::
                select('tender')
                ->distinct()
                ->where('ngo_profile_id', $this->user->ngo->id)
                ->where('schema', 'F204')
                ->get()->toArray();

            $forms = @array_column($forms, 'tender');

            $inbox = NgoRisk::
                where('ngo_profile_id', $this->user->ngo->id)
                ->where(function($query) {
                    $query->where('status', 2)
                        ->orWhereNull('status');
                })
                ->whereNotIn('tender_id', $forms)
                ->take(10)
                ->skip($start)
                ->orderBy('id', 'ASC')
                ->get();

            if(!$inbox->isEmpty()) {
                $tids = array_map(function ($v) {
                    return 'id=' . $v['tender_id'];
                }, $inbox->toArray());
            } else {
                return [];
            }
        }
        elseif(empty(Input::get('query'))) {
            $risks = RiskValue::
                    orderBy('risk_value', 'DESC')
                    //->where('tender_id', 'UA-2016-11-23-000471-c')
                    ->take(10)
                    ->skip($start)
                    ->get();

            $tids = array_map(function ($risk) {
                return 'id=' . $risk['tender_id'];
            }, $risks->toArray());
        }

        // $tids=[
        //     'id=8d08f08d442a4a92920c96bbef173bcf',
        //     'id=9e1480c80e674e868878e30bbdb79cbb',
        //     'id=5c22ac657c834549a6f7669c399f6c22',
        //     'id=9eb0d95702554c20ad9d28f02bb04f31'
        // ];

        // disable in production
        // $data = Cache::remember('search-'.$request->get('start'), 60, function() use($tids){
            $FormController = app('App\Http\Controllers\FormController');
            $FormController->search_type = 'tender';

            $json = $FormController->getSearchResults(empty($tids) ? Input::get('query') : $tids, !empty($tids));

            $data=json_decode($json);
        // });

        if(!empty($data->items)) {

            $tenders = [];

            $tpls = MvpTemplate::orderBy('is_default', 'DESC')->groupBy('role')->get();
            $role = [1=>'',2=>''];

            if(!$tpls->IsEmpty()) {
                if(!empty($tpls[0])) {
                    $role[$tpls[0]->role] = $tpls[0]->id;
                }
                if(!empty($tpls[1])) {
                    $role[$tpls[1]->role] = $tpls[1]->id;
                }
            }

            $parser=app('App\Http\Controllers\PageController');
                
            $parser->modifiers=[
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
                ->whereIn('schema', ['F101','F102','F111','F114','F115','F116'])
                ->get();

            foreach ($data->items AS $item) {
                $tender = $this->getTender($parser, $item, $reviews);
                $tenders[$tender->id]=$tender;
                    
                /*
                $tenders[$item->id]->awardRating = 0;
                $tenders[$item->id]->initialBids = !$item->__isMultiLot ? json_encode(@$item->__initial_bids, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) : '';
                $tenders[$item->id]->awards = !$item->__isMultiLot ? json_encode(@$item->awards, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) : '';
                $tenders[$item->id]->badAwards = !$item->__isMultiLot ? @$item->__count_unsuccessful_awards : '';
                $tenders[$item->id]->bids = !$item->__isMultiLot ? json_encode(@$item->__bids, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) : '';
                $tenders[$item->id]->featuresPrice = !$item->__isMultiLot ? (!empty($item->__features_price) ? ($item->__features_price * 100) : 0) : '';
                $tenders[$item->id]->featuresCount = !$item->__isMultiLot && !empty($item->features) ? (' + '.count($item->features).' '.t('indicators.feature_count')) : '';
                $tenders[$item->id]->docsCount = !$item->__isMultiLot ? count(@$item->__tender_documents) : '';
                $tenders[$item->id]->guarantee = !$item->__isMultiLot && @$item->guarantee->amount > 0.0 ? ($item->guarantee->amount . ' ' . $item->guarantee->currency) : t('indicators.guarantee_empty');
                $tenders[$item->id]->lots = $item->__isMultiLot ? json_encode($item->lots, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) : '';
                $tenders[$item->id]->items = !$item->__isMultiLot && !empty($item->items) ? json_encode($item->items, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) : '';
                $tenders[$item->id]->procuringEntity = $item->procuringEntity->name;
                $tenders[$item->id]->edrpou = $item->procuringEntity->identifier->id;
                $tenders[$item->id]->tenderProcurement = $item->__procedure_name;
                $tenders[$item->id]->tenderSum = $item->__full_formated_price;
                $tenders[$item->id]->award = '';
                $tenders[$item->id]->awardSum = '';
                $tenders[$item->id]->contractSum = '';
                $tenders[$item->id]->tenderDate = '';
                $tenders[$item->id]->awardRisks = [];
                $tenders[$item->id]->totalForms = JsonForm::byTender($item->tenderID)->ByForm(false)->count();

                if(!empty($item->enquiryPeriod->startDate)) {
                    $tenders[$item->id]->tenderDate = Carbon::createFromTimestamp(strtotime($item->enquiryPeriod->startDate))->format('d.m.Y');

                    if (!empty($item->__signed_contracts->dateSigned)) {
                        $tenders[$item->id]->tenderDate .= ' - '. Carbon::createFromTimestamp(strtotime($item->__signed_contracts->dateSigned))->format('d.m.Y');;
                    }
                }
                if(!empty($item->__active_award)) {
                    $tenders[$item->id]->award = $item->__active_award->suppliers[0]->name;
                    $tenders[$item->id]->awardSum = $item->__active_award->__full_formated_price;
                }
                if(!empty($item->__signed_contracts->value)) {
                    $tenders[$item->id]->contractSum = $item->__signed_contracts->__full_formated_price;
                }

                $scheme =  $item->procuringEntity->identifier->scheme .'-'. $item->procuringEntity->identifier->id;

                if(!empty($role[1])) {
                    $tenders[$item->id]->customer_route = route('page.profile_by_id', ['scheme' => $scheme, 'tpl'=>$role[1], 'role'=>'role1']);
                } else {
                    $tenders[$item->id]->customer_route = route('page.profile_by_id', ['scheme' => $scheme]);
                }

                if(!empty($item->awards)) {

                    $active_award = array_first($item->awards, function($key, $award) {
                        return $award->status == 'active';
                    });

                    if(!empty($active_award)) {
                        $scheme = $active_award->suppliers[0]->identifier->scheme . '-' . $active_award->suppliers[0]->identifier->id;

                        if (!empty($role[2])) {
                            $tenders[$item->id]->participant_route = route('page.profile_by_id',
                                ['scheme' => $scheme, 'tpl' => $role[2], 'role' => 'role2']);
                        } else {
                            $tenders[$item->id]->participant_route = route('page.profile_by_id',
                                ['scheme' => $scheme]);
                        }
                    }
                }*/
            }

            if(isset($inbox) && !empty($inbox)) {

                $tenderIDS = array_pluck($inbox, 'tender_id', 'id');
                $_tenders = [];

                foreach($tenderIDS as $_id => $tid) {
                    foreach ($tenders as $id => $tender) {
                        if($tid == $id) {
                            $_tenders[$tid] = $tender;
                            break;
                        }
                    }
                }

                $tenders = $_tenders;
                unset($_tenders);
            }

            //$ids = array_keys($tenders);
            //$riskValues = RiskValue::whereIn('tender_id', $ids)->get();
            /*$riskCodes = array_column($riskValues->toArray(), 'risk_flags');

            foreach($riskCodes as $k => &$risk) {
                if(strpos($risk, ',')) {
                    $riskCodes = array_merge($riskCodes, explode(',', $risk));
                    $risk = null;
                }
            }

            $riskCodes = array_unique(array_filter($riskCodes));
            $risks = Risk::whereIn('risk_code', $riskCodes)->get();*/

           // if(!empty($riskValues)) {
             //   foreach($riskValues as $_item) {
             //       if(isset($tenders[$_item->tender_id])) {
             //           $tenders[$_item->tender_id]->rating = $_item->risk_value;

                        /*$_risks = array_where($risks, function($key, $v) use($_item) {
                            return stripos($_item->risk_flags, $v->risk_code) !== FALSE;
                        });

                        if(!empty($_risks)) {
                            foreach($_risks as $risk) {
                                $tenders[$_item->tender_id]->risks[] = t('indicators.' . $risk->risk_title);

                                if(in_array($risk->risk_code, $this->awardsRisks)) {
                                    $tenders[$_item->tender_id]->awardRisks[] = t('indicators.' . $risk->risk_title);
                                    $tenders[$_item->tender_id]->awardRating = $_item->risk_value;
                                }
                            }
                        }

                        if(!empty($tenders[$_item->tender_id]->awardRisks)) {
                            $tenders[$_item->tender_id]->awardRisks = implode(',', $tenders[$_item->tender_id]->awardRisks);
                        }*/
                   // }
                //}
            //}

            /*
            $_tenders = JsonForm::
                whereIn('tender', $ids)
                ->where('schema', 'F201')
                ->where('payload', "not like", "'%abuseCode\":\"A028%'")
                ->where('payload', "not like", "'%abuseCode\":\"A029%'")
                ->with('ngo_profile')
                ->get();

            if(!$_tenders->isEmpty()) {
                foreach($_tenders as $_item) {
                    if(!empty($_item->ngo_profile)) {
                        $json = $_item->json;
                        $tenders[$_item['tender']]->ngo = $_item->ngo_profile->title;
                        $tenders[$_item['tender']]->ngo_data[$json->abuseCode] = $json->abuseName;
                    }
                }
            }*/

            /*$data_year = array();

            foreach ($tenders as $key => $arr) {
                $data_year[$key] = $arr->rating;
            }

            array_multisort($data_year, SORT_DESC, $tenders);*/
        } else {
            $tenders = [];
        }

        
        return response()->json([
            'tenders' => array_values($tenders)
        ], 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }
}
