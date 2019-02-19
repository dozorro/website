<div id="block_info_lot">

        <div class="margin-bottom-more">
            <div class="block_title">
                <h3>{{t('tender.information_about_lot')}}</h3>
            </div>
            <div class="row" >
                <div class="col-md-12">
                    <div class="tender-description__item">
                        <div class="tender-description__title">{{ t('tender.items_title') }}:</div>
                        <div class="tender-description__text">{{!empty($item->title) ? $item->title : t('tender.no_title')}}</div>
                    </div>
                    @if (!empty($item->description))
                        <div class="tender-description__item description-wr croped">
                            <div class="tender-description__title">{{ t('tender.description_subject_procurement') }}: </div>
                            <div class="tender--description--text tender-description__text description{{mb_strlen($item->description)>350?' croped':' open'}}">
                                {!!nl2br($item->description)!!}
                            </div>
                            @if (mb_strlen($item->description)>350)
                                <a class="search-form--open" href="">
                                    <span>{{t('interface.expand')}}</span>
                                    <span>{{t('interface.collapse')}}</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="col-md-6">
                    @if (!empty($item->value))
                        <div class="tender-description__item">
                            <div class="tender-description__title">
                                {{ t('form.search.tender_price') }}:
                            </div>
                            <div class="tender-description__text">
                                {{number_format($item->value->amount, 0, '', ' ')}} {{$item->value->currency}}
                                @if($item->value->valueAddedTaxIncluded)
                                    {{ t('tender.with_VAT') }}
                                @else
                                    {{ t('tender.without_VAT') }}
                                @endif
                            </div>
                        </div>
                    @endif
                    @if (!empty($item->minimalStep))
                        <div class="tender-description__item">
                            <div class="tender-description__title">
                                {{ t('tender.minimum_auction_step') }}:
                            </div>
                            <div class="tender-description__text">
                                {{number_format($item->minimalStep->amount, 0, '', ' ')}} {{$item->minimalStep->currency}}
                                @if($item->minimalStep->valueAddedTaxIncluded)
                                    {{ t('tender.with_VAT') }}
                                @else
                                    {{ t('tender.without_VAT') }}
                                @endif
                            </div>

                        </div>
                    @endif
                    <div class="tender-description__item">
                        <div class="tender-description__title">{{ t('tender.public_bidding') }}:</div>
                        <div class="tender-description__text">{{t('tender.lot_status.'.$item->status)}}</div>
                    </div>
                </div>
                @if (!empty($item->guarantee) && (int) $item->guarantee->amount>0)
                    <div class="tender-description__item">
                        <div class="tender-description__title">{{ t('tender.type_bid_security') }}:</div>
                        <div class="tender-description__text">{{ t('tender.electronic_bank_guarantee') }}</div>
                    </div>
                    <div class="tender-description__item">
                        <div class="tender-description__title">{{ t('tender.amount_bid_security') }}: </div>
                        <div class="tender-description__text">{{str_replace('.00', '', number_format($item->guarantee->amount, 2, '.', ' '))}} {{$item->guarantee->currency}}</div>
                    </div>
                @else
                    <div class="tender-description__item">
                        <div class="tender-description__title">{{ t('tender.type_bid_security') }}: </div>
                        <div class="tender-description__text">{{ t('tender.none') }}</div>
                    </div>
                @endif
            </div>
        </div>
</div>


