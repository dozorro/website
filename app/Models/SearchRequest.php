<?php

namespace App\Models;

use App\File;
use App\Helpers;
use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;
use DB;

class SearchRequest extends Model
{
    use ModelTranslation;

    protected $table = 'perevorot_dozorro_search_requests';
    public $backendNamespace = 'Perevorot\Dozorro\Models\SearchRequest';

    public function getRequestAttribute($value)
    {
        return '?'.ltrim(ltrim($value, '/'), '?');
    }
}
