<?php

namespace App\Classes;

use App;
use Cache;
use Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class Lang
 * @package App\Classes
 */
final class Lang
{
    /**
     * @var array
     */
    private static $locales;

    /**
     * @var string
     */
    private static $defaultLocale;

    private static $currentLocale;

    /**
     * @var array
     */
    private static $messages;

    public static function getLocalesData()
    {
        if (sizeof(self::$locales) < 1) {
            self::$locales = DB::table('rainlab_translate_locales')
                ->where('is_enabled', true)
                ->get();

            $uri=request()->server->get('REQUEST_URI');
            $first=request()->segment(1);
            $languages=array_pluck(self::$locales, 'code');

            $default=array_first(self::$locales, function($k, $l){
                return $l->is_default;
            })->code;

            self::$currentLocale=$default;
            $languages=array_diff($languages, [$default]);

            if($first!==null && in_array($first, $languages))
            {
                foreach($languages as $language)
                {
                    if(preg_match('/^\/'.$language.'(\/|\z|\?.*|#(.*))/', $uri))
                    {
                        self::$currentLocale=$language;
                    }
                }
            }

            array_walk(self::$locales, function($locale){
                $locale->is_current=self::$currentLocale==$locale->code;
            });
        }

        return self::$locales;
    }

    /**
     *
     */
    public static function getLocales()
    {
        if (sizeof(self::$locales) < 1) {
            self::$locales = DB::table('rainlab_translate_locales')
                ->where('is_enabled', true)
                ->get();

            $uri=request()->server->get('REQUEST_URI');

            $first=request()->segment(1);
    
            $languages=array_pluck(self::$locales, 'code');
            
            $default=array_first(self::$locales, function($k, $l){
                return $l->is_default;
            })->code;

            Config::set('locales.href', '/');
            Config::set('locales.current', $default);

            self::$currentLocale=$default;

            $languages=array_diff($languages, [$default]);
    
            if($first!==null && in_array($first, $languages))
            {
                foreach($languages as $language)
                {
                    if(preg_match('/^\/'.$language.'(\/|\z|\?.*|#(.*))/', $uri))
                    {
                        Config::set('locales.current', $language);
                        Config::set('locales.href', '/'.$language.'/');
                        
                        self::$currentLocale=$language;
                        
                        App::setLocale($language);

                        request()->server->set('REQUEST_URI', substr($uri, 3));
                    }
                }
            }
            else
                Config::set('locales.current', $default);

            Config::set('locales.is_default', $default==Config::get('locales.current'));

            array_walk(self::$locales, function($locale) use ($uri){
	            $locale->is_current=self::$currentLocale==$locale->code;

                if(preg_match('/^\/'.self::$currentLocale.'(\/|\z|\?.*|#(.*))/', $uri)) {
                    $locale->href = !$locale->is_default ? ('/'.$locale->code.substr($uri, 3)) : substr($uri, 3);
                } else {
                    $locale->href = !$locale->is_default ? ('/'.$locale->code.$uri) : $uri;
                }

                if(!$locale->href) {
                    $locale->href = '/';
                }
            });
        }

        return self::$locales;
    }

    public static function getCurrentLocale()
    {
	    return self::$currentLocale;
	}
	
    public static function getDefault()
    {
        if (sizeof(self::$locales) < 1) {
            self::getLocales();
        }

        if (!self::$defaultLocale) {
            foreach (self::$locales as $locale) {
                if (!$locale->is_default) {
                    continue;
                }

                self::$defaultLocale = $locale->code;
            }
        }

        return self::$defaultLocale;
    }

    /**
     * @param $code
     * @return mixed
     */
    public static function trans($code)
    {
        $currentLocale = self::getCurrentLocale();

        //Cache::forget('rainlab_translate_messages_'.$currentLocale);

        $messages=Cache::remember('rainlab_translate_messages_'.$currentLocale, 60*24, function() use($currentLocale){
            $messages = DB::table('rainlab_translate_messages')->get();

            $result = [];

            foreach ($messages as $message) {
                $translation = json_decode($message->message_data, true);
                $translation = !empty($translation[$currentLocale]) ? $translation[$currentLocale] : $message->code;

                $result[$message->code] = $translation;
            }

            return $result;
        });

        if (!array_key_exists($code, $messages)) {
            self::createTransMessage($code);
            Cache::forget('rainlab_translate_messages_'.$currentLocale);
        }

        return (array_key_exists($code, $messages) ? $messages[$code] : $code);
    }

    /**
     * @param $code
     */
    public static function createTransMessage($code)
    {
        DB::table('rainlab_translate_messages')->insert([
            'code' => $code,
            'message_data' => json_encode([
                'x' => $code,
            ]),
        ]);
    }
}
