<div class="tender-header__public-monitoring" v-if="tender.reviews_total">
    <div v-bind:class="{'tender-header__wrap tender-header__descr': true, 'toggled': toggled.reviews, 'loading': loading.reviews}">
        <button class="tender-header__descr-toggle" v-on:click="toggleRemote('reviews')"></button>
        <spinner size="small"></spinner>
        <div class="block-title">{{ t('indicators.reviews') }} (@{{ tender.reviews_total }})</div>
        <div class="tender-header_info__item" v-if="tender.remote.reviews.reviews && tender.remote.reviews.reviews.length">
            <div class="tender-header__descr-item chat-block" v-for="(review, rKey) in tender.remote.reviews.reviews">
                <div v-if="review.schema == 'F101'">
                    @include('partials.sidebar.vue.reviews.F101')
                </div>
                <div v-if="review.schema == 'F102'">
                    @include('partials.sidebar.vue.reviews.F102')
                </div>
                <div v-if="review.schema == 'F111'">
                    @include('partials.sidebar.vue.reviews.F111')
                </div>
                <div v-if="review.schema == 'F114'">
                    @include('partials.sidebar.vue.reviews.F114')
                </div>
                <div v-if="review.schema == 'F115'">
                    @include('partials.sidebar.vue.reviews.F115')
                </div>
                <div v-if="review.schema == 'F116'">
                    @include('partials.sidebar.vue.reviews.F116')
                </div>
            </div>
        </div>
    </div>
</div>