<?php

namespace App\Models\Monitoring;;

use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;

class MonitoringViolation extends Model
{
    use ModelTranslation;

    public $timestamps = false;
    public $dates = ['created_at'];
    protected $table = 'perevorot_dozorro_monitoring_violations';
    public $backendNamespace = 'Perevorot\Dozorro\Models\Monitoring\MonitoringViolation';

    public function violation()
    {
        return $this->belongsTo('App\Models\Violation');
    }
}
