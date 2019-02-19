<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActualTender extends Model
{
    protected $table = 'perevorot_dozorro_actual_tenders';

    public function get_format_data()
    {
        if(isset($this->data)) {
            $status=json_decode(file_get_contents('./sources/ua/status.json'), true);

            $data=json_decode($this->data);

            if(!empty($data->status))
                $data->status=$status[$data->status];
            
            return $data;

            $items = json_decode($this->data);

            if (isset($items->items) && !empty($items->items)) {
                return current($items->items);
            }
        }

        return false;
    }

    public function scopeLimit($query, $limit)
    {
        if($limit > 1)
        {
            return $query->take($limit)->get();
        }
        elseif($limit == 1)
        {
            return $query->first();
        }
        elseif(!$limit)
        {
            return $query->get();
        }
    }

    public static function getAllActualTenders($params = [])
    {
        $tenders = self::orderBy('sort_order', 'asc')->limit(@$params['limit']);

        $tender_ids = [];

        foreach ($tenders as $k => $tender) {
            $item = $tender->get_format_data();

            if(isset($item->id)) {
                array_push($tender_ids, $item->id);
            }
        }

        $forms = JsonForm::whereIn('tender', $tender_ids)->get();

        foreach ($tenders as $k => $tender) {
            $tenders[$k]->comments = sizeof(array_where($forms, function($key, $form) use ($tender){
                if(!empty($tender->data))
                    $json=json_decode($tender->data);

                return !empty($json) && $form->tender==$json->id;
            }));
        }

        return $tenders;
    }

}
