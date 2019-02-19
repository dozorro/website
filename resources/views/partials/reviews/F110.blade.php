<div class="reviews__body">
    @if(!empty($review->json->supplierQuestions) || !empty($review->json->supplierQuestionsComment))
    <div class="reviews__body__one">
        @if(!empty($review->json->supplierQuestions))
            <p><em>{{t('reviews.f110.supplierQuestions')}}</em></p>
            <span class="{{ $review->json->supplierQuestions }}">
                {{ $review->json->supplierQuestions=='yes'?t('yes'):t('no') }}
            </span>
        @endif
        @if(!empty($review->json->supplierQuestionsComment))
            <p>{!! auto_format($review->json->supplierQuestionsComment) !!}</p>
        @endif
    </div>
    @endif
    @if(!empty($review->json->procuringQuestions) || !empty($review->json->procuringQuestionsComment))
        <div class="reviews__body__one">
            @if(!empty($review->json->procuringQuestions))
                <p><em>{{t('reviews.f110.procuringQuestions')}}</em></p>
                <span class="{{ $review->json->procuringQuestions }}">
                    {{ $review->json->procuringQuestions=='yes'?t('yes'):t('no') }}
                </span>
            @endif
            @if(!empty($review->json->procuringQuestionsComment))
                <p>{!! auto_format($review->json->procuringQuestionsComment) !!}</p>
            @endif
        </div>
    @endif
    @if(!empty($review->json->answeredInTime) || !empty($review->json->answeredInTimeComment))
        <div class="reviews__body__one">
            @if(!empty($review->json->answeredInTime))
                <p><em>{{t('reviews.f110.answeredInTime')}}</em></p>
                <span class="{{ $review->json->answeredInTime }}">
                    {{ $review->json->answeredInTime=='yes'?t('yes'):t('no') }}
                </span>
            @endif
            @if(!empty($review->json->answeredInTimeComment))
                <p>{!! auto_format($review->json->answeredInTimeComment) !!}</p>
            @endif
        </div>
    @endif
    @if(!empty($review->json->communicationMethod) || !empty($review->json->communicationMethodComment))
        <div class="reviews__body__one">
            @if(!empty($review->json->communicationMethod))
                <p><em>{{t('reviews.f110.communicationMethod')}}</em></p>
                <p>
                    {{ \App\JsonForm::getF110Enum($review->json->communicationMethod) }}
                </p>
            @endif
            @if(!empty($review->json->communicationMethodComment))
                <p>{!! auto_format($review->json->communicationMethodComment) !!}</p>
            @endif
        </div>
    @endif
</div>