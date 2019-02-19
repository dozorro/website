<?php

namespace App\Classes\Blocks;

use App\Models\Partner;
use DB;
use Config;

/**
 * Class ActualTendersAndReviews
 * @package App\Classes\Blocks
 */
class Partners extends IBlock
{
    public function get()
    {
        $partners=Partner::enabled()->get();

        $partners->each(function ($item, $key) {
            return $item ? $item->translate() : null;
        });

        return [
            'partners' => $partners
        ];
    }
}
