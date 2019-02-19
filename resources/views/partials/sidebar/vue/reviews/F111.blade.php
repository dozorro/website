    <div class="chat-item question">
        <div class="chat-item-info">
            <div class="chat-item-time" v-if="review.user" v-html="review.user"></div>
            <div class="chat-item-author" v-html="review.date"></div>
        </div>
        <div class="chat-item-info" v-if="review.label">
            <div class="chat-item-author" v-html="review.label"></div>
        </div>

        <div class="reviews__body__one" v-if="review.json.cheapestWasDisqualifiedComment || review.json.cheapestWasDisqualified">
            <div class="reviews__title inline-layout">
                <h5 v-if="review.json.cheapestWasDisqualified">{{t('reviews.F111_cheapestWasDisqualified')}}?
                        <span v-if="review.json.cheapestWasDisqualified">
                                &nbsp;– <span v-if="review.json.cheapestWasDisqualified=='yes'">{{t('yes')}}</span>
                            <span v-if="review.json.cheapestWasDisqualified=='no'">{{t('no')}}</span>
                        </span>
                </h5>
            </div>

            <div class="chat-item-text" v-if="review.json.cheapestWasDisqualifiedComment" v-html="review.json.cheapestWasDisqualifiedComment"></div>
        </div>
        <div class="reviews__body__one" v-if="review.json.argumentativeDisqualificationComment || review.json.argumentativeDisqualification">
            <div class="reviews__title inline-layout">
                <h5 v-if="review.json.argumentativeDisqualification">{{t('reviews.F111_argumentativeDisqualification')}}?
                            <span v-if="review.json.argumentativeDisqualification">
                                &nbsp;– <span v-if="review.json.argumentativeDisqualification=='yes'">{{t('yes')}}</span>
                                <span v-if="review.json.argumentativeDisqualification=='no'">{{t('no')}}</span>
                            </span>
                </h5>
            </div>
            <div class="reviews__text" v-if="review.json.argumentativeDisqualificationComment">
                <p v-html="review.json.argumentativeDisqualificationComment"></p>
            </div>
        </div>
    </div>
    @include('partials.sidebar.vue.reviews.comment')