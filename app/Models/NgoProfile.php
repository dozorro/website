<?php

namespace App\Models;

use App\Helpers;
use App\JsonForm;
use App\Settings;
use App\Traits\ModelTranslation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Schema;

class NgoProfile extends Model
{
    use ModelTranslation;

    CONST CERT_LEVEL_1 = 1;
    CONST CERT_LEVEL_2 = 2;
    CONST CERT_LEVEL_3 = 3;
    CONST CERT_LEVEL_4 = 4;

    public $certsData = [
        self::CERT_LEVEL_1 => 'cert.in_progress',
        self::CERT_LEVEL_2 => 'cert.basic',
        self::CERT_LEVEL_3 => 'cert.high',
        self::CERT_LEVEL_4 => 'cert.cancel',
    ];

    public $table = 'perevorot_dozorro_ngo_profiles';
    public $backendNamespace = 'Perevorot\Dozorro\Models\NgoProfile';
    public $timestamps=false;

    public function getAdditionalData()
    {
        $data = new \stdClass();

        $data->total_under_control = $this->forms()
            ->where('schema', 'F201')
            ->where('payload', 'not like', '%abuseCode":"A028%')
            ->byDate($this->_date)
            ->where('is_hide', 0)
           ->groupBy('tender')
           ->get()
           ->count();
        $data->sum_under_control = $this->forms()
            ->where('schema', 'F201')
            ->where('payload', 'not like', '%abuseCode":"A028%')
            ->byDate($this->_date)
            ->where('is_hide', 0)
            ->groupBy('tender')
            ->get()
            ->sum('price');

        $data->f201_count = $this->forms()
            ->where('schema', 'F201')
            ->where('payload', 'like', '%parentForm%')
            ->byDate($this->_date)
            ->where('is_hide', 0)
            ->count();
        $data->f202_count = $this->forms()
            ->where('schema', 'F202')
            ->where('is_hide', 0)
            ->byDate($this->_date)
            ->count();
        $data->f203_count = $this->forms()
            ->where('schema', 'F203')
            ->byDate($this->_date)
            ->where('is_hide', 0)
            ->count();
        $data->total_abuse = $this->forms()
            ->where('schema', 'F201')
            ->where('payload', 'not like', '%abuseCode":"A028%')
            ->byDate($this->_date)
            ->where('is_hide', 0)
            ->count();

        $dt = Carbon::create(date('Y'), date('m'), 1)->format('Y-m-d');

        $data->month_total = $this->forms()
            ->where('schema', 'F201')
            ->where('date', '>=', $dt)
            ->where('is_hide', 0)
            ->groupBy('tender')
            ->get()
            ->count();
        $data->f204_succes = $this->forms()
            ->where('schema', 'F204')
            ->where('payload', 'like', '%succes%')
            ->byDate($this->_date)
            ->where('is_hide', 0)
            ->count();
        $data->f204_defeat = $this->forms()
            ->where('schema', 'F204')
            ->where('payload', 'like', '%defeat%')
            ->byDate($this->_date)
            ->where('is_hide', 0)
            ->count();
        $data->f204_cancel = $this->forms()
            ->where('schema', 'F204')
            ->where('payload', 'like', '%cancel%')
            ->byDate($this->_date)
            ->where('is_hide', 0)
            ->count();
        $data->total_below = $this->forms()
            ->whereIn('procurement_method_type', ['belowThreshold', 'reporting'])
            ->byDate($this->_date)
            ->where('is_hide', 0)
            ->count();
        $data->total_above = $this->forms()
            ->whereIn('procurement_method_type', ['aboveThresholdUA', 'aboveThresholdEU', 'aboveThresholdUA.defense', 'negotiation.quick', 'negotiation'])
            ->byDate($this->_date)
            ->where('is_hide', 0)
            ->count();

        $total = $data->f204_succes + $data->f204_defeat + $data->f204_cancel;

        if($data->f204_succes) {
            $data->f204_succes_percent = ($data->f204_succes * 100) / $total;
        } else {
            $data->f204_succes_percent = 0;
        }
        if($data->f204_defeat) {
            $data->f204_defeat_percent = ($data->f204_defeat * 100) / $total;
        } else {
            $data->f204_defeat_percent = 0;
        }
        if($data->f204_cancel) {
            $data->f204_cancel_percent = ($data->f204_cancel * 100) / $total;
        } else {
            $data->f204_cancel_percent = 0;
        }

        return $data;
    }

