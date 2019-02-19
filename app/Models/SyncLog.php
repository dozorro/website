<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $table = 'perevorot_dozorro_syncs_log';
    public $backendNamespace = 'Perevorot\Form\Models\SyncLog';

    protected $fillable = ['*'];
    public $timestamps = false;

    public $dates = ['created_at'];
}
