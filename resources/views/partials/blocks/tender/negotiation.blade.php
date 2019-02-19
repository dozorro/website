@if(in_array($item->procurementMethodType, ['negotiation', 'negotiation.quick']))
    <div class="col-sm-12 margin-bottom margin-bottom-more">
        <h3>{{t('tender.rationale_for_use_of_negotiation_procedure')}}</h3>
        
        @if (!empty($item->cause))
            <div class="row">
                <div class="col-md-12 margin-bottom">
                    <strong>{{t('tender.point_of_law')}}</strong>
                    <div>{{t('negotiation.'.$item->cause)}}</div>
                </div>
            </div>
        @endif

        @if (!empty($item->causeDescription))
            <div><strong>{{t('tender.foundation')}}</strong></div>
            <div>{!!nl2br($item->causeDescription)!!}</div>
        @endif
    </div>
@endif