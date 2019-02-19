<div class="tender-header__contracts tender-header-block" v-if="tender.contracts">
    <div v-bind:class="{ 'tender-header__wrap tender-header__descr tender-header__kick': true, 'toggled': toggled.contracts, 'loading': loading.contracts }">
        <button class="tender-header__descr-toggle" v-on:click="toggleRemote('contracts')"></button>
        <spinner size="small"></spinner>
        <div class="block-title">{{ t('indicators.contract_block') }}</div>
        <div class="tender-header_info__item" v-if="tender.remote.contracts && tender.remote.contracts.contracts.length" v-for="contract in tender.remote.contracts.contracts">
            <div class="tender-header__descr-item">
                <div class="detail-title">{{ t('tender.contract_title') }}</div>
                <div class="detail-value" v-html="contract.price"></div>
            </div>
            <div class="tender-header__descr-item">
                <div class="detail-title">{{t('indicators.contract.docs')}}</div>
                <div class="detail-value"><span v-html="contract.count"></span>{{ t('indicators.contract.docs_count') }}</div>
                <div class="kick-item" v-if="contract.documents">
                    <div class="kick-item-info-group">
                        <div class="sub-group">
                            <div class="sub-group-value-item" v-for="document in contract.documents">
                                <div class="sub-group-value">
                                    <a :href="document.url" target="_blank" v-html="document.title"></a>
                                </div>
                                <div class="sub-group-value-descr" v-html="document.date"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="detail-value" v-if="!contract.documents">{{ t('indicators.contract.docs_empty') }}</div>
            </div>
            <div class="tender-header__descr-item" v-if="contract.changes && contract.changes.length">
                <div class="detail-title">{{t('tender.changes_contract')}} <span><span v-html="contract.changes_count"></span> {{ t('indicators.changes_count') }}</span></div>
                <div class="kicks-container">
                    <div class="kick-item">
                        <button class="kick-item-info-btn" v-on:click="contract.hidden=!contract.hidden">
                            <span v-if="contract.hidden">{{ t('indicators.bid.detail') }}</span>
                            <span v-if="!contract.hidden">{{ t('indicators.bid.hide') }}</span>
                        </button>

                        <div v-bind:class="{ 'kick-item-info': true, 'hidden': contract.hidden }" v-for="change in contract.changes">
                            <div class="kick-item-info-group" v-if="change.rationaleTypes && change.rationaleTypes.length">
                                <div class="sub-group" v-for="rType in change.rationaleTypes">
                                    <div class="sub-group-value" style="font-weight: bold;" v-html="rType"></div>
                                </div>
                            </div>
                            <div class="kick-item-info-group">
                                <div class="sub-group">
                                    <div class="sub-group-value" v-html="change.rationale"></div>
                                    <div class="sub-group-value" v-html="change.date" style="margin-top: 15px;"></div>
                                </div>
                            </div>
                            <div class="kick-item">
                                <button class="kick-item-info-btn" v-on:click="change.hidden=!change.hidden"><span><text v-html="change.count"></text> {{ t('indicators.contract.changes_docs') }}</span></button>
                                <div v-bind:class="{ 'kick-item-info': true, 'hidden': change.hidden }">
                                    <div class="kick-item-info-group">
                                        <div class="sub-group">
                                            <div class="sub-group-value-item" v-for="document in change.contracts">
                                                <div class="sub-group-value">
                                                    <a :href="document.url" target="_blank" v-html="document.title"></a>
                                                </div>
                                                <div class="sub-group-value-descr" v-html="document.date"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="!contract.changes" class="detail-title">{{t('indicators.contract.changes_empty')}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>