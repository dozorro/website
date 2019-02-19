<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MvpMetricsData extends Model
{
    protected $table = 'dozorro_profile_tenderer_metrics_data';

    public function formatMetricValue($instance)
    {
        $value = $this->getMetricValueAttribute($instance->metric_value);
        $t = explode(' ', $value);

        if($instance->display_decimals) {
            $instance->metric_value = $value;
        } else {
            $instance->metric_value = trim(round($value).' '.@$t[1]);
        }

        return $instance;
    }

    public function showMetricValue($type)
    {
        $value = $this->changedMetricValue($type);
        return $value;
    }

    public function changedMetricValue($type)
    {
        $suffix = $type->suffix;
        $value = $this->metric_value;
        $minus = $value < 0;
        $value = abs($value);
        $count = strlen(round($value));
        $delimiter = '';

        switch ($count) {
            case 1:
                $value = number_format($value, 2, '.', '');
                break;
            case 2:
                $value = number_format($value, 1, '.', '');
                break;
            case 3:
                $value = number_format($value, 0, '.', '');
                break;
            case 4:
            case 5:
            case 6:
                $value = $value / 1000;
                $delimiter = t('profile.10^3');
                break;
            case 7:
            case 8:
            case 9:
                $value = $value / 1000000;
                $delimiter = t('profile.10^6');
                break;
            case 10:
            case 11:
            case 12:
                $value = $value / 1000000000;
                $delimiter = t('profile.10^9');
                break;
            default:
                $value = $value / 1000000000000;
                $delimiter = t('profile.10^12');
                break;
        }

        if($count > 2) {

            if(strpos($value, 'E-')) {
               $value = explode('E-', $value)[0];
            }

            $count = 3 - strlen(explode('.', $value)[0]);
            $count = $count < 0 ? 0 : $count;
            $value = number_format($value, $count, '.', '');
        }

        $value = $type->display_decimals ? $value : round($value);

        if(!empty($delimiter)) {
            $delimiter = '<span>'.$delimiter.'</span>';
        } else {
            $delimiter = (!empty($suffix) ? ('<span>'.$suffix.'</span>') : '');
        }

        if($minus) {
            $value *= -1;
        }

        return $value.' '.$delimiter;
    }
}
