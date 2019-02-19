<?php

namespace App\Http\Controllers;

use Input;
use Request;
use App;

class PrintController extends BaseController
{
    var $types = [
        'open',
        'limited',
        'limited-reporting',
        'report',
        'bids',
        'awards',
        'qualifications',
        'active-awards',
        'contract-ongoing',
        'contract-changes'
    ];
    
	public function plan_list($output)
	{
        $limit=100;

        list($query_array, $preselected_values)=app('App\Http\Controllers\PageController')->parse_search_query();

        if((substr_count(Request::server('QUERY_STRING'), 'edrpou')==1 && substr_count(Request::server('QUERY_STRING'), 'dateplan')==1)==false)
            return abort(404);

        $FormController=app('App\Http\Controllers\FormController');
        $FormController->search_type='plan';

        $query_array[]='limit='.$limit;
        $query_array[]='start=0';

        	$json=$FormController->getSearchResults($query_array);
        $data=json_decode($json);

        if(empty($data->items))
            return 'no items';

        $total=$data->total;
        $pages=(int) ceil($total/$limit);

        $items=$data->items;

        for($i=1;$i<$pages;$i++)
        {
            $query_array[sizeof($query_array)-1]='start='.$i*$limit;

            $json=$FormController->getSearchResults($query_array);
            $data=json_decode($json);

            if(empty($data->items))
                break;

            $items=array_merge($items, $data->items);

            usleep(1000000);
        }

        array_map(function($item){
            $item->__procedure=new \StdClass();
            $item->__procedure=false;

            if(in_array($item->tender->procurementMethodType, ['aboveThresholdUA', 'aboveThresholdEU'])){
                $item->__procedure='open';
            }

            if(in_array($item->tender->procurementMethodType, ['negotiation', 'negotiation.quick'])){
                $item->__procedure='talk';
            }

            if(empty($item->tender->procurementMethodType)){
                $item->__procedure='no';
            }

            if(in_array($item->tender->procurementMethodType, ['belowThreshold'])){
                $item->__procedure='belowThreshold';
            }

            if(in_array($item->tender->procurementMethodType, ['reporting'])){
                $item->__procedure='reporting';
            }
        }, $items);
        

        array_map(function($item){
            $date=explode('-', substr($item->planID, 5, 10));
            $date=mktime(0, 0, 0, (int) $date[1], (int) $date[2], (int) $date[0]);

            $item->__planDate=new \StdClass();
            $item->__planDate=$date;
        }, $items);

        array_map(function($item){
            $item->__dateModified=new \StdClass();
            $item->__dateModified=date('d.m.Y', $item->__planDate)!=date('d.m.Y', strtotime($item->dateModified)) ? true : false;
        }, $items);

        array_map(function($item){
            $item->__kekv=new \StdClass();
            $item->__kekv=array_where($item->additionalClassifications, function($key, $classification){
                return $classification->scheme=='КЕКВ';
            });
        }, $items);

        $page=app('App\Http\Controllers\PageController');
    
        foreach($items as $item)
            $page->plan_check_start_month($item);

        $main=array_where($items, function($key, $item){
            return in_array($item->__procedure, ['open', 'talk']);
        });

        $additional=array_where($items, function($key, $item){
            return in_array($item->__procedure, ['no', 'belowThreshold', 'reporting']);
        });

        return $this->render('pages.print.plan.list', [
            'main' => $main,
            'additional' => $additional,
            'budget' => head($items)->budget,
            'procuringEntity' => head($items)->procuringEntity,
        ]);;
	}

	public function one($tender_id, $type, $output='html', $lot_id=null)
	{
		if(!in_array($type, $this->types))
			abort(404);

        $item=app('App\Http\Controllers\PageController')->tender_parse($tender_id);

        if(!empty($item->lots) && sizeof($item->lots)==1)
            $lot_id=$item->lots[0]->id;

        //if($item->__print_href!=$type)
        //abort(404);

        if($output=='pdf')
        {
            $pdf=\App::make('dompdf.wrapper');
    
            $pdf->loadView('pages/print/tender/'.$type, [
                'item'=>$item,
                'lot_id'=>$lot_id
            ]);

            return $pdf->stream();
        }

        return $this->render('pages.print.tender.' . $type, [
            'item' => $item,
            'lot_id' => $lot_id
        ]);
	}
}
