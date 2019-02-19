<div class="reviews__body">
    @if(!empty($review->json->actionComment) || !empty($review->json->actionCode) || !empty($review->json->actionName))
        <div class="reviews__body__one">
            @if(!empty($review->json->actionName))
                <p class="reason-wrap">
                    <strong>{{ $review->json->actionName }}</strong>
                </p>
            @endif
            @if(!empty($review->json->actionComment))
                <p class="comment-wrap">{!! auto_format($review->json->actionComment) !!}</p>
            @endif
        </div>
    @endif
</div>
