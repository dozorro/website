<?php

namespace App\Models;

use App\Helpers;
use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;
use DB;

/**
 * Model
 */
class AmkuPracticeItem extends Model
{
    use ModelTranslation;

    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'perevorot_dozorro_amku_practice_items';

    public $backendNamespace = 'Perevorot\Dozorro\Models\AmkuPracticeItem';
}
