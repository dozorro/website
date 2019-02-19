@if(!in_array($item->procurementMethodType, ['negotiation', 'negotiation.quick']))
    <div class="margin-bottom margin-bottom-more">
        <div class="block_title">
            <h3>{{t('tender.information_on_procedure')}}</h3>
        </div>
        @if($item->procurementMethod=='open')
            <div class="list_date_tender inline-layout col-4">
                @if(!empty($item->enquiryPeriod->endDate))
                    <div class="item">
                        <p><strong>{{t('tender.period1')}}:</strong></p>
                        <p>{{t('tender.till')}} {{date('d.m.Y H:i', strtotime($item->enquiryPeriod->endDate))}}</p>
                    </div>
                @endif
                @if(!empty($item->complaintPeriod->endDate))
                    <div class="item">
                        <p><strong>{{t('tender.period2')}}:</strong></p>
                        <p>{{t('tender.till')}} {{date('d.m.Y H:i', strtotime($item->complaintPeriod->endDate))}}</p>
                    </div>
                @endif
                @if(!empty($item->tenderPeriod->endDate))
                    <div class="item">
                        <p><strong>{{t('tender.period3')}}:</strong></p>
                        <p>{{date('d.m.Y H:i', strtotime($item->tenderPeriod->endDate))}}</p>
                    </div>
                @endif
                @if(!empty($item->lots) && sizeof($item->lots)==1 && !empty($item->lots[0]->auctionPeriod->startDate))
                    <div class="item">
                        <p><strong>{{t('tender.period4')}}:</strong></p>
                        <p>{{date('d.m.Y H:i', strtotime($item->lots[0]->auctionPeriod->startDate))}}</p>
                    </div>
                @elseif(!$item->__isMultiLot && !empty($item->auctionPeriod->startDate))
                    <div class="item">
                        <p><strong>{{t('tender.period4')}}:</strong></p>
                        <p>{{date('d.m.Y H:i', strtotime($item->auctionPeriod->startDate))}}</p>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-md-6">
                    @if (!empty($item->value->amount))
                        <div class="tender-description__item">
                            <div class="tender-description__title">
                                {{t('tender.value_amount')}}
                            </div>
                            <div class="tender-description__text">
                                {{number_format($item->value->amount, 0, '', ' ')}} {{$item->value->currency}} {{!empty($item->value->valueAddedTaxIncluded)?t('tender.with_VAT'):t('tender.without_VAT')}}
                            </div>
                        </div>
                    @endif
                    @if (!empty($item->guarantee) && (int) $item->guarantee->amount>0)
                        <div class="tender-description__item">
                            <div class="tender-description__title">
                                {{t('tender.type_of_bid_security')}}:
                            </div>
                            <div class="tender-description__text">
                                {{t('tender.electronic_bank_guarantee')}}
                            </div>
                        </div>
                        <div class="tender-description__item">
                            <div class="tender-description__title">
                                {{t('tender.amount_of_bid_security')}}:
                            </div>
                            <div class="tender-description__text">
                                {{str_replace('.00', '', number_format($item->guarantee->amount, 2, '.', ' '))}} {{$item->guarantee->currency}}
                            </div>
                        </div>
                    @else
                        <div class="tender-description__item">
                            <div class="tender-description__title">
                                {{t('tender.type_of_bid_security')}}:
                            </div>
                            <div class="tender-description__text">
                                {{t('tender.missing')}}
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    @if(empty($item->lots) || sizeof($item->lots)==1)
                        @if (!empty($item->minimalStep->amount))
                            <div class="tender-description__item">
                                <div class="tender-description__title">
                                    {{t('tender.minimum_step_of_lowering_prices')}}:
                                </div>
                                <div class="tender-description__text">
                                    {{number_format($item->minimalStep->amount, 0, '', ' ')}} {{$item->minimalStep->currency}}
                                </div>
                            </div>
                        @endif
                        @if (!empty($item->value->amount) && !empty($item->minimalStep->amount))
                            <div class="tender-description__item">
                                <div class="tender-description__title">
                                    {{t('tender.minimum_step_of_lowering_prices')}}, %:
                                </div>
                                <div class="tender-description__text">
                                    {{str_replace('.00', '', number_format(($item->minimalStep->amount/$item->value->amount)*100, 2, '.', ' '))}} %
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @endif
        @if ($item->procurementMethod=='limited')
            <div class="row">
                <div class="col-sm-8"><strong>{{t('tender.appeal_intention_conclude_an_agreement')}}:</strong></div>
                <div class="col-sm-4">{{!empty($item->__active_award->complaintPeriod) ? t('tender.before').' '.date('d.m.Y H:i', strtotime($item->__active_award->complaintPeriod->endDate)) : t('tender.none2')}}</div>
            </div>
        @endif
    </div>
@endif
