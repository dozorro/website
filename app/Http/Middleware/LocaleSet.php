<?php namespace App\Http\Middleware;

use App;
use Closure;
use Config;

class LocaleSet
{
    public function handle($request, Closure $next)
    {
        $uri=$request->server->get('REQUEST_URI');

        $first=$request->segment(1);

        $languages=Config::get('locales.languages');
        $default=Config::get('locales.default');
        
        Config::set('locales.href', '/');
        Config::set('locales.current', $default);

        $languages=array_diff($languages, [$default]);

        if($first!==null && in_array($first, $languages))
        {
            foreach($languages as $language)
            {
                if(preg_match('/^\/'.$language.'(\/|\z|\?.*|#(.*))/', $uri))
                {
                    Config::set('locales.current', $language);
                    Config::set('locales.href', '/'.$language.'/');

                    App::setLocale($language);

                    $request->server->set('REQUEST_URI', substr($uri, 3));
                }
            }
        }
        else
            Config::set('locales.current', $default);

        Config::set('locales.is_default', $default==Config::get('locales.current'));

        return $next($request);
    }
}
