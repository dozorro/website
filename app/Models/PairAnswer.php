<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PairAnswer extends Model
{
    public $timestamps = false;
    protected $table = 'dozorro_model_answers';
    public $fillable = ['*'];
}
