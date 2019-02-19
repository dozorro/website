<div class="tender-header__kicks tender-header-block" v-if="tender.awards">
    <div v-bind:class="{ 'tender-header__wrap tender-header__descr tender-header__kick': true, 'toggled': toggled.qualifications, 'loading': loading.qualifications }">
        <button class="tender-header__descr-toggle" v-on:click="toggleRemote('qualifications')"></button>
        <spinner size="small"></spinner>
        <div class="block-title">{{ t('tender.protocol_disclosure') }}</div>

        {{--<button class="tender-header__descr-title risks-title risks-title-toggled">{{ t('indicators.risks') }}&nbsp;<span v-html="tender.__awards_risks.length"></span></button>--}}

        <div class="tender-header_info__item">
            <div class="tender-header__descr-item tender-header__descr-risks" v-if="tender.__awards_risks">
                <div class="risks-items">
                    {{--<div class="risk-coefficient">{{ t('indicators.risk') }} <strong v-html="tender.__rating"></strong></div>--}}
                    <div class="risk-values">
                        <ul class="risk-item" v-for="title in tender.__awards_risks">
                            <li v-html="title"></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="kicks-container" v-if="tender.remote.qualifications.awards && tender.remote.qualifications.awards.length">
                <div v-for="award in tender.remote.qualifications.awards" v-bind:class="{ 'kick-item': true,  'positive': award.status == 'active', 'pending': award.status == 'pending', 'negative': (award.status != 'active' && award.status != 'pending') }">
                    <div class="kick-item-head">
                        <div class="kick-item-title">
                            <a :href="award.supplierUrl" target="_blank" v-html="award.supplier"></a>
                        </div>
                        <div class="kick-item-bet"><span v-html="award.bid"></span><span>→</span><span v-html="award.amount"></span></div>
                        <div v-if="award.tags && award.tags.length" v-for="tag in award.tags">
                            <span v-html="tag"></span>&nbsp;
                        </div>
                    </div>
                    <button class="kick-item-info-btn" v-on:click="award.hidden=!award.hidden">
                        <span v-if="award.hidden">{{ t('indicators.bid.detail') }}</span>
                        <span v-if="!award.hidden">{{ t('indicators.bid.hide') }}</span>
                    </button>
                    <div v-bind:class="{ 'kick-item-info': true, 'hidden': award.hidden }">
                        <div class="kick-item-info-group">
                            <div class="sub-group">
                                <div class="sub-group-title">{{ t('indicators.bid.date') }}</div>
                                <div class="sub-group-value" v-html="award.bid_date"></div>
                            </div>
                        </div>
                        <div class="kick-item-info-group" v-if="award.documents_bid.length">
                            <div v-bind:class="{'sub-group': true, 'hidden': award.hide_documents_bid_five}">
                                <div class="sub-group-title">{{ t('indicators.bids_docs') }}</div>
                                <div class="sub-group-value-item" v-for="document in award.documents_bid_five">
                                    <div class="sub-group-value">
                                        <a :href="document.url" target=_blank v-html="document.title"></a>
                                    </div>
                                </div>
                            </div>

                             <div v-bind:class="{'kick-item': true, 'hidden': award.hide_documents_bid_all}">
                                <div class="kick-item-info-group">
                                    <div class="sub-group">
                                        <div class="sub-group-value-item" v-for="document in award.documents_bid_all">
                                            <div class="sub-group-value"><a :href="document.url" target="_blank" v-html="document.title"></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-bind:class="{'kick-item': true, 'hidden': award.hide_documents_bid}">
                                <div class="kick-item-info-group">
                                    <div class="sub-group">
                                        <div class="sub-group-value-item" v-for="document in award.documents_bid">
                                            <div class="sub-group-value"><a :href="document.url" target="_blank" v-html="document.title"></a></div>
                                            <div class="sub-group-value-descr" v-html="document.date"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="!award.hide_documents_bid_five">
                                <button class="tender-header__descr-title risks-title show-docs" v-on:click="award.hide_documents_bid_all=false;award.hide_documents_bid_five=true">{{ t('indicators.show_detail_docs') }}</button>
                            </div>
                            <div v-else-if="!award.hide_documents_bid_all">
                                <button class="tender-header__descr-title risks-title show-docs" v-on:click="award.hide_documents_bid=false;award.hide_documents_bid_all=true">{{ t('indicators.show_detail_docs') }}</button>
                                <button class="tender-header__descr-title risks-title show-docs" v-on:click="award.hide_documents_bid_all=true;award.hide_documents_bid_five=false">{{ t('indicators.hide_detail_docs') }}</button>
                            </div>
                            <div v-else-if="!award.hide_documents_bid">
                                <button class="tender-header__descr-title risks-title show-docs" v-on:click="award.hide_documents_bid_all=false;award.hide_documents_bid=true">{{ t('indicators.hide_detail_docs') }}</button>
                            </div>

                        </div>
                        <div class="kick-item-info-group">
                            <div class="sub-group">
                                <div class="sub-group-title">{{ t('indicators.award.contactPoint') }}</div>
                                <div class="sub-group-value" v-html="award.contact"></div>
                            </div>
                            <div class="sub-group">
                                <div class="sub-group-title">{{ t('indicators.award.address') }}</div>
                                <div class="sub-group-value" v-html="award.address"></div>
                            </div>
                        </div>
                        <div class="kick-item-info-group">
                            <div class="sub-group">
                                <div class="group-title">{{ t('indicators.award.results') }}: <span v-html="award.status_name"></span></div>
                                <div class="sub-group-value-item">
                                    <div class="sub-group-value">{{ t('indicators.award.date') }} <span v-html="award.date"></span></div>

                                    <div v-if="award.documents.length">
                                        <div v-bind:class="{'sub-group-value-descr': true, 'hidden': award.hide_documents_five}" v-for="document in award.documents_five">
                                            <a :href="document.url" target=_blank v-html="document.title"></a>
                                        </div>

                                        <div v-bind:class="{'kick-item': true, 'hidden': award.hide_documents_all}">
                                            <div class="kick-item-info-group">
                                                <div class="sub-group">
                                                    <div class="sub-group-value-item" v-for="document in award.documents_all">
                                                        <div class="sub-group-value"><a :href="document.url" target="_blank" v-html="document.title"></a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div v-bind:class="{'kick-item': true, 'hidden': award.hide_documents}">
                                            <div class="kick-item-info-group">
                                                <div class="sub-group">
                                                    <div class="sub-group-value-item" v-for="document in award.documents">
                                                        <div class="sub-group-value"><a :href="document.url" target="_blank" v-html="document.title"></a></div>
                                                        <div class="sub-group-value-descr" v-html="document.date"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div v-if="!award.hide_documents_five">
                                            <button class="tender-header__descr-title risks-title show-docs" v-on:click="award.hide_documents_all=false;award.hide_documents_five=true">{{ t('indicators.show_detail_docs') }}</button>
                                        </div>
                                        <div v-else-if="!award.hide_documents_all">
                                            <button class="tender-header__descr-title risks-title show-docs" v-on:click="award.hide_documents=false;award.hide_documents_all=true">{{ t('indicators.show_detail_docs') }}</button>
                                            <button class="tender-header__descr-title risks-title show-docs" v-on:click="award.hide_documents_all=true;award.hide_documents_five=false">{{ t('indicators.hide_detail_docs') }}</button>
                                        </div>
                                        <div v-else-if="!award.hide_documents">
                                            <button class="tender-header__descr-title risks-title show-docs" v-on:click="award.hide_documents_all=false;award.hide_documents=true">{{ t('indicators.hide_detail_docs') }}</button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button v-bind:class="{ 'kick-item-info-btn bottom':true, 'hidden': award.hidden }" v-on:click="award.hidden=!award.hidden">
                        <span>{{ t('indicators.bid.hide') }}</span>
                    </button>
                </div>
            </div>

            <div class="kicks-container" v-if="tender.remote.qualifications.bids && tender.remote.qualifications.bids.length">
                <div class="block-title">{{ t('indicators.bids.without_awards') }}</div>
                <div class="kick-item" v-for="bid in tender.remote.qualifications.bids">
                    <div class="kick-item-head">
                        <div class="kick-item-title">
                            <a :href="bid.bidUrl" v-html="bid.name"></a>
                        </div>
                        <div class="kick-item-bet"><span v-html="bid.bid"></span><span>→</span><span v-html="bid.amount"></span></div>
                        <div v-if="bid.tags && bid.tags.length" v-for="tag in bid.tags">
                            <span v-html="tag"></span>&nbsp;
                        </div>
                    </div>
                    <button class="kick-item-info-btn" v-on:click="bid.hidden=!bid.hidden">
                        <span v-if="bid.hidden">{{ t('indicators.bid.detail') }}</span>
                        <span v-if="!bid.hidden">{{ t('indicators.bid.hide') }}</span>
                    </button>
                    <div v-bind:class="{ 'kick-item-info': true, 'hidden': bid.hidden }">
                        <div class="kick-item-info-group">
                            <div class="sub-group">
                                <div class="sub-group-title">{{ t('indicators.bid.date') }}</div>
                                <div class="sub-group-value" v-html="bid.date"></div>
                            </div>
                        </div>
                        <div class="kick-item-info-group" v-if="bid.documents.length">
                            <div class="sub-group">
                                <div class="sub-group-title">{{ t('indicators.bids_docs') }}</div>
                                <div class="sub-group-value-item" v-for="document in bid.documents">
                                    <div class="sub-group-value"><a :href="document.url" v-html="document.title" target="_blank"></a></div>
                                    <div class="sub-group-value-descr" v-html="document.date"></div>
                                </div>
                            </div>
                        </div>
                        <div class="kick-item-info-group">
                            <div class="sub-group">
                                <div class="sub-group-title">{{ t('indicators.award.contactPoint') }}</div>
                                <div class="sub-group-value" v-html="bid.contact"></div>
                            </div>
                            <div class="sub-group">
                                <div class="sub-group-title">{{ t('indicators.award.address') }}</div>
                                <div class="sub-group-value" v-html="bid.address"></div>
                            </div>
                        </div>
                    </div>
                    <button v-bind:class="{ 'kick-item-info-btn bottom':true, 'hidden': bid.hidden }" v-on:click="bid.hidden=!bid.hidden">
                        <span>{{ t('indicators.bid.hide') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>