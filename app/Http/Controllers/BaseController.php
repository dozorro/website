<?php

namespace App\Http\Controllers;

use App\Area;
use App\Classes\Lang;
use App\Classes\User;
use App\Components\Seo;
use App\Helpers;
use App\Menu;
use App\Models\MvpTemplate;
use App\Models\UserType;
use App\Page;
use App\Settings;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookies;
use Config;

class BaseController extends Controller
{
    public $seoData = [];
    public $hide_modal = true;
    private $request;
    public $user;
    public $locales;
    public $profileAccess;
    public $profileRole1TplId;
    public $profileRole2TplId;

    public function __construct(\Illuminate\Http\Request $request)
    {
        $this->user = User::isAuth();
        $this->request = $request;
        $this->locales=Lang::getLocales();
        $this->profileAccess = @Settings::instance('perevorot.dozorro.custom_setting', 'Perevorot\Dozorro\Models\CustomSetting')->profile_access;

        if(starts_with($this->request->getPathInfo(), ['/search', '/tender', '/indicators'])) {
            $tpls = MvpTemplate::where('is_default', 1)->whereIn('role', [1, 2])->get();
            $role1 = array_first($tpls, function ($key, $item) {
                return $item->role == 1;
            });
            $role2 = array_first($tpls, function ($key, $item) {
                return $item->role == 2;
            });

            $this->profileRole1TplId = !empty($role1) ? $role1->id : 0;
            $this->profileRole2TplId = !empty($role2) ? $role2->id : 0;
        }
    }

    /**
     * @param $template
     * @param $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render($template, $data, $statusCode = 200)
    {
        $seo = new Seo([
            'page' => $this->seoData,
        ]);

        //Cache::forget('top-menu-'.Lang::getCurrentLocale());
        $mainMenu = Cache::remember('top-menu-'.Lang::getCurrentLocale(), 60*24*7, function() {
            return $this->getMenu('top-menu');
        });
        $bottomMenu = Cache::remember('bottom-menu-'.Lang::getCurrentLocale(), 60*24*7, function() {
            return $this->getMenu('bottom-menu');
        });

	    $FormController = app('App\Http\Controllers\FormController');
        $dataStatus = [];

        foreach($FormController->get_status_data() as $one)
            $dataStatus[$one['id']] = $one['name'];

        $feedbackTypes = Cache::remember('feedback-'.Lang::getCurrentLocale(), 60*24*7, function() {
            return @Settings::instance('perevorot.dozorro.custom_setting', 'Perevorot\Dozorro\Models\CustomSetting')->feedback;
        });

        if(empty($this->user) || !$userType = $this->user->user_type) {
            $userType = $this->request->cookie('user_type');
        }
        if(!empty($this->user) && empty($this->user->user_type)) {
            $userType = $this->request->cookie('user_type');

            if(!empty($userType) && $userType !== 'show_after_24') {
                $this->user->user_type = $userType;
                $this->user->user->user_type = $userType;
                $this->user->user->save();
            }
        }

        if(empty($userType)) {
            $userTypes = UserType::all()->each(function ($item, $key) {
                return $item ? $item->translate() : null;
            });
        } else {
            $userTypes = new Collection([]);
        }

        $userType = $userType == 'show_after_24' ? '' : $userType;

        $defaultData = [
            'userType' => $userType,
            'userTypes' => $userTypes,
            'profileRole1TplId' => $this->profileRole1TplId,
            'profileRole2TplId' => $this->profileRole2TplId,
            'profileAccess' => $this->profileAccess,
            'feedbackTypes' => $feedbackTypes,
            'main_menu' => $mainMenu,
            'bottom_menu' => $bottomMenu,
            'search_type' => 'tender',
            'seo' => $seo->onRender(),
            'locales' => $this->locales,
	        'dataStatus'=>$dataStatus,
            'currentLocale' => Lang::getCurrentLocale(),
            'defaultLocale' => Lang::getDefault(),
            'hide_modal' => $this->hide_modal,
            'user' => $this->user,
        ];

        return response()->view(
            $template,
            array_merge(
                $defaultData,
                $data
            ),
            $statusCode,
            [
                'Set-Cookie' => 'hide_modal=1; path=/'
            ]
        );
    }

    /**
     * @param $alias
     * @return mixed
     */
    private function getMenu($alias)
    {
        $menu = Menu::where('alias', $alias)->first();

        if (!$menu) {
            $menu = $this->createMenu($alias);
        }

        $pages = $menu->pages->sortBy('nest_left')->filter(function ($page) {
            return $page->nest_depth == 0 && !$page->is_hidden && !$page->is_disabled;
        });

        foreach($pages as $page)
            $page->getTranslations();

        return Helpers::filterActivePages($pages);
    }

    /**
     * @param $alias
     * @return mixed
     */
    private function createMenu($alias)
    {
        $defaultNames = [
            'top-menu' => 'Главное меню',
            'bottom-menu' => 'Нижнее меню'
        ];

        $menu = Menu::create([
            'alias' => $alias,
            'title' => $defaultNames[$alias],
        ]);

        return $menu;
    }

    /**
     * @return mixed
     */
    protected function getAreas()
    {
        $areas = Area::isEnabled()->orderByRaw("RAND()")->get();

        foreach ($areas as $area) {
            $area->getTranslations();
        }

        return $areas;
    }

    /**
     * @param $data
     */
    public function setSeoData($data)
    {
        $this->seoData = $data;
    }
}
