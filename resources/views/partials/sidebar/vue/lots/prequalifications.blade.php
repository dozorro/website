<div class="tender-header__kicks tender-header-block" v-if="lots[lotId].prequalifications">
    <div v-bind:class="{ 'tender-header__wrap tender-header__descr tender-header__kick': true, 'toggled': toggledLots[lotId].prequalifications, 'loading': loadingLots[lotId].prequalifications }">
        <button class="tender-header__descr-toggle" v-on:click="toggleRemote('prequalifications', lotId)"></button>
        <spinner size="small"></spinner>
        <div class="block-title">{{ t('tender.qualifications') }}</div>
        <div class="tender-header_info__item">
            <div class="kicks-container" v-if="lots[lotId] && lots[lotId].remote.prequalifications.prequalifications && lots[lotId].remote.prequalifications.prequalifications.length">
                <div v-for="qualification in lots[lotId].remote.prequalifications.prequalifications" v-bind:class="{ 'kick-item': true,  'positive': qualification.status == 'active', 'pending': qualification.status == 'pending', 'negative': (qualification.status != 'active' && qualification.status != 'pending') }">
                    <div class="kick-item-head">
                        <div class="kick-item-title">
                            <a :href="qualification.supplierUrl" target="_blank" v-html="qualification.supplier"></a>
                        </div>
                    </div>
                    <button class="kick-item-info-btn" v-on:click="qualification.hidden=!qualification.hidden">
                        <span v-if="qualification.hidden">{{ t('indicators.bid.detail') }}</span>
                        <span v-if="!qualification.hidden">{{ t('indicators.bid.hide') }}</span>
                    </button>
                    <div v-bind:class="{ 'kick-item-info': true, 'hidden': qualification.hidden }">
                        <div class="kick-item-info-group">
                            <div class="sub-group">
                                <div class="group-title">{{ t('indicators.qualification.results') }} <span v-html="qualification.status_name"></span></div>
                                <div class="sub-group-value-item">
                                    <div class="sub-group-value">{{ t('indicators.qualification.date') }} <span v-html="qualification.date"></span></div>
                                    <div class="sub-group-value-descr" v-if="qualification.documents.length" v-for="document in qualification.documents">
                                        <a :href="document.url" target=_blank v-html="document.title"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button v-bind:class="{ 'kick-item-info-btn bottom':true, 'hidden': qualification.hidden }" v-on:click="qualification.hidden=!qualification.hidden">
                        <span>{{ t('indicators.bid.hide') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>