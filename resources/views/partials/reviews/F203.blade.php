<div class="reviews__body">
    @if(!empty($review->json->resultComment) || !empty($review->json->resultCode) || !empty($review->json->resultName))
        <div class="reviews__body__one">
            @if(!empty($review->json->resultName))
                <p class="reason-wrap">
                    <strong>{{ $review->json->resultName }}</strong>
                </p>
            @endif
            @if(!empty($review->json->resultComment))
                <p class="comment-wrap">{!! auto_format($review->json->resultComment) !!}</p>
            @endif
        </div>
    @endif
</div>