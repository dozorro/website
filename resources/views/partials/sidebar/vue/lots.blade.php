<div class="tender-header__lots" v-if="tender.lots">
    <div v-for="(lot, lotId) in tender.lots">
        <div v-bind:class="{ 'tender-header__wrap tender-header__descr tender-header__lot tender-header-block': true, 'toggled': lot.toggled }">
            <button class="tender-header__descr-toggle" v-on:click="lot.toggled=!lot.toggled"></button>
            <spinner size="small"></spinner>
            <div class="block-title block-title-tender" v-html="lot.title"></div>
            <div class="tender-header_info__item">
                <div class="tender-header__descr-item" v-if="lot.description">
                    <div class="detail-value"><strong v-html="lot.description"></strong></div>
                </div>
                <div class="tender-header__descr-item tender-header__descr-date" v-if="lot.status">
                    <div class="detail-title">{{ t('indicators.lot_status') }}</div>
                    <div class="detail-value" v-html="lot.status"></div>
                </div>
                <div v-if="lot.__winner_name" class="tender-header__descr-item tender-header__descr-winner">
                    <div class="detail-title">{{ t('tender.active_awards_title') }}</div>
                    <div class="detail-value"><a :href="lot.__winner_url" v-html="lot.__winner_name"></a></div>
                </div>
                <div v-if="lot.__winner_price" class="tender-header__descr-item tender-header__descr-expected-price">
                    <div class="detail-title">{{ t('form.search.tender_price') }}</div>
                    <div class="detail-value" v-html="lot.__winner_price"></div>
                </div>
                <div class="tender-header__descr-item tender-header__descr-winner" v-if="lot.auctionUrl">
                    <div class="detail-title">{{ t('tender.lot_auctionUrl') }}</div>
                    <div class="detail-value"><a :href="lot.auctionUrl">{{ t('tender.lot_auctionUrl_open') }}</a></div>
                </div>
            </div>
        </div>
        <div v-bind:class="{'tender-header__lots-blocks level2': true, 'hidden': lot.toggled}">
            @include('partials.sidebar.vue.lots.dates')
            @include('partials.sidebar.vue.lots.features')
            @include('partials.sidebar.vue.lots.documents')
            @include('partials.sidebar.vue.lots.questions')
            @include('partials.sidebar.vue.lots.complaints')
            @include('partials.sidebar.vue.lots.items')
            @include('partials.sidebar.vue.lots.prequalifications')
            @include('partials.sidebar.vue.lots.qualifications')
            @include('partials.sidebar.vue.lots.contracts')
        </div>
    </div>
</div>