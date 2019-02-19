<?php

namespace App\Models;

use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;

/**
 * Model
 */
class User extends Model
{
    use ModelTranslation;

    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'perevorot_dozorro_users';
    public $backendNamespace = 'Perevorot\Dozorro\Models\User';

    public function getNgoProfileAttribute()
    {
        return $this->ngo_profile_id ? $this->ngoProfileRelation : false;
    }

    public function ngoProfileRelation()
    {
        return $this->belongsTo('App\Models\NgoProfile', 'ngo_profile_id');
    }

    public function getGroupAttribute()
    {
        return !empty($this->group_id) ? $this->groupRelation : false;
    }

    public function groupRelation()
    {
        return $this->belongsTo('App\Models\UserGroup', 'group_id');
    }

    public function getMonitoringAttribute()
    {
        return !empty($this->monitoring_id) ? $this->monitoringRelation : false;
    }

    public function monitoringRelation()
    {
        return $this->belongsTo('App\Models\Monitoring\Monitoring', 'monitoring_id');
    }

    public function showAvatarByName()
    {
        $names = explode(' ', $this->full_name);
        $avatar = '';

        foreach($names as $name) {
            $avatar .= mb_strtoupper(mb_substr($name, 0, 1));
        }

        return $avatar;
    }

    public function issetEdrpou($code) {

        $edrpou = [];

        if($this->edrpou) {
            $edrpou = explode("\n", str_replace("\r", '', trim($this->edrpou)));
        }
        if($this->main_edrpou) {
            $edrpou[] = $this->main_edrpou;
        }

        $edrpou = array_map(function($v) {
            return trim($v);
        }, $edrpou);
        
        if(!empty($edrpou)) {
            if (in_array($code, $edrpou)) {
                return true;
            }
        }

        return false;
    }

    public function findBySocialId($params) {
        $r = self::BySocialId($params['social_id'])->first();
        return $r ? $r->translate() : $r;
    }

    public function findByEmail($params) {
        $r = self::ByEmail($params['email'])->first();
        return $r ? $r->translate() : null;
    }

    public function scopeByEmail($query, $data) {
        if($data) {
            return $query->where('email', $data);
        }
    }

    public function scopeBySocialId($query, $data) {
        if($data) {
            return $query->where('social_id', $data);
        }
    }
}
