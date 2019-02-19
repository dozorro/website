<div class="reviews__body">
    @if(!empty($review->json->impartialParticipantRequirementsComment) || !empty($review->json->impartialParticipantRequirements))
        <div class="reviews__body__one">
            @if(!empty($review->json->impartialParticipantRequirements))
                <p><em>{{t('reviews.are_there_requirements_by_appropriate_and_impartial_those_that_do_not_provide_benefits_individual_participants')}}?</em></p>
            @endif
            @if(!empty($review->json->impartialParticipantRequirements))
                <span class="{{ $review->json->impartialParticipantRequirements }}">
                {{ $review->json->impartialParticipantRequirements=='yes'?t('yes'):t('no') }}
            </span>
            @endif
            @if(!empty($review->json->impartialParticipantRequirementsComment))
                <p>{!! auto_format($review->json->impartialParticipantRequirementsComment) !!}</p>
            @endif
        </div>
    @endif
</div>