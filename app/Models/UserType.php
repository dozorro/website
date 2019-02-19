<?php

namespace App\Models;

use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use ModelTranslation;

    protected $table = 'perevorot_dozorro_user_types';
    public $backendNamespace = 'Perevorot\Dozorro\Models\UserType';
}
