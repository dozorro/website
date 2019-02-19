<div class="tender-header__bets tender-header-block" v-if="tender.ngo">
    <div v-bind:class="{ 'tender-header__wrap tender-header__descr tender-header__bet': true, 'toggled': toggled.ngo, 'loading': loading.ngo }">
        <button class="tender-header__descr-toggle" v-on:click="toggleRemote('ngo')"></button>
        <div class="block-title">{{ t('indicators.ngo_block') }}</div>
        <div class="tender-header__descr-item" v-for="ngo in tender.remote.ngo.ngo">
            <div class="kicks-container">
                <div class="kick-item">
                    <div class="kick-item-head" style="padding-left: 14px;">
                        <div class="kick-item-title" v-html="ngo.name"></div>
                    </div>
                    <div v-if="ngo.f201 && ngo.f201.length" v-for="f201 in ngo.f201">
                        <button class="kick-item-info-btn" v-on:click="f201.hidden=!f201.hidden" style="height: auto;">
                            <span v-html="f201.name"></span>
                        </button>
                        <div v-bind:class="{ 'kick-item-info': true, 'hidden': f201.hidden }">
                            <div class="kick-item-info-group" v-if="f201.comment">
                                <div class="sub-group">
                                    <div class="sub-group-value" v-html="f201.comment"></div>
                                </div>
                            </div>
                            <div v-if="ngo.f202[f201.id] && ngo.f202[f201.id].length" v-for="f202 in ngo.f202[f201.id]">
                                <div class="kick-item-info-group">
                                    <div class="sub-group" v-on:click="f202.hidden=!f202.hidden" style="cursor: pointer;">
                                        <div class="sub-group-title" v-html="f202.name"></div>
                                        <div class="sub-group-value" v-if="f202.comment" v-html="f202.comment"></div>
                                        <div v-if="ngo.f204[f202.id] && ngo.f204[f202.id].length" v-for="f204 in ngo.f204[f202.id]">
                                            <div class="sub-group">
                                                <span class="label_status victory" v-if="f204.name=='succes'">{{t('tender.ngo.status_success')}}</span>
                                                <span class="label_status defeat" v-if="f204.name=='defeat'">{{t('tender.ngo.status_defeat')}}</span>
                                                <span class="label_status cancel" v-if="f204.name=='cancel'">{{t('tender.ngo.status_cancel')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-bind:class="{ 'kick-item-info': true, 'hidden': f202.hidden }" v-if="ngo.f203[f202.id] && ngo.f203[f202.id].length" v-for="f203 in ngo.f203[f202.id]">
                                    <div class="kick-item-info-group">
                                        <div class="sub-group" v-on:click="f203.hidden=!f203.hidden" style="cursor: pointer;">
                                            <div class="sub-group-title" v-html="f203.name"></div>
                                            <div class="sub-group-value" v-if="f203.comment" v-html="f203.comment"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="ngo.f204[f201.id] && ngo.f204[f201.id].length" v-for="f204 in ngo.f204[f201.id]">
                                <div class="kick-item-info-group">
                                    <div class="sub-group">
                                        <span class="label_status victory" v-if="f204.name=='succes'">{{t('tender.ngo.status_success')}}</span>
                                        <span class="label_status defeat" v-if="f204.name=='defeat'">{{t('tender.ngo.status_defeat')}}</span>
                                        <span class="label_status cancel" v-if="f204.name=='cancel'">{{t('tender.ngo.status_cancel')}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>