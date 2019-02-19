<?php

namespace App\Models;

use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    use ModelTranslation;

    protected $table = 'perevorot_dozorro_violations';
    public $backendNamespace = 'Perevorot\Dozorro\Models\Violation';

    public function monitoring()
    {
        return $this->belongsToMany('App\Models\Monitoring\MonitoringViolation', 'perevorot_dozorro_monitoring_violations');
    }
}