    public function getCertificateStatusAttribute()
    {
        return $this->certsData[$this->certificate];
    }

    public function badges()
    {
        return $this->belongsToMany('App\Models\Badge', 'perevorot_dozorro_badge_ngo', 'ngo_profile_id', 'badge_id')->withPivot('is_auto');
    }

    public function forms()
    {
        return $this->hasMany('App\JsonForm', 'ngo_profile_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\NgoReview', 'ngo_profile_id', 'id');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User', 'ngo_profile_id', 'id');
    }

    public function coins()
    {
        return $this->hasMany('App\Models\Coin', 'ngo_profile_id', 'id');
    }

    public function reviewsCount()
    {
        $th = $this->reviews();

        /*
        if(!empty($this->_date['date_to'])) {
            $dt = Carbon::createFromFormat('d.m.Y', $this->_date['date_to'])->format('Y-m-d H:i:s');
            $th->where('created_at', '<=', $dt);
        }
        if(!empty($this->_date['date_from'])) {
            $dt = Carbon::createFromFormat('d.m.Y', $this->_date['date_from'])->format('Y-m-d H:i:s');
            $th->where('created_at', '>=', $dt);
        }*/

        return $th->count();
    }

    public function coinsSum()
    {
        $coins = $this->coins();

        /*
        if(!empty($this->_date['date_to'])) {
            $dt = Carbon::createFromFormat('d.m.Y', $this->_date['date_to'])->format('Y-m-d H:i:s');
            $coins->where('dt', '<=', $dt);
        }
        if(!empty($this->_date['date_from'])) {
            $dt = Carbon::createFromFormat('d.m.Y', $this->_date['date_from'])->format('Y-m-d H:i:s');
            $coins->where('dt', '>=', $dt);
        }*/

        return $coins->sum('sum');
    }

    public function coinsTotalSum()
    {
        $coins = $this->coins();

        /*
        if(!empty($this->_date['date_to'])) {
            $dt = Carbon::createFromFormat('d.m.Y', $this->_date['date_to'])->format('Y-m-d H:i:s');
            $coins->where('dt', '<=', $dt);
        }
        if(!empty($this->_date['date_from'])) {
            $dt = Carbon::createFromFormat('d.m.Y', $this->_date['date_from'])->format('Y-m-d H:i:s');
            $coins->where('dt', '>=', $dt);
        }*/

        return $coins->where('sum', '>', '0')->sum('sum');
    }

    public function coin()
    {
        return $this->hasOne('App\Models\Coin', 'ngo_profile_id', 'id');
    }

    public function getBadges() {

        /*
        $badges = $this->badges();

        if(!empty($this->_date['date_to'])) {
            $dt = Carbon::createFromFormat('d.m.Y', $this->_date['date_to'])->format('Y-m-d H:i:s');
            $badges->where('dt', '<=', $dt);
        }
        if(!empty($this->_date['date_from'])) {
            $dt = Carbon::createFromFormat('d.m.Y', $this->_date['date_from'])->format('Y-m-d H:i:s');
            $badges->where('dt', '>=', $dt);
        }
        */

        $badges = $this->badges()->get();
        $badges = $badges->isEmpty() ? $badges : $badges->each(function ($item, $key) {
            return $item ? $item->translate() : null;
        });

        $setting = Settings::instance('perevorot.dozorro.form');

        foreach($badges as &$badge) {
            if($badge->next_badge_id) {
                $badge->next_badge = Badge::find($badge->next_badge_id);
            }
            if($badge->getOriginal('pivot_is_auto')) {
                foreach ($setting as $k => $v) {
                    if (stripos($k, 'badge') !== FALSE && (int)$v == $badge->id) {
                        $schema = explode('_', $k)[0];
                        $badge->forms = $this->forms()->BySingleForm($schema)->count();
                    }
                }
            }
        }

        return $badges;
    }

    public function getUsers() {
        return $this->users->each(function ($item, $key) {
            return $item ? $item->translate() : null;
        });
    }

    public function authors()
    {
        return $this->hasMany('App\Models\Blog\Author', 'ngo_profile_id', 'id');
    }

