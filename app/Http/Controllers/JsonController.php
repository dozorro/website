<?php namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Cache;
use Config;

class JsonController extends BaseController
{
    var $types=['all', 'tender', 'contractor', 'type1', 'type2', 'level1', 'level2', 'level3', 'level4'];
    
	public function platforms($type='all')
	{
		if(!in_array($type, $this->types))
			abort(404);

		$platforms=Config::get('platforms');

		foreach($platforms as $k=>$item)
		{
			$platforms[$k]['logo']=env('ROOT_URL').'/assets/images/platforms/'.$item['slug'].'.png';

            if($item[$type])
            {
                $url=parse_url(!empty($item['public']) ? $item['public'] : $item['href']);
    
                $platforms[$k]['href']=$url['scheme'].'://'.$url['host'].$url['path'];

                foreach($this->types as $t)
                    unset($platforms[$k][$t]);
            }
            else
                unset($platforms[$k]);
		};

		return response()->json($platforms, 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
	}
	
	public function announced_tenders()
	{
        $last=Cache::remember('get_last_homepage', 60, function()
        {
            return app('App\Http\Controllers\FormController')->getSearchResults([
                'status=active.enquiries',
                'status=active.tendering'
            ]);
        });

        $last=json_decode($last);
        $out=[];
        
        if(!empty($last->items))
        {
            foreach($last->items as $item)
            {
                array_push($out, [
                    'amount'=>$item->value->amount,
                    'currency'=>$item->value->currency,
                    'title'=>$item->title,
                    'tenderID'=>$item->tenderID,
                    'locality'=>!empty($item->procuringEntity->address->locality) ? $item->procuringEntity->address->locality : '',
                    'name'=>!empty($item->procuringEntity->name) ? $item->procuringEntity->name : '',
                ]);
            }
        }

        return response()->json($out, 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'UTF-8'
        ], JSON_UNESCAPED_UNICODE);
    	}
}
