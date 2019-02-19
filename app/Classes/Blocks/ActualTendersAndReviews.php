<?php

namespace App\Classes\Blocks;

use App\ActualTender;
use App\JsonForm;
use Carbon\Carbon;
use DB;

/**
 * Class ActualTendersAndReviews
 * @package App\Classes\Blocks
 */
class ActualTendersAndReviews extends IBlock
{
    /**
     * @return array
     */
    private function getTenders()
    {
        /**
         * @var array $tenders
         */
        $tenders = ActualTender::getAllActualTenders(['limit' => $this->block->value->actual_tenders_limit]);

        return $tenders;
    }

    /**
     * @return array
     */
    private function getReviews()
    {
        /**
         * @var array $comments
         */
        $reviews = DB::table('perevorot_dozorro_review_rating')->limit($this->block->value->last_reviews_limit)->get();
        Carbon::setLocale('uk');

        $status=json_decode(file_get_contents('./sources/ua/status.json'), true);

        foreach ($reviews as $k => $review) {

            $review->data = json_decode($review->data);
            $review->data->status=!empty($status[$review->data->status]) ? $status[$review->data->status] : $review->data->status;

            if(is_object($review->data) && isset($review->data->last_review_date)) {
                $review->data->last_review_date = new Carbon($review->data->last_review_date);
            }
            else
            {
                unset($reviews[$k]);
            }
        }

        return $reviews;
    }
    
    /**
     * @return array
     */
    public function get()
    {
        return [
            'reviews' => $this->getReviews(),
            'tenders' => $this->getTenders(),
        ];
    }
}
