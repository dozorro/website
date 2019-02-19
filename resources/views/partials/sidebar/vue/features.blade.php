<div class="tender-header__purchase-subjects tender-header-block" v-if="tender.__features && tender.__features.length">
    <div v-bind:class="{ 'tender-header__wrap tender-header__descr tender-header__purchase-subject': true, 'toggled': toggled.features }">
        <button class="tender-header__descr-toggle" v-on:click="toggle('features')"></button>
        <div class="block-title">{{ t('indicators.features_block') }}</div>
        <div class="tender-header_info__item toggled">
            <div class="tender-header__descr-item tender-header__descr-rating">
                <div class="detail-title">{{ t('tender.criteria_title') }}</div>
                <div class="detail-value"> {{ t('tender.price') }} <span v-html="tender.__features_price_real"></span> % (+ <span v-html="tender.__features.length"></span> {{ t('indicators.features_count') }})</div>
            </div>
        </div>
        <div class="tender-header_info__item">
            <div class="tender-header__descr-item tender-header__descr-rating">
                <div class="detail-title">{{ t('tender.criteria_title') }}</div>
                <div class="detail-value"> {{ t('tender.price') }} <span v-html="tender.__features_price_real"></span> % (+ <span v-html="tender.__features.length"></span> {{ t('indicators.features_count') }})</div>
            </div>
            <div class="tender-header__descr-item tender-header__descr-rating" v-for="feature in tender.__features">
                <div class="detail-title" v-html="feature.title"></div>
                <div class="detail-value" v-if="feature.enum" v-for="_enum in feature.enum">
                    <span v-html="_enum.title"></span>: <span v-html="_enum.value"></span>
                </div>
            </div>
        </div>
    </div>
</div>