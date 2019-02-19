<div class="reviews__body">
    @if(!empty($review->json->cheapestWasDisqualifiedComment) || !empty($review->json->cheapestWasDisqualified))
        <div class="reviews__body__one">
            <div class="reviews__title inline-layout">
                    @if(!empty($review->json->cheapestWasDisqualified))
                            <h4>{{t('reviews.F111_cheapestWasDisqualified')}}?
                                @if(!empty($review->json->cheapestWasDisqualified))
                                    <span class="{{ $review->json->cheapestWasDisqualified }}">
                                        &nbsp;– {{ $review->json->cheapestWasDisqualified=='yes'?t('yes'):t('no') }}
                                    </span>
                                @endif
                            </h4>
                    @endif

            </div>

            @if(!empty($review->json->cheapestWasDisqualifiedComment))
                <div class="reviews__text">
                    <p>{!! auto_format($review->json->cheapestWasDisqualifiedComment) !!}</p>
                </div>
            @endif
        </div>
    @endif
    @if(!empty($review->json->argumentativeDisqualificationComment) || !empty($review->json->argumentativeDisqualification))
        <div class="reviews__body__one">
                <div class="reviews__title inline-layout">
                @if(!empty($review->json->argumentativeDisqualification))
                        <h4>{{t('reviews.F111_argumentativeDisqualification')}}?
                            @if(!empty($review->json->argumentativeDisqualification))
                                <span class="{{ $review->json->argumentativeDisqualification }}">
                                &nbsp;– {{ $review->json->argumentativeDisqualification=='yes'?t('yes'):t('no') }}
                                </span>
                            @endif
                        </h4>
                @endif

                </div>

            @if(!empty($review->json->argumentativeDisqualificationComment))
                <div class="reviews__text">
                    <p>{!! auto_format($review->json->argumentativeDisqualificationComment) !!}</p>
                </div>

            @endif
        </div>
    @endif
</div>