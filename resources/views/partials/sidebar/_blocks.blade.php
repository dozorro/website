
@if(!empty($item->awards))
<div class="tender-header__kicks tender-header-block">
    <div class="tender-header__wrap tender-header__descr tender-header__kick toggled">
        <button class="tender-header__descr-toggle"></button>
        <div class="block-title">{{ t('tender.protocol_disclosure') }}</div>
        {{--
        @if(!$hide)
        <div class="tender-header_info__item toggled">
            <div class="tender-header__descr-item ">
                <div class="detail-title">{{ t('indicators.disqualification') }}</div>
                <div class="detail-value"><a href="/">{{ @$item->__count_unsuccessful_awards }}</a></div>
            </div>
            @if(!empty($item->__active_award))
                <div class="tender-header__descr-item">
                    <div class="detail-title">{{t('tender.active_awards_participant')}}</div>
                    <div class="detail-value object-link"><a href="/">{{ @$item->__active_award->suppliers[0]->name }}</a></div>
                </div>
                <div class="tender-header__descr-item">
                    <div class="detail-title">{{t('tender.active_awards_proposition')}}</div>
                    <div class="detail-value">{{ $item->__active_award->__full_formated_price }}</div>
                </div>
            @endif
            @if(!empty($item->__awardRisksTitle))
                <div class="tender-header__descr-item tender-header__descr-risks">
                    <button class="tender-header__descr-title risks-title">{{ t('indicators.risks') }}&nbsp;<span></span></button>
                    <div class="risks-items hidden">
                        <div class="risk-coefficient">{{ t('indicators.risk') }} <strong>{{ @$item->__awardRating }}</strong></div>
                        <div class="risk-values">
                            @foreach($item->__awardRisksTitle as $risk)
                                <div class="risk-item">{{ t('indicators.'.$risk) }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @endif
        --}}
        <div class="tender-header_info__item">
            @if(!empty($item->__awardRisksTitle))
                <div class="tender-header__descr-item tender-header__descr-risks">
                    <button class="tender-header__descr-title risks-title">{{ t('indicators.risks') }}&nbsp;<span></span></button>
                    <div class="risks-items hidden">
                        <div class="risk-coefficient">{{ t('indicators.risk') }} <strong>{{ @$item->__awardRating }}</strong></div>
                        <div class="risk-values">
                            @foreach($item->__awardRisksTitle as $risk)
                                <div class="risk-item">{{ t('indicators.'.$risk) }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            <div class="kicks-container">
                @foreach($item->awards as $award)
                <div class="kick-item {{ $award->status == 'active' ? 'positive' : ($award->status == 'pending' ? 'pending' : 'negative') }}">
                    <div class="kick-item-head">
                        <div class="kick-item-title">{{ @$award->suppliers[0]->name }}</div>
                        <div class="kick-item-bet"><span>{{ $item->__initial_bids[$award->bid_id] }}</span><span>→</span><span>{{ @$award->value->amount }}</span></div>
                    </div>
                    <button class="kick-item-info-btn" data-show-text="{{ t('indicators.bid.detail') }}" data-hide-text="{{ t('indicators.bid.hide') }}">
                        <span>{{ t('indicators.bid.detail') }}</span>
                    </button>
                    <div class="kick-item-info">
                        <div class="kick-item-info-group">
                            <div class="sub-group">
                                <div class="sub-group-title">{{ t('indicators.bid.date') }}</div>
                                <div class="sub-group-value">{{ \Carbon\Carbon::createFromTimestamp(strtotime($item->__initial_bids_dates[$award->bid_id]))->format('d.m.Y H:i') }}</div>
                            </div>
                        </div>
                        @if(!empty($award->__bid->documents))
                        <div class="kick-item-info-group">
                            <div class="sub-group">
                                <div class="sub-group-title">{{ t('indicators.bids_docs') }}</div>
                                @foreach($award->__bid->documents as $doc)
                                <div class="sub-group-value-item">
                                    <div class="sub-group-value"><a download href="{{ $doc->url }}">{{ $doc->title }}</a></div>
                                    <div class="sub-group-value-descr">{{ $doc->__format_date }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        <div class="kick-item-info-group">
                            <div class="sub-group">
                                <div class="sub-group-title">{{ t('indicators.award.contactPoint') }}</div>
                                <div class="sub-group-value">{{ @$award->__contactPoint }}</div>
                            </div>
                            <div class="sub-group">
                                <div class="sub-group-title">{{ t('indicators.award.address') }}</div>
                                <div class="sub-group-value">{{ @$award->__address }}</div>
                            </div>
                        </div>
                        <div class="kick-item-info-group">
                            <div class="sub-group">
                                <div class="group-title">{{ t('indicators.award.results') . ' ' . $award->__status_name }} </div>
                                <div class="sub-group-value-item">
                                    <div class="sub-group-value">{{ t('indicators.award.date') . ' ' . @$award->__format_date }}</div>
                                    @if(!empty($award->documents))
                                        @foreach($award->documents as $doc)
                                        <div class="sub-group-value-descr"><a download href="{{ $doc->url }}">{{ $doc->title }}</a></div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="kick-item-info-btn bottom hidden"><span>{{ t('indicators.bid.hide') }}</span></button>
                </div>
                @endforeach
            </div>
            @if(!empty($item->__bids))
            <div class="kicks-container">
                <?php $i = 0; ?>
                @foreach($item->__bids as $k => $bid)
                    @if(empty($bid->__award))

                        @if(!$i)
                        <div class="block-title">{{ t('indicators.bids.without_awards') }}</div>
                        @endif
                        <?php $i++; ?>

                    <div class="kick-item">
                        <div class="kick-item-head">
                            <div class="kick-item-title">{{ @$bid->tenderers[0]->name }}</div>
                            <div class="kick-item-bet"><span>{{ $item->__initial_bids[$bid->id] }}</span><span>→</span><span>{{ @$bid->value->amount }}</span></div>
                        </div>
                        <button class="kick-item-info-btn" data-show-text="{{ t('indicators.bid.detail') }}" data-hide-text="{{ t('indicators.bid.hide') }}"><span>{{ t('indicators.bid.detail') }}</span></button>
                        <div class="kick-item-info">
                            <div class="kick-item-info-group">
                                <div class="sub-group">
                                    <div class="sub-group-title">{{ t('indicators.bid.date') }}</div>
                                    <div class="sub-group-value">{{ \Carbon\Carbon::createFromTimestamp(strtotime($item->__initial_bids_dates[$award->bid_id]))->format('d.m.Y') }}</div>
                                </div>
                            </div>
                            @if(!empty($bid->documents))
                            <div class="kick-item-info-group">
                                    <div class="sub-group">
                                        <div class="sub-group-title">{{ t('indicators.bids_docs') }}</div>
                                        @foreach($bid->documents as $doc)
                                            <div class="sub-group-value-item">
                                                <div class="sub-group-value"><a download href="{{ $doc->url }}">{{ $doc->title }}</a></div>
                                                <div class="sub-group-value-descr">{{ $doc->__format_date }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                            </div>
                            @endif
                            <div class="kick-item-info-group">
                                <div class="sub-group">
                                    <div class="sub-group-title">{{ t('indicators.award.contactPoint') }}</div>
                                    <div class="sub-group-value">{{ @$bid->__contactPoint }}</div>
                                </div>
                                <div class="sub-group">
                                    <div class="sub-group-title">{{ t('indicators.award.address') }}</div>
                                    <div class="sub-group-value">{{ @$bid->__address }}</div>
                                </div>
                            </div>
                        </div>
                        <button class="kick-item-info-btn bottom hidden"><span>{{ t('indicators.bid.hide') }}</span></button>
                    </div>
                    @endif
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endif

@if(!empty($item->__contracts))
    @foreach($item->__contracts as $contract)
        @if($contract->status == 'active')
        <div class="tender-header__contracts tender-header-block">
            <div class="tender-header__wrap tender-header__descr tender-header__kick toggled">
                <button class="tender-header__descr-toggle"></button>
                <div class="block-title">{{t('indicators.contract_block')}}</div>
                @if(!$hide)
                <div class="tender-header_info__item toggled">
                    <div class="tender-header__descr-item">
                        <div class="detail-title">{{t('tender.contract_title')}}</div>
                        <div class="detail-value">{{ $contract->__full_formated_price }}</div>
                    </div>
                    <div class="tender-header__descr-item">
                        <div class="detail-title">{{t('indicators.contract.docs')}}</div>
                        @if(!empty($contract->documents))
                        <div class="detail-value">{{count($contract->documents). ' '.t('indicators.contract.docs_count')}}</div>
                        @else
                            <div class="detail-value">{{t('indicators.contract.docs_empty')}}</div>
                        @endif
                    </div>
                    <div class="tender-header__descr-item">
                        <div class="detail-title">{{t('tender.changes_contract')}}</div>
                        @if(!empty($contract->__changes))
                            <div class="detail-value"><span>{{ count($contract->__changes) . ' '. t('indicators.changes_count') }}</span></div>
                        @else
                            <div class="detail-value">{{t('indicators.contract.changes_empty')}}</div>
                        @endif
                    </div>
                </div>
                @endif
                <div class="tender-header_info__item">
                    <div class="tender-header__descr-item">
                        <div class="detail-title">{{t('tender.contract_title')}}</div>
                        <div class="detail-value">{{ $contract->__full_formated_price }}</div>
                    </div>
                    <div class="tender-header__descr-item">
                        <div class="detail-title">{{t('indicators.contract.docs')}}</div>
                        <div class="detail-value">{{count($contract->documents). ' '.t('indicators.contract.docs_count')}}</div>
                        @if(!empty($contract->documents))
                            <div class="kick-item">
                                <div class="kick-item-info-group">
                                    <div class="sub-group">
                                        @foreach($contract->documents as $doc)
                                            <div class="sub-group-value-item">
                                                <div class="sub-group-value"><a download href="{{ $doc->url }}">{{ $doc->title }}</a></div>
                                                <div class="sub-group-value-descr">{{ \Carbon\Carbon::createFromTimestamp(strtotime($doc->dateModified))->format('d.m.Y H:i') }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="detail-value">{{t('indicators.contract.docs_empty')}}</div>
                        @endif
                    </div>
                    <div class="tender-header__descr-item">
                        @if(!empty($contract->__changes))
                            <div class="detail-title">{{t('tender.changes_contract')}} <span>{{ count($contract->__changes) . ' '. t('indicators.changes_count') }}</span></div>
                            @foreach($contract->__changes as $change)
                                <div class="detail-value">{{@$change->rationale}}</div>
                                <div class="detail-value">{{ \Carbon\Carbon::createFromTimestamp(strtotime($change->date))->format('d.m.Y H:i') }}</div>
                                <div class="kick-item">
                                    <button class="kick-item-info-btn"><span>{{ (!empty($change->contract) ? count($change->contract) : 0) . ' '. t('indicators.docs_count') }}</span></button>
                                    <div class="kick-item-info hidden">
                                        <div class="kick-item-info-group">
                                            <div class="sub-group">
                                                <div class="sub-group-title">{{ t('indicators.contract.changes_docs') }}</div>
                                                @foreach($change->contract as $doc)
                                                    <div class="sub-group-value-item">
                                                        <div class="sub-group-value"><a download href="{{ $doc->url }}">{{ $doc->title }}</a></div>
                                                        <div class="sub-group-value-descr">{{ \Carbon\Carbon::createFromTimestamp(strtotime($doc->dateModified))->format('d.m.Y H:i') }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="detail-title">{{t('indicators.contract.changes_empty')}}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach
@endif