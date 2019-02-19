    <div class="tender-header__purchase-subjects tender-header-block" v-if="tender.__items_deliveryDate">
        <div v-bind:class="{ 'tender-header__wrap tender-header__descr tender-header__purchase-subject items-block ': true, 'toggled': toggled.items }">
            <button class="tender-header__descr-toggle" v-on:click="toggle('items')"></button>
            <div class="block-title">{{ t('tender.items') }}</div>
            <div class="tender-header_info__item">
                <div class="tender-header__descr-item tender-header__descr-rating" v-if="tender.description">
                    <div class="block-value block-value-description" v-html="tender.description"></div>
                </div>
                <div class="tender-header__descr-item tender-header__descr-rating">
                    <div class="detail-title">{{ t('tender.items_deliveryDate') }}</div>
                    <div class="detail-value" v-html="tender.__items_deliveryDate"></div>
                </div>
                <div class="tender-header__descr-item tender-header__descr-rating" v-if="tender.__items_address">
                    <div class="detail-title">{{ t('tender.items_address') }}</div>
                    <div class="detail-value" v-html="tender.__items_address"></div>
                </div>
                <div class="kicks-container" v-if="tender.items">
                    <div class="kick-item" v-for="item in tender.items">
                        <div class="kick-item-head" style="padding-left: 14px;">
                            <div class="kick-item-bet">
                                <span v-html="item.description"></span><br>
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
                                <div class="tender-header__descr-item tender-header__descr-rating">
                                    <div class="detail-title">{{ t('tender.items_title') }}</div>
                                    <div class="detail-value" v-html="item.description"></div>
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
    </div>