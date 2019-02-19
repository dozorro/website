<?php

namespace App\Traits;

use App\JsonForm;
use App\Models\MvpTemplate;
use App\Models\NgoProfile;
use App\Models\NgoRisk;
use App\Models\RiskFeedback;
use App\Models\RiskValue;
use App\Models\Product;
use App\Models\Risk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Request;
use Config;

trait SearchTrait
{
    private $awardsRisks = ['R1080','R2010','R2030','R2040','R2050','R3010','R3020','R3040','R3050','R3060','R3070','R3080'];
    public $forms = [
        'F101','F102','F103','F104','F105','F106','F107','F108','F109','F110','F111','F112','F201','F202','F203','F204',
    ];

    public function getTender($parser, $item, $reviews)
    {
        $tender=new \stdClass();

        $item=$parser->tender_parse($item->tenderID, $item, false);

        $tender->reviews_total = 0;

        if(!$reviews->isEmpty()) {
            foreach($reviews as $review) {
                if($review->tender == $item->id) {
                    $tender->reviews_total++;
                }
            }
        }

        $tender->__features = @$item->__features;
        $tender->lots=[];
        $tender->defaultMaxHeight = 110;
        $tender->rating = 0;
        $tender->id = $item->id;
        $tender->tenderID = $item->tenderID;
        $tender->title = $item->title;
        $tender->status = $item->__status_name;
        $tender->customer = $item->procuringEntity->name;
        $tender->description = @$item->description;
        $tender->item = $item->items[0]->classification->description;
        $tender->price=$item->__full_formated_price;
        $tender->ngo = '';
        $tender->risks = [];
        $tender->tender_route = route('page.tender_by_id', ['id'=>$item->tenderID]);
        $tender->tender_route_role = route('page.profile_by_id', ['scheme'=>$item->procuringEntity->identifier->scheme.'-'.$item->procuringEntity->identifier->id,'tpl'=>$this->profileRole1TplId,'role'=>'role1']);
        $tender->participant_route = '';
        $tender->customer_route = '';
        $tender->__totalHundredForms=JsonForm::byTender($item->tenderID)->ByForm(false)->count();
        $tender->__procedure_name=@$item->__procedure_name;
        $tender->__winner_name=@$item->__active_award->suppliers[0]->name;
        $tender->__winner_url=route('page.profile_by_id', ['scheme'=>@$item->__active_award->suppliers[0]->identifier->scheme.'-'.@$item->__active_award->suppliers[0]->identifier->id,'tpl'=>$this->profileRole2TplId,'role'=>'role2']);
        $tender->__winner_price=@$item->__signed_contracts[0]->__full_formated_price;

        $tender->procuringEntity=new \StdClass;
        $tender->procuringEntity->name=@$item->procuringEntity->name;
        $tender->procuringEntity->id=@$item->procuringEntity->identifier->id;
        $tender->procuringEntity->kind=t('tender.kind.'.@$item->procuringEntity->kind);
        $tender->procuringEntity->__contactPoint=@$item->procuringEntity->__contactPoint;
        $tender->procuringEntity->__address=@$item->procuringEntity->__address;
        $tender->procuringEntity->url = route('page.profile_by_id', ['scheme'=>$item->procuringEntity->identifier->scheme.'-'.$item->procuringEntity->identifier->id,'tpl'=>$this->profileRole1TplId,'role'=>'role1']);

        $tender->prequalifications=$item->procurementMethodType=='aboveThresholdEU' && !empty($item->qualifications);
        $tender->awards=!empty($item->awards);
        $tender->contracts=!empty($item->__contracts);
        $tender->__isMultiLot=$item->__isMultiLot;

        if(!empty($item->__tender_documents)) {
            if (sizeof($item->__tender_documents) > 1) {
                $tender->documents_total = @sizeof(array_filter($item->__tender_documents, function ($document) {
                    return empty($document->stroked);
                }));
            } elseif (sizeof($item->__tender_documents) == 1) {
                $doc = current($item->__tender_documents);
                $tender->documents_total = !strpos($doc->title, '.p7s') ? 1 : 0;
            }
        }

        $qs = $parser->get_questions($item);
        $_qs = $parser->get_other_questions($item);

        if(empty($_qs)) {
            $_qs = [];
        }
        if(empty($qs)) {
            $qs = [];
        }

        $qs = array_merge($qs, $_qs);

        $tender->questions_total=sizeof($qs);
        $tender->complaints_total = sizeof($parser->get_all_complaints($item));

        if(!$item->__isMultiLot) {

            $tender->__items_deliveryDate = @$item->__items_deliveryDate;
            $tender->__items_address = @$item->__items_address;

            $tender->items = [];

            if (!empty($item->items)) {
                foreach ($item->items as $one) {
                    array_push($tender->items, [
                        'cpv' => @$one->classification->id,
                        'hidden' => true,
                        'quantity' => @$one->quantity,
                        'unit_name' => @$one->unit->name,
                        'description' => @$one->description,
                        'cpv_description' => @$one->classification->description,
                        '__address' => @$one->__address,
                        '__format_delivery_date' => @$one->__format_delivery_date,
                    ]);
                }
            }
        }

        if(!empty($tender->__isMultiLot)) {
            foreach($item->lots as $lot) {

                $lot->__winner_url = !empty($lot->__active_award) ? route('page.profile_by_id', [
                    'scheme' => @$lot->__active_award->suppliers[0]->identifier->scheme . '-' . @$lot->__active_award->suppliers[0]->identifier->id,
                    'tpl' => $this->profileRole2TplId,
                    'role' => 'role2'
                ]) : null;

                $qs = $parser->get_questions_lots($item, $lot);
                $_qs = $parser->get_other_questions_lots($item, $lot);

                if(empty($_qs)) {
                    $_qs = [];
                }
                if(empty($qs)) {
                    $qs = [];
                }

                $qs = array_merge($qs, $_qs);
                $features = 0;

                if(!empty($item->features)) {
                    $features = @sizeof(array_filter($item->features, function ($feature) use ($lot) {
                        return $feature->featureOf == 'lot' && $feature->relatedItem==$lot->id;
                    }));
                }

                $documents_total = 0;

                if(!empty($item->documents)) {

                    $docs = array_filter($item->documents, function($document) use ($lot) {
                        return $document->documentOf == 'lot' && empty($document->stroked) && $document->relatedItem == $lot->id;
                    });

                    if (sizeof($docs) > 1) {
                        $documents_total = sizeof($docs);
                    } elseif (sizeof($docs) == 1) {
                        $tender->documents_total = !strpos(current($docs)->title, '.p7s') ? 1 : 0;
                    }
                }

                $awards = 0;

                if(!empty($item->awards)) {
                    $awards = @sizeof(array_filter($item->awards, function($award) use ($lot) {
                        return $award->lotID == $lot->id;
                    }));
                }

                $prequalifications = 0;

                if(!empty($item->qualifications)) {
                    $prequalifications = @sizeof(array_filter($item->qualifications, function($award) use ($lot) {
                        return $award->lotID == $lot->id;
                    }));
                }

                $tender->lots[$lot->id] = [
                    'id' => $lot->id,
                    'toggled' => true,
                    'title' => @$lot->title,
                    'description' => @$lot->description,
                    'status' => $lot->__status_name,
                    'auctionUrl' => @$lot->auctionUrl,
                    'auctionPeriod' => !empty($lot->auctionPeriod->startDate) ? \Carbon\Carbon::createFromTimestamp(strtotime($lot->auctionPeriod->startDate))->format('d.m.Y H:i') : null,
                    '__winner_name' => @$lot->__active_award->suppliers[0]->name,
                    '__winner_url' => $lot->__winner_url,
                    '__winner_price' => @$lot->__active_award->__full_formated_price,
                    '__items' => sizeof($lot->__items),
                    '__documents' => $documents_total,
                    '__questions' => sizeof($qs),
                    '__complaints' => sizeof($parser->get_all_complaints($item, $lot)),
                    '__features' => $features,
                    'awards' => $awards,
                    'prequalifications' => $prequalifications
                ];
            }
        }

        if($item->procurementMethod == 'open'){
            $tender->dates = \Carbon\Carbon::createFromTimestamp(strtotime($item->enquiryPeriod->startDate))->format('d.m.Y');
        }elseif($item->procurementMethod == 'limited' && !empty($item->__active_award)){
            $tender->dates = \Carbon\Carbon::createFromTimestamp(strtotime($item->__active_award->date))->format('d.m.Y');
        }elseif(!empty($item->__signed_contracts)){
            $tender->dates = \Carbon\Carbon::createFromTimestamp(strtotime($item->__signed_contracts[0]->date))->format('d.m.Y');
        }

        if(!empty($item->complaintPeriod)) {
            $tender->complaintPeriod=\Carbon\Carbon::createFromTimestamp(strtotime($item->complaintPeriod->startDate))->format('d.m.Y H:i').' - '.\Carbon\Carbon::createFromTimestamp(strtotime($item->complaintPeriod->endDate))->format('d.m.Y H:i');
        }

        if(!empty($item->enquiryPeriod)) {
            $tender->enquiryPeriod=\Carbon\Carbon::createFromTimestamp(strtotime($item->enquiryPeriod->startDate))->format('d.m.Y H:i').' - '.\Carbon\Carbon::createFromTimestamp(strtotime($item->enquiryPeriod->endDate))->format('d.m.Y H:i');
        }

        if(!empty($item->tenderPeriod)) {
            $tender->tenderPeriod=\Carbon\Carbon::createFromTimestamp(strtotime($item->tenderPeriod->startDate))->format('d.m.Y H:i').' - '.\Carbon\Carbon::createFromTimestamp(strtotime($item->tenderPeriod->endDate))->format('d.m.Y H:i');
        }

        if(!empty($item->auctionPeriod->startDate)) {
            $tender->auctionPeriod=\Carbon\Carbon::createFromTimestamp(strtotime($item->auctionPeriod->startDate))->format('d.m.Y H:i');
        }

        if(!empty($item->dozorro->riskCodes)) {
            $riskCodes = explode(' ', $item->dozorro->riskCodes);
        } else {
            $riskCodes = DB::table('dozorro_risk_values')->select('risk_code')->where('tender_id', $item->id)->get();

            if(!empty($riskCodes)) {
                $riskCodes = array_pluck($riskCodes, 'risk_code');
            }
        }

        if(!empty($riskCodes)) {
            $tender->riskScore = @$item->dozorro->riskScore;
            $tender->riskScoreHalf = !empty($tender->riskScore) ? round($tender->riskScore, 1) : 0;
            $tender->__rating = !empty($tender->riskScore) ? round($tender->riskScore*10, 1) : 0;

            $riskCodes = array_filter($riskCodes, function($v) {
                return !starts_with($v, 'F');
            });

            $risks = Risk::whereIn('risk_code', $riskCodes)->get();
            $risksComment = RiskFeedback::whereIn('risk_code', $riskCodes)->where('tender_id', $tender->id)->get();

            if(!$risks->isEmpty()) {
                foreach($risks as $_risk) {

                    $comments = [];

                    foreach($risksComment as $comment) {
                        if($comment->risk_code == $_risk->risk_code) {
                            $comment->risk_comment = addslashes($comment->risk_comment);
                            $comments[] = $comment;
                        }
                    }

                    $tender->__risks[] = [
                        'title' => t('indicators.'.$_risk->risk_title),
                        'description' => $_risk->description,
                        'code' => $_risk->risk_code,
                        'comments' => !empty($comments) ? json_encode($comments) : '',
                        'icon' => !empty($_risk->resource_url) ? $_risk->image : '',
                        'url' => !empty($_risk->resource_url) ? strtr($_risk->resource_url, ['{tender_id}'=>$item->id]) : '',
                    ];
                }

                foreach($risks as $risk) {
                    if (in_array($risk->risk_code, $this->awardsRisks)) {
                        $tender->__awards_risks[] = t('indicators.'.$risk->risk_title);
                    }
                }
            }
        }

        $tender->ngo = JsonForm::byTender($item->tenderID)->whereNotNull('ngo_profile_id')->get()->count();

        return $tender;
    }
    public function getRisks($item)
    {
        if(!Schema::hasTable('dozorro_risk_values')) {
            return false;
        }

        $data = [
            'risks_count' => 0,
            'risks_total' => 0,
            'risks' => [
                'R1' => ['type' => 'error_icon', 'data' => []],
               /* 'R2' => ['type' => 'error_icon', 'data' => []],
                'R3' => ['type' => 'user_icon', 'data' => []],
                'R4' => ['type' => 'doc-icon', 'data' => []],*/
            ]
        ];

        $risks = DB::table('dozorro_risk_values')
            ->where('tender_id', $item->id)
            ->where('risk_code', 'not like', 'F%')
            ->get();

        if(empty($risks)) {
            return null;
        }

        $titles = Risk::whereIn('risk_code', array_column($risks, 'risk_code'))->get();

        /*$titles = array_where($_titles, function($key, $item) use($risks) {
            return in_array($item->risk_code, array_column($risks, 'risk_code'));
        });*/

        $data['risks_count'] = count($risks);
        $data['risks_total'] = $titles->count();

        $lots = [];

        foreach($risks as $risk) {

            $title = array_first($titles, function($key, $title) use($risk) {
               return $title->risk_code == $risk->risk_code;
            });

            $risk->lot = '';

            if(!empty($risk->lot_id)) {
                $lot = array_first($item->lots, function($lk, $l) use($risk) {
                    return $risk->lot_id == $l->id;
                });

                if(!empty($lot)) {
                    $risk->lot = $lot->title;
                }

                $lots[] = $risk->lot_id;
            }

            if(!empty($title)) {
                $risk->title = t('indicators.'.$title->risk_title);
                $risk->desc = $title->description;
                $risk->important = $title->important;
                $risk->icon = !empty($title->resource_url) ? $title->image : '';
                $risk->url = !empty($title->resource_url) ? strtr($title->resource_url, ['{tender_id}'=>$item->id]) : '';
            } else {
                $risk->title = t('indicators.unknown_title');
                $risk->desc = t('indicators.unknown_title_help');
                $risk->important = false;
            }

            $data['risks']['R1']['data'][] = $risk;

            /*if(stripos($risk->risk_code, 'R1') !== false) {
                $data['risks']['R1']['data'][] = $risk;
            }
            elseif(stripos($risk->risk_code, 'R2') !== false) {
                $data['risks']['R2']['data'][] = $risk;
            }
            elseif(stripos($risk->risk_code, 'R3') !== false) {
                $data['risks']['R3']['data'][] = $risk;
            }
            elseif(stripos($risk->risk_code, 'R4') !== false) {
                $data['risks']['R4']['data'][] = $risk;
            }*/
        }

        if(!empty($data['risks']['R1']['data'])) {
            $data['risks']['R1']['count'] = count(array_where($risks, function($key, $risk) {
                return stripos($risk->risk_code, 'R1') !== false;
            }, $risks));
            $data['risks']['R1']['total'] = count(array_where($titles, function($key, $item){
                return stripos($item->risk_code, 'R1') !== false;
            }));
        } else {
            unset($data['risks']['R1']);
        }
        /*if(!empty($data['risks']['R2']['data'])) {
            $data['risks']['R2']['count'] = count(array_where($risks, function($key, $risk) {
                return stripos($risk->risk_code, 'R2') !== false;
            }, $risks));
            $data['risks']['R2']['total'] = count(array_where($_titles, function($key, $item){
                return stripos($item->risk_code, 'R2') !== false;
            }));
        } else {
            unset($data['risks']['R2']);
        }
        if(!empty($data['risks']['R3']['data'])) {
            $data['risks']['R3']['count'] = count(array_where($risks, function($key, $risk) {
                return stripos($risk->risk_code, 'R3') !== false;
            }, $risks));
            $data['risks']['R3']['total'] = count(array_where($_titles, function($key, $item){
                return stripos($item->risk_code, 'R3') !== false;
            }));
        } else {
            unset($data['risks']['R3']);
        }
        if(!empty($data['risks']['R4']['data'])) {
            $data['risks']['R4']['count'] = count(array_where($risks, function($key, $risk) {
                return stripos($risk->risk_code, 'R4') !== false;
            }, $risks));
            $data['risks']['R4']['total'] = count(array_where($_titles, function($key, $item){
                return stripos($item->risk_code, 'R4') !== false;
            }));
        } else {
            unset($data['risks']['R4']);
        }*/

        foreach($data['risks'] as $r => $v) {
            if(count($v['data']) > 1) {
                $sort = [];

                foreach ($v['data'] as $item) {
                    $sort[] = $item->lot;
                }

                array_multisort($sort, SORT_ASC, $data['risks'][$r]['data']);
            }
        }

        foreach($data['risks'] as $r => $v) {
            if (count($v['data']) > 1) {
                $lots = [];

                foreach ($v['data'] as $ik => $item) {
                    if(!empty($item->lot) && !in_array($item->lot, $lots)) {
                        $lots[] = $item->lot;
                    }
                    elseif(!empty($item->lot) && in_array($item->lot, $lots)) {
                        $data['risks'][$r]['data'][$ik]->lot = '';
                    }
                }
            }
        }

        foreach($data['risks'] as $r => $v) {
            if (count($v['data']) > 1) {
                foreach ($v['data'] as $ik => $item) {
                    if($ik > 0 && $item->lot) {
                        $data['risks'][$r]['data'][$ik]->groupTitle = t('indicators.risk_title_lot');
                        break 2;
                    }
                }
            }
        }

        return (object) $data;
    }

