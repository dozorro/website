<div class="reviews__body">
    @if(!empty($review->json->purchaserDutiesExecutionComment) || !empty($review->json->purchaserDutiesExecution))
        <div class="reviews__body__one">
            @if(!empty($review->json->purchaserDutiesExecution))
                <div class="reviews__stars inline-layout">
                    <h3>{{t('reviews.evaluate_quality_of_customer_their_duties')}}</h3>
                    {{--<span class="{{ $review->json->purchaserDutiesExecution }}">--}}
                        <ul class="tender-stars tender-stars--{{ $review->json->purchaserDutiesExecution }}">
                            <li></li><li></li><li></li><li></li><li></li>
                        </ul>
                    {{--</span>--}}
                </div>
            @endif
            @if(!empty($review->json->purchaserDutiesExecutionComment))
                <div class="reviews__text">
                    <p>{!! auto_format($review->json->purchaserDutiesExecutionComment) !!}</p>
                </div>
            @endif
        </div>
    @endif
    @if(!empty($review->json->purchaserInteractionProblemsComment) || !empty($review->json->purchaserInteractionProblems))
        <div class="reviews__body__one">
            @if(!empty($review->json->purchaserInteractionProblems))
                <div class="reviews__stars inline-layout">
                <h3>{{t('reviews.which_problems_have_arisen_in_conjunction_with_the_customer_select_one_or_more_options')}}:</h3>
                    <br>
                    <ul>
                    @foreach($review->json->purchaserInteractionProblems as $value)
                        <li>{{ \App\JsonForm::getF112Enum($value) }}</li>
                    @endforeach
                    </ul>
                </div>
            @endif
            @if(!empty($review->json->purchaserInteractionProblemsComment))
                <div class="reviews__text">
                    <p>{!! auto_format($review->json->purchaserInteractionProblemsComment) !!}</p>
                </div>
            @endif
        </div>
    @endif
</div>