@if(!empty($data->value))
<div class="col-md-6 block-profile-rating" style="padding: 30px;">

        <div class="title-container">
            @if(!empty($data->title))
                <h3>{{ $data->title }}</h3>
            @endif
                {{--<div class="info" style="margin-bottom: 15px;margin-left: 10px;">
                    <span class="info_icon"></span>
                    <div class="info_text">
                        <div>
                            {{ t('profile.rating.desc') }}
                        </div>
                    </div>
                </div>--}}
        </div>
        <div class="overflow-table">
            <div class="profile-rating-item">
                <div class="rating-number">{{ $data->value->ratings_avg }}</div>
                <div class="rating-total">
                    <div class="rating-amount"><span>{{ t('profile.rating.total') }}</span>&nbsp;<span>{{ $data->value->ratings_total }}</span></div>
                    <!--<div class="rating-stars">
                        <span class="rate-star active"></span>
                        <span class="rate-star active"></span>
                        <span class="rate-star active"></span>
                        <span class="rate-star active"></span>
                        <span class="rate-star"></span>
                    </div>-->
                </div>
                <div class="rating-stars-sections">
                    <?php $i = 1; ?>
                    @foreach($data->value->ratings as $field => $rating)
                        <div class="star-section">
                            <div class="star-rank">{{ $i }}</div>
                            <div class="star-scale">
                                <div class="star-scale-active" style="@if(!$rating)background: none;@endif width: {{ $data->value->__ratings[$field] }}%;">{{ $rating }}</div>
                            </div>
                        </div>
                        <?php $i++; ?>
                    @endforeach
                </div>
            </div>
        </div>

</div>
@endif
