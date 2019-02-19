<div class="reviews__body">
    @if(!empty($review->json->impartialProductRequirementsComment) || !empty($review->json->impartialProductRequirements))
        <div class="reviews__body__one">
            @if(!empty($review->json->impartialProductRequirements))
                <p><em>{{t('reviews.are_there_any_requirements_for_subject_procurement_impartial_and_those_that_do_not_provide_benefits_to_individual_participants')}}?</em></p>
            @endif
            @if(!empty($review->json->impartialProductRequirements))
                <span class="{{ $review->json->impartialProductRequirements }}">
                    {{ $review->json->impartialProductRequirements=='yes'?t('yes'):t('no') }}
                </span>
            @endif
            @if(!empty($review->json->impartialProductRequirementsComment))
                <p>{!! auto_format($review->json->impartialProductRequirementsComment) !!}</p>
            @endif
        </div>
    @endif
</div>