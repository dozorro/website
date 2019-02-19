@if(!empty($item->__items_deliveryDate))
    <div class="tender-header__purchase-subjects tender-header-block">
        <div class="tender-header__wrap tender-header__descr tender-header__purchase-subject items-block toggled">
            <button class="tender-header__descr-toggle"></button>
            <div class="block-title">{{ t('tender.positions') }}</div>
            <div class="tender-header_info__item toggled">
                @if(!empty($item->description))
                    <div class="block-value block-value-description">{{ $item->description }}</div>
                @endif
                <div class="tender-header__descr-item tender-header__descr-rating">
                    <div class="detail-title">{{ t('tender.items_deliveryDate') }}</div>
                    <div class="detail-value">{{ $item->__items_deliveryDate }}</div>
                </div>
                <div class="tender-header__descr-item tender-header__descr-rating">
                    <div class="detail-title">{{ t('tender.items_address') }}</div>
                    <div class="detail-value">{{ $item->__items_address }}</div>
                </div>
            </div>
            <div class="tender-header_info__item">
                @if(!empty($item->description))
                    <div class="tender-header__descr-item tender-header__descr-rating">
                        <div class="block-value block-value-description">{{ $item->description }}</div>
                    </div>
                @endif
                <div class="tender-header__descr-item tender-header__descr-rating">
                    <div class="detail-title">{{ t('tender.items_deliveryDate') }}</div>
                    <div class="detail-value">{{ $item->__items_deliveryDate }}</div>
                </div>
                <div class="tender-header__descr-item tender-header__descr-rating">
                    <div class="detail-title">{{ t('tender.items_address') }}</div>
                    <div class="detail-value">{{ $item->__items_address }}</div>
                </div>

                @if(!empty($items))
                    <div class="kicks-container">
                        @foreach($items as $k => $_item)
                            <div class="kick-item">
                                <div class="kick-item-head" style="padding-left: 14px;">
                                    <div class="kick-item-bet"><span>{{ $_item->description }}</span><br><span>{{ $_item->__format_delivery_date }}</span></div>
                                </div>
                                <button class="kick-item-info-btn" data-show-text="{{ t('indicators.item.detail') }}" data-hide-text="{{ t('indicators.item.hide') }}">
                                    <span>{{ t('indicators.item.detail') }}</span>
                                </button>
                                <div class="kick-item-info">
                                    <div class="kick-item-info-group">
                                        <div class="sub-group">
                                            <div class="sub-group-title">{{ t('indicators.item.date') }}</div>
                                            <div class="sub-group-value">{{ $_item->__format_delivery_date }}</div>
                                        </div>
                                    </div>
                                    <div class="kick-item-info-group">
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
                                <button class="kick-item-info-btn bottom hidden"><span>{{ t('indicators.item.hide') }}</span></button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif