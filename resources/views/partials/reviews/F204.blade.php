<div class="reviews__body">
    @if(!empty($review->json->reasonComment) || !empty($review->json->reason))
        <div class="reviews__body__one">
            @if(!empty($review->json->reason))
                <p class="reason-wrap"><strong>{{ t('F204.reason.'.$review->json->reason) }}</strong></p>
            @endif
            @if(!empty($review->json->reasonComment))
                <p class="comment-wrap">{{ auto_format($review->json->reasonComment) }}</p>
            @endif
        </div>
    @endif
</div>