<?php

namespace App\Http\Controllers;

use App\Page;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App;

class JudgePracticesController extends BaseController
{
    public function item(Request $request, $slug)
    {
        $model=new App\Models\JudgePractice();
        $page_model=new Page();
        $page=$page_model->where('url', '/amku-practice')->first();

        $locale = (trim($request->route()->getPrefix(), '/'))?:App\Classes\Lang::getDefault();

        $page_blocks=false;

        if(!empty($page->{'longread_' . $locale}))
        {
            $blocks = (array) json_decode($page->{'longread_' . $locale});
            $page_blocks = new App\Classes\Longread($blocks, $page->id, $page->backendNamespace);
        }

        $practice=$model->enabled()->where('slug', $slug)->first();

        foreach($practice->items as $item){
            $item->practice=json_decode($item->{'practice_'.$locale});
        }

        $practices=App\Models\JudgePractice::enabled()->get();

        $practices->each(function ($item, $key) {
            return $item ? $item->translate() : null;
        });

        return $this->render('pages/judgepractice/item', [
            'practice' => $practice,
            'locale' => $locale,
            'blocks' => $page_blocks ? $page_blocks->getBlocks() : null,
            'block' =>(object)[
                'data' => [
                    'practices'=>$practices
                ]
            ]
        ]);
    }
}
