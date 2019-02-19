<div class="tender-header__descriptions tender-header-block">
    <div v-bind:class="{ 'tender-header__wrap tender-header__descr': true, 'toggled': toggled.dates }">
        <button class="tender-header__descr-toggle" v-on:click="toggle('dates')"></button>
        <div class="block-title">{{ t('indicators.dates_block') }}</div>
        <div class="tender-header_info__item">
            <div class="tender-header__descr-item tender-header__descr-date" v-if="tender.complaintPeriod">
                <div class="detail-title">{{ t('tender.period2') }}</div>
                <div class="detail-value" v-html="tender.complaintPeriod"></div>
            </div>
            <div class="tender-header__descr-item tender-header__descr-date" v-if="tender.enquiryPeriod">
                <div class="detail-title">{{ t('tender.period1') }}</div>
                <div class="detail-value" v-html="tender.enquiryPeriod"></div>
            </div>
            <div class="tender-header__descr-item tender-header__descr-date" v-if="tender.tenderPeriod">
                <div class="detail-title">{{ t('tender.period3') }}</div>
                <div class="detail-value" v-html="tender.tenderPeriod"></div>
            </div>
            <div class="tender-header__descr-item tender-header__descr-date" v-if="tender.auctionPeriod">
                <div class="detail-title">{{ t('tender.period4') }}</div>
                <div class="detail-value" v-html="tender.auctionPeriod"></div>
            </div>
        </div>
    </div>
</div>