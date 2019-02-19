@if(!empty($review->json->claimAnswerSatisfaction) && !empty($review->json->claimAnswerSatisfactionComment))
    <div class="reviews__stars">
        <h3>{{t('reviews.claim_answer_satisfaction')}}:</h3>
        <ul class="tender-stars tender-stars--{{ $review->json->claimAnswerSatisfaction }}">
            <li></li><li></li><li></li><li></li><li></li>
        </ul>
    </div>

    <div class="reviews__body">
        <div class="reviews__text">
            <p>{!! auto_format($review->json->claimAnswerSatisfactionComment) !!}</p>
        </div>
    </div>
@endif