<?php

namespace App\Models;

use App\File;
use App\Helpers;
use App\Traits\ModelTranslation;
use Illuminate\Database\Eloquent\Model;
use DB;

class Badge extends Model
{
    use ModelTranslation;

    protected $table = 'perevorot_dozorro_badges';
    public $backendNamespace = 'Perevorot\Dozorro\Models\Badge';

    public function ngo_profiles()
    {
        return $this->belongsToMany('App\Models\NgoProfile', 'perevorot_dozorro_badge_ngo', 'badge_id', 'ngo_profile_id');
    }

    public static function getNgoProfiles2($data) {
        $nids = DB::table('perevorot_dozorro_badge_ngo')->distinct('ngo_profile_id')->select('ngo_profile_id')->whereIn('badge_id', $data)->lists('ngo_profile_id');

        if(!empty($nids)) {
            $ngo = new NgoProfile();
            return $ngo->ByEnabled()->whereIn('id', $nids)->orderByRaw("RAND()")->get();
        }

        return null;
    }

    public function getNgoProfiles() {

        $data = $this->ngo_profiles()->get();

        return $data->isEmpty() ? $data : $data->each(function ($item, $key) {
            return $item ? $item->translate() : null;
        });
    }

    public function get_image() {
        return File::where('attachment_type', $this->backendNamespace)
            ->where('attachment_id', $this->id)
            ->where('field', 'image')
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function getImageAttribute()
    {
        $file = File::where('attachment_type', $this->backendNamespace)
            ->where('attachment_id', $this->id)
            ->where('field', 'image')
            ->orderBy('id', 'DESC')
            ->first();

        if($file)
        {
            return $file = Helpers::getStoragePath($file->disk_name);
        }
        else
        {
            return '';
        }
    }
}
