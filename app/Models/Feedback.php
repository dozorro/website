<?php

namespace App\Models;

use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use ModelTranslation;

    protected $table = 'perevorot_dozorro_feedback';
    public $backendNamespace = 'Perevorot\Dozorro\Models\Feedback';
    public $fillable = ['*'];
    public $timestamps = false;

}
