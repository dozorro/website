<?php

namespace App\Models\Monitoring;

use App\Helpers;
use App\Models\Product;
use App\Traits\ModelTranslation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Collection;

/**
 * Model
 */
class Item extends Model
{
    use ModelTranslation;

    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'perevorot_dozorro_monitoring_tender_items';

    public function tender()
    {
        return $this->belongsTo('App\Models\Monitoring\Tender');
    }

    public static function findByTenderId($data) {
        $r = self::ByTenderId($data)->first();
        return $r ? $r->translate() : null;
    }

    public function scopeByTenderId($query, $data) {
        return $query->where('tender_id', $data);
    }

    public function scopeByTender($query, $data) {
        return $query->where('tender', $data);
    }

    public function formatPrice($price) {
        return number_format($price, 2, '.', ' ');
    }

    public function editPrice() {
        return number_format($this->price, 2, '.', ' ');
    }

    public static function getData($params = [])
    {
        return self::
            with('tender')
            ->byEdrpou(@$params['edrpou'])
            ->byTid(@$params['tid'])
            ->byRegion(@$params['regions'])
            ->byProducts(@$params['products'])
            ->byMeasures(@$params['measures'])
            ->byDate(@$params['date'])
            //->orderBy('date', 'desc')
            ->byPaginate(@$params['paginate']);
    }

    public function scopeByDate($query, $data)
    {
        if(!empty($data)) {
            return $query->whereHas('tender', function ($query) use ($data) {
                $date = Carbon::createFromFormat('d.m.Y', $data[0])->format('Y-m-d H:i:s');
                $query->where('date', '>=', $date);

                $date = Carbon::createFromFormat('d.m.Y', $data[1])->format('Y-m-d H:i:s');
                $query->where('date', '<=', $date);
            });
        }
    }

    public function scopeByTid($query, $data)
    {
        if (is_array($data) && !empty($data)) {
            return $query->whereHas('tender', function($query) use ($data) {
                $query->whereIn('tender_id', $data);
            });
        }
    }

    public function scopeByEdrpou($query, $data)
    {
        if (is_array($data) && !empty($data)) {
            return $query->whereHas('tender', function($query) use ($data) {
                $query->whereIn('entity_id', $data);
            });
        }
    }

    public function scopeByRegion($query, $data)
    {
        if (is_array($data) && !empty($data)) {
            return $query->whereHas('tender', function($query) use ($data) {
                $query->whereIn('region', $data);
            });
        }
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
        if(!$data->isEmpty()) {
            return $query->whereIn('name', array_pluck($data, 'name'));
        }
    }

    public function scopeByMeasures($query, $data)
    {
        if($data) {
            return $query->whereIn('measure', $data);
        }
    }
}