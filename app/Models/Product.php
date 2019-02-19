<?php

namespace App\Models;

use App\Helpers;
use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;
use DB;

/**
 * Model
 */
class Product extends Model
{
    use ModelTranslation;

    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'perevorot_dozorro_products';

    public $backendNamespace = 'Perevorot\Dozorro\Models\Product';

    public static function findByName($name)
    {
        return self::where('name', $name)->first();
    }
}