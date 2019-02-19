<?php namespace App\Http\Controllers;

use App\Classes\Cron;
use App\Traits\SearchTrait;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Settings;
use App\Helpers;
use Input;
use App;
use Cache;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Request;
use Redirect;
use Config;
use DateTime;
use DB;
use Lang;
use App\Models\Monitoring\Tender;
use App\Models\Monitoring\Monitoring;
use App\Models\Monitoring\Item;
use Jenssegers\Agent\Agent;
use Excel;

class PageController extends BaseController
{
    use App\Traits\ModelTranslation, SearchTrait;

    public $statuses;
    public $search_type;
    public $blocks;
    public $modifiers;
    public $commonModifiers=[
        'get_procedure',
        'get_open_title',
    ];

    public function page(\Illuminate\Http\Request $request)
    {
        $this->getPage($request);

        $dataStatus = [];

        foreach(app('App\Http\Controllers\FormController')->get_status_data() as $one)
            $dataStatus[$one['id']]=$one['name'];

        $this->statuses = $dataStatus;

        $blocks = $this->blocks->getBlocks();

        if(empty($blocks))
        {
            abort(404);
            return $this->render('errors/404', []);
        }

        return $this->render('pages/page', [
            'blocks' => $blocks,
            'dataStatuses' => $dataStatus,
        ]);
    }

    public function getPage($request)
    {
        //Get current locale
        $locale = (trim($request->route()->getPrefix(), '/'))?:App\Classes\Lang::getDefault();

        App::setLocale($locale);

        //Replace locales from url
        $url = rtrim(App\Helpers::replaceLocales($request->getRequestUri()), '/');

        if(empty($url))
            $url='/';

        //Find page by url
        $page = App\Page::where('url', $url)->first();

        $this->setSeoData((array) $page->getAttributes());

        if (!$page) {
            abort(404);
            return $this->render('errors/404', []);
        }

        $blocks = (array) json_decode($page->{'longread_' . $locale});
        $this->blocks = new App\Classes\Longread($blocks, $page->id);
    }

    public function home(\Illuminate\Http\Request $request)
    {
        $last=null;
        $auctions_items=null;

        $dataStatus=[];
        $this->getPage($request);

        foreach(app('App\Http\Controllers\FormController')->get_status_data() as $one)
            $dataStatus[$one['id']]=$one['name'];

        $data = [
            'dataStatus' => $dataStatus,
            'auctions' => $auctions_items,
            'blocks' => $this->blocks->getBlocks(),
            'last' => json_decode($last),
        ];

        return $this->render('pages/home', $data);
    }

    function search_redirect()
    {
        return Redirect::to(str_replace('/search', '/tender/search', Request::fullUrl()), 301);
    }

    public function search(\Illuminate\Http\Request $request, $search_type='tender')
    {
        $this->search_type=$this->get_search_type($search_type);
        list($query_array, $preselected_values)=$this->parse_search_query();

        $result='';

        if(empty($query_array)) {
            $data = [
                'search_type' => $this->search_type,
                'preselected_values' => json_encode($preselected_values, JSON_UNESCAPED_UNICODE),
                'highlight' => json_encode($this->getSearchResultsHightlightArray(trim(Request::server('QUERY_STRING'), '&')), JSON_UNESCAPED_UNICODE),
                'result' => [],
            ];

            return $this->render('pages/search', $data);
        }

        $FormController=app('App\Http\Controllers\FormController');
        $FormController->search_type=$this->search_type;

        $result = $FormController->getSearchResultsHtml($query_array);

        $data = [
            'search_type' => $this->search_type,
            'preselected_values' => json_encode($preselected_values, JSON_UNESCAPED_UNICODE),
            'highlight' => json_encode($this->getSearchResultsHightlightArray(trim(Request::server('QUERY_STRING'), '&')), JSON_UNESCAPED_UNICODE),
            'result' => $result->content(),
        ];

        return $this->render('pages/search', $data);
    }

    private function get_search_type($search_type='tender')
    {
        return in_array($search_type, ['tender', 'plan'])?$search_type:'tender';
    }

    public function plan($id)
    {
        $this->search_type='plan';
        $json=$this->getSearchResults(['pid='.$id]);

        $item=false;
        $error=false;

        if($json)
        {
            $data=json_decode($json);

            if(empty($data->error))
            {
                if(!empty($data->items[0]))
                {
                    $item=$data->items[0];
                }
            }
            else
                $error=$data->error;

            if(!$item)
                $error='План не найден';

            if($error)
            {
                $data = [
                    'item' => false,
                    'error' => $error
                ];

                return $this->render('pages/plan', $data);
            }
        }

        $item->__items=new \StdClass();
        $classification=[$item->classification];

        if(!empty($item->items))
        {
            foreach($item->items as $one)
            {
                if(!empty($one->classification))
                {
                    $classification[]=$one->classification;
                }
            }
        }

        $item->classification=$classification;

        $additionalClassifications=$item->additionalClassifications;

        if(!empty($item->items))
        {
            foreach($item->items as $one)
            {
                if(!empty($one->additionalClassifications))
                {
                    foreach($one->additionalClassifications as $c)
                        $additionalClassifications[]=$c;
                }
            }
        }

        $item->__items=array_where($additionalClassifications, function($key, $one){
            return $one->scheme!='КЕКВ';
        });

        $item->__items_kekv=new \StdClass();
        $item->__items_kekv=array_where($additionalClassifications, function($key, $one){
            return $one->scheme=='КЕКВ';
        });

        $this->get_procedure($item->tender);

        $this->parse_is_sign($item);
        $this->plan_check_start_month($item);

        if(isset($_GET['dump']) && in_array(getenv('APP_ENV'), ['local','develop']))
            dd($item);

        $data = [
            'item' => $item,
            'error' => $error,
            'areas' => $this->getAreas(),
        ];

        return $this->render('pages/plan', $data);
    }

    public function plan_check_start_month(&$item)
    {
        $item->__is_first_month=false;

        if(!empty($item->tender->tenderPeriod->startDate))
        {
            $date = strtotime($item->tender->tenderPeriod->startDate);

            //$item->__is_first_month=date('j', $date)==1 ? strftime('%B, %Y', $date) : false;
            $item->__is_first_month=date('j', $date)==1 ? Lang::get('months.'.date('n', $date)).', '.date('Y', $date) : false;
        }
    }

    public function tenders(\Illuminate\Http\Request $request) {

        $params = $request->all();

        if($request->has('edrpou') || $request->has('edrpous'))
        {
            $main = false;

            if($request->has('edrpou')) {
                $edrpos[] = $main = $request->get('edrpou');
            }
            if($request->has('edrpous')) {
                $e = explode(',', $request->get('edrpous'));
                $_edrpos = isset($edrpos) ? array_merge($edrpos, $e) : $e;
                $edrpos = $_edrpos;
            }

            foreach($edrpos as $code) {
                if($this->user->user->issetEdrpou($code)) {
                    $this->user->is_customer = true;
                    break;
                }
            }

            $_customer = false;

            if($main && $customer = App\Customer::where('main_edrpou', $main)->first()) {
                $customer->translate();
                $tpm = explode("\n", str_replace("\r", '', trim($customer->edrpou)));
                $edrpos = array_merge($edrpos, $tpm);
                $_customer = ['code' => $customer->main_edrpou, 'name' => $customer->title, 'image' => $customer->image()];
            } else {
                $CController = app('App\Http\Controllers\SearchCustomerController');
                $customer = @current($CController->search($request));
                $_customer = ['code' => $customer['key'], 'name' => $customer['value'], 'image' => false];
            }

            if(!$_customer && $main) {
                $_customer = ['code' => false, 'name' => t('tenders.edrpou_code') . ' ' . $main, 'image' => false];
            }

            $params['edrpou'] = $edrpos;
            $customer_total = App\JsonForm::getInfoByCustomer($params['edrpou']);
        }
        if($request->has('cpv'))
        {
            $_cpv = $request->get('cpv');

            if(!is_numeric(str_replace('-', '', $_cpv))) {

                $CController = app('App\Http\Controllers\FormController');
                $cpvs = $CController->getCpv();

                $cpv = array_where($cpvs, function($key, $ar) use($_cpv){
                    return $ar['id'] == $_cpv || $ar['name'] == $_cpv;
                });

                if(!empty($cpv))
                {
                    $_cpv = current($cpv)['id'];
                }
            }

            $params['cpv'] = $_cpv;
        }

        $params['ngo'] = false;
        $tenders = App\JsonForm::getReviews($params);
        $FormController = app('App\Http\Controllers\FormController');
        $dataStatus = [];

        foreach($FormController->get_status_data() as $one)
            $dataStatus[$one['id']] = $one['name'];

        foreach($FormController->get_region_data() as $one)
            $regions[$one['id']] = $one['name'];

        setlocale(LC_ALL, "en_US.UTF-8");
        asort($regions, SORT_LOCALE_STRING);

        if($request->ajax()) {

            $views = '';
            $views_mobile = '';

            foreach($tenders AS $item) {

                $views .= view('partials/_search_tender', [
                    'item' => $item->get_tender_json(),
                    'tender' => $item,
                    'dataStatus' => $dataStatus,
                    'for_mobile' => false,
                ])->render();

                $views_mobile .= view('partials/_search_tender', [
                    'item' => $item->get_tender_json(),
                    'tender' => $item,
                    'dataStatus' => $dataStatus,
                    'for_mobile' => true,
                ])->render();
            }

            return ['desktop' => $views, 'mobile' => $views_mobile];
        }

        $data = [
            'tenders' => $tenders,
            'regions' => $regions,
            'dataStatus' => $dataStatus,
            'customer' => isset($_customer) ? $_customer : false,
            'customer_total' => isset($customer_total) ? $customer_total : false,
        ];

        if(!empty($this->user->is_customer) && $request->has('export')) {
            $this->exportReviews($data);
        } else {
            return $this->render('pages/tenders', $data);
        }
    }

    public function exportReviews($data)
    {
        $fileName = 'reviews';
        $export = [];

        foreach($data['tenders'] as $row) {
            $tender = $row;
            $item = $row->get_tender_json();
            $ar = [];

            $ar['date'] = $tender->date;
            $ar['tenderID'] = $item->tenderID;
            $ar['title'] = $item->title;
            $ar['procuringEntity'] = $item->procuringEntity->name;
            $ar['amount'] = $item->value->amount;
            $ar['currency'] = $item->value->currency;
            $ar['reviews'] = $tender->reviews;
            $ar['tender_status'] = @$tender->tender_status;
            $ar['f201_count2'] = $tender->f201_count2;
            $ar['reaction'] = $tender->reaction ? t('tenders.result.reaction_yes') : t('tenders.result.reaction_no');
            $ar['f201_count'] = $tender->f201_count ? t('tenders.result.f201_yes') : t('tenders.result.f201_no');

            $export[] = $ar;
        }

        Excel::create($fileName, function($excel) use($export, $fileName, $data) {
            $excel->sheet($fileName, function($sheet) use($export) {
                $sheet->fromArray($export);
            });
        })->download('xls');
    }

    public function monitoring_tender(\Illuminate\Http\Request $request, $id, $_type = '')
    {
        if(!$this->user || !$this->user->monitoring || !$this->user->access_full || !$this->user->monitoring->is_enabled) {
            return redirect()->back();
        }

        if(!$item = $this->tender_parse($id)) {
            return redirect()->back();
        }

        if($item->status != 'complete') {
            return redirect()->back();
        }

        //dd($item);

        if(!$tender = $this->user->monitoring->tender($id)) {
            $tender = new Tender();

            $json = new \stdClass();
            $json->status = $item->status;
            $json->date = $item->date;
            $json->tenderID = $item->tenderID;
            $json->title = $item->title;
            $json->procuringEntity = $item->procuringEntity;
            $json->value = $item->value;

            $tender->tender_id = $id;
            $tender->tender = $item->id;
            $tender->region = isset($item->procuringEntity->address->postalCode)? $tender->get_region($item->procuringEntity->address->postalCode) : null;
            $tender->entity_id = isset($item->procuringEntity->identifier->id) ? $item->procuringEntity->identifier->id : null;
            $tender->json = json_encode($json);
            $tender->monitoring_id = $this->user->monitoring->id;
            $tender->save();
        }

        if($tender && $request->ajax()) {

            if(!$tender->region) {
                $tender->region = isset($item->procuringEntity->address->postalCode) ? $tender->get_region($item->procuringEntity->address->postalCode) : null;
            }
            if(!$tender->entity_id) {
                $tender->entity_id = isset($item->procuringEntity->identifier->id) ? $item->procuringEntity->identifier->id : null;
            }

            if(!count($request->all())) {
                return response()->json(['status' => 'empty']);
            }

            if ($_type == 'active') {
                $tender->is_ready = $request->get('ready');
                $tender->date = Carbon::now();
                $tender->save();
                return response()->json(['status' => 'ok']);
            }
            elseif ($_type == 'delete') {
                Item::where('tender_id', $tender->id)->where('id', $request->get('id'))->delete();
                return response()->json(['status' => 'ok']);
            }
            elseif (in_array($_type, ['create', 'update'])) {
                $params = $request->all();

                if($request->has('id')) {
                    $item = Item::where('tender_id', $tender->id)->where('id', $request->get('id'))->first();
                } else {
                    $item = new Item();
                    $item->tender_id = $tender->id;
                    $item->lot_id = $params['lotId'];
                    $item->type = $params['type'];
                }

                $price_tax = $params['tax'] ? ($params['price'] + ($params['price'] * ($params['tax'] / 100))) : 0;

                $item->name = $params['name'];
                $item->form = $params['form'];
                $item->sum = $price_tax ? $price_tax*$params['quantity'] : $params['price']*$params['quantity'];
                $item->price = $params['price'];
                $item->tax = $params['tax'];
                $item->price_tax = $price_tax;
                $item->quantity = $params['quantity'];
                $item->measure = $params['measure'];
                $item->save();

                return response()->json(['status' => 'ok', 'response' => ['id' => $item->id]]);
            }

            if ($request->has('is_other')) {

                $tender->is_other = $request->get('is_other') == 'true' ? 1 : 0;
                $tender->save();

                return response()->json(['status' => 'ok']);
            }
            elseif ($request->has('is_ok')) {

                $tender->is_ok = $request->get('is_ok') == 'true' ? 1 : 0;
                $tender->save();

                return response()->json(['status' => 'ok']);
            }
            elseif ($request->has('new_index')) {

                $tender->new_index = $request->get('new_index');
                $tender->save();

                return response()->json(['status' => 'ok']);
            }
        } else {
            if($_type == 'lots') {
                $items = !empty($item->__isMultiLot) ? $item->lots : [null];
                $type = 'lot';
            } elseif($_type == 'changes') {
                $items = !empty($item->__contracts_changes) ? $item->__contracts_changes : [null];
                $type = 'change';
            } else {
                return redirect()->back();
            }
        }

        $FormController = app('App\Http\Controllers\FormController');
        $dataStatus = [];

        foreach($FormController->get_status_data() as $one)
            $dataStatus[$one['id']] = $one['name'];

        $items_sum = array_sum(array_map(function($v) {
            return $v['sum'];
        }, $tender->items->toArray()));

        $violations = App\Models\Violation::all();
        $mviolations = $this->user->monitoring->monitoringViolations()->where('tid', $item->tenderID)->get();

        $data = [
            'mviolations' => $mviolations,
            'violations' => $violations,
            'monitoringItems' => $items,
            'monitoringType' => $type,
            'items_sum' => $items_sum,
            'items' => $tender->items,
            'multy' => false,
            'item'=>$item,
            'tender' => $tender,
            'rating' => 0,
            'error' => false,
            'dataStatus' => $dataStatus,
            'return_back' => route('page.tender_by_id', ['id' => $item->tenderID]),
        ];

        return $this->render('pages/monitoring_tender', $data);
    }

