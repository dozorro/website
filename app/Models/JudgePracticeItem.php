<?php

namespace App\Models;

use App\Helpers;
use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;
use DB;

/**
 * Model
 */
class JudgePracticeItem extends Model
{
    use ModelTranslation;

    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'perevorot_dozorro_judge_practice_items';

    public $backendNamespace = 'Perevorot\Dozorro\Models\JudgePracticeItem';
}
