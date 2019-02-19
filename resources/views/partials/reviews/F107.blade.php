<div class="reviews__body">
    @if(!empty($review->json->lotsExpediencyComment) || !empty($review->json->lotsExpediency))
        <div class="reviews__body__one">
            @if(!empty($review->json->lotsExpediency))
                <p><em>{{t('reviews.is_it_appropriate_in_your_opinion_the_separation_of_subject_of_procurement_into_lots_in_this_procurement')}}?</em></p>
            @endif
            @if(!empty($review->json->lotsExpediency))
                <span class="{{ $review->json->lotsExpediency }}">
                    {{ $review->json->lotsExpediency=='yes'?t('yes'):t('no') }}
                </span>
            @endif
            @if(!empty($review->json->lotsExpediencyComment))
                <p>{!! auto_format($review->json->lotsExpediencyComment) !!}</p>
            @endif
        </div>
    @endif
</div>