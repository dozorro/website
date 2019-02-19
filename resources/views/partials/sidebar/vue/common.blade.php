<div class="tender-header__descriptions tender-header-block">
    <div v-bind:class="{ 'tender-header__wrap tender-header__descr': true, 'toggled': toggled.common }">
        <button class="tender-header__descr-toggle" v-on:click="toggle('common')"></button>
        <div class="block-title block-title-tender" v-html="tender.title"></div>
        <div class="tender-header_info__item">
            <div class="tender-header__descr-item tender-header__descr-date">
                <div class="detail-title">{{ t('indicatiors.procedure.start') }} — {{ t('indicatiors.procedure.finish') }}</div>
                <div class="detail-value" v-html="tender.dates"></div>
            </div>
            <div class="tender-header__descr-item tender-header__descr-expected-price">
                <div class="detail-title">{{ t('tender.value_amount') }}</div>
                <div class="detail-value" v-html="tender.price"></div>
            </div>
        </div>
        <div class="tender-header_info__item">
            <div class="tender-header__descr-item tender-header__descr-feedback">
                <div class="detail-title">{{ t('indicators.total_forms') }}</div>
                <div class="detail-value" v-html="tender.__totalHundredForms"></div>
            </div>
            <div class="tender-header__descr-item tender-header__descr-rating hidden">
                <div class="detail-title">{{ t('indicators.rating') }}</div>
                <div class="detail-value">?</div>
            </div>
        </div>
    </div>
</div>