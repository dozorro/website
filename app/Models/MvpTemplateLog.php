<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MvpTemplateLog extends Model
{
    public $dates = ['created_at'];
    public $timestamps = false;

    protected $table = 'perevorot_dozorro_profile_tenderer_templates_log';

    public $backendNamespace = 'Perevorot\Dozorro\Models\MvpTemplateLog';
}
