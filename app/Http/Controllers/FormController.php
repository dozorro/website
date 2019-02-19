<?php

namespace App\Http\Controllers;

use App\Classes\Lang;
use App\Models\UserType;
use Cache;
use Illuminate\Support\Facades\Log;
use Input;
use View;
use Config;
use Session;
use DB;

class FormController extends BaseController
{
    public $blocks=[
        'measure',
        'risk',
        'cpv',
        'date',
        'dkpp',
        'edrpou',
        'region',
        'procedure_t',
        'procedure_p',
        'status',
        'tid'
    ];

    public $search_type='tender';

    public function search($search_type='tender')
    {
        $this->search_type=$search_type;

        $result=$this->getSearchResultsHtml(Input::get('query'), true);
        $userTypes = UserType::all('title');

        if(!empty($result->count)) {

            $json = [
                'userTypes' => !$userTypes->isEmpty() ? array_column($userTypes->toArray(), 'title') : [],
                'count' => $result->count,
                'html'=>$result->html->content(),
                'highlight'=>app('App\Http\Controllers\PageController')->getSearchResultsHightlightArray(implode('&', Input::get('query')))
            ];
        } else {
            $json = [
                'userTypes' => !$userTypes->isEmpty() ? array_column($userTypes->toArray(), 'title') : [],
                'count' => 0,
                'html'=>is_object($result) ? $result->content() : ''
            ];
        }

        return response()->json($json, 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function getSearchResultsHtml($query, $ga = false)
    {
        $out=false;
        $count = 0;

        if($query)
        {
            $json=$this->getSearchResults($query);
            $data=json_decode($json);

            if(!empty($data->items))
            {
                $count = $data->total;
                $callback=camel_case('prepare_'.$this->search_type);

                if($ga) {
                    return (object)['html' => $this->$callback($data), 'count' => $count];
                } else {
                    return $this->$callback($data);
                }
            }
            elseif(empty($data) || (property_exists($data, 'items') && is_array($data->items) && !sizeof($data->items)))
            {
                $out = $this->render('pages.results', [
                    'error' => trans('form.no_results')
                ]);
            }
            elseif(!empty($data->error))
            {
                $out = $this->render('pages.results', [
                    'error' => !empty($data->error) ? $data->error : false,
                ]);
            }
        }

        return $out;
    }

    private function prepareTender($data)
    {
        $dataStatus=[];

        foreach($data->items as $k=>$item)
        {
            $item->__icon=new \StdClass();
            $item->__icon=starts_with($item->tenderID, 'ocds-random-ua')?'pen':'mouse';

            $data->items[$k]=$item;
        }

        foreach($this->get_status_data() as $one)
            $dataStatus[$one['id']]=$one['name'];

        $tender_ids=array_pluck($data->items, 'id');

        $ngo_forms=DB::table('perevorot_dozorro_json_forms')->select('schema', 'tender')->whereIn('tender', $tender_ids)->where('schema', 'F201')->lists('schema', 'tender');

        foreach($data->items as $item) {
            $item->__is_F201=array_key_exists($item->id, $ngo_forms);
        }

        return $this->render('pages.results', [
            'total' => $data->total,
            'search_type' => $this->search_type,
            'error' => false,
            'dataStatus' => $dataStatus,
            'start' => ((int) Input::get('start') + Config::get('prozorro.page_limit')),
            'items' => $data->items
        ]);
    }

    private function preparePlan($data)
    {
        $page=app('App\Http\Controllers\PageController');

	    foreach($data->items as $item)
            $page->plan_check_start_month($item);

        return $this->render('pages.results', [
            'total' => $data->total,
            'search_type' => $this->search_type,
            'error' => false,
            'start' => ((int) Input::get('start') + Config::get('prozorro.page_limit')),
            'items' => $data->items,
        ]);
    }

    public function getSearchResults($query, $skipStart = false)
    {
        if(env('API_PRETEND'))
            return file_get_contents('./sources/pretend/results.json');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if(env('API_LOGIN') && env('API_PASSWORD')){
            curl_setopt($ch, CURLOPT_USERPWD, env('API_LOGIN') . ":" . env('API_PASSWORD'));
        }

        $risks = [];

        if(!empty($query)) {
            foreach ($query as $k => $q) {
                if (starts_with($q, ['risk'])) {
                    $risk = explode('=', $q, 2);
                    unset($query[$k]);
                    $risks[] = $risk[1];

                } elseif (starts_with($q, ['procedure_t', 'procedure_p'])) {
                    $url = explode('=', $q, 2);
                    unset($query[$k]);
                    foreach (explode(',', $url[1]) as $u) {
                        $query[] = 'proc_type=' . $u;
                    }
                } elseif (substr($q, 0, 4) == 'cpv=') {
                    $url = explode('=', $q, 2);
                    //$cpv=explode('-', $url[1]);

                    $query[$k] = $url[0] . '=' . $url[1];//rtrim($cpv[0], '0');
                } elseif (substr($q, 0, 5) == 'date[' || substr($q, 0, 9) == 'dateplan[') {
                    if (strpos($q, 'dateplan') !== false) {
                        $one_date = str_replace(['dateplan[', ']='], ['', '='], $q);
                    } else {
                        $one_date = str_replace(['date[', ']='], ['', '='], $q);
                    }

                    $one_date = preg_split('/(=|—)/', $one_date);

                    if (sizeof($one_date) == 3) {
                        $query[$k] = $one_date[0] . '_start=' . $this->convert_date($one_date[1]) . '&' . $one_date[0] . '_end=' . $this->convert_date($one_date[2],
                                new \DateInterval('P1D'));
                    } else {
                        unset($query[$k]);
                    }
                } else {
                    $url = explode('=', $q, 2);

                    if (!empty($url[1])) {
                        $query[$k] = $url[0] . '=' . str_replace([' '], ['+'], $url[1]);
                    } else {
                        unset($query[$k]);
                    }
                }
            }
        }

        if(!empty($risks)) {
            $start = Input::get('start');
            $_start = !$start ? 0 : $start;
            $min = count($risks);
            $sql = [];

            foreach($risks as $risk) {
                $sql[] = "risk_flags LIKE '%$risk%'";
            }

            $sql = 'SELECT distinct *
                          FROM `dozorro_risk_rating` WHERE '.implode(' OR ', $sql).'                            
                            order by risk_value DESC limit '.$_start.', 10';
                        //  group by tender_id having _count = '.$min.' limit '.$_start.', 10';

            $rows = DB::select($sql);

            if(!empty($rows)) {
                $query = array_merge($query, array_map(function($v) {
                    return 'id=' . $v;
                }, array_column($rows, 'tender_id')));
            }
        } elseif(!$skipStart) {
            $query[] = 'start=' . Input::get('start');
        }

        $request = head(\Symfony\Component\HttpFoundation\Request::createFromGlobals()->server);
        $page = $request['REQUEST_SCHEME'].'://'.$request['HTTP_HOST'].$request['REQUEST_URI'].$request['QUERY_STRING'];

        $path=Session::get('api_'.$this->search_type, Config::get('api.'.$this->search_type)).'?'.implode('&', $query).'&__url='.$page;

        if(isset($_GET['api']) && getenv('APP_ENV')=='local')
            dd($path);

		curl_setopt($ch, CURLOPT_URL, $path);

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

	private function convert_date($date, $add=false)
	{
		$out=new \DateTime($date);

		if($add)
			$out->add($add);

		return $out->format('Y-m-d');
	}

	public function autocomplete($type=false)
	{

		$out=0;

		if(Input::get('query') && in_array($type, $this->blocks))
		{
			$data_function='get_'.$type.'_data';
			$data=$this->$data_function();

			$query=mb_strtolower(Input::get('query'));
			$out=[];

			foreach($data as $one)
			{
				if(strpos(mb_strtolower($one['id']), $query)!==false || strpos(mb_strtolower($one['name']), $query)!==false)
				{
        				$item=[
						'id'=>$one['id'],
						'name'=>$one['name']
					];

					if(!empty($one['location']))
        					$item['location']=$one['location'];

					array_push($out, $item);
				}
			}
		}

		return response()->json($out, 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function check($search_type='tender', $type=false)
    {
        $out=0;

        if(Input::get('query') && in_array($type, $this->blocks))
        {
            $data_function='get_'.$type.'_data';
            $data=$this->$data_function();


            $query=mb_strtolower(Input::get('query'));

            foreach($data as $one)
            {
                if(strpos(mb_strtolower($one['id']), $query)!==false || strpos(mb_strtolower($one['name']), $query)!==false)
                {
                    $out=1;
                    break;
                }
            }
        }

        return response()->json($out, 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    }

    public function getCpv()
    {
        return $this->get_cpv_data();
    }

    public function data($type=false)
    {
        $out=false;

        if(in_array($type, $this->blocks))
        {
            $data_function='get_'.$type.'_data';

            $out=$this->$data_function();
        }

        return response()->json($out, 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
	}

	private function json($source)
	{
        $lang=Lang::getCurrentLocale();

		$data = Cache::remember('data_'.$source.'_'.$lang, 60, function() use ($lang, $source)
		{
		    if($source !== 'edrpou') {
                $raw = json_decode(file_get_contents('./sources/' . $lang . '/' . $source . '.json'), TRUE);
            } else {
                $raw = $this->data('edrpou');
            }

			$data=[];


			foreach($raw as $id=>$name)
			{
				array_push($data, [
					'id'=>$id,
					'name'=>$name
				]);
			}

		    return $data;
		});

		return $data;
	}

	public function get_cpv_data()
	{
		return $this->json('cpv');
	}

	private function get_dkpp_data()
	{
		return $this->json('dkpp');
	}

    public function get_region_data()
	{
		return $this->json('region');
	}

    public function get_procedure_t_data()
    {
        return $this->json('procedure_t');
    }

    private function get_procedure_p_data()
    {
        return $this->json('procedure_p');
    }

    private function get_proc_type_data()
    {
        return $this->json('proc_type');
    }

	public function get_status_data()
	{
		return $this->json('status');
	}

    public function get_measure_data()
    {
        Cache::forget('data_measure');
        $data = Cache::remember('data_measure', 60, function() {
            $raws = DB::table('perevorot_dozorro_monitoring_tender_items')
                ->distinct('measure')
                ->get();
            $data=[];

            foreach($raws as $row)
            {
                array_push($data, [
                    'id'=>$row->measure,
                    'name'=>$row->measure
                ]);
            }

            return $data;
        });

        return $data;
    }

    public function get_risk_data()
    {
        //Cache::forget('data_risk');
        $data = Cache::remember('data_risk', 60, function() {
            $raws = DB::table('dozorro_risks')
                ->select('risk_code','risk_title')
                ->get();
            $data=[];

            foreach($raws as $risk)
            {
                array_push($data, [
                    'id'=>$risk->risk_code,
                    'name'=>$risk->risk_code.' '.t('indicators.'.$risk->risk_title)
                ]);
            }

            return $data;
        });

        return $data;
    }

	private function get_tid_data()
	{
		return [
			['id'=>'1', 'name'=>'1'],
			['id'=>'2', 'name'=>'2'],
			['id'=>'3', 'name'=>'3'],
		];
	}

	public function get_edrpou_data($qval = false)
	{
        	//return $this->json('edrpou');

        	$query=mb_strtolower($qval ? $qval : (!empty(Input::get('query')) ? Input::get('query') : ''));
            $data=[];

        	if(mb_strlen($query)>1)
        {

            $tenderer = !empty(Input::get('tenderer')) ? Input::get('tenderer') : 0;

            //$response=file_get_contents(env('API_ORGSUGGEST').'?query='.urlencode($query).'&tenderer='.$tenderer);
            $url = env('API_ORGSUGGEST').'?query='.urlencode($query).'&tenderer='.$tenderer;

            $ch=curl_init();

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_URL, $url);

            if(env('API_LOGIN') && env('API_PASSWORD')){
                curl_setopt($ch, CURLOPT_USERPWD, env('API_LOGIN') . ":" . env('API_PASSWORD'));
            }

            $request = head(\Symfony\Component\HttpFoundation\Request::createFromGlobals()->server);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                //'Accept-Encoding: gzip',
                'X-Forwarded-For: '.@$request['REMOTE_ADDR']
            ));

            $response=curl_exec($ch);

            curl_close($ch);

            if($response)
            {
                $response=json_decode($response);

                if(sizeof($response->items))
                {
                    foreach($response->items as $item)
                    {
                        if(!empty($item->name))
                        {
                            array_push($data, [
                                'name'=>!empty($item->short) ? $item->short : $item->name,
                                'id'=>$item->edrpou,
                                'location'=>$item->location
                            ]);
                        }
                    }
                }
            }
        }
        else
        {
            /*
            $json=$this->json('edrpou');
            $data=[];

            foreach($json as $item)
            {
                array_push($data, [
                    'name'=>!empty($item['name'][2]) ? $item['name'][2] : $item['name'][1],
                    'id'=>$item['name'][0],
                    'location'=>$item['name'][3]
                ]);
            }*/
        }

		return $data;
	}
}
