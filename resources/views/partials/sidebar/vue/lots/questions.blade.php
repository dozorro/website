<div class="tender-header__public-monitoring" v-if="lot.__questions">
    <div v-bind:class="{'tender-header__wrap tender-header__descr': true, 'toggled': toggledLots[lotId].questions, 'loading': loadingLots[lotId].questions}">
        <button class="tender-header__descr-toggle" v-on:click="toggleRemote('questions', lotId)"></button>
        <spinner size="small"></spinner>
        <div class="block-title">{{ t('indicators.questions') }} (@{{ lot.__questions }})</div>
        <div class="tender-header_info__item" v-if="lots[lotId].remote.questions.questions && lots[lotId].remote.questions.questions.length">
            <div class="tender-header__descr-item chat-block" v-for="(question, qKey) in lots[lotId].remote.questions.questions">
                <div class="chat-item question" v-on:click="question.maxHeight+=tender.defaultMaxHeight">
                    <div class="chat-item-info">
                        <div class="chat-item-time" v-html="question.authorId"></div>
                        <div class="chat-item-author">
                            <a :href="question.authorUrl" target="_blank" v-html="question.authorName"></a>
                        </div>
                    </div>
                    <div class="chat-item-info">
                        <div class="chat-item-author" v-html="question.date"></div>
                    </div>
                    <div class="chat-item-text" v-bind:style="{maxHeight: question.maxHeight + 'px'}" v-html="question.description"></div>
                </div>
                <div class="chat-item answer" v-if="question.answer" v-on:click="question.answerMaxHeight+=tender.defaultMaxHeight">
                    <div class="chat-item-text" v-html="question.answer" v-bind:style="{maxHeight: question.answerMaxHeight + 'px'}"></div>
                    <div class="chat-item-info">
                        <div class="chat-item-author" v-html="question.dateAnswered"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>