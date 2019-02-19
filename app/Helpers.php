<?php

namespace App;

use App\Classes\Lang;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use App\Models\User;
use App\Models\NgoProfile;
use Request;
use DB;

class Helpers
{
    static $thumbs=[
        'list'=>[456, 312]
    ];

    public static function cut_str($str, $left, $right) {
        $str = substr(@stristr($str, $left), strlen($left));
        $leftLen = strlen(@stristr($str, $right));
        $leftLen = $leftLen ? -($leftLen) : strlen($str);
        $str = substr($str, 0, $leftLen);
        return $str;
    }

    public static function getTenderWord($d) {
        $y = $d % 10;
        $x = $d / 10 % 10;
        if ($x && $x == 1) return t('statistics.bad_one_tender');
        if ($y == 1) return t('statistics.bad_tenders');
        if ($y == 2) return t('statistics.bad_tender');
        if ($y == 3) return t('statistics.bad_tender');
        if ($y == 4) return t('statistics.bad_tender');
        return t('statistics.bad_tenders');
    }

    public static function urlize(&$object, $prefix='', $path=false, $switch=false)
    {
        if(!$path) {
            $path='/'.trim(app('request')->path(), '/');
        }

        array_walk($object, function($item) use ($prefix, $path, $switch) {
            if($switch!==false && is_array($prefix)){
                $prefix=$prefix[$item->{$switch}];
            }

            $item->url=$prefix.self::url($item->slug);
            $item->isCurrentUrl=$item->url==$path;
        });
    }

    public static function thumbize(&$object, $image, $type)
    {
        array_walk($object, function($item) use ($image, $type) {
            if(!empty($item->{$image})){
                $width=self::$thumbs[$type][0];
                $height=self::$thumbs[$type][1];

                if(empty($item->thumbs)){
                    $item->thumbs=new \StdClass();
                }

                if(empty($item->thumbs->{$image})){
                    $item->thumbs->{$image}=new \StdClass();
                }

                if(empty($item->thumbs->{$image}->{$type})){
                    $item->thumbs->{$image}->{$type}=new \StdClass();
                }

                $item->thumbs->{$image}->{$type}=self::parseImageFolder($item->{$image}->disk_name).'/thumb_' . $item->{$image}->id . '_' . $width . 'x' . $height . '_0_0_crop.' . pathinfo($item->{$image}->disk_name, PATHINFO_EXTENSION);
            }
        });
    }

    public static function url($slug)
    {
        return '/'.trim($slug, '/');
    }

    public static function storage($path='')
    {
        return env('DOMAIN_STATIC').'/uploads/public'.$path;
    }

    public static function getStoragePath($image, $absolute=false)
    {
        $split=str_split($image, 3);
        $path=($absolute && !env('STORAGE_URL') ? Request::url() : env('STORAGE_URL')).'/uploads/public';

        for($i=0;$i<3;$i++) {
            $path.='/'.$split[$i];
        }

        $path.='/'.$image;

        return $path;
    }

    public static function getMediaPath($image, $absolute=false)
    {
        $path=($absolute && !env('STORAGE_URL') ? Request::url() : env('STORAGE_URL')).'/media';
        $path.=$image;

        return $path;
    }

    public static function getUrlByContentType($type)
    {
        switch($type)
        {
            case 1: $url='article'; break;
            case 2: $url='news'; break;
            case 3: $url='article'; break;
        }

        return $url;
    }

    public static function parseImagePath($image)
    {
        return self::parseImageFolder($image).'/'.$image;
    }

    public static function parseImageFolder($image)
    {
        $split=str_split($image, 3);
        $path=self::storage();

        for($i=0;$i<3;$i++) {
            $path.='/'.$split[$i];
        }

        return $path;
    }

    /**
     * @param $news
     */
    public static function getDateTime(&$news)
    {
        foreach($news as $item) {
            $date = new Carbon($item->visible_at);

            $item->date = self::parseDate($date);
            $item->time = self::parseTime($date);
        }
    }

    /**
     * @param $month
     * @return mixed
     */
    public static function getMonthes($month)
    {
        $monthes=[
            'января',
            'февраля',
            'марта',
            'апреля',
            'мая',
            'июня',
            'июля',
            'августа',
            'сентября',
            'октября',
            'ноября',
            'декабря',
        ];

        return $monthes[$month];
    }

    /**
     * @param $date
     * @return Carbon|string
     */
    public static function parseDate($date)
    {
        if (gettype($date) === 'string') {
            $date = new Carbon($date);
        }

        //return $date->formatLocalized('%d ').self::getMonthes($date->format('n')-1).$date->formatLocalized(' %Y');
        return $date->format('d.m.Y H:i');
    }

    /**
     * @param $time
     * @return string
     */
    public static function parseTime($time)
    {
        if (gettype($time) === 'string') {
            $time = new Carbon($time);
        }

        return $time->formatLocalized('%H:%M');
    }

    public static function getModelByType($type)
    {
        switch($type)
        {
            case 1:case 3: $modelName=['Perevorot\Content\Models\Article', 'Perevorot\Content\Models\Rewrite']; break;
            case 2: $modelName=['Perevorot\Content\Models\News']; break;
        }

        return $modelName;
    }

    /**
     * @param Collection $pages
     */
    public static function filterActivePages(Collection $pages)
    {
        return $pages->map(function (\App\Page $item) use ($pages) {
            $path = '/' . trim(\Illuminate\Support\Facades\Request::path(), '/');

            $item->setAttribute('active', ($item->url === $path));

            $pages = $item->children();

            if (sizeof($pages) > 0) {
                $active = $pages->filter(function ($value) {
                    return $value->active;
                });

                if (sizeof($active) > 0) {
                    $item->setAttribute('active', true);
                }
            }

            return $item;
        })->filter(function ($page) {
            return !empty($page->title);
        });
    }

