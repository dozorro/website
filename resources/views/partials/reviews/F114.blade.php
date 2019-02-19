@if(!empty($review->json->answerSatisfaction) && !empty($review->json->answerSatisfactionComment))
    <div class="reviews__stars">
        <h3>{{t('reviews.answer_satisfaction')}}:</h3>
        <ul class="tender-stars tender-stars--{{ $review->json->answerSatisfaction }}">
            <li></li><li></li><li></li><li></li><li></li>
        </ul>
    </div>

    <div class="reviews__body">
        <div class="reviews__text">
            <p>{!! auto_format($review->json->answerSatisfactionComment) !!}</p>
        </div>
    </div>
@endif