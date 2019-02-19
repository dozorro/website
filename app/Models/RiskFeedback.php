<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskFeedback extends Model
{
    public $timestamps = false;
    
    protected $table = 'dozorro_risks_inbox_feedback';
}
