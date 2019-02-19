<div class="reviews__body">
    @if(!empty($review->json->bestPriceComment) || !empty($review->json->bestPrice))
        <div class="reviews__body__one">
            <p><strong>{{t('reviews.description_of_requirements')}}</strong></p>
            @if(!empty($review->json->bestPrice))
                <h4>{{t('reviews.does_the _ubject_procurement_requirements_to_purchase_quality_goods_at_the_best_price')}}?<span class="{{ $review->json->bestPrice }}"> - {{ $review->json->bestPrice=='yes'?'ТАК':'НІ' }}</span></h4>
            @endif
            @if(!empty($review->json->bestPriceComment))
                <p>{!! auto_format($review->json->bestPriceComment) !!}</p>
            @endif
        </div>
    @endif
    @if(!empty($review->json->maxCompetitionComment) || !empty($review->json->maxCompetition))
        <div class="reviews__body__one">
            <p><strong>{{t('reviews.competition')}}</strong></p>
            @if(!empty($review->json->maxCompetition))
                <p><em>{{t('reviews.following_requirements_ensure_maximum_competition_among_participants')}}?</em><br><span class="{{ $review->json->maxCompetition }}">{{ $review->json->maxCompetition=='yes'?'ТАК':'НІ' }}</span></p>
            @endif
            @if(!empty($review->json->maxCompetitionComment))
                <p>{!! auto_format($review->json->maxCompetitionComment) !!}</p>
            @endif
        </div>
    @endif
    @if(!empty($review->json->productQualityComment) || !empty($review->json->productQuality))
        <div class="reviews__body__one">
            <p><strong>{{t('reviews.quality_product')}}</strong></p>
            @if(!empty($review->json->productQuality))
                <p><em>{{t('reviews.is_it_clear_potential_suppliers_whose_product_quality_customer_expects_buy')}}?</em><br><span class="{{ $review->json->productQuality }}">{{ $review->json->productQuality=='yes'?'ТАК':'НІ' }}</span></p>
            @endif
            @if(!empty($review->json->productQualityComment))
                <p>{!! auto_format($review->json->productQualityComment) !!}</p>
            @endif
        </div>
    @endif
    @if(!empty($review->json->qualitativeCriteriaComment) || !empty($review->json->qualitativeCriteria))
        <div class="reviews__body__one">
            <p><strong>{{t('reviews.assessment_criteria')}}</strong></p>
            @if(!empty($review->json->qualitativeCriteria))
                <p><em>{{t('reviews.is_there_a_procedure_for_assessing_qualitative_criteria_during_procurement_subject_qualification_contract_performance_agreement')}}?</em><br><span class="{{ $review->json->qualitativeCriteria }}">{{ $review->json->qualitativeCriteria=='yes'?'ТАК':'НІ' }}</span></p>
            @endif
            @if(!empty($review->json->qualitativeCriteriaComment))
                <p>{!! auto_format($review->json->qualitativeCriteriaComment) !!}</p>
            @endif
        </div>
    @endif
</div>