    public function donors()
    {
        return $this->hasMany('App\Models\NgoDonor', 'ngo_profile_id', 'id');
    }

    public function inboxes()
    {
        return $this->hasMany('App\Models\NgoRisk', 'ngo_profile_id', 'id');
    }

    public function inbox()
    {
        return $this->hasOne('App\Models\NgoRisk');
    }

    public function updateInbox(JsonForm $form)
    {
        if(Schema::hasTable('dozorro_risks_inbox')) {
            $inbox = $this->inbox()->where('tender_id', $form->tender)->first();

            if ($inbox) {
                $inbox->status = $form->status;
                $inbox->date = Carbon::now()->format('Y-m-d H:i:s');
                $inbox->save();
            }
        }
    }

    public function getDonors() {

        $donors = $this->donors();

        /*
        if(!empty($this->_date['date_to'])) {
            $dt = Carbon::createFromFormat('d.m.Y', $this->_date['date_to'])->format('Y-m-d H:i:s');
            $donors->where('dt', '<=', $dt);
        }
        if(!empty($this->_date['date_from'])) {
            $dt = Carbon::createFromFormat('d.m.Y', $this->_date['date_from'])->format('Y-m-d H:i:s');
            $donors->where('dt', '>=', $dt);
        }*/

        return $donors->get()->each(function ($item, $key) {
            return $item ? $item->translate() : null;
        });
    }

    public function authors_posts()
    {
        /*if(!empty($this->_date)) {

            $dates = $this->_date;

            $authors = $this->authors()->whereHas('posts', function ($query) use($dates) {

                if(!empty($dates['date_to'])) {
                    $dt = Carbon::createFromFormat('d.m.Y', $dates['date_to'])->format('Y-m-d H:i:s');
                    $query->where('created_at', '<=', $dt);
                }
                if(!empty($dates['date_from'])) {
                    $dt = Carbon::createFromFormat('d.m.Y', $dates['date_from'])->format('Y-m-d H:i:s');
                    $query->where('created_at', '>=', $dt);
                }

            })->get();
        } else {*/
            $authors = $this->authors()->has('posts')->get();
        //}

        $authors = array_where($authors, function($k, $v) {
            return $v;
        });

        /*if(!empty($this->_date)) {

            $dates = $this->_date;

            return array_sum(array_map(function ($author) use($dates) {

                $query = $author->posts();

                if(!empty($dates['date_to'])) {
                    $dt = Carbon::createFromFormat('d.m.Y', $dates['date_to'])->format('Y-m-d H:i:s');
                    $query->where('created_at', '<=', $dt);
                }
                if(!empty($dates['date_from'])) {
                    $dt = Carbon::createFromFormat('d.m.Y', $dates['date_from'])->format('Y-m-d H:i:s');
                    $query->where('created_at', '>=', $dt);
                }

                return $query->count();
            }, $authors));
        } else {*/
            return array_sum(array_map(function ($author) {
                return $author->posts->count();
            }, $authors));
        //}
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

    public function checkRegion($tender_region) {

        if(!is_numeric($tender_region)) {
            return false;
        }

        $tender_region = substr($tender_region, 0, 2);

        foreach(explode(',', $this->region) as $region) {

            if (stripos($region, "-") !== FALSE) {
                $range = explode("-", $region);
            } else {
                $range[] = (int)$region;
                $range[] = (int)$region;
            }

            for ($i = $range[0]; $i <= $range[1]; $i++) {
                if ($i == (int)$tender_region) {
                    return true;
                }
            }
        }

        return false;
    }

    public function showRegions() {

        $regions = json_decode(file_get_contents('./sources/ua/region.json'), TRUE);
        $_regions = [];

        foreach(explode(',', $this->region) as $v) {
            if(isset($regions[$v])) {
                $_regions[] = $regions[$v];
            }
        }

        return implode(', ', $_regions);
    }

    public static function findByMainEdrpou($data) {
        return self::where('main_edrpou', $data)->byEnabled()->first()->translate();
    }

    public static function findBySlug($data) {
        return self::where('slug', $data)->byEnabled()->first()->translate();
    }

    public function scopeByEnabled($query) {
        return $query->where('is_enabled', 1);
    }

    public function image()
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