    public function monitoring(\Illuminate\Http\Request $request, $slug, $formType = 'tenders')
    {
        $monitor = Monitoring::findBySlug($slug);

        if(!$this->user || !$monitor) {
            abort(404);
        }

        if(!$this->user->access_read || $monitor->users->isEmpty()) {
            return redirect()->back();
        }

        $is_ajax = $request->method() == 'POST';
        $FormController = app('App\Http\Controllers\FormController');
        $tenders = $regions = $dataStatus = [];
        $params = $request->all();

        if($is_ajax) {
            if ($request->has('edrpou')) {
                $edrpos[] = $request->get('edrpou');

                if ($customer = App\Customer::where('main_edrpou', $request->get('edrpou'))->first()) {
                    $tpm = explode("\n", str_replace("\r", '', trim($customer->edrpou)));
                    $edrpos = array_merge($edrpos, $tpm);
                }

                $params['edrpou'] = $edrpos;
            }
            if ($request->has('cpv')) {
                $_cpv = $request->get('cpv');

                if (!is_numeric(str_replace('-', '', $_cpv))) {

                    $CController = app('App\Http\Controllers\FormController');
                    $cpvs = $CController->getCpv();

                    $cpv = array_where($cpvs, function ($key, $ar) use ($_cpv) {
                        return $ar['id'] == $_cpv || $ar['name'] == $_cpv;
                    });

                    if (!empty($cpv)) {
                        $_cpv = current($cpv)['id'];
                    }
                }

                $params['cpv'] = $_cpv;
            }

            foreach($FormController->get_status_data() as $one)
                $dataStatus[$one['id']] = $one['name'];

            if(!$is_ajax) {
                foreach ($FormController->get_region_data() as $one)
                    $regions[$one['id']] = $one['name'];

                setlocale(LC_ALL, "en_US.UTF-8");
                asort($regions, SORT_LOCALE_STRING);
            }

            if ($formType == 'tenders') {
                $this->search_type = 'tender';
                $edrpou = [];

                if($monitor->edrpou) {
                    $edrpou = explode("\n", str_replace("\r", '', trim($monitor->edrpou)));
                }
                if($monitor->main_edrpou) {
                    $edrpou[] = trim($monitor->main_edrpou);
                }
                if($request->has('edrpou')) {
                    $edrpou[] = trim($request->get('edrpou'));
                }
                if(!empty($edrpou)) {
                    $query = array_map(function ($v) {
                        return "edrpou={$v}";
                    }, $edrpou);
                }
                if($monitor->cpv) {
                    $query[] = 'cpv_like='.substr($monitor->cpv, 0, 3);
                }
                if($request->has('region')) {
                    $query[] = 'region='.trim($request->get('region'));
                }

                $query[] = 'status=complete';
                $query[] = 'start='.(!isset($params['page']) ? 0 : $params['page']*10);

                $tenders = json_decode($this->getSearchResults($query));

                if($is_ajax) {

                    $views = '';
                    $views_mobile = '';

                    if(isset($tenders->items) && sizeof($tenders->items)) {
                        $params['ready'] = 1;
                        $_tenders = Tender::getData($params)->lists('tender_id')->toArray();

                        $tenders->items = array_where($tenders->items, function($k, $item) use($_tenders) {
                            return !in_array($item->tenderID, $_tenders);
                        });

                        if(!empty($params['tid'])){
                            $tenders->items=array_where($tenders->items, function($k, $item) use($params){
                                return $item->tenderID==$params['tid'];
                            });
                        }

                        foreach ($tenders->items AS $item) {
                            $views .= view('partials/_search_monitoring_tenders', [
                                'item' => $item,
                                'for_mobile' => false,
                                'dataStatus' => $dataStatus,
                                'formType' => $formType,
                            ])->render();
                            $views_mobile .= view('partials/_search_monitoring_tenders', [
                                'item' => $item,
                                'for_mobile' => true,
                                'dataStatus' => $dataStatus,
                                'formType' => $formType,
                            ])->render();
                        }
                    }

                    return [
                        'desktop' => $views,
                        'mobile' => $views_mobile,
                        'lastPage' => isset($tenders->total) && $tenders->total ? ceil($tenders->total/10) : 0,
                    ];
                }

            } elseif ($formType == 'works') {
                $params['ready'] = 1;
                $params['paginate'] = 10;
                $tenders = Tender::getData($params);

                if($is_ajax) {

                    $views = '';
                    $views_mobile = '';

                    if(!$tenders->isEmpty()) {
                        foreach ($tenders AS $item) {
                            $views .= view('partials/_search_monitoring_tenders', [
                                'item' => json_decode($item->json),
                                'for_mobile' => false,
                                'dataStatus' => $dataStatus,
                                'formType' => $formType,
                            ])->render();
                            $views_mobile .= view('partials/_search_monitoring_tenders', [
                                'item' => json_decode($item->json),
                                'for_mobile' => true,
                                'dataStatus' => $dataStatus,
                                'formType' => $formType,
                            ])->render();
                        }
                    }

                    return [
                        'desktop' => $views,
                        'mobile' => $views_mobile,
                        'lastPage' => $tenders->lastPage(),
                    ];
                }
            }
        }

        foreach ($FormController->get_region_data() as $one)
            $regions[$one['id']] = $one['name'];

        setlocale(LC_ALL, "en_US.UTF-8");
        asort($regions, SORT_LOCALE_STRING);

        $data = [
            'tenders' => $tenders,
            'dataStatus' => $dataStatus,
            'monitor' => $monitor,
            'regions' => $regions,
            'formType' => $formType,
            'withParams' => count($request->all()),
        ];

        return $this->render('pages/monitoring', $data);
    }

    public function ngo(\Illuminate\Http\Request $request, $slug, $formType = 'F201')
    {
        $is_ajax = $request->method() == 'POST';

        $ngo = App\Models\NgoProfile::findBySlug($slug);

        if(!$ngo) {
            abort(404);
        }

        if($ngo->is_closed && (empty($this->user->ngo) || $ngo->id != $this->user->ngo->id)) {
            abort(404);
        }

        if(!empty($this->user->ngo)) {
            $this->user->__moder_forms = $ngo->id == $this->user->ngo->id || !empty($this->user->superadmin);
        }

        if($formType == 'moderation' && empty($this->user->__moder_forms)) {
            abort(404);
        }

        if(!$is_ajax) {
            $ngo->count_authors_posts = $ngo->authors_posts();
            $ngo->logo = $ngo->show_logo();
            $ngo->badges = $ngo->getBadges();
            $ngo->additional = $ngo->getAdditionalData();
        }

        $params = $request->all();
        $tenders = new Collection([]);
        $FormController = app('App\Http\Controllers\FormController');
        $regions = $dataStatus = [];
        $forms = new Collection([]);

        if($is_ajax) {
            if ($request->has('edrpou')) {
                $edrpos[] = $request->get('edrpou');

                if ($customer = App\Customer::where('main_edrpou', $request->get('edrpou'))->first()) {
                    $customer->translate();
                    $tpm = explode("\n", str_replace("\r", '', trim($customer->edrpou)));
                    $edrpos = array_merge($edrpos, $tpm);
                }

                $params['edrpou'] = $edrpos;
            }
            if ($request->has('cpv')) {
                $_cpv = $request->get('cpv');

                if (!is_numeric(str_replace('-', '', $_cpv))) {

                    $CController = app('App\Http\Controllers\FormController');
                    $cpvs = $CController->getCpv();

                    $cpv = array_where($cpvs, function ($key, $ar) use ($_cpv) {
                        return $ar['id'] == $_cpv || $ar['name'] == $_cpv;
                    });

                    if (!empty($cpv)) {
                        $_cpv = current($cpv)['id'];
                    }
                }

                $params['cpv'] = $_cpv;
            }

            $params['date_from'] = $request->has('date_from') ? $request->get('date_from') : null;
            $params['date_to'] = $request->has('date_to') ? $request->get('date_to') : null;

            if ($formType != 'region') {

                $params['ngo'] = null;
                $params['paginate'] = false;
                $params['addSelect']['fields'] = ['id', 'tender'];
                $params['withForm'] = 'F113';

                $tenders2 = App\JsonForm::getNgoReviews($params);

                unset($params['withForm']);
                unset($params['addSelect']['fields']);

                $params['exclude'] = !$tenders2->isEmpty() ? $tenders2->lists('tender') : [];

                $params['formStatus'] = $formType;
                $params['paginate'] = 10;
                $params['ngo'] = true;
                $params['ngo_profile'] = $ngo->id;
                $params['addSelect']['*'] = 1;
                $params['addSelect']['lastDate'] = 1;
                //$params['addSelect']['lastSchema'] = 1;
                $params['addSelect']['payload'] = 1;
                $params['addSelect']['formType'] = $formType;
                //$params['addSelect']['withoutForm'] = 'F101';
                //$params['withoutForm'] = 'F101';
                $tenders = App\JsonForm::getNgoReviews($params);

                if(!$tenders->isEmpty() && $formType != 'F204') {
                    $forms = App\JsonForm::byTenderId($tenders->lists('tender')->toArray())->ByForm(true)->get();
                    $tenders->each(function ($item, $key) use($forms) {
                        $item->show_forms2($forms, $item->tender, 'F201');
                        $item->show_forms2($forms, $item->tender, 'F202');
                        $item->show_forms2($forms, $item->tender, 'F203');
                        return $item;
                    });
                }
            } elseif ($formType == 'region') {
                $params['ngo'] = true;
                $params['ngoWithForm'] = 'F113';
                $params['user'] = [];
                $params['paginate'] = false;
                $params['addSelect']['fields'] = ['id', 'tender'];

                $tenders2 = App\JsonForm::getNgoReviews($params);

                //unset($params['withForm']);
                $params['paginate'] = 10;
                $params['addSelect']['*'] = 1;
                $params['addSelect']['lastDate'] = 1;
                $params['ngo'] = null;
                //$params['withoutForm'] = 'F101';
                $params['exclude'] = !$tenders2->isEmpty() ? $tenders2->lists('tender') : [];
                $params['region'] = isset($params['region']) && $params['region'] ? $params['region'] : $ngo->region;
                unset($params['addSelect']['fields']);

                $tenders = $params['region'] ? App\JsonForm::getNgoReviews($params) : new Collection([]);
            }
        }

        foreach($FormController->get_status_data() as $one)
            $dataStatus[$one['id']] = $one['name'];

        if(!$is_ajax) {
            foreach ($FormController->get_region_data() as $one)
                $regions[$one['id']] = $one['name'];

            setlocale(LC_ALL, "en_US.UTF-8");
            asort($regions, SORT_LOCALE_STRING);
        }

        if($is_ajax) {

            $views = '';
            $views_mobile = '';

            if(!$tenders->isEmpty()) {
                foreach ($tenders AS $item) {
                    if(!$item->skip) {
                        $views .= view('partials/_search_tender_ngo', [
                            'item' => $item->get_tender_json(),
                            'tender' => $item,
                            'for_mobile' => false,
                            'dataStatus' => $dataStatus,
                            'formType' => $formType,
                            'forms' => $forms,
                        ])->render();

                        $views_mobile .= view('partials/_search_tender_ngo', [
                            'item' => $item->get_tender_json(),
                            'tender' => $item,
                            'for_mobile' => true,
                            'dataStatus' => $dataStatus,
                            'formType' => $formType,
                            'forms' => $forms,
                        ])->render();
                    }
                }
            }

            return [
                'desktop' => $views,
                'mobile' => $views_mobile,
                'lastPage' => $tenders->lastPage(),
            ];
        }

        $data = [
            'forms' => $forms,
            'tenders' => $tenders,
            'dataStatus' => $dataStatus,
            'ngo' => $ngo,
            'regions' => $regions,
            'formType' => $formType,
            'withParams' => count($request->all()),
            'showBadges' => true
        ];

        return $this->render('pages/ngo', $data);
    }

    public function multy_form(\Illuminate\Http\Request $request, $form, $tender_ids = null)
    {
        if(!$tender_ids || !in_array($form, ['F201', 'F202', 'F203'])) {
            return redirect()->back();
        }

        if(!$this->user->ngo) {
            return redirect()->back();
        }

        $ngo_form_data=[];
        $update_field=@Settings::instance('perevorot.dozorro.form')->{$form.'_field'};
        $update_code_field=@Settings::instance('perevorot.dozorro.form')->{$form.'_code_field'};

        if(!empty($update_field) && !empty($update_code_field))
        {
            $ngo_form_data[$form]=Helpers::parseSettings(Settings::instance('perevorot.dozorro.form')->{$form}, true);
        }

        $parents=[];
        $tender_ids_array=explode(',', $tender_ids);

        if($form=='F202') {
            $parents=DB::table('perevorot_dozorro_json_forms')->select('object_id')->where(function($q) use ($tender_ids_array) {
                foreach($tender_ids_array as $tender_id) {
                    $q->orWhere('tender_json', 'LIKE', '%"tenderID":"'.trim($tender_id).'"%');
                }
            })
            ->where('ngo_profile_id', $this->user->ngo->id)
            ->where('schema', 'F201')
            ->where('is_hide', 0)
            ->lists('object_id');
        }elseif($form=='F203') {
            $parents=DB::table('perevorot_dozorro_json_forms')->select('object_id')->where(function($q) use ($tender_ids_array) {
                foreach($tender_ids_array as $tender_id) {
                    $q->orWhere('tender_json', 'LIKE', '%"tenderID":"'.trim($tender_id).'"%');
                }
            })
            ->where('ngo_profile_id', $this->user->ngo->id)
            ->where('schema', 'F201')
            ->where('is_hide', 0)
            ->lists('object_id');

            if(!empty($parents)) {
                $parents=DB::table('perevorot_dozorro_json_forms')->select('object_id')->where(function($q) use ($tender_ids_array) {
                    foreach($tender_ids_array as $tender_id) {
                        $q->orWhere('tender_json', 'LIKE', '%"tenderID":"'.trim($tender_id).'"%');
                    }
                })
                ->where(function($q) use ($parents) {
                    foreach($parents as $parent) {
                        $q->orWhere('payload', 'LIKE', '%"parentForm":"'.trim($parent).'"%');
                    }
                })
                ->where('ngo_profile_id', $this->user->ngo->id)
                ->where('schema', 'F202')
                ->lists('object_id');
            }
        }

        if($form!='F201' && empty($parents)) {
            return redirect()->back();
        }

        $data = [
            'multy' => true,
            'ngo_form_data' => $ngo_form_data,
            'form' => $form,
            'rating' => 0,
            'tender_ids' => $tender_ids,
            'parents' => $parents,
            'return_back' => $request->header('referer') ? $request->header('referer') : '/tender/search',
        ];

        return $this->render('pages/forms/index', $data);
    }

    public function tender_form(\Illuminate\Http\Request $request, $id, $form, $parentForm = null)
    {
        if (!$this->user->ngo) {
            return redirect()->back();
        }
        elseif(!is_numeric($parentForm)) {
            if (!$request->has('parents') && $form != 'F201') {
                return redirect()->back();
            }
        }

        $item = $this->tender_parse($id);

        $ngo_form_data = [];
        $update_field = @Settings::instance('perevorot.dozorro.form')->{$form . '_field'};
        $update_code_field = @Settings::instance('perevorot.dozorro.form')->{$form . '_code_field'};

        if (!empty($update_field) && !empty($update_code_field)) {
            $ngo_form_data[$form] = Helpers::parseSettings(Settings::instance('perevorot.dozorro.form')->{$form},
                true);
        }

        $FormController = app('App\Http\Controllers\FormController');
        $dataStatus = [];

        foreach ($FormController->get_status_data() as $one) {
            $dataStatus[$one['id']] = $one['name'];
        }

        if(!is_numeric($parentForm)) {
            $parents = $request->has('parents') ? $request->get('parents') : [$parentForm];

            $data = [
                'selected' => '',
                'multy' => false,
                'item' => $item,
                'ngo_form_data' => $ngo_form_data,
                'form' => $form,
                'rating' => 0,
                'dataStatus' => $dataStatus,
                'parents' => $parents,
                'edit' => false,
                'comment' => '',
                'return_back' => route('page.tender_by_id', ['id' => $item->tenderID]),
            ];
        } else {

            $_form = App\JsonForm::find($parentForm);

            if(empty($_form)) {
                abort(404);
            }
            elseif($_form->status != 2) {
                abort(403);
            }

            $json = $_form->json;
            $selected = $_form->schema == 'F202' ? $json->actionCode : '';
            $selected = $_form->schema == 'F203' ? $json->resultCode : $selected;

            //$ngo_form_data[$form] = array_filter(array_flip($ngo_form_data[$form]), function($v) use($json, $_form) {
               // return ($_form->schema == 'F202' && $v == $json->actionCode) || ($_form->schema == 'F203' && $v == $json->resultCode);
            //});
            //$ngo_form_data[$form] = array_flip($ngo_form_data[$form]);

            $data = [
                'selected' => $selected,
                'multy' => false,
                'item' => $item,
                'ngo_form_data' => $ngo_form_data,
                'form' => $form,
                'rating' => 0,
                'dataStatus' => $dataStatus,
                'parents' => [],
                'edit' => $_form->id,
                'comment' => $_form->schema == 'F202' ? $json->actionComment : $json->resultComment,
                'return_back' => route('page.ngo', ['slug'=>$this->user->ngo->slug,'formType'=>'moderation']),
            ];
        }

        return $this->render('pages/forms/index', $data);
    }

