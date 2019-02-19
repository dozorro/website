<div class="tender-header__descriptions tender-header-block">
    <div v-bind:class="{ 'tender-header__wrap tender-header__descr': true, 'toggled': toggled.procedure }">
        <button class="tender-header__descr-toggle" v-on:click="toggle('procedure')"></button>
        <div class="block-title">{{ t('tender.information_on_procedure') }}</div>
        <div class="tender-header_info__item">
            <div class="tender-header__descr-item tender-header__descr-date">
                <div class="detail-title">{{ t('tender.tenderID') }}</div>
                <div class="detail-value"><a :href="tender.tender_route" v-html="tender.tenderID" target="_blank"></a></div>
            </div>
            <div class="tender-header__descr-item tender-header__descr-date">
                <div class="detail-title">{{ t('tender.procurementMethodType') }}</div>
                <div class="detail-value" v-html="tender.__procedure_name"></div>
            </div>
            <div class="tender-header__descr-item tender-header__descr-expected-price" v-if="tender.description">
                <div class="detail-title">{{ t('tender.description') }}</div>
                <div class="detail-value" v-html="tender.description"></div>
            </div>
            <div class="tender-header__descr-item tender-header__descr-expected-price">
                <div class="detail-title">{{ t('tender.status') }}</div>
                <div class="detail-value" v-html="tender.status"></div>
            </div>
        </div>
        <div class="tender-header_info__item" v-if="tender.__winner_name">
            <div class="tender-header__descr-item tender-header__descr-winner">
                <div class="detail-title">{{ t('indicatiors.procedure.active.award') }}</div>
                <div class="detail-value"><a :href="tender.__winner_url" v-html="tender.__winner_name"></a></div>
                <div class="detail-value" target="_blank" v-html="tender.__winner_price"></div>
            </div>
        </div>
    </div>
</div>