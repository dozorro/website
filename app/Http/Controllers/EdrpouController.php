<?php

namespace App\Http\Controllers;

use App\JsonForm;
use Carbon\Carbon;
use Input;

class EdrpouController extends BaseController
{
    public function results()
    {
        $this->setSeoData([
            'title' => 'Пошук за замовником',
        ]);

        if(empty(Input::get('code'))){
            abort(404);
        }

        $codes=explode(',', Input::get('code'));
        $forms=JsonForm::whereIn('entity_id', $codes)->groupBy('tender')->orderBy('date', 'DESC')->get();
        $items=[];
        
        foreach($forms as $form){
            array_push($items, json_decode($form->tender_json));
        }

        return $this->render('pages.edrpou', [
            'items'=>$items
        ]);
    }
}
