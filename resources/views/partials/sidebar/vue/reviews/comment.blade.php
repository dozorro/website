<div class="chat-item answer" v-if="review.comments" v-on:click="comment.maxHeight+=tender.defaultMaxHeight" v-for="comment in review.comments">
    <div class="chat-item-text" v-bind:style="{maxHeight: comment.maxHeight + 'px'}" v-html="comment.json.comment"></div>
    <div class="chat-item-info">
        <div class="chat-item-date" v-html="comment.date"></div>
        <div class="chat-item-author" v-html="comment.label"></div>
    </div>
</div>