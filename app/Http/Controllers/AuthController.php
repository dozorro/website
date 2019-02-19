<?php

namespace App\Http\Controllers;

use App\Classes\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Cookies;

class AuthController extends BaseController
{
    private $availableProviders = [
        'facebook',
        'twitter',
        'google',
    ];

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider(Request $request, $provider)
    {
        $request->session()->flush();

        if(array_key_exists('feedback', $request->all())) {
            $request->session()->set('referer_uri', $request->header('referer', '/')."#feedback");
        } else {
            $request->session()->set('referer_uri', $request->header('referer', '/'));
        }

        if (!in_array($provider, $this->availableProviders)) {
            abort(404);
        }

        $socialite = Socialite::driver($provider);

        return $socialite->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback(Request $request, $provider)
    {
        if (!in_array($provider, $this->availableProviders)) {
            abort(404);
        }

        $state = $request->get('state');
        $request->session()->put('state', $state);

        if(\Auth::check()==false) {
            session()->regenerate();
        }

        $user = Socialite::driver($provider)->user();
        $data = [];

        switch ($provider) {
            case 'facebook': {
                $data = [
                    'social_id' => $user->id,
                    'email' => $user->email,
                    'full_name' => $user->name,
                    'avatar' => $user->avatar_original
                ];
            }
                break;

            case 'twitter': {
                $data = [
                    'social_id' => $user->id,
                    'email' => $user->email,
                    'full_name' => $user->name,
                    'avatar' => $user->avatar_original
                ];
            }
                break;

            case 'google': {
                $data = [
                    'social_id' => $user->id,
                    'email' => $user->email,
                    'full_name' => $user->name,
                    'avatar' => $user->avatar_original
                ];
            }
                break;
        }

        $userData = User::store((object) $data, $provider);

        //$request->session()->put('user', $userData);

        if(is_array($userData)) {
            Artisan::call('cache:clear');
            return response()->redirectTo($request->session()->get('referer_uri'))->withCookie(cookie('user', $userData, time() + (60 * 60 * 24 * 365), '/', $request->getHost()));
        } else {
            return response()->redirectTo($request->session()->get('referer_uri'))->withCookie(cookie('user', '', time() - (60 * 60 * 24 * 365), '/', $request->getHost()));
        }
    }
}
