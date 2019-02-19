<div class="tender-header__purchase-subjects tender-header-block" v-if="tender.documents_total">
    <div v-bind:class="{ 'tender-header__wrap tender-header__descr tender-header__purchase-subject': true, 'toggled': toggled.documents, 'loading': loading.documents }">
        <button class="tender-header__descr-toggle" v-on:click="toggleRemote('documents')"></button>
        <spinner size="small"></spinner>
        <div class="block-title">{{ t('indicators.docs_block') }}</div>
        <div class="tender-header_info__item toggled">
            <div class="tender-header__descr-item">
                <div class="detail-value">@{{ tender.documents_total }} {{ t('indicators.docs_count') }}</div>
            </div>
        </div>

        <div class="tender-header_info__item" v-if="tender.remote.documents.documents">
            <div class="tender-header__descr-item">
                <div class="detail-value">@{{ tender.documents_total }} {{ t('indicators.docs_count') }}</div>
                <div style="margin-top: 15px;" v-bind:class="{'kick-item': true, 'hidden': tender.remote.documents.hide_documents_five}" v-if="tender.remote.documents.documents_five && tender.remote.documents.documents_five.length">
                    <div class="kick-item-info-group">
                        <div class="sub-group">
                            <div class="sub-group-value-item" v-for="document in tender.remote.documents.documents_five">
                                <div class="sub-group-value"><a :href="document.url" target="_blank" v-html="document.title"></a></div>
                            </div>
                        </div>
                    </div>
                </div>

               <div style="margin-top: 15px;" v-bind:class="{'kick-item': true, 'hidden': tender.remote.documents.hide_documents_all}">
                    <div class="kick-item-info-group">
                        <div class="sub-group">
                            <div class="sub-group-value-item" v-for="document in tender.remote.documents.documents_all">
                                <div class="sub-group-value"><a :href="document.url" target="_blank" v-html="document.title"></a></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 15px;" v-bind:class="{'kick-item': true, 'hidden': tender.remote.documents.hide_documents}">
                    <div class="kick-item-info-group">
                        <div class="sub-group">
                            <div class="sub-group-value-item" v-for="document in tender.remote.documents.documents">
                                <div class="sub-group-value"><a :href="document.url" target="_blank" v-html="document.title"></a></div>
                                <div class="sub-group-value-descr" v-html="document.date"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="!tender.remote.documents.hide_documents_five">
                    <button class="tender-header__descr-title risks-title show-docs" v-on:click="tender.remote.documents.hide_documents_all=false;tender.remote.documents.hide_documents_five=true">{{ t('indicators.show_detail_docs') }}</button>
                </div>
                <div v-else-if="!tender.remote.documents.hide_documents_all">
                    <button class="tender-header__descr-title risks-title show-docs" v-on:click="tender.remote.documents.hide_documents=false;tender.remote.documents.hide_documents_all=true">{{ t('indicators.show_detail_docs') }}</button>
                    <button class="tender-header__descr-title risks-title show-docs" v-on:click="tender.remote.documents.hide_documents_all=true;tender.remote.documents.hide_documents_five=false">{{ t('indicators.hide_detail_docs') }}</button>
                </div>
                <div v-else-if="!tender.remote.documents.hide_documents">
                    <button class="tender-header__descr-title risks-title show-docs" v-on:click="tender.remote.documents.hide_documents_all=false;tender.remote.documents.hide_documents=true">{{ t('indicators.hide_detail_docs') }}</button>
                </div>
            </div>
        </div>

        <div class="tender-header__descr-item tender-header__descr-risks" v-if="tender.remote.documents.stroked && tender.remote.documents.stroked.length">
            <button class="tender-header__descr-title risks-title docs-history" v-on:click="tender.remote.documents.off=!tender.remote.documents.off">{{ t('tender.history_change') }}</button>
            <div v-bind:class="{ 'risks-items': true, 'hidden': tender.remote.documents.off }">
                <div class="risk-values">
                    <div class="item inline-layout" v-for="document in tender.remote.documents.stroked">
                        <div class="date" v-html="tender.remote.documents.documents[document.d].date"></div>
                        <div class="name_doc">
                            <a :href="tender.remote.documents.documents[document.d].url" target="_blank" v-html="tender.remote.documents.documents[document.d].title" class="word-break"></a>
                        </div>
                        <div class="list_doc">
                            <div class="item inline-layout">
                                <div class="date" v-html="document.data"></div>
                                <div class="name_doc">
                                    <a :href="document.url" target="_blank" v-html="document.title" class="word-break stroked"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>