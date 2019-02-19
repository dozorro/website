<div class="reviews__body">
    @if(!empty($review->json->correctClassifiersCodesComment) || !empty($review->json->correctClassifiersCodes))
        <div class="reviews__body__one">
            @if(!empty($review->json->correctClassifiersCodes))
                <p><em>{{t('reviews.correctly_identified_customer_product_code_goods_purchased')}}?</em></p>
            @endif
            @if(!empty($review->json->correctClassifiersCodes))
                <span class="{{ $review->json->correctClassifiersCodes }}">
                    {{ $review->json->correctClassifiersCodes=='yes'?t('yes'):t('no') }}
                </span>
            @endif
            @if(!empty($review->json->correctClassifiersCodesComment))
                <p>{!! auto_format($review->json->correctClassifiersCodesComment) !!}</p>
            @endif
        </div>
    @endif
</div>