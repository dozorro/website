<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class MvpMetricsType extends Model
{
    protected $table = 'dozorro_profile_tenderer_metrics_types';

    /*
    public function mvp_data()
    {
        return $this->belongsTo('App\Models\MvpMetricsData', 'code', 'metric_id');
    }

    public function mvp_data_second()
    {
        return $this->belongsTo('App\Models\MvpMetricsData', 'second_metric', 'metric_id');
    }

    public function mvp_data_third()
    {
        return $this->belongsTo('App\Models\MvpMetricsData', 'third_metric', 'metric_id');
    }
    */

    public function loadMvpData($metricsData)
    {
        $this->mvp_data = array_first($metricsData, function($key, $item) {
            return $this->code == $item->metric_id;
        });
        $this->mvp_data_second = array_first($metricsData, function($key, $item) {
            return $this->second_metric == $item->metric_id;
        });
        $this->mvp_data_third = array_first($metricsData, function($key, $item) {
            return $this->third_metric == $item->metric_id;
        });
    }
}
