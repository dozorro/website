<?php

namespace App\Classes\Blocks;

use App\Classes\User;
use App\Models\Monitoring\Monitoring;
use App\Models\NgoProfile;
use App\Customer;
use DB;
use Illuminate\Support\Collection;

/**
 * Class CustomersBlock
 * @package App\Classes\Blocks
 */
class NgoProfilesBlock extends IBlock
{
    /**
     * @return array
     */
    public function get()
    {
        return null;

        $users = NgoProfile::where('is_enabled', 1)->get()->each(function ($item, $key) {
            return $item ? $item->translate() : null;
        });
        $customers = Customer::where('is_enabled', 1)->get()->each(function ($item, $key) {
            return $item ? $item->translate() : null;
        });
        $user = User::isAuth();
        $monitoring = ($user && isset($user->monitoring) && $user->monitoring && $user->access_read) ?
            Monitoring::byEnabled()->get()->each(function ($item, $key) {
                return $item ? $item->translate() : null;
            })
        : new Collection([]);

        foreach($users AS $ngo) {
            $ngo->count_authors_posts = $ngo->authors_posts();
            $ngo->logo = $ngo->show_logo();
        }

	    return (object) [
            'ngos' => $users,
            'customers' => $customers,
            'monitoring' => $monitoring,
        ];
    }
}
