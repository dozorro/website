<div class="tender-header__descriptions tender-header__customer tender-header-block">
    <div v-bind:class="{ 'tender-header__wrap tender-header__descr': true, 'toggled': toggled.customer }">
        <button class="tender-header__descr-toggle" v-on:click="toggle('customer')"></button>
        <div class="block-title">{{ t('indicators.block_customer') }}</div>
        <div class="tender-header_info__item toggled">
            <div class="tender-header__descr-item tender-header__descr-date">
                <div class="detail-value">
                    <a target="_blank" :href="tender.procuringEntity.url">@{{ tender.procuringEntity.name }}</a>
                </div>
            </div>
        </div>
        <div class="tender-header_info__item">
            <div class="tender-header__descr-item tender-header__descr-date">
                <div class="detail-value">
                    <a target="_blank" :href="tender.procuringEntity.url">@{{ tender.procuringEntity.name }}</a>
                </div>
                <div class="detail-value">
                    @{{ tender.procuringEntity.id }}
                </div>
            </div>
            <div class="tender-header__descr-item tender-header__descr-date">
                <div class="detail-title">{{ t('tender.kind') }}</div>
                <div class="detail-value">@{{ tender.procuringEntity.kind }}</div>
            </div>
            <div class="tender-header__descr-item tender-header__descr-expected-price">
                <div class="detail-title">{{ t('tender.contactPoint') }}</div>
                <div class="detail-value">@{{ tender.procuringEntity.__contactPoint }}</div>
            </div>
            <div class="tender-header__descr-item tender-header__descr-date">
                <div class="detail-title">{{ t('tender.procuringEntity_address') }}</div>
                <div class="detail-value">@{{ tender.procuringEntity.__address }}</div>
            </div>
        </div>
    </div>
</div>