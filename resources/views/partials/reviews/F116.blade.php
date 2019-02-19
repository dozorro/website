@if(!empty($review->json->complaintAnswerSatisfaction) && !empty($review->json->complaintAnswerSatisfactionComment))
    <div class="reviews__stars">
        <h3>{{t('reviews.complaint_answer_satisfaction')}}:</h3>
        <ul class="tender-stars tender-stars--{{ $review->json->complaintAnswerSatisfaction }}">
            <li></li><li></li><li></li><li></li><li></li>
        </ul>
    </div>

    <div class="reviews__body">
        <div class="reviews__text">
            <p>{!! auto_format($review->json->complaintAnswerSatisfactionComment) !!}</p>
        </div>
    </div>
@endif