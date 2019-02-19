<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Page;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App;
use App\ActualTender;

class ComplaintsController extends BaseController
{
    public function item(Request $request, $slug, $content_slug=false)
    {
        $type=$slug=='below' ? 1:2;
        $model=new Complaint();
        $page_model=new Page();
        $page=$page_model->where('url', '/complaints')->first();

        $locale = (trim($request->route()->getPrefix(), '/'))?:App\Classes\Lang::getDefault();

        $page_blocks=false;

        if(!empty($page->{'longread_' . $locale}))
        {
            $blocks = (array) json_decode($page->{'longread_' . $locale});
            $page_blocks = new App\Classes\Longread($blocks, $page->id, $page->backendNamespace);
        }

        $items=$model->where('type', '=', $type)->get();
        $content_blocks=false;

        if($content_slug)
        {
            $content=array_first($items, function($k, $item) use($content_slug){
                return $item->slug==$content_slug;
            });

            $blocks = (array) json_decode($content->{'longread_' . $locale});
            $content_blocks = new App\Classes\Longread($blocks, $content->id, $content->backendNamespace);
            $content_blocks=$content_blocks->getBlocks();
            
            if(!$content)
                abort(404);
        }

        return $this->render('pages/complaints/type', [
            'items' => $items,
            'type' => $slug,
            'content_blocks' => $content_blocks,
            'blocks' => $page_blocks ? $page_blocks->getBlocks() : null,
        ]);
    }
}
