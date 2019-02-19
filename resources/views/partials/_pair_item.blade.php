<li data-id="{{ $k }}" style="opacity:0.001;pointer-events:none">
    <div class="text-center js_pairs" pair-slider data-swipe="{{ $user->is_pairs }}">
        <div></div>
        <div class="block_pairs__bg ">
            <div class="pair-block">
                <div class="inline-layout col-2">
                    <div class="item left-tender @if($rightTenderIsNull){{'single'}}@endif">
                        <a href="#" style="outline:none">
                            @foreach($pair as $risk_code => $value)
                                <strong class="width100 block_question" data-risk="{{ $risk_code }}">{{ t('pairs.'.$value['risk_title']) }}</strong>
                                <span @if($rightTenderIsNull){{'class=width100'}}@endif @if($risk_code == 'F1000'){{'style=font-size:14px;'}}@endif>{{ in_array($value['value_1'], ['true', 'false']) ? ('pairs.'.$value['value_1']) : $value['value_1'] }}</span>
                            @endforeach
                        </a>
                    </div>

                    <div class="item tender_sep right-tender @if($rightTenderIsNull){{'single'}}@endif">
                        <a href="#" style="outline:none">
                            @foreach($pair as $risk_code => $value)
                                <strong class="width100 block_question opacity0" data-risk="{{ $risk_code }}">{{ t('pairs.'.$value['risk_title']) }}</strong>
                                <span class="@if($rightTenderIsNull){{'width100 opacity0'}}@endif" @if($risk_code == 'F1000'){{'style=font-size:14px;'}}@endif>{{ in_array($value['value_2'], ['true', 'false']) ? ('pairs.'.$value['value_2']) : $value['value_2'] }}</span>
                            @endforeach
                        </a>
                    </div>
                </div>
            </div>

            <div class="item" style="margin-top: 25px;@if($rightTenderIsNull){{'display:none;'}}@endif">
                @if($user->is_pairs_button)
                    <a class="pairs_button pair-answer none-active" data-href="/pairs/update/{{ $k }}/?answer=" href="#" style="outline:none">
                        {{ t('pairs.answer_yes') }}
                    </a>
                @endif
                <a class="pairs_button pair-favorite none-active" href="#">
                    {{ t('pairs.favorite') }}
                </a>
                <p id="pair-comment" style="margin:15px auto;display:none;">
                    <textarea name="comment" style="height: 100px;width: 400px;"></textarea>
                </p>
            </div>
        </div>
        <div></div>
    </div>
</li>