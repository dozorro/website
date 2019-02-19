<?php

namespace App\Models\Monitoring;

use App\Helpers;
use App\Models\Product;
use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

/**
 * Model
 */
class Tender extends Model
{
    use ModelTranslation;

    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'perevorot_dozorro_monitoring_tenders';

    public function items()
    {
        return $this->hasMany('App\Models\Monitoring\Item', 'tender_id', 'id');
    }

    public function get_region($code) {

        $regions = json_decode(file_get_contents(public_path() . '/sources/'.Config::get('locales.current').'/region.json'));

        foreach($regions as $id => $name) {

            if(stripos($id, "-") !== FALSE) {
                $range = explode("-", $id);
            } else {
                $range[] = (int)$id;
                $range[] = (int)$id;
            }

            $pc = substr($code, 0, 2);

            for($i = $range[0]; $i <= $range[1]; $i++) {
                if($i == (int)$pc) {
                    return $id;
                    break 2;
                }
            }
        }
    }

    public static function getData($params = [])
    {
        return self::
            with('items')
            ->byEdrpou(@$params['edrpou'])
            ->byRegion(@$params['region'])
            ->byProducts($params)
            ->byCpv(@$params['cpv'])
            ->byStatus(@$params['status'])
            ->byTender(@$params['tid'])
            ->byIsReady(@$params['ready'])
            ->orderBy('date', 'desc')
            ->byPaginate(@$params['paginate']);
    }

    public function scopebyPaginate($query, $data) {
        if($data) {

            $_data = $query->paginate(10);

            foreach($_data AS $k => $item) {
                $_data[$k] = $item->translate();
            }

            return $_data;
        } else {
            return $query->get()->each(function ($item, $key) {
                return $item ? $item->translate() : null;
            });;
        }
    }

    public function scopeByProducts($query, $data)
    {
        if(!empty($data['product_names'])) {
            $names = Product::whereIn('id', $data['product_names'])->get();

            if (!$names->isEmpty()) {
                return $query->whereHas(
                    'items', function ($query) use ($names) {
                        $query->whereIn('name', array_pluck($names, 'name'));
                    }
                );
            }
        }
    }

    public function scopeByRegion($query, $data)
    {
        if (is_array($data) && !empty($data)) {
            return $query->whereIn('region', $data);
        } elseif($data) {
            return $query->whereIn('region', explode(',', $data));
        }
    }

    public function scopeByIsReady($query, $data)
    {
        if ($data !== null) {
            return $query->where('is_ready', $data);
        }
    }

    public function scopeByStatus($query, $data)
    {
        if ($data) {
            return $query->where('tender_status', $data);
        }
    }

    public function scopeByCpv($query, $data)
    {
        if ($data) {
            return $query->where('cpv', 'like', '%' . $data . '%');
        }
    }

    public function scopeByEdrpou($query, $data)
    {
        if ($data && is_array($data) && !empty($data)) {
            return $query->whereIn('entity_id', $data);
        }
    }

    public function scopeByTenderId($query, $data)
    {
        if ($data) {
            return $query->where('tender', $data);
        }
    }

    public function scopeByTender($query, $data)
    {
        if ($data) {
            return $query->where('tender_id', $data);
        }
    }
}