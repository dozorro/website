<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadTenderLog extends Model
{
    protected $table = 'perevorot_dozorro_json_forms_bad_tenders_log';
    public $backendNamespace = 'Perevorot\Form\Models\BadTenderLog';

    protected $fillable = ['*'];
    public $timestamps = false;

    public $dates = ['created_at'];
}