    public function tender($id)
    {        
        $agent = new Agent();

        if($agent->isMobile()) {
            return $this->render('pages/tender', ['sidebarMode'=>true]);
        }

        $admins = DB::table('backend_users')->lists('email');
        $dataStatus=[];

        foreach(app('App\Http\Controllers\FormController')->get_status_data() as $one)
            $dataStatus[$one['id']]=$one['name'];

        $item=$this->tender_parse($id);

        $riskAccess = @App\Settings::instance('perevorot.dozorro.custom_setting', 'Perevorot\Dozorro\Models\CustomSetting')->risk_in_search;
        $riskInLots = @App\Settings::instance('perevorot.dozorro.custom_setting', 'Perevorot\Dozorro\Models\CustomSetting')->risk_in_lots;

        if(!empty($this->user->is_tender_risks) || !empty($riskAccess)) {
            $item->__risks = $this->getRisks($item);
        }

        $item->riskScore = @$item->dozorro->riskScore;
        $item->riskScoreHalf = !empty($item->riskScore) ? round($item->riskScore, 1) : 0;
        $item->__rating = !empty($item->riskScore) ? round($item->riskScore*10, 1) : 0;

        //dd($item->riskScoreHalf, $item->__rating);

        $f200_reviews = App\JsonForm::where('tender', $item->id)
            ->where('model', '=', 'form')
            ->where('schema', 'LIKE', 'F20%')
            //->whereIn('status', [1,2])
            ->orderBy('date', 'DESC')
            ->get();

        Helpers::parseUserData($f200_reviews);
        Helpers::parseUserRelationData($f200_reviews);

        $reviews = App\JsonForm::where('tender', $item->id)
            ->where('model', '=', 'form')
            ->where('schema', 'NOT LIKE', 'F20%')
            ->orderBy('date', 'DESC')
            ->get();

        foreach($reviews as $review) {
            $review->ngo_work = false;

            foreach($f200_reviews as $freview) {
                if ($review->object_id == $freview->json_parent_form) {
                    $review->ngo_work = true;
                    break;
                }
            }
        }

        Helpers::parseUserData($reviews);
        Helpers::parseUserRelationData($reviews);

        $review_comments=App\JsonForm::where('schema', 'comment')
            ->whereIn('thread', array_pluck($reviews, 'object_id'))
            ->get();

        Helpers::parseUserData($review_comments);
        Helpers::parseUserRelationData($review_comments);

        $review_comments_by_object_id=[];

        foreach($review_comments as $comment) {
            if(empty($review_comments_by_object_id[$comment->thread])){
                $review_comments_by_object_id[$comment->thread]=[];
            }

            $review_comments_by_object_id[$comment->thread][]=$comment;
        }

        foreach($reviews as $review) {
            $review->__comments=!empty($review_comments_by_object_id[$review->object_id]) ? $review_comments_by_object_id[$review->object_id] : [];
        }

        if(isset($_GET['local_dump']) && getenv('APP_ENV')=='local') {
            dd($reviews, $f200_reviews);
        }

        $reviews_total=sizeof($reviews);

        $generalReviews = array_where($reviews, function($key, $review){
            return $review->schema == 'F101';
        });

        $score = 0;
        $rating = 0;

        foreach($generalReviews as $review) {
            $score+=(isset($review->json->overallScore) ? $review->json->overallScore : $review->json->generalScore);
        };

        if($generalReviews && sizeof($generalReviews)>0){
            $rating=round(
                $score / sizeof($generalReviews)
            );
        }

        $all_reviews=$reviews;

        $reviews = (new App\Classes\Reviews($reviews))->getReviews();

        $ngo_form_data=[];

        foreach(['F201', 'F202', 'F203'] as $code)
        {
            $update_field=@Settings::instance('perevorot.dozorro.form')->{$code.'_field'};
            $update_code_field=@Settings::instance('perevorot.dozorro.form')->{$code.'_code_field'};

            if(!empty($update_field) && !empty($update_code_field))
            {
                $ngo_form_data[$code]=Helpers::parseSettings(Settings::instance('perevorot.dozorro.form')->{$code}, true);
            }
        }

        $f200_reviews_new = [];
        $last_form = null;

        if(!$f200_reviews->isEmpty()) {

            $users = $f200_reviews->lists('region', 'ngo_profile_id');
            $last_form = null;

            // group by ngo

            foreach($users as $id => $region) {
                if($ngo_profile = App\Models\NgoProfile::find($id)) {
                    //$f200_reviews_new[$ngo_profile->id]['__moder_forms'] = $ngo_profile->id == $this->user->ngo->id || !empty($this->user->superadmin);
                    $f200_reviews_new[$ngo_profile->id]['ngo'] = $ngo_profile;
                    $f200_reviews_new[$ngo_profile->id]['ngo']->badges = $ngo_profile->getBadges();

                    $array = array_where($f200_reviews, function($k, $v) use($region, $ngo_profile) {
                        return $ngo_profile->id == $v->ngo_profile_id;
                    });

                    if(!empty($array)) {

                        if(!isset($f200_reviews_new[$ngo_profile->id]['data'])) {
                            $f200_reviews_new[$ngo_profile->id]['data'] = [];
                        }

                        $f200_reviews_new[$ngo_profile->id]['data'] = array_merge($f200_reviews_new[$ngo_profile->id]['data'], $array);
                    }
                }
            }

            if(isset($_GET['ngo_dump']) && getenv('APP_ENV')=='local') {
                dd($f200_reviews, $users, $f200_reviews_new);
            }

            // check to closed before F204

            foreach($f200_reviews_new as $ngo => $v) {

                $f204 = '';

                // grab data

                foreach($v['data'] as $k => $r) {

                    if($r->schema == 'F204') {
                        if(!$f204 || $f204->date < $r->date) {
                            $f204 = $r;
                        }
                    }

                    if(!$last_form || $last_form->date < $r->date) {
                        $last_form = $r;
                    }

                    $f200_reviews_new[$ngo]['data'][$k]->closed = false;
                }

                $close_all = false;

                // close all or until to F204 if last F204 has parent 201

                if($last_form->schema == 'F204') {
                    $close_all = array_first($v['data'], function ($rk, $r) use ($last_form) {
                        return $r->schema == 'F201' && $r->object_id == $last_form->jsonParentForm;
                    });

                    if($close_all) {
                        $v['data'] = array_map(function($r) {
                            $r->closed = true;
                            return $r;
                        }, $v['data']);
                    }
                } elseif($f204) {
                    $close_all = array_first($v['data'], function ($rk, $r) use ($f204) {
                        return $r->schema == 'F201' && $r->object_id == $f204->jsonParentForm;
                    });

                    if($close_all) {
                        $v['data'] = array_map(function($r) use($f204) {
                            $r->closed = $r->date < $f204->date ? true : false;
                            return $r;
                        }, $v['data']);
                    }
                }

                if($close_all) {

                    if($last_form->schema == 'F204') {
                        $f200_reviews_new[$ngo]['f204'] = $f204;
                    }

                    foreach ($v['data'] as $f2k => $f2) {
                        if ($f2->closed && $f2->schema == 'F202') {

                            $close_f202 = false;

                            foreach ($v['data'] as $f4k => $f4) {
                                if ($f4->schema == 'F204' && $f2->object_id == $f4->jsonParentForm) {
                                    $close_f202 = $f4;
                                    break;
                                }
                            }

                            if ($close_f202) {
                                $v['data'][$f2k]->f204 = $close_f202;
                            }
                        }
                    }
                }
                elseif(!$close_all)
                {
                    // closed until F204 if parent F201
                    foreach ($v['data'] as $rk => $r) {
                        if (!$r->closed && $r->schema == 'F204') {

                            $f201 = null;

                            foreach ($v['data'] as $rk2 => $r2) {
                                if ($r2->schema == 'F201' && $r2->object_id == $r->jsonParentForm) {
                                    $f201 = $r;
                                    break;
                                }
                            }

                            if ($f201 !== null) {
                                foreach ($v['data'] as $rk2 => $r2) {
                                    if($r2->date <= $f201->date) {
                                        $r2->closed = true;
                                    }
                                }
                            }
                        }
                    }

                    // closed only F202
                    foreach ($v['data'] as $f2k => $f2) {
                        if (!$f2->closed && $f2->schema == 'F202') {

                            $close_f202 = false;

                            foreach ($v['data'] as $f4k => $f4) {
                                if ($f4->schema == 'F204' && $f2->object_id == $f4->jsonParentForm) {
                                    $close_f202 = $f4;
                                    break;
                                }
                            }

                            if ($close_f202) {
                                $v['data'][$f2k]->closed = true;
                                $v['data'][$f2k]->f204 = $close_f202;
                            }
                        }
                    }

                    // closed F201 if all F202 are closed
                    $_f202 = array_where($v['data'], function ($kd, $vd) {
                        return $vd->schema == 'F202' && !$vd->closed;
                    });

                    if(empty($_f202)) {

                        $_f201 = array_last($v['data'], function ($kd, $vd) use($f204, $v) {
                            return $vd->schema == 'F201' &&
                                array_last($v['data'], function ($kd2, $vd2) use($vd, $f204) {
                                    return $vd2->schema == 'F202' && $vd2->jsonParentForm == $vd->object_id && $vd2->object_id == $f204->jsonParentForm;
                                });
                        });

                        if($_f201) {
                            $slug = 'F204';
                            $jsonFormPath = public_path() . '/sources/forms/F204.json';
                            $jsonForm = new \App\Classes\JsonForm($jsonFormPath, $slug);

                            $FormController=app('App\Http\Controllers\JsonFormController');
                            $response = $FormController->payload2($slug, 'form', $jsonForm, $_f201->object_id, $item, $f204);

                            if($response) {

                                $f200_reviews_new[$ngo]['f204'] = $f204;

                                $v['data'] = array_map(function($r) {
                                    $r->closed = true;
                                    return $r;
                                }, $v['data']);
                            }
                        }
                    }
                }

                $f200_reviews_new[$ngo]['close_all'] = count($v['data']) == count(array_where($v['data'], function ($kd, $vd) {
                    return $vd->closed;
                }));

                $f200_reviews_new[$ngo]['last_f201'] = array_first($v['data'], function ($kd, $vd) {
                    return $vd->schema == 'F201';
                });
                
                $f201_ids = array_where($v['data'], function ($kd, $vd) {
                    return $vd->schema == 'F201';
                });

                $f201_ids = array_map(function($__v) {
                    return $__v->object_id;
                }, $f201_ids);

                $f200_reviews_new[$ngo]['comments'] = App\JsonForm::where('tender', $item->id)
                    ->where('model', '=', 'comment')
                    ->where('schema', 'comment')
                    ->whereIn('thread', $f201_ids)
                    ->orderBy('date', 'DESC')
                    ->get();

                $v['data'] = array_map(function($r) {
                    $r->issetComment = $r->isset_comment() || ($r->closed && $r->f204);
                    return $r;
                }, $v['data']);
            }

            $last_form = $last_form ? $last_form->schema : null;

            if(isset($_GET['total_ngo_dump']) && getenv('APP_ENV')=='local') {
                dd($f200_reviews_new);
            }
        }
        //dd($f200_reviews_new);
        $data = [
            'last_form' => $last_form,
            'item' => $item,
            'lot_id' => Input::get('lot_id'),
            'back' => starts_with(Request::server('HTTP_REFERER'), env('ROOT_URL').'/search') ? Request::server('HTTP_REFERER') : false,
            'dataStatus' => $dataStatus,
            'error' => $this->error,
            'reviews' => $reviews,
            'all_reviews' => $all_reviews,
            'reviews_total' => $reviews_total,
            'rating' => $rating,
            'areas' => $this->getAreas(),
            'ngo_form_data' => $ngo_form_data,
            'f200_reviews'=>$f200_reviews_new,
            'is_admin' => ($this->user && $this->user->email && !empty($admins) && in_array($this->user->email, $admins)),
            'sidebarMode'=>false,
            'riskAccess' => $riskAccess,
            'riskInLots' => $riskInLots,
        ];

        return $this->render('pages/tender', $data);
    }

    var $error;

