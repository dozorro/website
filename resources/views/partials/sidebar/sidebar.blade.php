    <div class="tender-header__descriptions tender-header-block">
        <div class="tender-header__wrap tender-header__descr toggled">
            <button class="tender-header__descr-toggle"></button>
            <div class="block-title block-title-tender">{{ $item->title }}</div>
            <div class="tender-header_info__item">
                <div class="tender-header__descr-item tender-header__descr-date">
                    <div class="detail-title">{{ t('indicatiors.procedure.start') }} - {{ t('indicatiors.procedure.finish') }}</div>
                    <div class="detail-value">
                        @if($item->procurementMethod == 'open')
                            {{ \Carbon\Carbon::createFromTimestamp(strtotime($item->enquiryPeriod->startDate))->format('d.m.Y') }}
                        @elseif($item->procurementMethod == 'limited' && !empty($item->__active_award))
                            {{ \Carbon\Carbon::createFromTimestamp(strtotime($item->__active_award->date))->format('d.m.Y') }}
                        @endif
                        @if(!empty($item->__signed_contracts))
                             - {{ \Carbon\Carbon::createFromTimestamp(strtotime($item->__signed_contracts[0]->date))->format('d.m.Y') }}
                        @endif
                    </div>
                </div>
                <div class="tender-header__descr-item tender-header__descr-expected-price">
                    <div class="detail-title">{{ t('tender.value_amount') }}</div>
                    <div class="detail-value">{{ @$item->__full_formated_price }}</div>
                </div>
            </div>
            <div class="tender-header_info__item">
                <div class="tender-header__descr-item tender-header__descr-feedback">
                    <div class="detail-title">{{ t('indicators.total_forms') }}</div>
                    <div class="detail-value">{{ @$item->__totalHundredForms }}</div>
                </div>
                <div class="tender-header__descr-item tender-header__descr-rating hidden">
                    <div class="detail-title">{{ t('indicators.rating') }}</div>
                    <div class="detail-value">98%</div>
                </div>
            </div>
        </div>
    </div>
    <div class="tender-header__descriptions tender-header-block">
        <div class="tender-header__wrap tender-header__descr toggled">
            <button class="tender-header__descr-toggle"></button>
            <div class="block-title">{{ t('tender.information_on_procedure') }}</div>
            <div class="tender-header_info__item">
                <div class="tender-header__descr-item tender-header__descr-date">
                    <div class="detail-title">{{ t('tender.tenderID') }}</div>
                    <div class="detail-value">
                        {{ $item->tenderID}}
                    </div>
                </div>
                <div class="tender-header__descr-item tender-header__descr-date">
                    <div class="detail-title">{{ t('tender.procurementMethodType') }}</div>
                    <div class="detail-value">
                        {{ @$item->__procedure_name }}
                    </div>
                </div>
                @if(!empty($item->description))
                    <div class="tender-header__descr-item tender-header__descr-expected-price">
                        <div class="detail-title">{{ t('tender.description') }}</div>
                        <div class="detail-value">{{ $item->description }}</div>
                    </div>
                @endif
            </div>
            @if(!empty($item->__active_award))
            <div class="tender-header_info__item">
                <div class="tender-header__descr-item tender-header__descr-winner">
                    <div class="detail-title">{{ t('indicatiors.procedure.active.award') }}</div>
                    <div class="detail-value"><a href="">{{ @$item->__active_award->suppliers[0]->name }}</a></div>
                    <div class="detail-value">{{ @$item->__signed_contracts[0]->__full_formated_price }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="tender-header__descriptions tender-header-block">
        <div class="tender-header__wrap tender-header__descr toggled">
            <button class="tender-header__descr-toggle"></button>
            <div class="block-title">{{ t('indicators.dates_block') }}</div>
            <div class="tender-header_info__item">
                @if(!empty($item->complaintPeriod))
                <div class="tender-header__descr-item tender-header__descr-date">
                    <div class="detail-title">{{ t('tender.period2') }}</div>
                    <div class="detail-value">
                        {{ \Carbon\Carbon::createFromTimestamp(strtotime($item->complaintPeriod->startDate))->format('d.m.Y H:i') }} -
                        {{ \Carbon\Carbon::createFromTimestamp(strtotime($item->complaintPeriod->endDate))->format('d.m.Y H:i') }}
                    </div>
                </div>
                @endif
                @if(!empty($item->enquiryPeriod))
                <div class="tender-header__descr-item tender-header__descr-date">
                    <div class="detail-title">{{ t('tender.period1') }}</div>
                    <div class="detail-value">
                        {{ \Carbon\Carbon::createFromTimestamp(strtotime($item->enquiryPeriod->startDate))->format('d.m.Y H:i') }} -
                        {{ \Carbon\Carbon::createFromTimestamp(strtotime($item->enquiryPeriod->endDate))->format('d.m.Y H:i') }}
                    </div>
                </div>
                @endif
                @if(!empty($item->tenderPeriod))
                <div class="tender-header__descr-item tender-header__descr-date">
                    <div class="detail-title">{{ t('tender.period3') }}</div>
                    <div class="detail-value">
                        {{ \Carbon\Carbon::createFromTimestamp(strtotime($item->tenderPeriod->startDate))->format('d.m.Y H:i') }} -
                        {{ \Carbon\Carbon::createFromTimestamp(strtotime($item->tenderPeriod->endDate))->format('d.m.Y H:i') }}
                    </div>
                </div>
                @endif
                @if(!empty($item->auctionPeriod))
                <div class="tender-header__descr-item tender-header__descr-date">
                    <div class="detail-title">{{ t('tender.period4') }}</div>
                    <div class="detail-value">
                        {{ \Carbon\Carbon::createFromTimestamp(strtotime($item->auctionPeriod->startDate))->format('d.m.Y H:i') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @if(!empty($item->__risksTitle))
    <div class="tender-header__descriptions tender-header-block">
        <div class="tender-header__wrap tender-header__descr toggled">
            <button class="tender-header__descr-toggle"></button>
            <button class="tender-header__descr-title risks-title risks-title-toggled">{{ t('indicators.risks') }}&nbsp;<span></span></button>
            <div class="tender-header_info__item">
                <div class="tender-header__descr-item tender-header__descr-risks">
                    <button class="tender-header__descr-title risks-title hidden">{{ t('indicators.risks') }}&nbsp;<span></span></button>
                    <div class="risks-items hidden">
                        <div class="risk-coefficient">{{ t('indicators.risk') }} <strong>{{ @$item->__rating }}</strong></div>
                        <div class="risk-values">
                            @foreach($item->__risksTitle as $risk)
                            <div class="risk-item">{{ t('indicators.'.$risk) }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="tender-header__descriptions tender-header__customer tender-header-block">
        <div class="tender-header__wrap tender-header__descr toggled">
            <button class="tender-header__descr-toggle"></button>
            <div class="block-title">{{ t('indicators.block_customer') }}</div>
            <div class="tender-header_info__item toggled">
                <div class="tender-header__descr-item tender-header__descr-date">
                    <div class="detail-value">{{ @$item->procuringEntity->name.', '.$item->procuringEntity->identifier->id }}</div>
                </div>
            </div>
            <div class="tender-header_info__item">
                <div class="tender-header__descr-item tender-header__descr-date">
                    <div class="detail-value">{{ @$item->procuringEntity->name.', '.$item->procuringEntity->identifier->id }}</div>
                </div>
                <div class="tender-header__descr-item tender-header__descr-date">
                    <div class="detail-title">{{ t('tender.kind') }}</div>
                    <div class="detail-value">{{ @$item->procuringEntity->kind }}</div>
                </div>
                <div class="tender-header__descr-item tender-header__descr-expected-price">
                    <div class="detail-title">{{ t('tender.contactPoint') }}</div>
                    <div class="detail-value">{{ @$item->procuringEntity->__contactPoint }}</div>
                </div>
                <div class="tender-header__descr-item tender-header__descr-date">
                    <div class="detail-title">{{ t('tender.procuringEntity_address') }}</div>
                    <div class="detail-value">{{ @$item->procuringEntity->__address }}</div>
                </div>
            </div>
        </div>
    </div>
    @if($item->__isMultiLot)
        @include('partials.sidebar._documents', ['item'=>$item, 'class'=>'', 'hide' => false])
    @endif
    @if(!empty($item->__ratings_total))
        <div class="tender-header__bids tender-header-block">
            <div class="tender-header__wrap tender-header__descr block_statistic tender-header__bid toggled">
                <button class="tender-header__descr-toggle"></button>
                <div class="block-title">{{ t('indicators.responses') }}</div>
                <div class="tender-header_info__item toggled">
                    <div class="tender-header__descr-item tender-header__descr-rating">
                        <div class="detail-title">{{ t('indicators.responses_prayer') }}</div>
                        <div class="detail-value"><a href="/">{{ $item->__ratings_total }}</a></div>
                    </div>
                </div>
                <div class="tender-header_info__item">
                    <div class="tender-header__descr-item tender-header__descr-rating">
                        <div class="detail-title">{{ t('indicators.responses_prayer') }}</div>
                        <div class="detail-value"><a href="/">{{ $item->__ratings_total }}</a></div>
                    </div>
                    <div class="tender-header__descr-item">
                        <div class="detail-title">{{ t('indicators.responses_avg') }}</div>
                        <div class="detail-value"><strong>{{ $item->__ratings_avg }}</strong></div>
                    </div>
                    <div class="tender-header__descr-item block-profile-rating">
                    <div class="profile-rating-item">
                        <div class="rating-stars-sections">
                            @foreach($item->__ratings as $schema => $rating)
                                <div class="star-section">
                                    <div class="star-rank">
                                        {{ $schema }}
                                    </div>
                                    <div class="star-scale">
                                        <div class="star-scale-active" style=" width: {{ $item->___ratings[$schema] }}%;">
                                            {{ $rating }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    @endif
    @if($item->__isMultiLot)
        <div class="tender-header__lots">
            @foreach($item->lots as $lot)
                <div class="tender-header__wrap tender-header__descr tender-header__lot toggled tender-header-block">
                    <button class="tender-header__descr-toggle"></button>
                    <div class="block-title block-title-tender">{{ $lot->title }}</div>
                    <div class="tender-header_info__item">
                        @if(!empty($lot->description))
                        <div class="tender-header__descr-item">
                            <div class="detail-value"><strong>{{ $lot->description }}</strong></div>
                        </div>
                        @endif
                        <div class="tender-header__descr-item tender-header__descr-date">
                            <div class="detail-title">{{ t('indicators.lot_status') }}</div>
                            <div class="detail-value">{{ !empty($lot->__status_name) ? $lot->__status_name : 'Без названия' }}</div>
                        </div>
                        @if(!empty($lot->__active_award))
                        <div class="tender-header__descr-item tender-header__descr-winner">
                            <div class="detail-title">{{ t('tender.active_awards_title') }}</div>
                            <div class="detail-value"><a href="">{{ @$lot->__active_award->suppliers[0]->name }}</a></div>
                        </div>
                        <div class="tender-header__descr-item tender-header__descr-expected-price">
                            <div class="detail-title">{{ t('form.search.tender_price') }}</div>
                            <div class="detail-value">{{ @$lot->__active_award->__full_formated_price }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="tender-header__lots-blocks level2 hidden">
                    @include('partials.sidebar._items', ['items'=>@$lot->__items, 'item'=>$lot])
                    @include('partials.sidebar._lot', ['items'=>@$lot->__items, 'item'=>$lot])
                </div>
            @endforeach
        </div>
    @else
        @include('partials.sidebar._items', ['items'=>$item->items, 'item'=>$item])
        @include('partials.sidebar._item', ['items'=>$item->items, 'item'=>$item])
        @include('partials.sidebar._blocks', ['item'=>$item, 'class'=>'', 'hide' => false])
    @endif
    @if(!empty($item->__ngo))
        <div class="tender-header__bets tender-header-block">
            <div class="tender-header__wrap tender-header__descr tender-header__bet">
                <button class="tender-header__descr-toggle"></button>
                <div class="block-title">{{ t('indicators.ngo_block') }}</div>
                @foreach($item->__ngo as $ngo)
                    <div class="tender-header__descr-item">
                        <div class="detail-value">{{ $ngo }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif