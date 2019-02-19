    <div class="tender-header__purchase-subjects tender-header-block" v-if="lot.__items">
        <div v-bind:class="{ 'tender-header__wrap tender-header__descr tender-header__purchase-subject items-block ': true, 'toggled': toggledLots[lotId].items, 'loading': loadingLots[lotId].items }">
            <button class="tender-header__descr-toggle" v-on:click="toggleRemote('items', lotId)"></button>
            <spinner size="small"></spinner>
            <div class="block-title">{{ t('tender.lot_items') }}</div>
            <div class="tender-header_info__item" v-if="lots[lotId] && lots[lotId].remote.items.other">
                <div class="tender-header__descr-item tender-header__descr-rating" v-if="lots[lotId].description">
                    <div class="block-value block-value-description" v-html="lots[lotId].description"></div>
                </div>
                <div class="tender-header__descr-item tender-header__descr-rating">
                    <div class="detail-title">{{ t('tender.items_deliveryDate') }}</div>
                    <div class="detail-value" v-html="lots[lotId].remote.items.other.__items_deliveryDate"></div>
                </div>
                <div class="tender-header__descr-item tender-header__descr-rating">
                    <div class="detail-title">{{ t('tender.items_address') }}</div>
                    <div class="detail-value" v-html="lots[lotId].remote.items.other.__items_address"></div>
                </div>
            </div>
                <div class="kicks-container" v-if="lots[lotId] && lots[lotId].remote.items.items && lots[lotId].remote.items.items.length">
                    <div class="kick-item" v-for="item in lots[lotId].remote.items.items">
                        <div class="kick-item-head" style="padding-left: 14px;">
                            <div class="kick-item-bet">
                                <div v-html="item.description" style="overflow: hidden;max-height: 50px;"></div><br>
                                <span v-html="item.__format_delivery_date"></span>
                            </div>
                        </div>
                        <button class="kick-item-info-btn" v-on:click="item.hidden=!item.hidden">
                            <span v-if="item.hidden">{{ t('indicators.item.detail') }}</span>
                            <span v-if="!item.hidden">{{ t('indicators.item.hide') }}</span>
                        </button>
                        <div v-bind:class="{ 'kick-item-info': true, 'hidden': item.hidden }">
                            <div class="kick-item-info-group">
                                <div class="sub-group">
                                    <div class="sub-group-title">{{ t('indicators.item.date') }}</div>
                                    <div class="sub-group-value" v-html="item.__format_delivery_date"></div>
                                </div>
                            </div>
                            <div class="kick-item-info-group">
                                <div class="tender-header__descr-item tender-header__descr-rating" v-on:click="item.maxHeight+=tender.defaultMaxHeight">
                                    <div class="detail-title">{{ t('tender.items_title') }}</div>
                                    <div class="detail-value" v-bind:style="{maxHeight: item.maxHeight + 'px'}" v-html="item.description"></div>
                                </div>
                                <div class="tender-header__descr-item tender-header__descr-rating">
                                    <div class="detail-title">{{ t('tender.delivery_date') }}</div>
                                    <div class="detail-value" v-html="item.__format_delivery_date"><span class="option"></span></div>
                                </div>
                                <div class="tender-header__descr-item tender-header__descr-rating">
                                    <div class="detail-title">{{ t('tender.cpv') }}</div>
                                    <div class="detail-value"><span v-html="item.cpv"></span>, <span v-html="item.cpv_description"></span></div>
                                </div>
                                <div class="tender-header__descr-item tender-header__descr-rating">
                                    <div class="detail-title">{{ t('tender.item_q') }}</div>
                                    <div class="detail-value"><span v-html="item.quantity"></span> <span v-html="item.unit_name"></span></div>
                                </div>
                                <div class="tender-header__descr-item tender-header__descr-rating">
                                    <div class="detail-title">{{ t('tender.item_address') }}</div>
                                    <div class="detail-value" v-html="item.__address"></div>
                                </div>
                            </div>
                        </div>
                        <button v-bind:class="{ 'kick-item-info-btn bottom':true, 'hidden': item.hidden }" v-on:click="item.hidden=!item.hidden">
                            <span>{{ t('indicators.item.hide') }}</span>
                        </button>
                    </div>
                </div>
        </div>
    </div>