    public function tender_parse($id, $item = null, $fromIndex = true)
    {
        $this->search_type='tender';
        $this->error=false;

        if($fromIndex) {
            $json = $this->getSearchResults(['tid=' . $id]);
            $item = false;

            if ($json) {
                $data = json_decode($json);

                if (empty($data->error)) {
                    $item = array_first($data->items, function ($k, $one) use ($id) {
                        return $one->tenderID === $id || env('API_PRETEND');
                    });
                } else {
                    $this->error = $data->error;
                }

                if (!$item) {
                    $this->error = 'Тендер не найден';
                }

                if ($this->error) {
                    $data = [
                        'item',
                        false,
                        'error',
                        $this->error
                    ];

                    //print_r($data);exit;

                    return $this->render('pages/tender', $data);
                }
            }
        }

        $dates = [];
        $address = [];

        foreach($item->items as &$v) {
            $v->__format_delivery_date = null;
            
            if(!empty($v->deliveryDate->endDate)) {

                if(!isset($dates[$v->deliveryDate->endDate])) {
                    $dates[$v->deliveryDate->endDate] = 1;
                } else {
                    $dates[$v->deliveryDate->endDate] += 1;
                }

                $v->__format_delivery_date = Carbon::createFromTimestamp(strtotime($v->deliveryDate->endDate))->format('d.m.Y');
            }

            $info = [];

            if(!empty($v->deliveryAddress->locality)) {
                $info[] = $v->deliveryAddress->locality;
            }
            if(!empty($v->deliveryAddress->region)) {
                $info[] = $v->deliveryAddress->region;
            }
            if(!empty($v->deliveryAddress->streetAddress)) {
                $info[] = $v->deliveryAddress->streetAddress;
            }

            if(!empty($info)) {
                $v->__address = implode(', ', $info);

                if(!isset($address[$v->__address])) {
                    $address[$v->__address] = 1;
                } else {
                    $address[$v->__address] += 1;
                }
            }
        }

        if(!empty($address)) {
            $item->__items_address = array_flip(array_unique($address))[max($address)];
        }
        if(!empty($dates)) {
            $item->__items_deliveryDate = Carbon::createFromTimestamp(strtotime(array_flip(array_unique($dates))[max($dates)]))->format('d.m.Y');
        }

        if(!empty($item->value->amount)) {
            $item->__full_formated_price = number_format($item->value->amount, 0, '',
                    ' ') . ' ' . $item->value->currency . ' ' . (t($item->value->valueAddedTaxIncluded ? 'tender.with_VAT' : 'tender.without_VAT'));
        } else {
            $item->__full_formated_price = t('tender.empty_price');
        }

        $dataStatus = [];

        foreach(app('App\Http\Controllers\FormController')->get_status_data() as $one)
            $dataStatus[$one['id']]=$one['name'];

        $this->statuses = $dataStatus;

        if(isset($dataStatus[$item->status])) {
            $item->__status_name = $dataStatus[$item->status];
        } else {
            $item->__status_name = $item->status;
        }

        if(empty($item->procurementMethodType))
        {
            $item->procurementMethodType=new \StdClass();
            $item->procurementMethodType='';
        }

        $this->get_print_href($item);
        $this->get_multi_lot($item);
        $this->get_single_lot($item);
        $this->get_eu_lots($item);

        if(!empty($item->awards))
        {
            usort($item->awards, function ($a, $b)
            {
                $datea = new DateTime($a->date);
                $dateb = new DateTime($b->date);

                return $datea>$dateb;
            });
        }

        $features_price=1;

        if(!empty($item->features))
        {
            $tender_features = [];

            foreach($item->features as $key => $feature) {
                if($feature->featureOf=='item' || $feature->featureOf=='tenderer' || (!empty($item->lots) && sizeof($item->lots)==1 && $feature->featureOf=='lot')) {
                    $tender_features[] = $feature;
                }
            }

            /*
            $tender_features=array_where($item->features, function($key, $feature) use ($item){
                return $feature->featureOf=='item' || $feature->featureOf=='tenderer' || (!empty($item->lots) && sizeof($item->lots)==1 && $feature->featureOf=='lot');
            });*/
        }

        if(!empty($tender_features))
        {
            $enums = 0;

            foreach($tender_features as $k=>$feature)
            {
                if(!empty($feature->enum)) {
                    $tender_features[$k]->__enum_titles = implode(', ', array_column($feature->enum, 'title'));
                    $tender_features[$k]->__enum_values = implode(', ', array_column($feature->enum, 'value'));
                    $enums += count($feature->enum);
                }

                $max=0;

                foreach($feature->enum as $one)
                    $max=max($max, floatval($one->value));

                $tender_features[$k]->max = new \stdClass();
                $tender_features[$k]->max=$max;

                $features_price-=$max;

                usort($feature->enum, function ($a, $b)
                {
                    return strcmp($b->value, $a->value);
                });

                $tender_features[$k]->enum=$feature->enum;
            }

            $item->__features=$tender_features;
            $item->__enums=$enums;
        }

        if(!empty($item->lots) && sizeof($item->lots)==1 && !empty($item->bids))
        {
            foreach($item->bids as $k=>$one)
            {
                $item->bids[$k]->value=new \StdClass();
                $item->bids[$k]->value=!empty($one->lotValues) ? head($one->lotValues)->value : 0;
            }
        }

        if(!$item->__isMultiLot)
        {
            if($features_price<1 && !empty($item->bids))
            {
                foreach($item->bids as $k=>$bid)
                {
                    $item->bids[$k]->__featured_coef=new \StdClass();
                    $item->bids[$k]->__featured_coef=null;

                    $item->bids[$k]->__featured_price=new \StdClass();
                    $item->bids[$k]->__featured_price=null;

                    if(!empty($bid->parameters))
                    {
                        $featured_coef=trim(number_format(1+array_sum(array_pluck($bid->parameters, 'value'))/$features_price, 10, '.', ' '), '.0');

                        $item->bids[$k]->__featured_coef=$featured_coef;
                        $item->bids[$k]->__featured_price=isset($bid->value->amount) ? str_replace('.00', '', number_format($bid->value->amount/$featured_coef, 2, '.', ' ')) : 0;
                    }
                }
            }

            if($features_price<1 && !empty($item->bids))
            {
                usort($item->bids, function ($a, $b)
                {
                    return floatval($a->__featured_price)>floatval($b->__featured_price);
                });
            }
            elseif(!empty($item->bids))
            {
                usort($item->bids, function ($a, $b) use ($features_price)
                {
                    return empty($a->value) || empty($b->value) || (floatval($a->value->amount)>floatval($b->value->amount));
                });
            }
        }

        $item->__features_price=new \StdClass();
        $item->__features_price=$features_price;
        $item->__features_price_real=$features_price*100;

        if($item->__isMultiLot) {
            $item->__tender_price = array_sum(array_map(function($lot) {
                return $lot->status == 'active' ? $lot->value->amount : 0;
            }, (array)$item->lots));
        }

        $this->parse_eu($item);

        $item->__icon=new \StdClass();
        $item->__icon=starts_with($item->tenderID, 'ocds-random-ua')?'pen':'mouse';

        if(!$this->modifiers) {
            $this->modifiers=[
                'get_active_apply',
                'get_contracts',
                'get_contracts_changes',
                'get_contracts_ongoing',
                'get_signed_contracts',
                'get_initial_bids',
                'get_initial_bids_dates',
                'get_yaml_documents',
                'get_tender_documents',
                'get_bids',
                'get_awards',
                'get_uniqie_awards',
                'get_uniqie_bids',
                'get_claims',
                'get_complaints',
                'get_opened_questions',
                'get_opened_claims',
                'get_questions',
                'get_qualifications',
                'get_lots',
                'get_procedure',
                'get_open_title',
                'parse_is_sign',
                'get_cancellations',
                'get_action_url_singlelot',
                'get_auction_period',
                'get_button_007',
                'get_stage2TenderID',
                'get_stage1TenderID',
            ];
        }else{
            $this->modifiers=array_merge($this->commonModifiers, $this->modifiers);
        }

        foreach($this->modifiers as $modifier) {
            if($modifier=='get_contracts'){
                $this->{$modifier}($item, !empty($item->contracts) ? $item->contracts : false, false, $fromIndex);
            }elseif($modifier=='get_contracts_changes'){
                $this->{$modifier}($item, !empty($item->__contracts) ? $item->__contracts : false, false, $fromIndex);
            }elseif($modifier=='get_contracts_ongoing'){
                $this->{$modifier}($item, !empty($item->__contracts) ? $item->__contracts : false);
            }elseif($modifier=='get_lots'){
                $this->{$modifier}($item, $dataStatus, $fromIndex);
            }elseif($modifier=='get_button_007'){
                $this->{$modifier}($item, $item->procuringEntity);;
            }else{
                $this->{$modifier}($item);
            }
        }

        // $this->get_active_apply($item);
        // $this->get_contracts_changes($item, !empty($item->__contracts) ? $item->__contracts : false, $fromIndex);
        // $this->get_contracts_ongoing($item, !empty($item->__contracts) ? $item->__contracts : false);
        // $this->get_signed_contracts($item);
        // $this->get_initial_bids($item);
        // $this->get_initial_bids_dates($item);
        // $this->get_yaml_documents($item);
        // $this->get_tender_documents($item);
        // $this->get_bids($item);
        // $this->get_awards($item);
        // $this->get_uniqie_awards($item);
        // $this->get_uniqie_bids($item);
        // $this->get_claims($item);
        // $this->get_complaints($item);
        // $this->get_opened_questions($item);
        // $this->get_opened_claims($item);
        // $this->get_questions($item);
        // $this->get_qualifications($item);
        // $this->get_lots($item, $dataStatus, $fromIndex);
        // $this->get_procedure($item);
        // $this->get_open_title($item);
        // $this->parse_is_sign($item);
        // $this->get_cancellations($item);
        // $this->get_action_url_singlelot($item);
        // $this->get_auction_period($item);
        // $this->get_button_007($item, $item->procuringEntity);
        // $this->get_stage2TenderID($item);
        // $this->get_stage1TenderID($item);

        $item->procuringEntity->__address=implode(', ', (array)$item->procuringEntity->address);
        $item->procuringEntity->__contactPoint=implode(', ', (array)$item->procuringEntity->contactPoint);

        if(isset($_GET['dump']) && getenv('APP_DEBUG'))
            dd($item);

        return $item;
    }

    public function get_auction_period(&$item)
    {
        if(!empty($item->lots) && sizeof($item->lots)==1 && !empty($item->lots[0]->auctionUrl) && empty($item->auctionPeriod))
        {
            $item->auctionPeriod=new \StdClass();
            $item->auctionPeriod=$item->lots[0]->auctionPeriod;
        }
    }

    public function get_print_href(&$item)
    {
        $item->__print_href=new \StdClass();
        $item->__print_href=false;

        if(!empty($item->procurementMethodType))
        {
            if(in_array($item->procurementMethod, ['open', 'selective']) && $item->procurementMethodType!='belowThreshold')
                $item->__print_href='open';

            if($item->procurementMethod=='limited' && $item->procurementMethodType!='reporting')
                $item->__print_href='limited';

            if($item->procurementMethod=='limited' && $item->procurementMethodType=='reporting')
                $item->__print_href='limited-reporting';
        }
    }
    
    public function getSearchResults($query)
    {
        if(env('API_PRETEND'))
            return file_get_contents('./sources/pretend/tender.json');

        $request = head(\Symfony\Component\HttpFoundation\Request::createFromGlobals()->server);
        $page = $request['REQUEST_SCHEME'].'://'.$request['HTTP_HOST'].$request['REQUEST_URI'].$request['QUERY_STRING'];

        $url=Config::get('api.'.$this->search_type).'?'.implode('&', $query).'&__url='.$page;

        if(isset($_GET['api']) && getenv('APP_ENV')=='local')
            dump($url);

        //$header=get_headers($url)[0];

        //if(strpos($header, '200 OK')!==false)
        //{
            $ch=curl_init();

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_URL, $url);

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
        //}
        //else
        //{
        //    $result=json_encode([
        //        'error' => $header
        //    ], JSON_UNESCAPED_UNICODE);
        //}

        if(isset($_GET['api']) && getenv('APP_ENV')=='local')
            dd(json_decode($result));

        Log::info(__FUNCTION__.': '.$url);