    public function parse_search_query($keyByName = false)
    {
        $preselected_values=[];
        $query_array=[];
        $query_string=trim(Request::server('QUERY_STRING'), '&');
        $result='';

        if($query_string)
        {
            $query_array=explode('&', urldecode($query_string));

            if(sizeof($query_array))
            {
                foreach($query_array as $item)
                {
                    $item=explode('=', $item);

                    if(empty($item[1]))
                        continue;

                    $source=$item[0];
                    $search_value=!empty($item[1]) ? $item[1] : null;

                    $_source = starts_with($source, 'cpv') ? 'cpv' : $source;
                    $_search_value = $search_value;

                    if($_source == 'cpv' && is_numeric($search_value) && strlen($search_value) == 3) {
                        $_search_value .= '00000-0';
                    }

                    $value=$this->get_value($_source, $_search_value);

                    if($value) {
                        $preselected_values[$source][$search_value] = $value;
                    }
                    else {
                        if(!$keyByName) {
                            $preselected_values[$source][] = $search_value;
                        } else {
                            if($source == 'measure') {
                                $preselected_values[$source][urlencode($search_value)] = $search_value;
                            }
                            elseif(starts_with($source, 'product_name') || $source == 'risk') {
                                $preselected_values[$source][$search_value] = $search_value;
                            } else {
                                $preselected_values[$source][] = $search_value;
                            }
                        }
                    }
                }
            }
        }

        if(!empty($preselected_values['risk']) || !empty($preselected_values['risks'])) {

            $risks = Risk::all();
            $risks = array_pluck($risks->toArray(), 'risk_title', 'risk_code');

            foreach($preselected_values['risk'] as $k => $v) {
                $preselected_values['risk'][$k] = $k.' '.t('indicators.'.$risks[$v]);
            }
        }
        if(!empty($preselected_values['product_name'])) {

            $products = Product::whereIn('id', $preselected_values['product_name'])->get()->toArray();
            $products = array_pluck($products, 'name', 'id');

            foreach($preselected_values['product_name'] as $k => $v) {
                $preselected_values['product_name'][$k] = $products[$k];
            }
        }
        if(!empty($preselected_values['product_name_other'])) {

            $products = Product::whereIn('id', $preselected_values['product_name_other'])->get()->toArray();
            $products = array_pluck($products, 'description', 'id');

            foreach($preselected_values['product_name_other'] as $k => $v) {
                $preselected_values['product_name_other'][$k] = $products[$k];
            }
        }

        return [$query_array, $preselected_values];
    }

    public function get_value($source, $search_value)
    {
        $lang=Config::get('locales.current');

        $data=[];

        if($source !== 'edrpou') {

            if (!file_exists('./sources/' . $lang . '/' . $source . '.json'))
                return $data;

            $raw = json_decode(file_get_contents('./sources/' . $lang . '/' . $source . '.json'), TRUE);
        } else {
            $FormController = app('App\Http\Controllers\FormController');
            $raws = $FormController->get_edrpou_data($search_value);
            $raw = array_pluck($raws, 'name', 'id');
        }

        foreach($raw as $id=>$name)
        {
            array_push($data, [
                'id'=>$id,
                'name'=>$name
            ]);
        }

        foreach($data as $item)
        {
            if($item['id']==$search_value)
                return $item['name'];
        }


        return FALSE;
    }

    public function getSearchResultsHightlightArray($query)
    {
        $query_string=$query;
        $highlight=[];

        if($query_string)
        {
            $query_array=explode('&', urldecode($query_string));

            if(sizeof($query_array))
            {
                foreach($query_array as $item)
                {
                    $item=explode('=', $item);

                    $source=$item[0];

                    if($source=='query')
                    {
                        $search_value=$item[1];
                        $highlight[]=$item[1];

                        $value=$this->get_value($source, $search_value);
                        $highlight[]=$value;
                    }
                }
            }

            $highlight=array_unique(array_filter($highlight));
        }

        return $highlight;
    }
}