<div class="reviews__body">
    @if(!empty($review->json->correctExpectedCostComment) || !empty($review->json->correctExpectedCost))
        <div class="reviews__body__one">
            @if(!empty($review->json->correctExpectedCost))
                <p><em>{{t('reviews.correctly_identified_the_expected_customer_value')}}?</em></p>
            @endif
            @if(!empty($review->json->correctExpectedCost))
                <span class="{{ $review->json->correctExpectedCost }}">
                    {{ $review->json->correctExpectedCost=='yes'?t('yes'):t('no') }}
                </span>
            @endif
            @if(!empty($review->json->correctExpectedCostComment))
                <p>{!! auto_format($review->json->correctExpectedCostComment) !!}</p>
            @endif
        </div>
    @endif
</div>