<?php

namespace App;

use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use ModelTranslation;

    /**
     * @var string
     */
    protected $table = 'system_settings';

    /**
     * @var
     */
    public static $instance;

    /**
     * @var mixed
     */
    public static $value;

    /**
     * @param $key
     * @return mixed
     */

    public $backendNamespace = 'Perevorot\Dozorro\Models\Form';

    public static function instance($key, $backendNamespace = null)
    {
        if (!self::$instance) {
            /*self::$instance = (new static())
                ->where('item', $key)
                ->first();
            ;*/
            self::$instance = (new static())
                ->all();
            ;
        }

        if (self::$instance) {

            $setting = self::$instance->where('item', $key)->first();

            $setting->translate($backendNamespace);
            self::$value = json_decode($setting->value, 1);
            self::$value = array_intersect_key(array_replace(self::$value, $setting->attributes), json_decode($setting->value, 1));
        }

        return (object) self::$value;
    }
}
