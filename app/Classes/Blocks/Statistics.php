<?php

namespace App\Classes\Blocks;

use App\JsonForm;
use DB;
use App\TenderStatistic;

/**
 * Class ActualTendersAndReviews
 * @package App\Classes\Blocks
 */
class Statistics extends IBlock
{
    /**
     * @return array
     */
    private function getData()
    {
        /**
         * @var array $tenders
         */
        $data = TenderStatistic::first();

        $ar = ['comments', 'reviews', 'tenders_sum', 'tenders_sum_text', 'violation_sum', 'violation_sum_text'];

        foreach($ar AS $field)
        {
            if(stripos($data->{$field}, '{COMMENTS}') !== FALSE)
            {
                $data->{$field} = preg_replace('/{COMMENTS}/', JsonForm::getCommentsCount(), $data->{$field});
            }
            elseif(stripos($data->{$field}, '{REVIEWS}') !== FALSE)
            {
                $data->{$field} = preg_replace('/{REVIEWS}/', JsonForm::getReviewsCount(['ngo' => false]), $data->{$field});
            }
        }

        return $data;
    }


    /**
     * @return array
     */
    public function get()
    {
        return [
            'stats' => $this->getData(),
        ];
    }
}
