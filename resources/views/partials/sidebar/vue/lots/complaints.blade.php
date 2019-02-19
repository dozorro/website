<div class="tender-header__public-monitoring tender-header-block" v-if="lot.__complaints">
    <div v-bind:class="{'tender-header__wrap tender-header__descr': true, 'toggled': toggledLots[lotId].complaints, 'loading': loadingLots[lotId].complaints}">
        <button class="tender-header__descr-toggle" v-on:click="toggleRemote('complaints', lotId)"></button>
        <spinner size="small"></spinner>
        <div class="block-title">{{ t('indicators.complaints') }} (@{{ lot.__complaints }})</div>
        <div class="tender-header_info__item" v-if="lots[lotId].remote.complaints.complaints && lots[lotId].remote.complaints.complaints.complaints.length">
            <div class="tender-header__descr-item chat-block" v-for="(complaint, cKey) in lots[lotId].remote.complaints.complaints.complaints">
                <div class="chat-item question" v-on:click="complaint.maxHeight+=tender.defaultMaxHeight">
                    <div class="chat-item-text" v-bind:style="{maxHeight: complaint.maxHeight + 'px'}" v-html="complaint.description"></div>
                    <div class="chat-item-info">
                        <div class="chat-item-time" v-html="complaint.authorId"></div>
                        <div class="chat-item-author">
                            <a :href="complaint.authorUrl" target="_blank" v-html="complaint.authorName"></a>
                        </div>
                    </div>
                    <div class="chat-item-info">
                        <div class="chat-item-time" v-html="complaint.date"></div>
                        <div class="chat-item-author" v-html="complaint.status"></div>
                    </div>
                </div>
                <div v-if="complaint.documentsOwner && complaint.documentsOwner.length">
                    <div class="detail-title">{{t('tender.complaints.documentsOwner')}}</div>
                    <button class="tender-header__descr-title risks-title show-docs" v-on:click="complaint.hiddenDocumentsOwner=!complaint.hiddenDocumentsOwner">{{ t('indicators.show_docs') }}</button>
                    <div v-bind:class="{'kick-item': true, 'hidden': complaint.hiddenDocumentsOwner}">
                        <div class="kick-item-info-group">
                            <div class="sub-group">
                                <div class="sub-group-value-item" v-for="document in complaint.documentsOwner">
                                    <div class="sub-group-value"><a target="_blank" v-html="document.title" :href="document.url"></a></div>
                                    <div class="sub-group-value-descr" v-html="document.date"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="complaint.documentsReviewer && complaint.documentsReviewer.length">
                    <div class="detail-title">{{t('tender.complaints.documentsReviewer')}}</div>
                    <button class="tender-header__descr-title risks-title show-docs" v-on:click="complaint.hiddenDocumentsReviewer=!complaint.hiddenDocumentsReviewer">{{ t('indicators.show_docs') }}</button>
                    <div v-bind:class="{'kick-item': true, 'hidden': complaint.hiddenDocumentsReviewer}">
                        <div class="kick-item-info-group">
                            <div class="sub-group">
                                <div class="sub-group-value-item" v-for="document in complaint.documentsReviewer">
                                    <div class="sub-group-value"><a target="_blank" v-html="document.title" :href="document.url"></a></div>
                                    <div class="sub-group-value-descr" v-html="document.date"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tender-header_info__item" v-if="lots[lotId].remote.complaints.complaints && lots[lotId].remote.complaints.complaints.qualificationComplaints.length">
            <div class="tender-header__descr-item">
                <div class="detail-title">{{ t('indicators.qualification_complaints') }}</div>
            </div>
            <div class="tender-header__descr-item chat-block" v-for="(complaint, cKey) in lots[lotId].remote.complaints.complaints.qualificationComplaints">
                <div class="chat-item question" v-on:click="complaint.maxHeight+=tender.defaultMaxHeight">
                    <div class="chat-item-text" v-bind:style="{maxHeight: complaint.maxHeight + 'px'}" v-html="complaint.description"></div>
                    <div class="chat-item-info">
                        <div class="chat-item-time" v-html="complaint.authorId"></div>
                        <div class="chat-item-author">
                            <a :href="complaint.authorUrl" target="_blank" v-html="complaint.authorName"></a>
                        </div>
                    </div>
                    <div class="chat-item-info">
                        <div class="chat-item-time" v-html="complaint.date"></div>
                        <div class="chat-item-author" v-html="complaint.status"></div>
                    </div>
                </div>
                <div v-if="complaint.documentsOwner && complaint.documentsOwner.length">
                    <div class="detail-title">{{t('tender.complaints.documentsOwner')}}</div>
                    <button class="tender-header__descr-title risks-title show-docs" v-on:click="complaint.hiddenDocumentsOwner=!complaint.hiddenDocumentsOwner">{{ t('indicators.show_docs') }}</button>
                    <div v-bind:class="{'kick-item': true, 'hidden': complaint.hiddenDocumentsOwner}">
                        <div class="kick-item-info-group">
                            <div class="sub-group">
                                <div class="sub-group-value-item" v-for="document in complaint.documentsOwner">
                                    <div class="sub-group-value"><a target="_blank" v-html="document.title" :href="document.url"></a></div>
                                    <div class="sub-group-value-descr" v-html="document.date"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="complaint.documentsReviewer && complaint.documentsReviewer.length">
                    <div class="detail-title">{{t('tender.complaints.documentsReviewer')}}</div>
                    <button class="tender-header__descr-title risks-title show-docs" v-on:click="complaint.hiddenDocumentsReviewer=!complaint.hiddenDocumentsReviewer">{{ t('indicators.show_docs') }}</button>
                    <div v-bind:class="{'kick-item': true, 'hidden': complaint.hiddenDocumentsReviewer}">
                        <div class="kick-item-info-group">
                            <div class="sub-group">
                                <div class="sub-group-value-item" v-for="document in complaint.documentsReviewer">
                                    <div class="sub-group-value"><a target="_blank" v-html="document.title" :href="document.url"></a></div>
                                    <div class="sub-group-value-descr" v-html="document.date"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tender-header_info__item" v-if="lots[lotId].remote.complaints.complaints && lots[lotId].remote.complaints.complaints.awardComplaints.length">
            <div class="tender-header__descr-item">
                <div class="detail-title">{{ t('indicators.award_complaints') }}</div>
            </div>
            <div class="tender-header__descr-item chat-block" v-for="(complaint, cKey) in lots[lotId].remote.complaints.complaints.awardComplaints">
                <div class="chat-item question" v-on:click="complaint.maxHeight+=tender.defaultMaxHeight">
                    <div class="chat-item-text" v-bind:style="{maxHeight: complaint.maxHeight + 'px'}" v-html="complaint.description"></div>
                    <div class="chat-item-info">
                        <div class="chat-item-time" v-html="complaint.authorId"></div>
                        <div class="chat-item-author">
                            <a :href="complaint.authorUrl" target="_blank" v-html="complaint.authorName"></a>
                        </div>
                    </div>
                    <div class="chat-item-info">
                        <div class="chat-item-time" v-html="complaint.date"></div>
                        <div class="chat-item-author" v-html="complaint.status"></div>
                    </div>
                </div>
                <div v-if="complaint.documentsOwner && complaint.documentsOwner.length">
                    <div class="detail-title">{{t('tender.complaints.documentsOwner')}}</div>
                    <button class="tender-header__descr-title risks-title show-docs" v-on:click="complaint.hiddenDocumentsOwner=!complaint.hiddenDocumentsOwner">{{ t('indicators.show_docs') }}</button>
                    <div v-bind:class="{'kick-item': true, 'hidden': complaint.hiddenDocumentsOwner}">
                        <div class="kick-item-info-group">
                            <div class="sub-group">
                                <div class="sub-group-value-item" v-for="document in complaint.documentsOwner">
                                    <div class="sub-group-value"><a target="_blank" v-html="document.title" :href="document.url"></a></div>
                                    <div class="sub-group-value-descr" v-html="document.date"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="complaint.documentsReviewer && complaint.documentsReviewer.length">
                    <div class="detail-title">{{t('tender.complaints.documentsReviewer')}}</div>
                    <button class="tender-header__descr-title risks-title show-docs" v-on:click="complaint.hiddenDocumentsReviewer=!complaint.hiddenDocumentsReviewer">{{ t('indicators.show_docs') }}</button>
                    <div v-bind:class="{'kick-item': true, 'hidden': complaint.hiddenDocumentsReviewer}">
                        <div class="kick-item-info-group">
                            <div class="sub-group">
                                <div class="sub-group-value-item" v-for="document in complaint.documentsReviewer">
                                    <div class="sub-group-value"><a target="_blank" v-html="document.title" :href="document.url"></a></div>
                                    <div class="sub-group-value-descr" v-html="document.date"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>