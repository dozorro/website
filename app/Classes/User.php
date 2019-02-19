<?php

namespace App\Classes;

use DB;
use Illuminate\Http\Request;

class User
{
    /**
     *
     */
     
    static $ngo;
    static $user;

    public static function isAuth()
    {
        return self::data();
    }

    public static function isNGO()
    {
        if(!empty(self::isAuth())){
            /*
            $exists=DB::select('SELECT * FROM perevorot_dozorro_users p INNER JOIN perevorot_dozorro_user_groups g ON g.id=p.group_id WHERE p.id = ? AND g.slug= ? ', [
                self::data()->user_id,
                'ngo'
            ]);

            return !empty($exists);
            */

            return isset(self::data()->ngo) && self::data()->ngo !== null;
        }
        
        return false;
    }

    public static function ngo()
    {
        if(!empty(self::isAuth()))
        {
            /*
            if(empty(self::$ngo))
            {
                self::$ngo=DB::select('SELECT * FROM perevorot_dozorro_users p INNER JOIN perevorot_dozorro_user_groups g ON g.id=p.group_id WHERE p.id = ? AND g.slug= ? ', [
                    self::data()->user_id,
                    'ngo'
                ]);
            }
            */

            self::$ngo = isset(self::data()->ngo) ? self::data()->ngo : null;

            return self::$ngo;

            //return self::$ngo ? self::$ngo[0] : false;
        }
        
        return false;
    }

    /**
     *
     */
    public static function data()
    {
        if(!self::$user) {
            $request = app(Request::class);
            self::$user = $request->cookie('user');

            if (!self::$user) {
                return false;
            }

            if (!empty(self::$user['social_id'])) {

                $userModel = new \App\Models\User();
                $dbUser = $userModel->findBySocialId(['social_id' => self::$user['social_id']]);

                if ($dbUser instanceof \App\Models\User) {
                    self::$user['ngo'] = $dbUser->ngo_profile;
                    self::$user['group'] = $dbUser->group;
                    self::$user['user'] = $dbUser;
                    self::$user['is_profile'] = $dbUser->is_profile;
                    self::$user['is_pairs'] = $dbUser->is_pairs;
                    self::$user['is_pairs_review'] = $dbUser->is_pairs_review;
                    self::$user['is_pairs_button'] = $dbUser->is_pairs_button;
                    self::$user['is_indicators'] = $dbUser->is_indicators;
                    self::$user['is_profile_links'] = $dbUser->is_profile_links;
                    self::$user['is_random_pairs'] = $dbUser->is_random_pairs;
                    self::$user['is_tender_risks'] = $dbUser->is_tender_risks && env('TENDER_INDICATORS', false);
                    self::$user['main_edrpou'] = $dbUser->main_edrpou;
                    self::$user['monitoring'] = $dbUser->monitoring;
                    self::$user['user_type'] = $dbUser->user_type;
                    self::$user['superadmin'] = $dbUser->group && $dbUser->group->slug == 'superadmin' ? true : false;
                    self::$user['access_full'] = $dbUser->group && $dbUser->group->slug == 'admin' ? true : false;
                    self::$user['access_read'] = $dbUser->group && in_array($dbUser->group->slug, ['moderator','admin']) ? true : false;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        if(!isset(self::$user['ngo'])) {
            self::$user['ngo'] = null;
        }
        if(!isset(self::$user['group'])) {
            self::$user['group'] = null;
        }
        if(!isset(self::$user['monitoring'])) {
            self::$user['monitoring'] = null;
        }

        return (object) self::$user;
    }

    /**
     *
     */
    public static function store($data, $social)
    {
        $user = new \App\Models\User();
        $_user = null;

        if(!$data->email) {
            $_user = $user->findBySocialId(['social_id' => $data->social_id]);
        }
        elseif($_user = $user->findByEmail(['email' => $data->email])) {
            $_user->social_id = $data->social_id;
            $_user->save();
        }
        else {
            $_user = $user->findBySocialId(['social_id' => $data->social_id]);
        }

        if(!$_user)
        {
            $_user = new \App\Models\User();

            $_user->avatar = $data->avatar;
            $_user->email = $data->email;
            $_user->full_name = $data->full_name;
            $_user->social = $social;
            $_user->social_id = $data->social_id;

            $_user->save();
        } else {
            if($data->avatar) {
                $_user->avatar = $data->avatar;
                $_user->save();
            }
        }

        if(isset($_user->id)) {
            return [
                'avatar' => $data->avatar,
                'full_name' => $data->full_name,
                'email' => $data->email,
                'social' => $social,
                'user_id' => $_user->id,
                'social_id' => $data->social_id,
            ];
        } else {
            return false;
        }
    }
}
