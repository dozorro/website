@if(!empty($item->__features))
    <div class="tender-header__purchase-subjects tender-header-block">
        <div class="tender-header__wrap tender-header__descr tender-header__purchase-subject level2 toggled">
            <button class="tender-header__descr-toggle"></button>
            <div class="block-title">{{ t('indicators.features_block') }}</div>
            <div class="tender-header_info__item toggled">
                <div class="tender-header__descr-item tender-header__descr-rating">
                    <div class="detail-title">{{ t('tender.criteria_title') }}</div>
                    <div class="detail-value"> {{ t('tender.price') .' '. ($item->__features_price * 100) . '% ' . ('+ '.count($item->__features).' '. t('indicators.features_count') ) }}</div>
                </div>
            </div>
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
        <div class="tender-header__wrap tender-header__descr tender-header__purchase-subject level2 toggled">
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

@include('partials.sidebar._documents', ['item'=>$item, 'hide' => false])
{{--
@if(!empty($items))
 <div class="tender-header__kicks tender-header-block">
     <div class="tender-header__wrap tender-header__descr tender-header__kick level3 items-block toggled">
         <button class="tender-header__descr-toggle"></button>
         <div class="block-title">{{ t('tender.positions_detail') }}</div>

         <div class="tender-header_info__item toggled">
             <div class="tender-header__descr-item tender-header__descr-rating">
                 <div class="detail-title">{{ t('tender.items_title') }}</div>
                 <div class="detail-value" style="max-height: 35px;overflow: hidden">{{ $items[0]->description }}</div>
             </div>
             <div class="tender-header__descr-item tender-header__descr-rating">
                 <div class="detail-title">{{ t('tender.delivery_date') }}</div>
                 <div class="detail-value">
                     {{ $items[0]->__format_delivery_date }}
                     <span class="option item-option">
                         @if(count($items) > 1)
                             + {{ count($items)-1 }} {{ t('indicators.items_variants') }}
                         @endif
                     </span>
                 </div>
             </div>
         </div>
     </div>
 </div>

 <div class="tender-header__purchase-subjects tender-header-block">
     @foreach($items as $k => $_item)
         <div class="tender-header__wrap tender-header__descr tender-header__purchase-subject level3 items-block toggled">
             <button class="tender-header__descr-toggle"></button>
             <div class="block-title">{{ t('tender.positions_detail') }}</div>
             <div class="tender-header_info__item toggled">
                 <div class="tender-header__descr-item tender-header__descr-rating">
                     <div class="detail-title">{{ t('tender.items_title') }}</div>
                     <div class="detail-value" style="max-height: 35px;overflow: hidden">{{ $_item->description }}</div>
                 </div>
                 <div class="tender-header__descr-item tender-header__descr-rating">
                     <div class="detail-title">{{ t('tender.delivery_date') }}</div>
                     <div class="detail-value">{{ $_item->__format_delivery_date }}
                         @if(!$k)
                             <span class="option item-option">
                             @if(count($items) > 1)
                                 + {{ count($items)-1 }} {{ t('indicators.items_variants') }}
                             @endif
                         </span>
                         @endif
                     </div>
                 </div>
             </div>
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