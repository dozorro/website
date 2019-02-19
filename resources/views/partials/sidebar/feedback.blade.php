
<div v-if="tender" data-form-modal="risk" class="none" style="z-index: 999;">
    <div id="overlay" class="overlay2" data-form-modal="risk"></div>
    <div class="modal_div show welcome-modal" data-form-modal="risk" style="height: 450px!important;width: 580px!important;overflow: hidden;z-index: 999;">
        <div class="modal_close"></div>
        <div class="content-holder">
            <h3>{{t('tender.search.NGO_risk_comment')}}</h3>

            <div style="text-align: left;">
                <form id="risk-form">
                    <input type="hidden" name="risk_code" value="">
                    <input type="hidden" name="tender_id" v-bind:value="tender.id">
                    <input type="hidden" name="email" value="{{ @$user->email }}">
                    <input type="hidden" name="full_name" value="{{ @$user->user->full_name }}">
                    <input type="hidden" name="user_id" value="{{ @$user->ngo->id }}">
                    <div>
                        @foreach(range(1, 5) as $index)
                            <div style="float: left;">
                                <label for="risk-mark-{{ $index }}">{{ $index }}</label>
                                <input type="radio" name="risk_value" value="{{ $index }}" id="risk-mark-{{ $index }}">
                                &nbsp;
                            </div>
                        @endforeach
                    </div>
                    <br>
                    <br>
                    <div style="clear: both;">
                        <textarea name="comment" style="width: 500px; height: 250px;"></textarea>
                        <br>
                        <br>
                        <button type="submit">{{ t('tender.search.risk_submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div v-if="tender" data-form-modal="risk_comments" class="none" style="z-index: 999;">
    <div id="overlay" class="overlay2" data-form-modal="risk_comments"></div>
    <div class="modal_div show welcome-modal" data-form-modal="risk_comments" style="height: 450px!important;width: 580px!important;overflow: hidden;z-index: 999;">
        <div class="modal_close"></div>
        <div class="content-holder">
            <h3>{{t('tender.search.NGO_risk_comment')}}</h3>

            <div class="risk_comment_data" style="text-align: left;">

            </div>
        </div>
    </div>
</div>