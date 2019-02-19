<div class="tender-header__descriptions tender-header-block" v-if="tender.__risks">
    <div v-bind:class="{ 'tender-header__wrap tender-header__descr': true, 'toggled': toggled.risks }">
        <button class="tender-header__descr-toggle" v-on:click="toggle('risks')"></button>

        <div class="block-title">{{ t('indicators.risks') }}</div>
        <div class="tender-header-visible-item">
            <div class="rating_stars" v-bind:data-rating="tender.riskScore">
                <div v-for="i in 10" v-bind:class="{ 'star': true, 'active': tender.__rating >= i, 'half': tender.__rating >= i && tender.__rating < i+1 && tender.riskScoreHalf <= 0.5}"></div>
            </div>
            <div class="rating-indicators-amount">
                <span v-html="tender.__risks.length"></span> <span>{{ t('indicators.risks_count') }}</span>
            </div>
        </div>

        <div class="tender-header_info__item">
            <div class="tender-header__descr-item tender-header__descr-risks">
                <div class="risks-items">
                    {{--<div class="risk-coefficient">{{ t('indicators.risk') }} <strong v-html="tender.__rating"></strong></div>--}}
                    <div class="risk-values">
                        <ul class="risk-item" v-for="(risk, rKey) in tender.__risks">
                            <li>
                                <span v-on:click="showInfoBox(rKey)" v-html="risk.title"></span>&nbsp;
                                @if(!empty($riskFeedback))
                                    <span data-form="risk" v-bind:data-risk-code="risk.code" class="comment_number comment-risk">+</span>
                                    <span data-form="risk_comments" v-if="risk.comments" v-bind:data-risk-comments="risk.comments" class="comment_number show-comments-risk"></span>
                                @endif
                                <span v-if="risk.url">
                                    <a :href="risk.url" target="_blank">
                                        <span class="risk-icon-sidebar profile-role1">{{ t('indicators.risks.externalurl') }}</span>
                                    </a>
                                </span>
                                <div class="info">
                                    <div v-bind:class="{'info_text': true, 'show': riskKey==rKey}">
                                        <div v-html="risk.description"></div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>