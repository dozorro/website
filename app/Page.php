<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use App\Traits\ModelTranslation;

class Page extends Model
{
    use ModelTranslation;

    protected $table = 'perevorot_page_page';
    public $backendNamespace = 'Perevorot\Page\Models\Page';
    
    protected $children = [];

    public function children()
    {
        if (!$this->children) {
            $pages = Page::where('nest_left', '>', $this->nest_left)
                ->where('nest_right', '<', $this->nest_right)
                ->where('nest_depth', $this->nest_depth + 1)
                ->where('menu_id', $this->menu_id)
                ->where('is_hidden', false)
                ->where('is_disabled', false)
                ->orderBy('nest_left')
                ->get()
            ;

            $this->children = Helpers::filterActivePages($pages);
        }

        return $this->children;
    }
    
    public function alias_page()
    {
        return Page::where('id', $this->alias_page_id)->first();
    }
    
    public function getHrefAttribute()
    {
        switch($this->type){
            case 1:
                $href=Helpers::getLocalizedUrl(!$this->url && $this->url_external ? $this->url_external : $this->url);

                break;

            case 3:
                $href=Helpers::getLocalizedUrl(!empty($this->alias_page_id) ? $this->alias_page()->url : '');

                break;

            case 4:
                $href=$this->url_external;

                break;
            default:
                $href = '';
                break;
        }

        return $href;
    }
}
