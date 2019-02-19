<?php

namespace App\Classes\Blocks;

use DB;
use App\Models\AmkuPractice;
use Config;

/**
 * Class ActualTendersAndReviews
 * @package App\Classes\Blocks
 */
class AmkuPractices extends IBlock
{
    public function get()
    {
        $practices=AmkuPractice::enabled()->get();

        $practices->each(function ($item, $key) {
            return $item ? $item->translate() : null;
        });

        return [
            'practices' => $practices
        ];
    }
}
