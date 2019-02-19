<div data-form="{{ $review->model }}" data-date="{{ $review->date->format('d.m.Y H:i') }}">
    <div class="reviews__item-inner">
        <div class="reviews__header">
            {{--<span class="number">3</span>--}}
            <span class="name-wrap">
                @if($review->user->ngo_profile)
                    <span class="name">{{ $review->user->ngo_profile->title }}</span>
                @endif
                <span class="date">{{ $review->date->format('d.m.Y H:i') }}</span>
            </span>
        </div>
        @if(!empty(\App\Classes\User::ngo()->id) && !empty($review->authorJson->ngo->id) && \App\Classes\User::ngo()->id==$review->authorJson->ngo->id)
            @if($review->schema=='F201')
                <a href="" class="ngo-form-selector"
                    data-formjs="open_ngo"
                    data-form="F202"
                    data-model="form"
                    data-parent="{{ $review->object_id }}"
                    data-form-title="{{t('tender.review.ngo_F202.form_title')}}"
                    data-submit-button="{{t('tender.review.ngo_F202.submit_button')}}">
                        {{t('tender.review.ngo_F202.action')}}
                </a>
                &nbsp;&nbsp;
                <a href="" class="ngo-form-selector"
                    data-formjs="open_ngo"
                    data-form="F203"
                    data-model="form"
                    data-parent="{{ $review->object_id }}"
                    data-form-title="{{t('tender.review.ngo_F203.form_title')}}"
                    data-submit-button="{{t('tender.review.ngo_F203.submit_button')}}">
                        {{t('tender.review.ngo_F203.action')}}
                </a>
                &nbsp;&nbsp;
                <a href="" class="ngo-form-selector"
                    data-formjs="jsonForm"
                    data-form="F204"
                    data-model="form"
                    data-parent="{{ $review->object_id }}"
                    data-form-title="{{t('tender.review.ngo_F204.form_title')}}"
                    data-init="F204"
                    data-submit-button="{{t('tender.review.ngo_F204.submit_button')}}">
                        {{t('tender.review.ngo_F204.action')}}
                </a>
            @endif
        @endif

        @include('partials/reviews/'.$review->schema)
    </div>
</div>
<br><br>