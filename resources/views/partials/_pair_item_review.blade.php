<li>
    <div class="text-center js_pairs" pair-slider>
        <div></div>
        <div class="block_pairs__bg ">
            <div>
                <div class="inline-layout col-2">
                    <div class="item">
                        <a onclick="return false;" style="outline:none">
                            @foreach($answers[0]->risks as $value)
                                <strong class="width100 block_question">{{ t('pairs.'.$value['risk_title']) }}</strong>
                                <span @if($risk_code == 'F1000'){{'style=font-size:14px;'}}@endif>{{ in_array($value['value_1'], ['true', 'false']) ? ('pairs.'.$value['value_1']) : $value['value_1'] }}</span>
                            @endforeach
                        </a>
                    </div>

                    <div class="item tender_last">
                        <a onclick="return false;" style="outline:none">
                            @foreach($answers[0]->risks as $value)
                                <strong class="width100 block_question opacity0">{{ t('pairs.'.$value['risk_title']) }}</strong>
                                <span @if($risk_code == 'F1000'){{'style=font-size:14px;'}}@endif>{{ in_array($value['value_2'], ['true', 'false']) ? ('pairs.'.$value['value_2']) : $value['value_2'] }}</span>
                            @endforeach
                        </a>
                    </div>
                </div>
            </div>

            <div class="item" style="margin-top: 25px;">
                <div class="answers-block zero-answer col-sm-12">
                    <?php foreach($answers as $answer): ?>
                        <?php if($answer->answer == 0): ?>
                            <span class="profile-user-img img-responsive img-circle">
                                <span>{{ $answer->user->showAvatarByName() }}</span>
                            </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="answers-block col-sm-12">
                    <div class="plus-answer col-sm-4">
                        <?php foreach($answers as $answer): ?>
                            <?php if($answer->answer == 1): ?>
                                <span class="profile-user-img img-responsive img-circle">
                                    <span>{{ $answer->user->showAvatarByName() }}</span>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="review-form col-sm-4">
                        <form action="{{ route('page.pairs.review.update') }}" method="post" id="pair-form1">
                            {{ csrf_field() }}
                            <input type="hidden" name="decision" value="1">
                            <input type="hidden" name="id" value="{{ $answers[0]->pair_id }}">
                            <a class="pairs_button pair-favorite none-active" href="#" onclick="$('#pair-form1').submit()">
                                {{ t('pairs.accept') }}
                            </a>
                        </form>
                        <form action="{{ route('page.pairs.review.update') }}" method="post" id="pair-form2">
                            {{ csrf_field() }}
                            <input type="hidden" name="decision" value="-1">
                            <input type="hidden" name="id" value="{{ $answers[0]->pair_id }}">
                            <a class="pairs_button pair-favorite none-active" href="#" onclick="$('#pair-form2').submit()">
                                {{ t('pairs.discuss') }}
                            </a>
                        </form>
                    </div>
                    <div class="minus-answer col-sm-4">
                        <?php foreach($answers as $answer): ?>
                            <?php if($answer->answer == -1): ?>
                                <span class="profile-user-img img-responsive img-circle">
                                    <span>{{ $answer->user->showAvatarByName() }}</span>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div></div>
    </div>
</li>