<?php

namespace App\Models;

use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;

class MvpTemplate extends Model
{
    use ModelTranslation;

    protected $table = 'perevorot_dozorro_mvp_templates';

    public $backendNamespace = 'Perevorot\Dozorro\Models\MvpTemplate';

    public function setting()
    {
        return $this->belongsTo('App\Models\MvpSetting', 'setting_id', 'id');
    }
}
