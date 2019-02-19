<?php

namespace App\Http\Controllers;

use App\Models\NgoProfile;
use App\Models\NgoReview;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class NgoReviewsController extends BaseController
{
    public function stats($slug)
    {
        $ngo = NgoProfile::findBySlug($slug);
        $reviews = $ngo->reviews()->byStatus('complete')->get()->groupBy('status');
        $_reviews = [
            NgoReview::REVIEW_STATUS1 => isset($reviews[NgoReview::REVIEW_STATUS1]) ? $reviews[NgoReview::REVIEW_STATUS1]->count() : 0,
            NgoReview::REVIEW_STATUS2 => isset($reviews[NgoReview::REVIEW_STATUS2]) ? $reviews[NgoReview::REVIEW_STATUS2]->count() : 0,
            NgoReview::REVIEW_STATUS3 => isset($reviews[NgoReview::REVIEW_STATUS3]) ? $reviews[NgoReview::REVIEW_STATUS3]->count() : 0,
        ];

        return $this->render('pages/ngo_reviews_stats', [
            'reviews' => $_reviews,
            'statuses' => NgoReview::$statusData
        ]);
    }

    public function index(Request $request, $formType = 'new')
    {
        if(!$this->user || !$this->user->superadmin) {
            return redirect()->back();
        }

        if ($formType == 'submit') {
            return $this->submit($request);
        }

        $is_ajax = $request->method() == 'POST';

        if($is_ajax) {
            if ($formType == 'new') {
                $reviews = NgoReview::byStatus('new')->orderBy('created_at', 'desc')->paginate(10);
            }
            elseif ($formType == 'complete') {
                $reviews = NgoReview::byStatus('complete')->orderBy('created_at', 'desc')->paginate(10);
            }
            
            $views = '';
            $views_mobile = '';

            foreach ($reviews AS $item) {
                $views .= view('partials/_search_ngo_reviews', [
                    'item' => $item,
                    'for_mobile' => false,
                    'formType' => $formType,
                    'statuses' => NgoReview::$statusData
                ])->render();
                $views_mobile .= view('partials/_search_ngo_reviews', [
                    'item' => $item,
                    'for_mobile' => true,
                    'formType' => $formType,
                    'statuses' => NgoReview::$statusData
                ])->render();
            }

            return [
                'desktop' => $views,
                'mobile' => $views_mobile,
                'lastPage' => $reviews->lastPage()
            ];
        }

        $data = [
            'formType' => $formType,
            'statuses' => NgoReview::$statusData
        ];

        return $this->render('pages/ngo_reviews', $data);
    }

    public function submit(Request $request)
    {
        if(!$this->user || !$this->user->superadmin) {
            return redirect()->back();
        }

        if(!empty($request->all())) {
            $review = NgoReview::find($request->get('id'));
            $review->comment = $request->get('comment');
            $review->status = $request->get('status');
            $review->save();
        }

        return redirect()->back();
    }

    public function save(Request $request)
    {
        if($this->user && !empty($request->all())) {
            $review = new NgoReview();
            $review->text = $request->get('text');
            $review->ngo_profile_id = $request->get('ngo_profile_id');
            $review->user_id = $this->user->user->id;
            $review->tender_id = $request->get('tender_id');
            $review->created_at = Carbon::now();
            $review->save();
        }

        return redirect()->back();
    }
}
