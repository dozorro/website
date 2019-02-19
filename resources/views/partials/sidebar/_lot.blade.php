@if(!empty($item->__risksTitle))
    <div class="tender-header__purchase-subjects tender-header-block">
        <div class="tender-header__wrap tender-header__descr tender-header__purchase-subject toggled">
            <button class="tender-header__descr-toggle"></button>
            <button class="tender-header__descr-title risks-title risks-title-toggled">{{ t('indicators.risks') }}&nbsp;<span></span></button>
            <div class="tender-header_info__item">
                <div class="tender-header__descr-item tender-header__descr-risks">
                    <button class="tender-header__descr-title risks-title hidden">{{ t('indicators.risks') }}&nbsp;<span></span></button>
                    <div class="risks-items hidden">
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
{{--
@if(!empty($items))
    <div class="tender-header__purchase-subjects tender-header-block">
        @foreach($items as $k => $_item)
            <div class="tender-header__wrap tender-header__descr tender-header__purchase-subject items-block  toggled">
                <button class="tender-header__descr-toggle"></button>
                <div class="block-title">{{ t('tender.positions') }}</div>
                <div class="tender-header_info__item">
                    <div class="tender-header__descr-item tender-header__descr-rating">
                        <div class="detail-title">{{ t('tender.items_title') }}</div>
                        <div class="detail-value">{{ $_item->description }}</div>
                    </div>
                    <div class="tender-header__descr-item tender-header__descr-rating">
                        <div class="detail-title">{{ t('tender.delivery_date') }}</div>
                        <div class="detail-value">{{ $_item->__format_delivery_date }}<span class="option"></span></div>
                    </div>
                    <div class="tender-header__descr-item tender-header__descr-rating">
                        <div class="detail-title">{{ t('tender.cpv') }}</div>
                        <div class="detail-value">{{ $_item->classification->id.', '.$_item->classification->description }}</div>
                    </div>
                    <div class="tender-header__descr-item tender-header__descr-rating">
                        <div class="detail-title">{{ t('tender.item_q') }}</div>
                        <div class="detail-value">{{ $_item->quantity.' '.$_item->unit->name }}</div>
                    </div>
                    <div class="tender-header__descr-item tender-header__descr-rating">
                        <div class="detail-title">{{ t('tender.item_address') }}</div>
                        <div class="detail-value">{{ @$_item->__address }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
--}}
@if(!empty($item->__features))
    <div class="tender-header__purchase-subjects tender-header-block">
        <div class="tender-header__wrap tender-header__descr tender-header__purchase-subject toggled">
            <button class="tender-header__descr-toggle"></button>
            <div class="block-title">{{ t('indicators.features_block') }}</div>
            <div class="tender-header_info__item">
                <div class="tender-header__descr-item tender-header__descr-rating">
                    <div class="detail-title">{{ t('tender.criteria_title') }}</div>
                    <div class="detail-value"> {{ t('tender.price') .' '. ($item->__features_price * 100) . '% ' . ('+ '.count($item->__features).' '. t('indicators.features_count') ) }}</div>
                </div>
                @foreach($item->__features as $feature)
                    <div class="tender-header__descr-item tender-header__descr-rating">
                        <div class="detail-title">{{ $feature->title }}</div>
                        @if(!empty($feature->enum))
                            <div class="detail-value">{!! implode('<br>', array_column($feature->enum, 'title')) !!}</div>
                            <div class="detail-value">{!! implode('<br>', array_column($feature->enum, 'value')) !!}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

@if(!empty($item->guarantee->amount))
    <div class="tender-header__purchase-subjects tender-header-block">
        <div class="tender-header__wrap tender-header__descr tender-header__purchase-subject toggled">
            <button class="tender-header__descr-toggle"></button>
            <div class="block-title">{{ t('indicators.guarantee_block') }}</div>
            <div class="tender-header__descr-item">
                <div class="detail-title">{{ t('tender.amount_bid_security') }}</div>
                <div class="detail-value">
                    {{ @$item->guarantee->amount > 0.0 ? ($item->guarantee->amount . ' ' . $item->guarantee->currency) : t('tender.missing') }}
                </div>
            </div>
        </div>
    </div>
@endif

@include('partials.sidebar._documents', ['item'=>$item, 'hide' => true])
@include('partials.sidebar._blocks', ['items'=>$items, 'hide' => true])