        return $result;
    }

    private function get_active_apply(&$item)
    {
        $item->__is_apply=new \stdClass();
        $item->__is_apply=in_array($item->status, ['active.enquiries', 'active.tendering']);

        $platforms=[];

        if($item->procurementMethodType=='belowThreshold')
        {
            $platforms=array_where(Config::get('platforms'), function($key, $platform){
                return $platform['level2']==true;
            });
        }
        elseif($item->procurementMethodType!='belowThreshold' && $item->procurementMethodType!='reporting')
        {
            $platforms=array_where(Config::get('platforms'), function($key, $platform){
                return $platform['level4']==true;
            });
        }

        shuffle($platforms);

        $item->__is_apply_platforms=$platforms;
    }
    
    private function get_initial_bids(&$item)
    {
        $bidders_by_lot=[];
        $bid_by_bidders=[];

        if(!empty($item->bids))
        {
            foreach($item->bids as $bid)
                $bid_by_bidders[$bid->id]=0;
        }

        if(!empty($item->documents))
        {
            foreach($item->documents as $document)
            {
                if(pathinfo($document->title, PATHINFO_EXTENSION)=='yaml' && !empty($document->url))
                {
                    try
                    {
                        $yaml=Cache::remember('yaml_'.md5($document->url), 60, function() use ($document){
                            $yaml_file=@file_get_contents($document->url);

                            return !empty($yaml_file) ? Yaml::parse($yaml_file) : [];
                        });

                        if(!empty($yaml['timeline']['auction_start']['initial_bids']))
                        {
                            foreach($yaml['timeline']['auction_start']['initial_bids'] as $bid)
                            {
                                if(!empty($yaml['lot_id']))
                                    $bidders_by_lot[$yaml['lot_id']][$bid['bidder']]=$bid['amount'];

                                $bid_by_bidders[$bid['bidder']]=$bid['amount'];
                            }
                        }
                    }
                    catch (ParseException $e) {}
                }
            }
        }

        $item->__initial_bids=new \StdClass();
        $item->__initial_bids=$bid_by_bidders;

        $item->__initial_bids_by_lot=new \StdClass();
        $item->__initial_bids_by_lot=$bidders_by_lot;
    }

    private function get_initial_bids_dates(&$item)
    {
        $bidders_by_lot=[];
        $bid_by_bidders=[];

        if(!empty($item->bids))
        {
            foreach($item->bids as $bid)
                $bid_by_bidders[$bid->id]=0;
        }

        if(!empty($item->documents))
        {
            foreach($item->documents as $document)
            {
                if(pathinfo($document->title, PATHINFO_EXTENSION)=='yaml' && !empty($document->url))
                {
                    try
                    {
                        $yaml=Cache::remember('yaml_'.md5($document->url), 60, function() use ($document){
                            $yaml_file=@file_get_contents($document->url);

                            return !empty($yaml_file) ? Yaml::parse($yaml_file) : [];
                        });

                        if(!empty($yaml['timeline']['auction_start']['initial_bids']))
                        {
                            foreach($yaml['timeline']['auction_start']['initial_bids'] as $bid)
                            {
                                if(!empty($yaml['lot_id']))
                                    $bidders_by_lot[$yaml['lot_id']][$bid['bidder']]=$bid['date'];

                                $bid_by_bidders[$bid['bidder']]=$bid['date'];
                            }
                        }
                    }
                    catch (ParseException $e) {}
                }
            }
        }

        $item->__initial_bids_dates=new \StdClass();
        $item->__initial_bids_dates=$bid_by_bidders;

        $item->__initial_bids_dates_by_lot=new \StdClass();
        $item->__initial_bids_dates_by_lot=$bidders_by_lot;
    }

    private function get_action_url_singlelot(&$item)
    {
        if(!empty($item->lots) && sizeof($item->lots)==1 && !empty($item->lots[0]->auctionUrl))
        {
            $item->auctionUrl=new \StdClass();
            $item->auctionUrl=$item->lots[0]->auctionUrl;
        }
    }

    private function get_multi_lot(&$item)
    {
        $item->__isMultiLot=new \StdClass();
        $item->__isMultiLot=(!empty($item->lots) && sizeof($item->lots)>1);

        if(!$item->__isMultiLot  && Input::get('lot_id')) {
            header('Location: /tender/'.$item->tenderID);
            exit;
        }
    }

    private function get_single_lot(&$item)
    {
        $item->__isSingleLot=new \StdClass();
        $item->__isSingleLot=(!empty($item->lots) && sizeof($item->lots)==1) || empty($item->lots);
    }

    private function get_opened_questions(&$item)
    {
        $item->__isOpenedQuestions=new \StdClass();
        $item->__isOpenedQuestions=false;

        if(!empty($item->__complaints_claims))
        {
            $claims=array_unique(array_pluck($item->__complaints_claims, 'status'));

            if(sizeof(array_intersect(['claim', 'ignored'], $claims)) > 0)
                $item->__isOpenedQuestions=true;
        }

        if(!$item->__isOpenedQuestions && !empty($item->questions))
        {
            $questions=array_where($item->questions, function($key, $question){
                return empty($question->answer);
            });

            if(sizeof($questions))
                $item->__isOpenedQuestions=true;
        }
    }

    private function get_opened_claims(&$item)
    {
        $item->__isOpenedClaims=new \StdClass();
        $item->__isOpenedClaims=false;

        if(!empty($item->__complaints_complaints))
        {
            $complaints=array_pluck($item->__complaints_complaints, 'status');

            if(sizeof(array_intersect(['pending', 'accepted', 'stopping'], $complaints)) > 0)
                $item->__isOpenedClaims=true;
        }
    }

    private function get_uniqie_awards(&$item)
    {
        $item->__unique_awards=new \StdClass();
        $item->__unique_awards=null;

        if(!empty($item->awards))
        {
            $ids=[];

            foreach($item->awards as $award)
            {
                foreach($award->suppliers as $supplier)
                {
                    array_push($ids, $supplier->identifier->id);
                }
            }

            $ids=array_unique($ids);
            $item->__unique_awards=sizeof($ids);
        }
    }

    public function get_awards(&$item)
    {
        $item->__active_award=new \StdClass();
        $item->__active_award=null;
        $item->__count_unsuccessful_awards=new \StdClass();
        $item->__count_unsuccessful_awards=0;
        $count_unsuccessful_awards=0;
        $count_unsuccessful_awards2=0;

        if(!empty($item->awards))
        {
            foreach($item->awards as $award)
            {
                switch ($award->status) {
                    case 'unsuccessful':
                        $award->__status_name = t('tender.big_status_unsuccessful');
                        break;
                    case 'active':
                        $award->__status_name = t('tender.big_status_active');
                        $award->__status_name_other = t('tender.qualification.status_active');
                        break;
                    case 'pending':
                        $award->__status_name = t('tender.big_status_pending');
                        break;
                    case 'cancelled':
                        $award->__status_name = t('tender.big_status_cancelled');
                        break;
                    default:
                        $award->__status_name = $award->status;
                        break;
                }

                if(!empty($award->documents)) {
                    foreach($award->documents as $document) {
                        $document->__format_date = Carbon::createFromTimestamp(strtotime($document->dateModified))->format('d.m.Y H:i');
                    }
                }

                if(!empty($item->bids)) {
                    $award->__bid = array_first((!empty($item->__bids) ? $item->__bids : $item->bids),
                        function ($bk, $bid) use ($award) {
                            return $bid->id == $award->bid_id;
                        });
                }

                $award->__format_date = Carbon::createFromTimestamp(strtotime($award->date))->format('d.m.Y H:i');

                if(!empty($award->value->amount)) {
                    $award->__full_formated_price = number_format($award->value->amount, 0, '',
                            ' ') . ' ' . $award->value->currency . ' ' . (t($award->value->valueAddedTaxIncluded ? 'tender.with_VAT' : 'tender.without_VAT'));
                } else {
                    $award->__full_formated_price = t('tender.empty_price');
                }

                $contactPoint = [];

                if(!empty($award->suppliers[0])) {
                    $award->__address = implode(', ', (array)$award->suppliers[0]->address);

                    /*if(!empty($award->suppliers[0]->name)) {
                        $contactPoint[] = $award->suppliers[0]->name;
                    }*/

                    $contactPoint[] = $award->suppliers[0]->identifier->id;

                    if(!empty($award->suppliers[0]->contactPoint)) {
                        $contactPoint = array_merge($contactPoint, (array)$award->suppliers[0]->contactPoint);
                    }

                    $award->__contactPoint = implode(', ', $contactPoint);
                }

                if($award->status=='active')
                    $item->__active_award=$award;

                if($award->status=='unsuccessful') {
                    $count_unsuccessful_awards++;
                    $count_unsuccessful_awards2++;
                }

                if($award->status=='cancelled')
                    $count_unsuccessful_awards++;
            }

            if($count_unsuccessful_awards==sizeof($item->awards))
                $item->__unsuccessful_awards=true;

            $item->__count_unsuccessful_awards=$count_unsuccessful_awards2;
        }

        /*
        $work_days=$this->parse_work_days();

        if(!empty($item->__active_award->complaintPeriod->endDate))
        {
            $date=date_create($item->__active_award->complaintPeriod->endDate);
            $sub_days=0;

            if(in_array($item->procurementMethodType, ['aboveThresholdUA', 'aboveThresholdEU', 'negotiation']))
                $sub_days=10;

            elseif(in_array($item->procurementMethodType, ['negotiation.quick']))
                $sub_days=5;

            elseif(in_array($item->procurementMethodType, ['belowThreshold']))
                $sub_days=2;

            if(in_array($item->procurementMethodType, ['belowThreshold', 'aboveThresholdUA.defense']))
            {
                $now=new DateTime();

                for($i=0;$i<$sub_days;$i++)
                {
                    $now->sub(new \DateInterval('P1D'));

                    if(in_array($now->format('Y-m-d'), $work_days))
                    {
                        $i--;
                        $sub_days++;
                    }
                }
            }

        }
        */

        if(!empty($item->__active_award))
            $item->__active_award->__date=date('d.m.Y H:i', strtotime($item->__active_award->date));

        if(!empty($item->__isMultiLot))
            $item->__active_award=null;
    }

    private function get_claims(&$item, $__type='tender', $return=false)
    {
        $__complaints_claims=[];

        foreach(['complaints', 'qualifications/complaints', 'awards/complaints'] as $type)
        {
            $path=explode('/', $type);

            if(!empty($item->{$path[0]}))
            {
                $array=$item->{$path[0]};
                $found_claims=[];

                if(sizeof($path)>1)
                {
                    foreach($array as $item_claim)
                    {
                        if(!empty($item_claim->{$path[1]}))
                            $found_claims=array_merge($found_claims, $item_claim->complaints);
                    }
                }
                else
                    $found_claims=$array;

                $data=array_where($found_claims, function($key, $claim) use($path, $item, $__type){
                    return $claim->type=='claim' && (empty($claim->questionOf) || ($claim->questionOf==$__type || ($claim->questionOf=='lot' && !$item->__isMultiLot)));
                });

                if($data)
                    $__complaints_claims=array_merge($__complaints_claims, $data);
            }

            if($__complaints_claims)
                $__complaints_claims=array_values($__complaints_claims);

            $__complaints_claims=array_where($__complaints_claims, function($key, $claim){
                return $claim->status!='draft';
            });

            foreach($__complaints_claims as $k=>$claim)
            {
                $__complaints_claims[$k]->__documents_owner=empty($claim->documents)?false:array_where($claim->documents, function($key, $document){
                    return in_array($document->author, ['complaint_owner']);
                });

                $__complaints_claims[$k]->__documents_tender_owner=empty($claim->documents)?false:array_where($claim->documents, function($key, $document){
                    return in_array($document->author, ['tender_owner']);
                });

                $__complaints_claims[$k]->__status_name=t('tender.complain_statuses.'.$claim->status);
            }

            if(!$return)
            {
                $item->__complaints_claims=new \StdClass();
                $item->__complaints_claims=$__complaints_claims;
            }
            else
                return $__complaints_claims;
        }
    }

    private function get_complaints(&$item, $__type='tender', $return=false)
    {
        $__complaints_complaints=[];

        foreach(['complaints', 'qualifications/complaints', 'awards/complaints'] as $type)
        {
            $path=explode('/', $type);

            if(!empty($item->{$path[0]}))
            {
                $array=$item->{$path[0]};
                $found_complaints=[];

                if(sizeof($path)>1)
                {
                    foreach($array as $item_complaint)
                    {
                        if(!empty($item_complaint->{$path[1]}))
                            $found_complaints=array_merge($found_complaints, $item_complaint->complaints);
                    }
                }
                else
                    $found_complaints=$array;

                $data=array_where($found_complaints, function($key, $complaint) use($path, $item, $__type){
                    return $complaint->type=='complaint'&& (empty($complaint->questionOf) || ($complaint->questionOf==$__type || ($complaint->questionOf=='lot' && !$item->__isMultiLot)));
                });

                if($data)
                    $__complaints_complaints=array_merge($__complaints_complaints, $data);
            }
        }

        if(sizeof($__complaints_complaints))
        {
            foreach($__complaints_complaints as $k=>$complaint)
            {
                if(!empty($complaint->documents))
                {
                    $__complaints_complaints[$k]->__documents_owner=new \StdClass();
                    $__complaints_complaints[$k]->__documents_owner=array_where($complaint->documents, function($key, $document){
                        return $document->author=='complaint_owner';
                    });

                    $__complaints_complaints[$k]->__documents_reviewer=new \StdClass();
                    $__complaints_complaints[$k]->__documents_reviewer=array_where($complaint->documents, function($key, $document){
                        return in_array($document->author, ['aboveThresholdReviewers', 'reviewers']);
                    });
                }
            }

            $__complaints_complaints=array_values($__complaints_complaints);
        }

        //if(empty($__complaints_complaints->__status_name))
        //{
            foreach($__complaints_complaints as $k=>$complain)
            {
                //if(empty($complain->__status_name))
                //{
                    $key=($item->procurementMethodType!='belowThreshold' ? '!' : '').'belowThreshold';
                    $status_key=$complain->status;

                    if($complain->status=='stopping')
                        $status_key=$complain->status.(!empty($complain->dateAccepted) ? '+' : '-').'dateAccepted';

                    $__complaints_complaints[$k]->__status_name=trans('tender.complaints_statuses.'.$key.'.'.$status_key);
                    $__complaints_complaints[$k]->__status_name_t=t('tender.complaints_statuses.'.$key.'.'.$status_key);
                //}
            }
        //}

        $__complaints_complaints=array_where($__complaints_complaints, function($key, $complain){
            return $complain->status!='draft';
        });

        if(!$return)
        {
            $item->__complaints_complaints=new \StdClass();
            $item->__complaints_complaints=$__complaints_complaints;
        }
        else
            return $__complaints_complaints;

    }

    public function get_all_complaints(&$item, $lot = null, $return = false)
    {
        $complaintsTypes=[
            'complaints' => [],
            'qualificationComplaints' => [],
            'awardComplaints' => [],
        ];
        $complaints=[];

        $below = $item->procurementMethodType == 'belowThreshold';

        if (!empty($item->complaints)) {

            foreach ($item->complaints as $complaint) {
                if (($below && $complaint->type == 'complaint') || $complaint->type == 'claim') {
                    continue;
                }

                if((empty($lot) && (empty($complaint->relatedLot) || !$item->__isMultiLot)) || (!empty($lot) && !empty($complaint->relatedLot) && $complaint->relatedLot == $lot->id)) {
                    $complaints[] = $complaint;
                    $complaintsTypes['complaints'][]= $complaint;
                }
            }
        }
        if (!empty($item->qualifications)) {
            $qComplaints = [];

            foreach ($item->qualifications as $qualification) {
                if (!empty($qualification->complaints)) {
                    $qComplaints = array_merge($qComplaints, $qualification->complaints);
                }
            }

            if (!empty($qComplaints)) {
                foreach ($qComplaints as $complaint) {
                    if (($below && $complaint->type == 'complaint') || $complaint->type == 'claim') {
                        continue;
                    }

                    if((empty($lot) && (empty($complaint->relatedLot) || !$item->__isMultiLot)) || (!empty($lot) && !empty($complaint->relatedLot) && $complaint->relatedLot == $lot->id)) {
                        $complaints[] = $complaint;
                        $complaintsTypes['qualificationComplaints'][]= $complaint;
                    }
                }
            }
        }
        if (!empty($item->awards)) {
            $aComplaints = [];

            foreach ($item->awards as $award) {
                if (!empty($award->complaints)) {
                    $aComplaints = array_merge($aComplaints, $award->complaints);
                }
            }

            if (!empty($aComplaints)) {
                foreach ($aComplaints as $complaint) {
                    if (($below && $complaint->type == 'complaint') || $complaint->type == 'claim') {
                        continue;
                    }

                    if((empty($lot) && (empty($complaint->relatedLot) || !$item->__isMultiLot)) || (!empty($lot) && !empty($complaint->relatedLot) && $complaint->relatedLot == $lot->id)) {
                        $complaints[] = $complaint;
                        $complaintsTypes['awardComplaints'][]= $complaint;
                    }
                }
            }
        }

        return $return ? $complaintsTypes : $complaints;
    }

    public function get_questions(&$item, $type='tender', $return=false)
    {
        if(!empty($item->questions))
        {
            if(empty($type) || $type=='tender') {
                $types = ['tender', 'item'];
            } else {
                $types[] = $type;
            }

            $questions=array_where($item->questions, function($key, $question) use ($item, $types){
                return in_array($question->questionOf, $types) || !$item->__isMultiLot;
            });

            if(!$return)
            {
                $item->__questions=new \StdClass();
                $item->__questions=array_values($questions);
            }
            else
                return $questions;
        }

        if($return)
            return [];
    }

    public function get_questions_lots($item, $lot)
    {
        if(!empty($item->questions) && $item->__isMultiLot)
        {
            $item_ids=[];

            if(!empty($lot->__items))
                $item_ids=array_pluck((array)$lot->__items, 'id');

            $questions=array_where($item->questions, function($key, $question) use ($item, $lot, $item_ids){
                return !empty($question->relatedItem) && ($question->questionOf=='lot' && $question->relatedItem==$lot->id) || ($question->questionOf=='item' && in_array($question->relatedItem, $item_ids));
            });

            return array_values($questions);
        }

        return [];
    }

    public function get_other_questions($item)
    {
        if($item->__isMultiLot) {
            return [];
        }

        $questions = [];

        if($item->procurementMethodType != 'belowThreshold') {
            if (!empty($item->complaints)) {
                foreach ($item->complaints as $complaint) {
                    if ($complaint->type == 'claim' && empty($complaint->relatedLot)) {
                        $questions[] = $complaint;
                    }
                }
            }
        }

        if(!empty($item->qualifications)) {
            $qComplaints = [];

            foreach ($item->qualifications as $qualification) {
                if (!empty($qualification->complaints)) {
                    $qComplaints = array_merge($qComplaints, $qualification->complaints);
                }
            }

            if (!empty($qComplaints)) {
                foreach ($qComplaints as $complaint) {
                    if ($complaint->type == 'claim' && empty($complaint->relatedLot)) {
                        $questions[] = $complaint;
                    }
                }
            }
        }

        if(!empty($item->awards)) {
            $aComplaints = [];

            foreach ($item->awards as $award) {
                if (!empty($award->complaints)) {
                    $aComplaints = array_merge($aComplaints, $award->complaints);
                }
            }

            if (!empty($aComplaints)) {
                foreach ($aComplaints as $complaint) {
                    if ($complaint->type == 'claim' && empty($complaint->relatedLot)) {
                        $questions[] = $complaint;
                    }
                }
            }
        }

        return $questions;
    }

    public function get_other_questions_lots($item, $lot = null)
    {
        if(!$item->__isMultiLot || empty($lot)) {
            return [];
        }

        $questions = [];

        if($item->procurementMethodType != 'belowThreshold') {
            if (!empty($item->complaints)) {
                foreach ($item->complaints as $complaint) {
                    if ($complaint->type == 'claim' && !empty($complaint->relatedLot) && $complaint->relatedLot == $lot->id) {
                        $questions[] = $complaint;
                    }
                }
            }
        }

        if(!empty($item->qualifications)) {
            $qComplaints = [];

            foreach ($item->qualifications as $qualification) {
                if (!empty($qualification->complaints)) {
                    $qComplaints = array_merge($qComplaints, $qualification->complaints);
                }
            }

            if (!empty($qComplaints)) {
                foreach ($qComplaints as $complaint) {
                    if ($complaint->type == 'claim' && !empty($complaint->relatedLot) && $complaint->relatedLot == $lot->id) {
                        $questions[] = $complaint;
                    }
                }
            }
        }

        if(!empty($item->awards)) {
            $aComplaints = [];

            foreach ($item->awards as $award) {
                if (!empty($award->complaints)) {
                    $aComplaints = array_merge($aComplaints, $award->complaints);
                }
            }

            if (!empty($aComplaints)) {
                foreach ($aComplaints as $complaint) {
                    if ($complaint->type == 'claim' && !empty($complaint->relatedLot) && $complaint->relatedLot == $lot->id) {
                        $questions[] = $complaint;
                    }
                }
            }
        }

        return $questions;
    }

    private function get_tender_documents(&$item, $type='tender')
    {
        if(!empty($item->documents))
        {
            $item->__tender_documents=new \StdClass();

            if($type=='tender' && (empty($item->lots) || (!empty($item->lots) && sizeof($item->lots)==1)))
                $type=['tender', 'lot', 'item'];
            elseif($type=='tender')
                $type=['tender', 'item'];
            else
                $type=[$type];

            $item->__tender_documents = [];

            foreach($item->documents as $key => $document) {
                if($item->procurementMethodType=='' || in_array($document->documentOf, $type)) {
                    $item->__tender_documents[] = $document;
                }
            }

            /*
            $item->__tender_documents=array_where($item->documents, function($key, $document) use ($type, $item){
                return $item->procurementMethodType=='' || in_array($document->documentOf, $type);
            });
            */

            usort($item->__tender_documents, function ($a, $b)
            {
                return intval(strtotime($b->dateModified))>intval(strtotime($a->dateModified));
            });

            $ids=[];

            foreach($item->__tender_documents as $document)
            {
                $document->__foramt_date = Carbon::createFromTimestamp(strtotime($document->dateModified))->format('d.m.Y H:i');

                if(in_array($document->id, $ids))
                {
                    $document->stroked=new \StdClass();
                    $document->stroked=true;
                }

                $ids[]=$document->id;
            }

            $stroked = [];

            foreach($item->__tender_documents as $key => $document) {
                if(!empty($document->stroked)) {
                    $stroked[] = $document;
                }
            }

            $item->__tender_documents_stroked=sizeof($stroked)>0;
        }
    }

    private function get_uniqie_bids(&$item, $is_lot=false)
    {
        $item->__unique_bids=new \StdClass();
        $item->__unique_bids=null;

        if($is_lot)
            $bids=!empty($item->__bids)?$item->__bids:false;
        elseif(!empty($item->lots) && sizeof($item->lots)==1)
            $bids=!empty($item->bids)?$item->bids:false;
        else
            $bids=!empty($item->bids)?$item->bids:false;

        if(!empty($bids))
        {
            $bids=array_where($bids, function($key, $bid){
                return empty($bid->status) || !in_array($bid->status, ['deleted', 'invalid']);
            });

            $ids=[];

            foreach($bids as $award)
            {
                foreach($award->tenderers as $tenderer)
                {
                    array_push($ids, $tenderer->identifier->id);
                }
            }

            $ids=array_unique($ids);
            $item->__unique_bids=sizeof($ids);
        }
    }

    private function get_bids(&$item, $return=false)
    {
        $item->__active_bids =new \StdClass();
        $item->__active_bids =null;

        if(!empty($item->bids))
        {
            $item->__active_bids = array_where($item->bids, function($key, $bid){
                return $bid->status == 'active';
            });

            if(in_array($item->status, ['active.pre-qualification', 'active.auction', 'active.pre-qualification.stand-still']))
            {
                if(!$return)
                    $item->__bids=null;
                else
                    return null;

            }
            elseif($item->procurementMethod=='open' || ($item->procurementMethod=='selective' && ($item->procurementMethodType=='competitiveDialogueUA.stage2' || $item->procurementMethodType=='competitiveDialogueEU.stage2')))
            {
                $item->__bids=new \StdClass();
                $item->__bids=[];

                if(!empty($item->bids))
                {
                    $bids=$item->bids;

                    foreach($bids as $k=>$bid)
                    {
                        if(!empty($bid->date)) {
                            $bid->__format_date = Carbon::createFromTimestamp(strtotime($bid->date))->format('d.m.Y H:i');
                        }

                        $contactPoint = [];

                        if(!empty($bid->tenderers[0])) {
                            $bid->__address = implode(', ', (array)$bid->tenderers[0]->address);

                            /*if(!empty($bid->tenderers[0]->name)) {
                                $contactPoint[] = $bid->tenderers[0]->name;
                            }*/

                            $contactPoint[] = $bid->tenderers[0]->identifier->id;

                            if(!empty($bid->tenderers[0]->contactPoint)) {
                                $contactPoint = array_merge($contactPoint, (array)$bid->tenderers[0]->contactPoint);
                            }

                            $bid->__contactPoint = implode(', ', $contactPoint);
                        }

                        if(!empty($bid->documents)) {
                            foreach ($bid->documents as $document) {
                                $document->__format_date = Carbon::createFromTimestamp(strtotime($document->dateModified))->format('d.m.Y H:i');
                            }
                        }

                        $documents=!empty($bid->documents) ? $bid->documents : [];

                        $eligibilityDocuments=!empty($bid->eligibilityDocuments) ? $bid->eligibilityDocuments : [];

                        $documents=array_merge($documents, $eligibilityDocuments);

                        $financialDocuments=!empty($bid->financialDocuments) ? $bid->financialDocuments : [];

                        $documents=array_merge($documents, $financialDocuments);

                        if(!empty($documents))
                        {
                            $bids[$k]->__documents_public=new \StdClass();
                            $bids[$k]->__documents_public=[];

                            $bids[$k]->__documents_confident=new \StdClass();
                            $bids[$k]->__documents_confident=[];

                            $bids[$k]->__documents_public=array_where($documents, function($key, $document){
                                return empty($document->confidentiality) || $document->confidentiality!='buyerOnly';
                            });

                            $bids[$k]->__documents_confident=array_where($documents, function($key, $document){
                                return !empty($document->confidentiality) && $document->confidentiality=='buyerOnly';
                            });

                            $bids[$k]->__documents_public=$this->parse_document_changes($bids[$k]->__documents_public);
                            $bids[$k]->__documents_confident=$this->parse_document_changes($bids[$k]->__documents_confident);
                        }

                        if(!empty($item->awards))
                        {
                            foreach($item->awards as $award)
                            {
                                if(!empty($award->bid_id) && $award->bid_id==$bid->id)
                                {
                                    $bid->__award=$award;
                                }
                            }
                        }

                        if(!empty($item->qualifications))
                        {
                            foreach($item->qualifications as $qualification)
                            {
                                if(!empty($qualification->bidID) && $qualification->bidID==$qualification->id)
                                {
                                    $bid->__qualification=$qualification;
                                }
                            }
                        }
                    }

                    if(!empty($bids))
                    {
                        $bids=array_where($bids, function($key, $bid){
                            return empty($bid->status) || !in_array($bid->status, ['deleted', 'invalid', 'draft', 'unsuccessful']);
                        });
                    }

                    if(!$return)
                        $item->__bids=$bids;
                    else
                        return $bids;
                }
            }
        }
    }

    private function parse_document_changes($documents)
    {
        $dates=[];
        $sort_ids=[];

        foreach($documents as $document)
        {
            $date = new Carbon(!empty($document->dateModified) ? $document->dateModified : $document->datePublished);
            $date->setTimezone(Config::get('app.timezone'));

            $dates[]=$date;
            $sort_ids[]=$document->id;
        }

        $titles=array_pluck($documents, 'title');

        array_multisort($dates, SORT_DESC, $documents);

        $out=[];

        $ids=[];

        foreach($documents as $document)
        {
            $d=(object) json_decode(json_encode($document));

            $d->stroked=new \StdClass();

            $d->stroked=in_array($d->id, $ids);

            $out[$d->id.(!empty($d->dateModified) ? $d->dateModified : $d->datePublished)]=$d;
            $ids[]=$d->id;
        }

        return $out;
    }

    private function get_button_007(&$item, $procuringEntity)
    {
        $item->__button_007=false;

        if(($item->procurementMethod=='open' && in_array($item->procurementMethodType, ['aboveThresholdEU', 'aboveThresholdUA', 'aboveThresholdUA.defense', 'belowThreshold'])) || ($item->procurementMethod=='limited' && in_array($item->procurementMethodType, ['negotiation', 'negotiation.quick', 'reporting'])))
        {
            if(!empty($item->__documents))
            {
                $has_active_contracts=array_first($item->__documents, function($key, $contract){
                    return $contract->status=='active';
                });

                if($has_active_contracts && $item->status=='complete' && (!empty($item->__active_award->complaintPeriod->endDate) || !empty($item->__active_award->date)))
                {
                    $date=!empty($item->__active_award->complaintPeriod) ? date_create($item->__active_award->complaintPeriod->endDate) : date_create($item->__active_award->date);
                    $sub_days=0;

                    if(in_array($item->procurementMethodType, ['aboveThresholdUA', 'aboveThresholdEU', 'negotiation']))
                        $sub_days=10;

                    elseif(in_array($item->procurementMethodType, ['negotiation.quick']))
                        $sub_days=5;

                    elseif(in_array($item->procurementMethodType, ['aboveThresholdUA.defense']))
                        $sub_days=4;

                    elseif(in_array($item->procurementMethodType, ['belowThreshold']))
                        $sub_days=2;

                    elseif(in_array($item->procurementMethodType, ['reporting']))
                    {
                        $sub_days=0;
                        $date=date_create($item->__active_award->date);
                    }

                    $now=new DateTime();
                    $work_days=$this->parse_work_days();

                    for($i=0;$i<$sub_days;$i++)
                    {
                        $now->sub(new \DateInterval('P1D'));

                        if(in_array($now->format('Y-m-d'), $work_days))
                        {
                            $i--;
                            $sub_days++;
                        }
                    }

                    $date_from=date_format($date->sub(new \DateInterval('P'.$sub_days.'D')), 'Y.m.d');

                    $item->__button_007=(object) [
                        'edrpou'=>$procuringEntity->identifier->id,
                        'date_from'=>$date_from,
                        'partner'=>$item->__active_award->suppliers[0]->identifier->id,
                    ];
                }
            }
        }
    }

    private function get_qualifications(&$item, $return=false, $lot=false)
    {
        if(!empty($item->qualifications))
        {
            $__qualifications=[];

            $cnt=1;

            foreach($item->qualifications as $qualification)
            {
                $qualification->__format_date = Carbon::createFromTimestamp(strtotime($qualification->date))->format('d.m.Y H:i');
                $qualification->__status_name = t('tender.qualification_status.'.$qualification->status);

                $qualification->__name=new \StdClass();
                $qualification->__name='';

                $qualification->__documents=new \StdClass();
                $qualification->__documents=[];

                $qualification->__bid_documents=new \StdClass();
                $qualification->__bid_documents=[];

                $qualification->__bid_documents_public=new \StdClass();
                $qualification->__bid_documents_public=[];

                $qualification->__bid_documents_confident=new \StdClass();
                $qualification->__bid_documents_confident=[];

                if(starts_with($item->status, 'active.pre-qualification') || starts_with($item->status, 'active.auction') || starts_with($item->status, 'active.pre-qualification.stand-still'))
                    $qualification->__name='Учасник '.$cnt;

                if(!empty($item->bids))
                {
                    $item->bids=array_where($item->bids, function($key, $bid){
                        return empty($bid->status) || !in_array($bid->status, ['deleted', 'invalid']);
                    });
                }

                $bid=array_first($item->bids, function($key, $bid) use ($qualification){
                    return $qualification->bidID==$bid->id;
                });

                if(!empty($qualification->documents))
                    $qualification->__documents=$qualification->documents;

                if(!empty($bid))
                {
                    $qualification->__bid = $bid;
                    $documents=!empty($bid->documents) ? $bid->documents : [];
                    $eligibilityDocuments=!empty($bid->eligibilityDocuments) ? $bid->eligibilityDocuments : [];

                    $documents=array_merge($documents, $eligibilityDocuments);

                    $financialDocuments=!empty($bid->financialDocuments) ? $bid->financialDocuments : [];

                    $documents=array_merge($documents, $financialDocuments);

                    if(!empty($bid->tenderers[0]))
                        $qualification->__name=$bid->tenderers[0]->name;

                    $qualification->__bid_documents=$documents;

                    $qualification->__bid_documents_public=array_where($qualification->__bid_documents, function($key, $document){
                        return empty($document->confidentiality) || $document->confidentiality!='buyerOnly';
                    });

                    $qualification->__bid_documents_confident=array_where($qualification->__bid_documents, function($key, $document){
                        return !empty($document->confidentiality) && $document->confidentiality=='buyerOnly';
                    });
                }

                array_push($__qualifications, $qualification);

                $cnt++;
            }

            if($item->procurementMethodType=='aboveThresholdEU')
            {
                $__qualifications=array_where($__qualifications, function($key, $qualification) use ($lot){
                    if($lot && $lot->status=='cancelled')
                        $out=!empty($qualification->lotID) && $lot->id==$qualification->lotID;
                    elseif($lot)
                        $out=$lot->id==$qualification->lotID && (empty($qualification->status) || !in_array($qualification->status, ['cancelled']));
                    else
                        $out=true;
                        //$out=empty($qualification->status) || !in_array($qualification->status, ['cancelled']);

                    return $out;
                });

            }

            if(!$return)
            {
                $item->__qualifications=new \StdClass();
                $item->__qualifications=$__qualifications;
            }
            else
                return $__qualifications;
        }
    }

    private function get_items(&$item)
    {
        if($item->__isMultiLot) {
            foreach ($item->lots as &$lot) {
                $current_lot = Input::get('lot_id') ? Input::get('lot_id') : false;

                if($current_lot == $lot->id) {
                    $lot->__items = new \StdClass();
                    $lot->__items = [];

                    if(!empty($item->items)) {
                        foreach($item->items as $key => $_item) {
                            if(!empty($_item->relatedLot) && $_item->relatedLot == $lot->id) {
                                $lot->__items[] = $_item;
                                break;
                            }
                        }
                    }

                    if(!empty($lot->__items)) {
                        array_multisort($lot->__items, SORT_ASC);

                        $address = [];
                        $dates = [];

                        foreach($lot->__items as $v) {
                            $v->__format_delivery_date = null;

                            if(!empty($v->deliveryDate->endDate)) {

                                if(!isset($dates[$v->deliveryDate->endDate])) {
                                    $dates[$v->deliveryDate->endDate] = 1;
                                } else {
                                    $dates[$v->deliveryDate->endDate] += 1;
                                }

                                $v->__format_delivery_date = Carbon::createFromTimestamp(strtotime($v->deliveryDate->endDate))->format('d.m.Y');
                            }

                            $info = [];

                            if(!empty($v->deliveryAddress->locality)) {
                                $info[] = $v->deliveryAddress->locality;
                            }
                            if(!empty($v->deliveryAddress->region)) {
                                $info[] = $v->deliveryAddress->region;
                            }
                            if(!empty($v->deliveryAddress->streetAddress)) {
                                $info[] = $v->deliveryAddress->streetAddress;
                            }

                            if(!empty($info)) {
                                $v->__address = implode(', ', $info);

                                if(!isset($address[$v->__address])) {
                                    $address[$v->__address] = 1;
                                } else {
                                    $address[$v->__address] += 1;
                                }
                            }
                        }

                        if (!empty($address)) {
                            $lot->__items_address = array_flip(array_unique($address))[max($address)];
                        }
                        if (!empty($dates)) {
                            $lot->__items_deliveryDate = Carbon::createFromTimestamp(strtotime(array_flip(array_unique($dates))[max($dates)]))->format('d.m.Y');
                        }
                    }
                }
            }
        }
    }

    public function get_features(&$item, &$lot)
    {
        $tender_features = [];
        
        if(!empty($item->features))
        {

            foreach($item->features as $key => $feature) {
                if($feature->featureOf=='tenderer' || ($feature->featureOf=='lot' && $feature->relatedItem==$lot->id)) {
                    $tender_features[] = $feature;
                }
            }

            /*
            $tender_features=array_where($item->features, function($key, $feature) use ($lot){
                return $feature->featureOf=='tenderer' || ($feature->featureOf=='lot' && $feature->relatedItem==$lot->id);
            });*/
        }

        $features_price=1;

        if(!empty($tender_features))
        {
            $enums = 0;

            foreach($tender_features as $k=>$feature)
            {
                if(!empty($feature->enum)) {
                    $tender_features[$k]->__enum_titles = implode(', ', array_column($feature->enum, 'title'));
                    $tender_features[$k]->__enum_values = implode(', ', array_column($feature->enum, 'value'));
                    $enums += count($feature->enum);
                }

                $max=0;

                foreach($feature->enum as $one)
                    $max=max($max, floatval($one->value));

                $tender_features[$k]->max = new \stdClass();
                $tender_features[$k]->max=$max;

                $features_price-=$max;

                usort($feature->enum, function ($a, $b)
                {
                    return strcmp($b->value, $a->value);
                });

                $tender_features[$k]->enum=$feature->enum;
            }

            array_multisort($tender_features, SORT_ASC);

            $lot->__features=new \StdClass();
            $lot->__features=$tender_features;
            $lot->__enums=$enums;
        }

        $lot->__features_price=new \StdClass();
        $lot->__features_price=$features_price;
        $lot->__features_price_real=$features_price*100;
    }

    private function get_lots_base(&$item)
    {
        if(!empty($item->lots) && sizeof($item->lots)>1)
        {
            foreach($item->lots as &$lot)
            {
                if(!empty($lot->value->amount)) {
                    $lot->__full_formated_price = number_format($lot->value->amount, 0, '', ' ') . ' ' . $lot->value->currency . ' ' . (t($lot->value->valueAddedTaxIncluded ? 'tender.with_VAT' : 'tender.without_VAT'));
                } else {
                    $lot->__full_formated_price = t('tender.empty_price');
                }

                if(isset($this->statuses[$lot->status])) {
                    $lot->__status_name = $this->statuses[$lot->status];
                } else {
                    $lot->__status_name = $lot->status;
                }

                $lot->__items=new \StdClass();
                $lot->__items=[];

                if(!empty($item->items))
                {
                    foreach($item->items as $key => $_item) {
                        if(!empty($_item->relatedLot) && $_item->relatedLot==$lot->id) {
                            $lot->__items[] = $_item;
                        }
                    }
                }

                $lot->awards=new \StdClass();
                $lot->awards=[];

                if(!empty($item->awards))
                {
                    foreach($item->awards as $key => $award) {
                        if(!empty($award->lotID) && $award->lotID==$lot->id) {
                            $lot->awards[] = $award;
                        }
                    }

                    $lot->__active_award = new \StdClass();
                    $lot->__active_award = null;

                    foreach($lot->awards as $key => $award) {
                        if($award->status=='active') {

                            if(!empty($award->value->amount)) {
                                $award->__full_formated_price = number_format($award->value->amount, 0, '', ' ') . ' ' . $award->value->currency . ' ' . (t($award->value->valueAddedTaxIncluded ? 'tender.with_VAT' : 'tender.without_VAT'));
                            } else {
                                $award->__full_formated_price = t('tender.empty_price');
                            }

                            $lot->__active_award = $award;
                            break;
                        }
                    }
                }

                //$this->get_signed_contracts($item);
            }
        }
    }

    private function get_lots(&$item, $dataStatus, $fromIndex = true)
    {
        if(!empty($item->lots) && sizeof($item->lots)>1)
        {
            /*
            usort($item->lots, function ($a, $b)
            {
                return strcmp($a->title, $b->title);
            });
            */

            $tender_bids=$this->get_bids($item, true);
            $parsed_lots=[];

            if($item->status=='cancelled')
            {
                foreach($item->lots as $lot)
                {
                    $lot->status='cancelled';

                    if(!empty($item->cancellations))
                    {
                        $lot->__cancellations=$item->cancellations;
                    }
                }
            }

            $current_lot=Input::get('lot_id') ? Input::get('lot_id') : false;

            $lot_titles=[];

            foreach($item->lots as &$lot)
            {
                if(!empty($lot->value->amount)) {
                    $lot->__full_formated_price = number_format($lot->value->amount, 0, '', ' ') . ' ' . $lot->value->currency . ' ' . (t($lot->value->valueAddedTaxIncluded ? 'tender.with_VAT' : 'tender.without_VAT'));
                } else {
                    $lot->__full_formated_price = t('tender.empty_price');
                }

                if(isset($dataStatus[$lot->status])) {
                    $lot->__status_name = $dataStatus[$lot->status];
                } else {
                    $lot->__status_name = $lot->status;
                }

                $lot_titles[]=(object)[
                    'id'=>$lot->id,
                    'title'=>$lot->title,
                    'lotNumber'=>!empty($lot->lotNumber) ? $lot->lotNumber : false
                ];
            }

            $item->__lot_titles=$lot_titles;
            $item->__lot_k=0;

            if(!$current_lot && $fromIndex)
                return true;

            foreach($item->lots as $k=>&$lot)
            {
                if($lot->id == $current_lot || !$fromIndex)
                {
                    $item->__lot_k=$k;

                    // $lot = clone $lot;

                    if(!empty($item->__eu_bids[$lot->id]))
                    {
                        $lot->__eu_bids=new \StdClass();
                        $lot->__eu_bids=$item->__eu_bids[$lot->id];
                    }

                    $lot->procurementMethod=$item->procurementMethod;
                    $lot->procurementMethodType=$item->procurementMethodType;

                    if(!empty($item->__initial_bids_by_lot))
                    {
                        if(!empty($item->__initial_bids_by_lot[$lot->id]))
                            $lot->__initial_bids=$item->__initial_bids_by_lot[$lot->id];
                    }
                    else
                        $lot->__initial_bids=$item->__initial_bids;

                    if(!empty($item->__initial_bids_dates_by_lot))
                    {
                        if(!empty($item->__initial_bids_dates_by_lot[$lot->id]))
                            $lot->__initial_bids_dates=$item->__initial_bids_dates_by_lot[$lot->id];
                    }
                    else
                        $lot->__initial_bids_dates=$item->__initial_bids_dates;

                    $lot->__icon=new \StdClass();
                    $lot->__icon=false;

                    $lot->__items=[];

                    foreach($item->items as $key => $it) {
                        if(!empty($it->relatedLot) && $it->relatedLot==$lot->id) {
                            $lot->__items[] = $it;
                        }
                    }

                    /*
                    $lot->__items=array_where($item->items, function($key, $it) use ($lot){
                        return !empty($it->relatedLot) && $it->relatedLot==$lot->id;
                    });
                    */

                    if(!empty($lot->__items)) {
                        array_multisort($lot->__items, SORT_ASC);

                        $address = [];
                        $dates = [];

                        foreach($lot->__items as $v) {
                            $v->__format_delivery_date = null;

                            if(!empty($v->deliveryDate->endDate)) {

                                if(!isset($dates[$v->deliveryDate->endDate])) {
                                    $dates[$v->deliveryDate->endDate] = 1;
                                } else {
                                    $dates[$v->deliveryDate->endDate] += 1;
                                }

                                $v->__format_delivery_date = Carbon::createFromTimestamp(strtotime($v->deliveryDate->endDate))->format('d.m.Y');
                            }

                            $info = [];

                            if(!empty($v->deliveryAddress->locality)) {
                                $info[] = $v->deliveryAddress->locality;
                            }
                            if(!empty($v->deliveryAddress->region)) {
                                $info[] = $v->deliveryAddress->region;
                            }
                            if(!empty($v->deliveryAddress->streetAddress)) {
                                $info[] = $v->deliveryAddress->streetAddress;
                            }

                            if(!empty($info)) {
                                $v->__address = implode(', ', $info);

                                if(!isset($address[$v->__address])) {
                                    $address[$v->__address] = 1;
                                } else {
                                    $address[$v->__address] += 1;
                                }
                            }
                        }

                        if (!empty($address)) {
                            $lot->__items_address = array_flip(array_unique($address))[max($address)];
                        }
                        if (!empty($dates)) {
                            $lot->__items_deliveryDate = Carbon::createFromTimestamp(strtotime(array_flip(array_unique($dates))[max($dates)]))->format('d.m.Y');
                        }
                    }

                    $lot->__questions=new \StdClass();
                    $lot->__questions=$this->get_questions_lots($item, $lot);

                    $lot->__complaints_claims=new \StdClass();
                    $lot->__complaints_claims=[];

                    foreach($this->get_claims($item, 'lot', true) as $key => $claim) {
                        if(!empty($claim->relatedLot) && $claim->relatedLot==$lot->id) {
                            $lot->__complaints_claims[] = $claim;
                        }
                    }

                    /*
                    $lot->__complaints_claims=array_where($this->get_claims($item, 'lot', true), function($key, $claim) use ($lot){
                        return !empty($claim->relatedLot) && $claim->relatedLot==$lot->id;
                    });*/

                    $lot->__complaints_complaints=new \StdClass();
                    $lot->__complaints_complaints=[];

                    foreach($this->get_complaints($item, 'lot', true) as $key => $complaint) {
                        if(!empty($complaint->relatedLot) && $complaint->relatedLot==$lot->id) {
                            $lot->__complaints_complaints[]= $complaint;
                        }
                    }

                    /*
                    $lot->__complaints_complaints=array_where($this->get_complaints($item, 'lot', true), function($key, $complaint) use ($lot){
                        return !empty($complaint->relatedLot) && $complaint->relatedLot==$lot->id;
                    });
                    */

                    if(!empty($item->documents))
                    {
                        $lot->__tender_documents=new \StdClass();
                        $lot->__tender_documents=[];

                        foreach($item->documents as $key => $document) {
                            if(!empty($document->documentOf) && $document->documentOf=='lot' && $document->relatedItem==$lot->id) {
                                $lot->__tender_documents[] = $document;
                            }
                        }

                        /*
                        $lot->__tender_documents=array_where($item->documents, function($key, $document) use ($lot){
                            return !empty($document->documentOf) && $document->documentOf=='lot' && $document->relatedItem==$lot->id;
                        });*/

                        usort($lot->__tender_documents, function ($a, $b)
                        {
                            return intval(strtotime($b->dateModified))>intval(strtotime($a->dateModified));
                        });

                        $ids=[];

                        foreach($lot->__tender_documents as $document)
                        {
                            $document->__format_date = Carbon::createFromTimestamp(strtotime($document->dateModified))->format('d.m.Y H:i');

                            if(in_array($document->id, $ids))
                            {
                                $document->stroked=new \StdClass();
                                $document->stroked=true;
                            }

                            $ids[]=$document->id;
                        }

                        $stroked = [];

                        foreach($lot->__tender_documents as $key => $document) {
                            if(!empty($document->stroked)) {
                                $stroked[] = $document;
                            }
                        }

                        $lot->__tender_documents_stroked=sizeof($stroked)>0;
                    }

                    $lot->awards=new \StdClass();
                    $lot->awards=[];

                    if(!empty($item->awards))
                    {
                        $lot->awards = [];

                        foreach($item->awards as $key => $award) {
                            if(!empty($award->lotID) && $award->lotID==$lot->id) {
                                $lot->awards[] = $award;
                            }
                        }

                        /*
                        $lot->awards=array_where($item->awards, function($key, $award) use ($lot){
                            return !empty($award->lotID) && $award->lotID==$lot->id;
                        });*/

                        $lot->__active_award = new \StdClass();
                        $lot->__active_award = null;

                        foreach($lot->awards as $key => $award) {
                            if($award->status=='active') {
                                $lot->__active_award = $award;
                                break;
                            }
                        }

                        /*
                        $lot->__active_award = array_first($lot->awards, function($key, $award) {
                            return $award->status=='active';
                        });
                        */
                    }

                    if(!empty($item->features))
                    {
                        $tender_features = [];

                        foreach($item->features as $key => $feature) {
                            if($feature->featureOf=='tenderer' || ($feature->featureOf=='lot' && $feature->relatedItem==$lot->id)) {
                                $tender_features[] = $feature;
                            }
                        }

                        /*
                        $tender_features=array_where($item->features, function($key, $feature) use ($lot){
                            return $feature->featureOf=='tenderer' || ($feature->featureOf=='lot' && $feature->relatedItem==$lot->id);
                        });*/
                    }

                    $features_price=1;

                    if(!empty($tender_features))
                    {
                        $enums = 0;

                        foreach($tender_features as $k=>$feature)
                        {
                            if(!empty($feature->enum)) {
                                $tender_features[$k]->__enum_titles = implode(', ', array_column($feature->enum, 'title'));
                                $tender_features[$k]->__enum_values = implode(', ', array_column($feature->enum, 'value'));
                                $enums += count($feature->enum);
                            }

                            $max=0;

                            foreach($feature->enum as $one)
                                $max=max($max, floatval($one->value));

                            $tender_features[$k]->max = new \stdClass();
                            $tender_features[$k]->max=$max;

                            $features_price-=$max;

                            usort($feature->enum, function ($a, $b)
                            {
                                return strcmp($b->value, $a->value);
                            });

                            $tender_features[$k]->enum=$feature->enum;
                        }

                        array_multisort($tender_features, SORT_ASC);

                        $lot->__features=new \StdClass();
                        $lot->__features=$tender_features;
                        $lot->__enums=$enums;
                    }

                    $lot->__features_price=new \StdClass();
                    $lot->__features_price=$features_price;

                    if(!empty($tender_bids))
                    {
                        $bids = [];

                        foreach($tender_bids as $key => $bid) {
                            if(!empty($bid->lotValues)) {
                                $lotValues = [];

                                foreach($bid->lotValues as $k => $value) {
                                    if($value->relatedLot==$lot->id) {
                                        $lotValues[] = $value;
                                    }
                                }

                                if(!empty($lotValues)) {
                                    $bids[]=$bid;
                                }
                            }
                        }

                        /*
                        $bids=array_where($tender_bids, function($key, $bid) use ($lot){
                            return !empty($bid->lotValues) && (!empty(array_where($bid->lotValues, function($k, $value) use ($lot){
                                return $value->relatedLot==$lot->id;
                            })));
                        });
                        */
                    }

                    if(!empty($item->features))
                    {
                        $features_by_lot = [];

                        foreach($item->features as $k => $feature) {
                            if($feature->featureOf!='lot' || ($feature->featureOf=='lot' && $feature->relatedItem==$lot->id)) {
                                $features_by_lot[] = $feature;
                            }
                        }

                        /*
                        $features_by_lot=array_where($item->features, function($k, $feature) use ($lot){
                            return $feature->featureOf!='lot' || ($feature->featureOf=='lot' && $feature->relatedItem==$lot->id);
                        });*/
                    }else
                        $features_by_lot=[];

                    if(!empty($tender_bids))
                    {
                        $lot->__bids=new \StdClass();
                        $lot->__bids=[];
                        $lot->bids_values=new \StdClass();
                        $lot->bids_values=[];

                        foreach($tender_bids as $bid)
                        {
                            $lot_bid=false;

                            if(!empty($bid->lotValues))
                            {
                                $lot_bid = [];

                                foreach($bid->lotValues as $key => $value) {
                                    if($value->relatedLot===$lot->id) {
                                        $lot_bid[] = $value;
                                    }
                                }

                                /*
                                $lot_bid=array_where($bid->lotValues, function($key, $value) use ($lot) {
                                    return $value->relatedLot===$lot->id;
                                });*/
                            }

                            if(!empty($lot_bid))
                            {
                                $bid_value=array_values($lot_bid)[0];

                                $bid->__featured_coef=new \StdClass();
                                $bid->__featured_price=new \StdClass();

                                if(!empty($bid->parameters))
                                {
                                    $value=0;

                                    foreach($features_by_lot as $feature)
                                    {
                                        $param = null;

                                        foreach($bid->parameters as $k => $param2) {
                                            if($param2->code==$feature->code) {
                                                $param = $param2;
                                                break;
                                            }
                                        }

                                        /*
                                        $param=array_first($bid->parameters, function($k, $param) use($feature){
                                            return $param->code==$feature->code;
                                        });
                                        */

                                        if($param)
                                            $value+=$param->value;
                                    }

                                    $featured_coef=trim(number_format(1+$value/$lot->__features_price, 10, '.', ' '), '.0');

                                    $bid->__featured_coef=$featured_coef;
                                    $bid->__featured_price=str_replace('.00', '', number_format($bid_value->value->amount/$featured_coef, 2, '.', ' '));
                                }

                                $bid->value=new \StdClass();
                                $bid->value=clone $bid_value->value;

                                $cloned_bid=clone $bid;
                                //$cloned_bid->__documents_public = [];
                                //$cloned_bid->__documents_confident = [];

                                if(!empty($cloned_bid->__documents_public)) {
                                    $__documents_public = [];

                                    foreach ($cloned_bid->__documents_public as $key => $document) {
                                        if($document->documentOf=='tender' || (($document->documentOf=='lot' || $document->documentOf=='item') && $document->relatedItem==$lot->id)) {
                                            $__documents_public[] = $document;
                                        }
                                    }

                                    $cloned_bid->__documents_public = $__documents_public;
                                } else {
                                    $cloned_bid->__documents_public = [];
                                }

                                if(!empty($cloned_bid->__documents_confident)) {
                                    $__documents_confident = [];

                                    foreach ($cloned_bid->__documents_confident as $key => $document) {
                                        if($document->documentOf=='tender' || (($document->documentOf=='lot' || $document->documentOf=='item') && $document->relatedItem==$lot->id)) {
                                            $__documents_confident[] = $document;
                                        }
                                    }
                                    $cloned_bid->__documents_confident = $__documents_confident;
                                } else {
                                    $cloned_bid->__documents_confident = [];
                                }

                                /*
                                $cloned_bid->__documents_public=!empty($cloned_bid->__documents_public) ? array_where($cloned_bid->__documents_public, function($key, $document) use ($lot){
                                    return $document->documentOf=='tender' || (($document->documentOf=='lot' || $document->documentOf=='item') && $document->relatedItem==$lot->id);
                                }):[];
                                */

                                /*
                                $cloned_bid->__documents_confident=!empty($cloned_bid->__documents_confident) ? array_where($cloned_bid->__documents_confident, function($key, $document) use ($lot){
                                    return $document->documentOf=='tender' || (($document->documentOf=='lot' || $document->documentOf=='item') && $document->relatedItem==$lot->id);
                                }):[];
                                */

                                $lot->__bids[]=$cloned_bid;
                            }
                        }

                        foreach($lot->__bids as $__bid)
                            $__bid->__award=null;

                        if(!empty($item->awards))
                        {
                            foreach($lot->__bids as $__bid)
                            {
                                foreach($item->awards as $award)
                                {
                                    if($award->bid_id==$__bid->id && $award->lotID==$lot->id)
                                        $__bid->__award=$award;
                                }
                            }
                        }

                        usort($lot->__bids, function ($a, $b)
                        {
                            return floatval($a->value->amount)<floatval($b->value->amount);
                        });
                    }

                    if(!empty($item->qualifications))
                    {
                        $lot->__qualifications=new \StdClass();
                        $lot->__qualifications=[];

                        foreach($this->get_qualifications($item, true, $lot) as $key => $qualification) {
                            if(!empty($qualification->lotID) && $qualification->lotID==$lot->id) {
                                $lot->__qualifications[] = $qualification;
                            }
                        }

                        /*
                        $lot->__qualifications=array_where($this->get_qualifications($item, true, $lot), function($key, $qualification) use ($lot){
                            return !empty($qualification->lotID) && $qualification->lotID==$lot->id;
                        });
                        */
                    }

                    if(!empty($item->cancellations) && empty($lot->__cancellations))
                    {
                        $lot->__cancellations=new \StdClass();
                        $lot->__cancellations=[];

                        foreach($item->cancellations as $key => $cancellation) {
                            if($cancellation->cancellationOf=='lot' && $cancellation->relatedLot==$lot->id) {
                                $lot->__cancellations[] = $cancellation;
                            }
                        }

                        /*
                        $lot->__cancellations=array_where($item->cancellations, function($key, $cancellation) use ($lot){
                            return $cancellation->cancellationOf=='lot' && $cancellation->relatedLot==$lot->id;
                        });
                        */
                    }

                    $lot->tenderID=$item->tenderID;

                    $this->get_uniqie_awards($lot);
                    $this->get_uniqie_bids($lot, true);
                    $this->get_awards($lot);
                    $this->get_contracts($lot, !empty($item->__contracts) ? $item->__contracts : false, $lot->id, $fromIndex);
                    $this->get_contracts_changes($lot, !empty($item->__contracts) ? $item->__contracts : false, $lot->id, $fromIndex);
                    $this->get_contracts_ongoing($lot, !empty($item->__contracts) ? $item->__contracts : false, $lot->id);
                    $this->get_button_007($lot, $item->procuringEntity);

                    $parsed_lots[]=$lot;
                }
            }

           // $item->lots=$parsed_lots;
        }
    }

    private function get_signed_contracts(&$item)
    {
        if(!empty($item->contracts))
        {
            if(!empty($item->lots) && !empty($item->__isMultiLot)) {
                if(!empty($item->awards))
                {
                    foreach($item->awards as $award)
                    {
                        if(!empty($award->lotID))
                        {
                            $contracts_by_lotid = [];

                            foreach($item->contracts as $key => $contract) {
                                if($contract->awardID==$award->id) {
                                    $contracts_by_lotid[] = $contract;
                                }
                            }
                            /*
                            $contracts_by_lotid = array_where($item->contracts, function($key, $contract) use($award){
                                return $contract->awardID==$award->id;
                            });
                            */

                            foreach($item->lots as &$lot)
                            {
                                if($lot->id == $award->lotID) {
                                    $lot->__signed_contracts = $contracts_by_lotid;
                                    sort($lot->__signed_contracts);
                                }
                            }
                        }
                    }
                }
            } else {

                $item->__signed_contracts = [];

                foreach($item->contracts as $key => $contract) {
                    if(!empty($contract->dateSigned)) {
                        $item->__signed_contracts[] = $contract;
                    }
                }

                /*
                $item->__signed_contracts = array_where($item->contracts, function ($key, $contract) {
                    return !empty($contract->dateSigned);
                });
                */
                sort($item->__signed_contracts);
            }
        }
    }

    private function get_contracts(&$item, $contracts=false, $lotID=false, $fromIndex = true)
    {

        if(!empty($contracts))
        {
            $contracts_by_lotid=[];
            $documents=[];

            if(!empty($item->awards))
            {
                foreach($item->awards as $award)
                {
                    if(!empty($award->lotID))
                    {
                        foreach($contracts as $key => $contract) {
                            if($contract->awardID==$award->id) {
                                $contracts_by_lotid[$award->lotID][] = $contract;
                            }
                        }

                        /*
                        $contracts_by_lotid[$award->lotID]=array_where($contracts, function($key, $contract) use($award){
                            return $contract->awardID==$award->id;
                        });
                        */
                    }
                }
            }

            if(!empty($lotID))
                $contracts=!empty($contracts_by_lotid[$lotID]) ? $contracts_by_lotid[$lotID] : [];

            $item->__contracts_price=new \StdClass();
            $item->__contracts_price=0;
            $item->__contracts_dateSigned=new \StdClass();
            $item->__contracts_dateSigned=null;

            $item->__contracts=new \StdClass();
            $item->__contracts=null;

            if(!empty($contracts))
            {
                foreach($contracts as $contract)
                {
                    $contract->__full_formated_price = null;

                    if(!empty($contract->value->amount)) {
                        $contract->__full_formated_price = number_format($contract->value->amount, 0, '',
                                ' ') . ' ' . $contract->value->currency . ' ' . (t($contract->value->valueAddedTaxIncluded ? 'tender.with_VAT' : 'tender.without_VAT'));
                    }

                    if($contract->status == 'active' && !empty($contract->value)) {
                        $item->__contracts_price += $contract->value->amount;
                    }

                    if(!empty($contract->dateSigned) && (!$item->__contracts_dateSigned || $contract->dateSigned < $item->__contracts_dateSigned)) {
                        $item->__contracts_dateSigned = $contract->dateSigned;
                    }

                    if(!empty($contract->documents))
                    {
                        foreach($contract->documents as $document)
                        {
                            if(!empty($contract->dateSigned))
                            {
                                $document->dateSigned=new \StdClass();
                                $document->dateSigned=$contract->dateSigned;
                            }

                            $document->status=new \StdClass();
                            $document->status=$contract->status;

                            $documents[]=$document;
                        }
                    }
                }

                $item->__contracts=$contracts;
            }

            usort($documents, function ($a, $b)
            {
                $datea = new DateTime($a->datePublished);
                $dateb = new DateTime($b->datePublished);

                return $datea>$dateb;
            });

            $_documents = [];

            foreach($documents as $key => $document) {
                if(!in_array($document->status, ['cancelled'])) {
                    $_documents[] = $document;
                }
            }

            /*
            $documents=array_where($documents, function($key, $document){
                return !in_array($document->status, ['cancelled']);
            });
            */

            $item->__documents=$_documents;
        }
    }

    private function get_contracts_ongoing(&$item, $contracts=false, $lotID=false)
    {
        if(!empty($contracts))
        {
            $contracts_by_lotid=[];
            $documents=[];

            if(!empty($item->awards))
            {
                foreach($item->awards as $award)
                {
                    if(!empty($award->lotID))
                    {
                        foreach($contracts as $key => $contract) {
                            if($contract->awardID==$award->id) {
                                $contracts_by_lotid[$award->lotID][] = $contract;
                            }
                        }
                        /*
                        $contracts_by_lotid[$award->lotID]=array_where($contracts, function($key, $contract) use($award){
                            return $contract->awardID==$award->id;
                        });*/
                    }
                }
            }

            if(!empty($lotID))
                $contracts=!empty($contracts_by_lotid[$lotID]) ? $contracts_by_lotid[$lotID] : [];

            $__contract_active = null;

            foreach($contracts as $key => $contract) {
                if(!empty($contract->status) && $contract->status=='active') {
                    $__contract_active = $contract;
                    break;
                }
            }
            /*
            $__contract_active=array_first($contracts, function($key, $contract){
                return !empty($contract->status) && $contract->status=='active';
            });*/

            $item->__contract_ongoing=null;

            if($__contract_active)
            {
                $id=$__contract_active->id;
                $item->__contract_ongoing=$this->parse_contracts_json($id);
            }
        }
    }

    private function get_contracts_changes(&$item, $contracts=false, $lotID=false, $fromIndex = true)
    {
        if(!empty($contracts))
        {
            $contracts_by_lotid=[];
            $documents=[];

            if(!empty($item->awards))
            {
                foreach($item->awards as $award)
                {
                    if(!empty($award->lotID))
                    {
                        foreach($contracts as $key => $contract) {
                            if($contract->awardID==$award->id) {
                                $contracts_by_lotid[$award->lotID][] = $contract;
                            }
                        }
                        /*
                        $contracts_by_lotid[$award->lotID]=array_where($contracts, function($key, $contract) use($award){
                            return $contract->awardID==$award->id;
                        });
                        */
                    }
                }
            }

            $item->__contracts_changes = null;

            if($fromIndex) {

                if(!empty($lotID))
                    $contracts=!empty($contracts_by_lotid[$lotID]) ? $contracts_by_lotid[$lotID] : [];

                $__contracts_active = null;

                foreach($contracts as $key => $contract) {
                    if(!empty($contract->status) && $contract->status=='active') {
                        $__contracts_active = $contract;
                        break;
                    }
                }
                /*
                $__contracts_active = array_first($contracts, function ($key, $contract) {
                    return !empty($contract->status) && $contract->status == 'active';
                });
                */

                if ($__contracts_active) {
                    $id = $__contracts_active->id;
                    $contracts = $this->parse_contracts_json($id);
                    $rationale_types = $this->parse_rationale_type();

                    if (!empty($contracts->changes)) {
                        foreach ($contracts->changes as $change) {
                            $change->__contract_id = $id;
                            $change->contract = [];

                            foreach($contracts->documents as $key => $document) {
                                if(!empty($document->documentOf) && $document->documentOf == 'change' && $document->relatedItem == $change->id) {
                                    $change->contract[] = $document;
                                }
                            }

                            /*
                            $change->contract = array_where($contracts->documents,
                                function ($key, $document) use ($change) {
                                    return !empty($document->documentOf) && $document->documentOf == 'change' && $document->relatedItem == $change->id;
                                });
                            */

                            foreach ($change->rationaleTypes as $k => $rationaleType) {
                                $change->rationaleTypes[$k] = !empty($rationale_types->$rationaleType) ? $rationale_types->$rationaleType->title : $rationaleType;
                            }
                        }

                        $item->__contracts_changes = $contracts->changes;

                        foreach($item->__contracts as $contract) {
                            foreach ($item->__contracts_changes as $change) {
                                if($contract->id == $change->__contract_id) {
                                    $contract->__changes[] = $change;
                                }
                            }
                        }
                    }
                }
            } else {
                if(!empty($item->__contracts)) {
                    foreach ($item->__contracts as $k => $contract) {
                        $_contract = $this->parse_contracts_json($contract->id);
                        $rationale_types = $this->parse_rationale_type();

                        if (!empty($_contract->changes)) {
                            foreach ($_contract->changes as $change) {
                                $change->contract = [];

                                foreach($_contract->documents as $key => $document) {
                                    if(!empty($document->documentOf) && $document->documentOf == 'change' && $document->relatedItem == $change->id) {
                                        $change->contract[] = $document;
                                    }
                                }

                                /*
                                $change->contract = array_where($_contract->documents,
                                    function ($key, $document) use ($change) {
                                        return !empty($document->documentOf) && $document->documentOf == 'change' && $document->relatedItem == $change->id;
                                    });
                                */

                                foreach ($change->rationaleTypes as $k => $rationaleType) {
                                    $change->rationaleTypes[$k] = !empty($rationale_types->$rationaleType) ? $rationale_types->$rationaleType->title : $rationaleType;
                                }
                            }

                            foreach ($item->__contracts as $_c) {
                                if ($_c->id == $_contract->id) {
                                    $_c->__changes = $_contract->changes;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function get_eu_lots(&$item)
    {
        if($item->procurementMethod=='open' && $item->procurementMethodType=='aboveThresholdEU')
        {
            if(!empty($item->bids))
            {
                if($item->__isMultiLot)
                {
                    $item->__eu_bids=new \StdClass();
                    $item->__eu_bids=[];

                    foreach($item->bids as $bid)
                    {
                        if(!empty($bid->documents))
                        {
                            $bid->__documents_public=new \StdClass();
                            $bid->__documents_public=[];

                            $bid->__documents_confident=new \StdClass();
                            $bid->__documents_confident=[];

                            $bid->__documents_public=array_where($bid->documents, function($key, $document){
                                return empty($document->confidentiality) || $document->confidentiality!='buyerOnly';
                            });

                            $bid->__documents_confident=array_where($bid->documents, function($key, $document){
                                return !empty($document->confidentiality) && $document->confidentiality=='buyerOnly';
                            });
                        }

                        $eu_bids=[];

                        if(!empty($item->qualifications))
                        {
                            $lots=array_where($item->qualifications, function($key, $qualification) use ($bid){
                                return $qualification->bidID==$bid->id;
                            });

                            foreach($lots as $lot)
                            {
                                if(empty($item->__eu_bids[$lot->lotID]))
                                    $item->__eu_bids[$lot->lotID]=[];

                                $item->__eu_bids[$lot->lotID][]=clone $bid;
                            }
                        }
                    }
                }
                else
                {
                    foreach($item->bids as $k=>$bid)
                    {
                        if(!empty($bid->documents))
                        {
                            $item->bids[$k]->__documents_public=new \StdClass();
                            $item->bids[$k]->__documents_public=[];

                            $item->bids[$k]->__documents_confident=new \StdClass();
                            $item->bids[$k]->__documents_confident=[];

                            $item->bids[$k]->__documents_public=array_where($bid->documents, function($key, $document){
                                return empty($document->confidentiality) || $document->confidentiality!='buyerOnly';
                            });

                            $item->bids[$k]->__documents_confident=array_where($bid->documents, function($key, $document){
                                return !empty($document->confidentiality) && $document->confidentiality=='buyerOnly';
                            });
                        }
                    }

                    $item->__eu_bids=$item->bids;
                }
            }
        }
    }

    private function get_cancellations(&$item, $type='tender')
    {
        $item->__cancellations=new \StdClass();
        $item->__cancellations=null;

        if(!empty($item->cancellations))
        {
            $item->__cancellations=array_where($item->cancellations, function($key, $cancellation) use ($type, $item){
                return $cancellation->cancellationOf==$type || (!empty($item->lots) && sizeof($item->lots)==1 && $cancellation->cancellationOf=='lot');
            });
        }
    }

    private function get_yaml_documents(&$item)
    {
        if(!empty($item->documents))
        {
            $yaml_files=[];

            foreach($item->documents as $k=>$document)
            {
                if(pathinfo($document->title, PATHINFO_EXTENSION)=='yaml')
                {
                    array_push($yaml_files, $document);
                    unset($item->documents[$k]);
                }
            }

            if(!sizeof($item->documents))
                $item->documents=null;

            $item->__yaml_documents=new \StdClass();
            $item->__yaml_documents=$yaml_files;
        }
    }

    private function get_open_title(&$item)
    {
        $title=false;

        if($item->procurementMethod=='open' && $item->procurementMethodType=='aboveThresholdUA.defense')
            $title='hide';

        elseif($item->procurementMethod=='open' && in_array($item->procurementMethodType, ['competitiveDialogueUA']))
            $title=5;

        elseif($item->procurementMethod=='open' && in_array($item->procurementMethodType, ['competitiveDialogueEU']))
            $title=6;

        elseif($item->procurementMethod=='selective' && in_array($item->procurementMethodType, ['competitiveDialogueUA.stage2']))
            $title=7;

        elseif($item->procurementMethod=='selective' && in_array($item->procurementMethodType, ['competitiveDialogueEU.stage2']))
            $title=8;

        elseif($item->procurementMethod=='open' && $item->procurementMethodType!='belowThreshold')
            $title=1;

        elseif($item->procurementMethod=='open' && $item->procurementMethodType=='belowThreshold')
            $title=2;

        elseif($item->procurementMethod=='limited' && $item->procurementMethodType!='reporting')
            $title=3;

        elseif($item->procurementMethod=='limited' && $item->procurementMethodType=='reporting')
            $title=4;

        if($title=='hide')
        {
            $item->__open_name=new \StdClass();
            $item->__open_name='hide';
        }
        elseif($title)
        {
            $item->__open_name=new \StdClass();
            $item->__open_name=trans('tender.info_title.title'.$title);
        }
    }

    private function get_stage1TenderID(&$item)
    {
        if(!empty($item->dialogueID))
        {
            $stage1Tender=file_get_contents(env('API').'/tenders/'.$item->dialogueID);

            if(!empty($stage1Tender))
            {
                $stage1Tender=json_decode($stage1Tender);

                if(!empty($stage1Tender->data->tenderID))
                    $item->__stage1TenderID=$stage1Tender->data->tenderID;
            }
        }
    }

    private function get_stage2TenderID(&$item)
    {
        if(!empty($item->stage2TenderID))
        {
            $stage2Tender=file_get_contents(env('API').'/tenders/'.$item->stage2TenderID);

            if(!empty($stage2Tender))
            {
                $stage2Tender=json_decode($stage2Tender);

                if(!empty($stage2Tender->data->tenderID))
                    $item->__stage2TenderID=$stage2Tender->data->tenderID;
            }
        }
    }

    private function get_procedure(&$item)
    {
        if($item->procurementMethod=='open' && $item->procurementMethodType=='belowThreshold')
            $name='Допорогові закупівлі';

        if($item->procurementMethod=='open' && $item->procurementMethodType=='aboveThresholdUA')
            $name='Відкриті торги';

        if($item->procurementMethod=='open' && $item->procurementMethodType=='aboveThresholdEU')
            $name='Відкриті торги з публікацією англ.мовою';

        if($item->procurementMethod=='limited' && $item->procurementMethodType=='reporting')
            $name='Звіт про укладений договір';

        if($item->procurementMethod=='limited' && $item->procurementMethodType=='negotiation')
            $name='Переговорна процедура';

        if($item->procurementMethod=='limited' && $item->procurementMethodType=='negotiation.quick')
            $name='Переговорна процедура за нагальною потребою';

        if($item->procurementMethodType=='')
            $name='Без застосування електронної системи';

        if($item->procurementMethod=='open' && $item->procurementMethodType=='aboveThresholdUA.defense')
            $name='Переговорна процедура для потреб оборони';

        if($item->procurementMethod=='open' && $item->procurementMethodType=='competitiveDialogueUA')
            $name='Конкурентний діалог';

        if($item->procurementMethod=='open' && $item->procurementMethodType=='competitiveDialogueEU')
            $name='Конкурентний діалог з публікацією на англ. мові';

        if($item->procurementMethod=='selective' && $item->procurementMethodType=='competitiveDialogueUA.stage2')
            $name='Конкурентний діалог (2ий етап)';

        if($item->procurementMethod=='selective' && $item->procurementMethodType=='competitiveDialogueEU.stage2')
            $name='Конкурентний діалог з публікацією на англ. мові (2ий етап)';

        $item->__procedure_name=new \StdClass();
        $item->__procedure_name=!isset($name) ? $item->procurementMethodType : $name;
    }

    private function parse_is_sign(&$item)
    {
        $item->__is_sign=new \StdClass();

        $is_sign=false;

        if(!empty($item->documents))
        {
            $is_sign=array_where($item->documents, function($key, $document){
                return $document->title=='sign.p7s';
            });
        }

        if($is_sign)
        {
            $url=head($is_sign)->url;
            $url=substr($url, 0, strpos($url, 'documents/')-1);

            $item->__sign_url=new \StdClass();
            $item->__sign_url=$url;
        }

        $item->__is_sign=!empty($is_sign);
    }

    private function parse_eu(&$item)
    {
        if(!empty($item->procurementMethod))
        {
            if($item->procurementMethod=='open' && $item->procurementMethodType=='aboveThresholdEU')
            {
                //if(in_array($item->status, ['active.pre-qualification', 'active.auction', 'active.pre-qualification.stand-still']))
                //    unset($item->bids);
            }
        }
    }

    private function sanitize_html($html)
    {
        $search = array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s');
        $replace = array('>', '<', '\\1');

        $html = preg_replace($search, $replace, $html);

        return $html;
    }

    private function parse_contracts_json($id)
    {
        //Cache::forget('contracts_'.$id);
        return Cache::remember('contracts_'.$id, 15, function() use ($id)
        {
            $url=env('API_CONTRACT').'/'.$id;

            try {
                $headers = get_headers($url);
                $contents = false;

                if (!empty($headers) && ((int)substr($headers[0], 9, 3) == 200 || (int)substr($headers[0], 9, 3) == 301))
                    $contents = file_get_contents($url);

                return $contents ? json_decode($contents)->data : false;
            } catch (\Exception $e) {
            } catch (\ParseException $e) {
                return false;
            }
       });
    }

    private function parse_rationale_type()
    {
        return Cache::remember('rationale_type', 60, function()
        {
            $contents=file_get_contents('http://standards.openprocurement.org/codelists/contract-change-rationale_type/uk.json');

            return $contents ? json_decode($contents) : false;
        });
    }

    private function parse_work_days()
    {
        return Cache::remember('work_days', 60, function()
        {
            $days=[];

            $off=file_get_contents('http://standards.openprocurement.org/calendar/workdays-off.json');
            $on=file_get_contents('http://standards.openprocurement.org/calendar/weekends-on.json');

            if($off)
                $days=array_merge($days, json_decode($off, true));

            if($on)
                $days=array_merge($days, json_decode($on, true));

            return $days;
        });
    }
}
