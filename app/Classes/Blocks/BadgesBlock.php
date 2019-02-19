<?php

namespace App\Classes\Blocks;

use App\Classes\Lang;
use App\Models\Badge;
use App\Models\User;
use App\Models\NgoProfile;
use Carbon\Carbon;
use App\Customer;
use App\Helpers;
use DB;
use App\JsonForm;
use Illuminate\Support\Facades\Cache;
use Psy\Util\Json;

/**
 * Class CustomersBlock
 * @package App\Classes\Blocks
 */
class BadgesBlock extends IBlock
{
    /**
     * @return array
     */
    public function get()
    {
        if(in_array(env('APP_ENV'), ['local','dev','develop'])) {
            //Cache::forget('badges-' . Lang::getCurrentLocale());
        }

        $data = Cache::remember('badges-'.Lang::getCurrentLocale(), 60*24, function() {
            if (!empty($this->block->value->badges)) {
                foreach ($this->block->value->badges as &$badge) {
                    $badge->badgesList = Badge::whereIn('name', $badge->badge)->get();

                    foreach($badge->badgesList AS &$_badge) {
                        $_badge->logo = $_badge->image;
                    }
                }
            }

            return [
                'title' => $this->block->value->title,
                'desc' => $this->block->value->desc,
                'badges' => (array)$this->block->value->badges,
            ];
        });

        return (object) $data;
    }
}
