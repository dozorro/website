<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coin extends Model {

    protected $table = 'perevorot_dozorro_coins';
    public $backendNamespace = 'Perevorot\Dozorro\Models\Coin';
    protected $fillable = ['sum', 'type', 'dt', 'author', 'comment', 'ngo_profile_id', 'object_id'];
    public $timestamps = false;
    public $dates = ['dt'];

}
