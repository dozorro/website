<div v-if="review.json.complaintAnswerSatisfaction && review.json.complaintAnswerSatisfactionComment">
    <div class="chat-item question">
        <div class="chat-item-info">
            <div class="chat-item-time" v-if="review.user" v-html="review.user"></div>
            <div class="chat-item-author" v-html="review.date"></div>
        </div>
        <div class="chat-item-info" v-if="review.label">
            <div class="chat-item-author" v-html="review.label"></div>
        </div>

        <div class="reviews__stars">
            <h3>{{t('reviews.complaint_answer_satisfaction')}}:</h3>
            <ul class="tender-stars tender-stars--" :class="'tender-stars--'+review.json.complaintAnswerSatisfaction">
                <li></li><li></li><li></li><li></li><li></li>
            </ul>
        </div>

        <div class="chat-item-text" v-html="review.json.complaintAnswerSatisfactionComment"></div>
    </div>
</div>
@include('partials.sidebar.vue.reviews.comment')