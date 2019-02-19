<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model
 */
class UserGroup extends Model
{
    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'perevorot_dozorro_user_groups';
}