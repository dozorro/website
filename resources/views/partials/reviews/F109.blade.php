<div class="reviews__body">
    @if(!empty($review->json->cancellationLegitimacyComment) || !empty($review->json->cancellationLegitimacy))
        <div class="reviews__body__one">
            @if(!empty($review->json->cancellationLegitimacy))
                <p><em>{{t('reviews.is_it_appropriate_in_your_opinion_the_use_of_bank_guarantee_in_procurement')}}?</em></p>
            @endif
            @if(!empty($review->json->cancellationLegitimacy))
                <span class="{{ $review->json->cancellationLegitimacy }}">
                    {{ $review->json->cancellationLegitimacy=='yes'?t('yes'):t('no') }}
                </span>
            @endif
            @if(!empty($review->json->cancellationLegitimacyComment))
                <p>{!! auto_format($review->json->cancellationLegitimacyComment) !!}</p>
            @endif
        </div>
    @endif
</div>