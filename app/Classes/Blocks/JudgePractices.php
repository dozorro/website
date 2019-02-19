<?php

namespace App\Classes\Blocks;

use App\Models\JudgePractice;
use DB;
use Config;

/**
 * Class ActualTendersAndReviews
 * @package App\Classes\Blocks
 */
class JudgePractices extends IBlock
{
    public function get()
    {
        $practices=JudgePractice::enabled()->get();

        $practices->each(function ($item, $key) {
            return $item ? $item->translate() : null;
        });

        return [
            'practices' => $practices
        ];
    }
}
