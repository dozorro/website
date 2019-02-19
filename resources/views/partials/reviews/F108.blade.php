<div class="reviews__body">
    @if(!empty($review->json->bankGuaranteeExpediencyComment) || !empty($review->json->bankGuaranteeExpediency))
        <div class="reviews__body__one">
            @if(!empty($review->json->bankGuaranteeExpediency))
                <p><em>{{t('reviews.is_it_appropriate_in_your_opinion_the_use_of_bank_guarantee_in_procurement')}}?</em></p>
            @endif
            @if(!empty($review->json->bankGuaranteeExpediency))
                <span class="{{ $review->json->bankGuaranteeExpediency }}">
                    {{ $review->json->bankGuaranteeExpediency=='yes'?t('yes'):t('no') }}
                </span>
            @endif
            @if(!empty($review->json->bankGuaranteeExpediencyComment))
                <p>{!! auto_format($review->json->bankGuaranteeExpediencyComment) !!}</p>
            @endif
        </div>
    @endif
</div>