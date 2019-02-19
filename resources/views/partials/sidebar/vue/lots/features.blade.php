<div class="tender-header__purchase-subjects tender-header-block" v-if="lot.__features">
    <div v-bind:class="{ 'tender-header__wrap tender-header__descr tender-header__purchase-subject': true, 'toggled': toggledLots[lotId].features, 'loading': loadingLots[lotId].features }">
        <button class="tender-header__descr-toggle" v-on:click="toggleRemote('features', lotId)"></button>
        <spinner size="small"></spinner>
        <div class="block-title">{{ t('indicators.features_block') }}</div>
        <div class="tender-header_info__item" v-if="lots[lotId].remote.features.features && lots[lotId].remote.features.features.length">
            <div class="tender-header__descr-item tender-header__descr-rating">
                <div class="detail-title">{{ t('tender.criteria_title') }}</div>
                <div class="detail-value"> {{ t('tender.price') }} <span v-html="lots[lotId].remote.features.other.__features_price_real"></span> % (+ <span v-html="lots[lotId].remote.features.features.length"></span> {{ t('indicators.features_count') }})</div>
            </div>
            <div class="tender-header__descr-item tender-header__descr-rating" v-for="feature in lots[lotId].remote.features.features">
                <div class="detail-title" v-html="feature.title"></div>
                <div class="detail-value" v-if="feature.enum" v-for="_enum in feature.enum">
                    <span v-html="_enum.title"></span><br><span v-html="_enum.value"></span>
                </div>
            </div>
        </div>
    </div>
</div>