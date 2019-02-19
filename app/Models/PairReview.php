<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PairReview extends Model
{
    public $timestamps = false;
    protected $table = 'dozorro_model_review';

    public function pair()
    {
        return $this->belongsTo('App\Models\Pair');
    }

    public function answers()
    {
        return $this->hasMany('App\Models\PairAnswer', 'pair_id', 'pair_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_email', 'email');
    }
}
