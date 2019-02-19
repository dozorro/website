@if(!empty($review->json->overallScore) && !empty($review->json->overallScoreComment))
<div class="reviews__stars">
    <h3>{{t('reviews.conditions_of_purchase')}}:</h3>
    <ul class="tender-stars tender-stars--{{ $review->json->overallScore }}">
        <li></li><li></li><li></li><li></li><li></li>
    </ul>
</div>

<div class="reviews__body">
    <div class="reviews__text">
        <p>{!! auto_format($review->json->overallScoreComment) !!}</p>
    </div>
</div>
@endif