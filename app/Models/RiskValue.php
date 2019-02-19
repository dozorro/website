<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskValue extends Model
{
    protected $table = 'dozorro_risk_rating';

    public function risk()
    {
        return $this->belongsTo('App\Models\Risk', 'risk_flags', 'risk_code');
    }
}
