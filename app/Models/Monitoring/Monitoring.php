<?php

namespace App\Models\Monitoring;

use App\Helpers;
use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;
use DB;

/**
 * Model
 */
class Monitoring extends Model
{
    use ModelTranslation;

    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'perevorot_dozorro_monitoring';

    public $backendNamespace = 'Perevorot\Dozorro\Models\Monitoring';

    public function users()
    {
        return $this->hasMany('App\Models\User', 'monitoring_id', 'id');
    }

    public function getUsers() {
        return $this->users->each(function ($item, $key) {
            return $item ? $item->translate() : null;
        });
    }

    public function monitoringViolations()
    {
        return $this->hasMany('App\Models\Monitoring\MonitoringViolation', 'monitoring_id', 'id');
    }

    public function tenders()
    {
        return $this->hasMany('App\Models\Monitoring\Tender', 'monitoring_id', 'id');
    }

    public function tender($id)
    {
        $r = $this->tenders->where('tender_id', $id)->first();
        return $r ? $r->translate() : null;
    }

    public static function findBySlug($data) {
        $r = self::where('slug', $data)->byEnabled()->first();
        return $r ? $r->translate() : null;
    }

    public function scopeByEnabled($query) {
        return $query->where('is_enabled', 1);
    }

    public function getLogoAttribute() {
        return $this->show_logo();
    }

    public function show_logo() {
        $logotype=DB::table('system_files')
            ->where('field', 'image')
            ->where('is_public', true)
            ->where('attachment_type', $this->backendNamespace)
            ->where('attachment_id', $this->id)
            ->first();

        if($logotype){
            return Helpers::getStoragePath($logotype->disk_name);
        }
    }
}