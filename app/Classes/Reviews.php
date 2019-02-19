<?php

namespace App\Classes;

use Illuminate\Database\Eloquent\Collection;
use App;
use DB;

/**
 * Class Reviews
 * @package App\Classes
 */
class Reviews
{
    /**
     * @var Collection $reviews
     */
    private $reviews;

    /**
     * Reviews constructor.
     * @param $reviews
     */
    public function __construct($reviews)
    {
        $this->reviews = $this->handleReviews($reviews);
    }

    /**
     * @param $reviews
     * @return Collection
     */
    private function handleReviews($reviews)
    {
        $groupedReviews = $this->groupReviews($reviews);
        return $groupedReviews;
        /*
        $reviews = $reviews->filter(function ($review) {
            return empty($review->author->email);
        });
        */

        //return $reviews->merge($groupedReviews)->sortByDesc('created_at');
    }

    /**
     * @param $reviews
     * @return Collection
     */
    private function groupReviews($reviews)
    {
        $reviewsResult = [];
        $groupedReviews = new Collection();

        foreach ($reviews as $item => $review)
        {
            $userData=json_decode(json_encode($review->author), true);
            if(isset($userData['auth'])) {
                ksort($userData['auth']);

                if(isset($userData['auth']['contactPoint'])) {
                    ksort($userData['auth']['contactPoint']);
                }
            }
            if(isset($userData['contactPoint'])) {
                ksort($userData['contactPoint']);
            }
            ksort($userData);
            $key=md5(json_encode($userData));

            if (!array_key_exists($key, $reviewsResult)) {
                $reviewsResult[$key] = new Collection();
            }

            $reviewsResult[$key]->add($review);
        }

        foreach ($reviewsResult as $key => $item)
        {
            if (empty($key))
                continue;

            $filteredReviews = $item->where('schema', 'F101')->sortBy('date')->first();

            if (empty($filteredReviews))
                $filteredReviews = $item->sortBy('date')->first();

            $groupedReviews->add($this->handleGroupedReviews($filteredReviews, $item));
        }

        return $groupedReviews;
    }

    /**
     * @param $firstReview
     * @param $reviews
     * @return mixed
     */
    private function handleGroupedReviews($firstReview, $reviews)
    {
        $firstReview->reviews = $reviews->filter(function ($review) use ($firstReview) {
            return $review->id != $firstReview->id;
        });

        return $firstReview;
    }

    /**
     * @return mixed
     */
    public function getReviews()
    {
        return $this->reviews;
    }
}
