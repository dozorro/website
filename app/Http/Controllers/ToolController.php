<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Page;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App;
use App\ActualTender;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ToolController extends BaseController
{
    public function tool(Request $request, $slug)
    {
        $page_model = new Page();
        $page = $page_model->where('url', '/tools'.($slug ? ('/'.$slug) : ''))->first();
        $locale = (trim($request->route()->getPrefix(), '/')) ?: App\Classes\Lang::getDefault();
        $page_blocks = false;
        $ngos = null;

        if (!empty($page->{'longread_' . $locale})) {
            $blocks = (array)json_decode($page->{'longread_' . $locale});
            $page_blocks = new App\Classes\Longread($blocks, $page->id, $page->backendNamespace);
        }

        $blocks = $page_blocks ? $page_blocks->getBlocks() : null;

        return $this->render('pages/tool', [
            'blocks' => $blocks,
        ]);
    }
}
