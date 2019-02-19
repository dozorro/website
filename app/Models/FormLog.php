<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormLog extends Model
{
    protected $table = 'perevorot_dozorro_json_forms_log';
    public $backendNamespace = 'Perevorot\Form\Models\Log';

    protected $fillable = ['*'];
    public $timestamps = false;

    public $dates = ['created_at'];
}
