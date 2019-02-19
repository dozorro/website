<div class="reviews__body">
    @if(!empty($review->json->abuseCode) || !empty($review->json->abuseName))
        <div class="reviews__body__one">
            @if(!empty($review->json->abuseName))
                <p class="reason-wrap">
                    <strong>{{ $review->json->abuseName }}</strong>
                </p>
            @endif
            @if(!empty($review->json->abuseComment))
                <p class="comment-wrap">{!! auto_format($review->json->abuseComment) !!}</p>
            @endif
        </div>
    @endif
</div>
