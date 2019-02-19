<div class="chat-item question">
    <div class="chat-item-info">
        <div class="chat-item-time" v-if="review.user" v-html="review.user"></div>
        <div class="chat-item-author" v-html="review.date"></div>
    </div>
    <div class="chat-item-info" v-if="review.label">
        <div class="chat-item-author" v-html="review.label"></div>
    </div>
        <div class="reviews__body__one" v-if="review.json.bestPriceComment || review.json.bestPrice">
            <p><strong>{{t('reviews.description_of_requirements')}}</strong></p>
            <h5 v-if="review.json.bestPrice">{{t('reviews.does_the _ubject_procurement_requirements_to_purchase_quality_goods_at_the_best_price')}}?
                 -
                    <span v-if="review.json.bestPrice=='yes'">{{t('yes')}}</span>
                    <span v-if="review.json.bestPrice=='no'">{{t('no')}}</span>
            </h5>
            <p class="chat-item-text" v-if="review.json.bestPriceComment" v-html="review.json.bestPriceComment"></p>
        </div>

        <div class="reviews__body__one" v-if="review.json.maxCompetitionComment || review.json.maxCompetition">
            <p><strong>{{t('reviews.competition')}}</strong></p>
            <p v-if="review.json.maxCompetition"><em>{{t('reviews.following_requirements_ensure_maximum_competition_among_participants')}}?</em><br>
                    <span v-if="review.json.maxCompetition=='yes'">{{t('yes')}}</span>
                    <span v-if="review.json.maxCompetition=='no'">{{t('no')}}</span>
            </p>
            <p v-if="review.json.maxCompetitionComment" v-html="review.json.maxCompetitionComment"></p>
        </div>

        <div class="reviews__body__one" v-if="review.json.productQualityComment || review.json.productQuality">
            <p><strong>{{t('reviews.quality_product')}}</strong></p>
            <p v-if="review.json.productQuality"><em>{{t('reviews.is_it_clear_potential_suppliers_whose_product_quality_customer_expects_buy')}}?</em><br>
                <span v-if="review.json.productQuality=='yes'">{{t('yes')}}</span>
                <span v-if="review.json.productQuality=='no'">{{t('no')}}</span>
            </p>
            <p v-if="review.json.productQualityComment" v-html="review.json.productQualityComment"></p>
        </div>

        <div class="reviews__body__one" v-if="review.json.qualitativeCriteriaComment || review.json.qualitativeCriteria">
            <p><strong>{{t('reviews.assessment_criteria')}}</strong></p>
            <p v-if="review.json.qualitativeCriteria"><em>{{t('reviews.is_there_a_procedure_for_assessing_qualitative_criteria_during_procurement_subject_qualification_contract_performance_agreement')}}?</em><br>
                <span v-if="review.json.qualitativeCriteria=='yes'">{{t('yes')}}</span>
                <span v-if="review.json.qualitativeCriteria=='no'">{{t('no')}}</span>
            </p>
            <p v-if="review.json.qualitativeCriteriaComment" v-html="review.json.qualitativeCriteriaComment"></p>
        </div>
</div>
@include('partials.sidebar.vue.reviews.comment')