    /**
     * @param $url
     * @return mixed
     */
    public static function replaceLocales($url)
    {
        $locales = [];

        foreach (Lang::getLocales() as $lang) {
            $locales[] = $lang->code;
        }

        $locales = implode('|', $locales);

        $url = preg_replace('/\/(' . $locales . ')/', '', $url);
        return empty($url) ? '/' : $url;
    }

    /**
     * @param $url
     * @return string
     */
    public static function getLocalizedUrl($url)
    {
        $defaultLocale = Lang::getDefault();
        $locale = App::getLocale();
        $url = trim(trim($url), '/');

        if($locale != $defaultLocale)
            $url = $locale.'/'.$url;

        return '/'.$url;
    }

    public static function parseUserRelationData(&$array)
    {
        $user_ids=array_values(array_unique(array_filter(array_pluck($array, 'user_id'))));
        $users=User::whereIn('id', $user_ids)->get()->keyBy('id');
        $_users = $users->toArray();
        //$ngo_profile_id=array_values(array_unique(array_filter(array_pluck($array, 'ngo_profile_id'))));
        //$ngo_profiles=NgoProfile::whereIn('id', $ngo_profile_id)->get()->keyBy('id');

        foreach($array as $review) {
            $user=false;

            if(array_key_exists($review->user_id, $_users)) {
                $user=$users[$review->user_id];

                //if(!empty($user->ngo_profile_id)) {
                //    $user->__ngo_profile=array_key_exists($user->ngo_profile_id, $ngo_profiles) ? $ngo_profiles[$user->ngo_profile_id] : new NgoProfile();
                //}
            }

            $review->__user= $user ? $user : new User();
        }
    }

    public static function parseUserData(&$array)
    {
        $emails=[];

        foreach($array as $one) {
            if(isset($one->author->contactPoint->email) || isset($one->author->email)) {
                $emails[] = isset($one->author->contactPoint->email) ? $one->author->contactPoint->email : $one->author->email;
            }
        }

        if(empty($emails)) {
            foreach($array as $comment)
            {
                $comment->userGroup=false;
            }
            return false;
        }

        $groups=DB::table('perevorot_dozorro_user_groups')->get();

        $groups_translations=DB::table('rainlab_translate_attributes')
            ->where('locale', App::getLocale())
            ->whereIn('model_id', array_pluck($groups, 'id'))
            ->where('model_type', 'Perevorot\Dozorro\Models\UserGroup')
            ->get();

        foreach($groups as $group)
        {
            $model=array_first($groups_translations, function($k, $item) use($group){
                return $item->model_id==$group->id;
            });

            if($model)
            {
                $attributes = json_decode($model->attribute_data);

                foreach ($attributes as $field => $value) {
                    $group->{$field} = $value;
                }
            }
        }

        /*
        $ngos=DB::table('perevorot_dozorro_ngo')->get();

        $ngo_translations=DB::table('rainlab_translate_attributes')
            ->where('locale', App::getLocale())
            ->whereIn('model_id', array_pluck($ngos, 'id'))
            ->where('model_type', 'Perevorot\Dozorro\Models\Ngo')
            ->get();

        foreach($ngos as $ngo)
        {
            $model=array_first($ngo_translations, function($item) use($ngo){
                return $item->model_id==$ngo->id;
            });

            if($model)
            {
                $attributes = json_decode($model->attribute_data);

                foreach ($attributes as $field => $value) {
                    $ngo->{$field} = $value;
                }
            }
        }

        $user_ngo=DB::table('perevorot_dozorro_users')->select('ngo_id', 'email')->whereIn('email', $emails)->get();
        */

        $user_groups=DB::table('perevorot_dozorro_users')->select('group_id', 'email')->whereIn('email', $emails)->get();
        $groups_by_email=[];
        //$ngo_by_email=[];

        foreach($user_groups as $one)
        {
            $groups_by_email[$one->email]=array_first($groups, function($k, $group) use($one){
                return $group->id==$one->group_id;
            });
        }

        /*
        foreach($user_ngo as $one)
        {
            $ngo_by_email[$one->email]=array_first($ngos, function($k, $ngo) use($one){
                return $ngo->id==$one->ngo_id;
            });
        }
        */

        foreach($array as $k=>$comment)
        {
            $email = !empty($comment->author->contactPoint->email) ? $comment->author->contactPoint->email : (!empty($comment->author->email) ? $comment->author->email : false);

            //$comment->userNgo=!empty($ngo_by_email[$comment->getPayload()->author->email]) ? $ngo_by_email[$comment->getPayload()->author->email] : false;
            $comment->userGroup=!empty($groups_by_email[$email]) ? $groups_by_email[$email] : false;
        }
    }

    static function parseSettings($text, $return_array=false)
    {
        if(empty($text))
            return (object)[];

        $array=explode("\r\n", trim($text));
        $out=[];

        foreach($array as $k=>$one)
        {
            $ar=explode('=', trim($one));
            $out[$ar[0]]=$ar[1];
        }

        return $return_array ? $out : (object) $out;
    }

    /**
     * @param $locale
     * @return string
     */
//    public static function getTranslatedUrl($locale)
//    {
//        $locales = Locale::get(['code']);
//
//        if(!self::$defaultLocale)
//            self::$defaultLocale=\RainLab\Translate\Models\Locale::getDefault();
//
//        $url = Request::path();
//
//        $url = trim(trim($url), '/');
//        $url = preg_replace('/^(ru|en)\/?/', '', $url);
//
//        if($locale!=self::$defaultLocale->code)
//            $url=$locale.'/'.$url;
//
//        return '/'.$url;
//    }
}
