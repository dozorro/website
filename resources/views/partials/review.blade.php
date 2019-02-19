<div class="reviews__item{{ empty($parent)?' reviews__item--deep-1':'' }}{{ !empty($parent)?' reviews__item--deep-2':'' }}@if (isset($parent)) review__parent-{{ $parent->id }} hide @endif custom-review" data-form="{{ $review->model }}" data-date="{{ $review->date->format('d.m.Y H:i') }}">
    <div class="reviews__item-inner">
        <div class="reviews__header">
            @if(!in_array($review->schema, ['F114','F115','F116']) && !in_array($item->status, ['active.enquires','active.tendering','active.auction']))
            <span class="reviews__author reviews__author--{{ $review->checkAuthorStatus() }}">
                @if($review->user && ($review->user->ngo_profile || $review->user->issetEdrpou($review->entity_id)))
                    {{ $review->showAuthorName() }}
                @else
                    @if($review->is_anon)
                        {{t('reviews.review_author_is_anon')}}
                    @else
                        {{ $review->showAuthorName() }}
                    @endif
                @endif
            </span>
            @endif
            @if($review->ngo_work)
                <a href="#ngo" class="goto-ngo reviews__author" style="position:relative;">
                    {{t('reviews.ngo_work')}}
                </a>
            @endif
            <span class="reveiw__date" style="position:relative">
                @if($review->was_sent)
                    {{ $review->date->format('d.m.Y H:i') }}
                @else
                    <div class="three-bounce">
                        <div class="one"></div>
                        <div class="two"></div>
                        <div class="three"></div>
                    </div>
                    Ожидает публикации
                @endif
            </span>
            @if($is_admin)
                <span class="reveiw__date" style="margin-right:20px;">{{ $review->object_id}}</span>
            @endif
        </div>

        @include('partials/reviews/'.$review->schema)

        <div class="reviews__footer">
            @if(sizeof($review->comments()))
                <a href="" class="reviews__read-comments" data-formjs="comments" data-object-id="{{ $review->object_id }}">Читати коментарі: {{ sizeof($review->comments()) }}</a>
            @endif

            @if(sizeof($review->reviews) > 0 && $show_related)
                <a href="" data-parent="{{ $review->id }}" class="open-reviews__button">
                    <span>{{t('reviews.see_all_reviews_blog')}}</span>
                    <span>{{t('reviews.hide_all_user_reviews')}}</span>
                </a>
            @endif
            <div data-thread="{{ $review->object_id }}" form-comment >
                <a href=""
                   class="open-comment__button"
                   data-formjs="jsonForm"
                   data-form="comment"
                   data-form-title="{{t('reviews.your_comment')}}"
                   data-submit-button="{{t('reviews.add_comment')}}"
                   data-model="comment"
                   data-validate="comment"
                   data-init="comment">
                    {{t('reviews.add_comment')}}
                </a>
            </div>

            @if($user && $user->ngo)
                <div style="margin-left: 25px;" class="tender-header__review-button">
                    <a style="background-color: #e55166;color:white!important;" class="tender-header__link3" href="{{ route('page.tender_form', ['id' => $item->tenderID, 'form' => 'F201', 'parentForm'=>$review->object_id]) }}">{{t('tender.add_violations')}}</a>
                </div>
            @endif

            @if($review->issetCommentEdrpou())
                <div class="label_custom_reviews">{{t('reviews.comment_from_customer')}}</div>
            @endif

            @if($review->showGroup())
                <div class="label_custom_reviews2" style="{{ $review->userGroup->color ? 'background:'.$review->userGroup->color : ''}}">{{ $review->userGroup->name }}</div>
            @endif

            @if(!empty($review->userNgo))
                <em>{{ $review->userNgo->name }}</em>
            @endif
        </div>
        @if (sizeof($review->comments()))
            @foreach ($review->comments() as $comment)
                @if(!empty($comment->json->comment))
                    <div class="reviews__item reviews__item--deep-2 custom-review comments">
                        <div class="reviews__item-inner">
                            <div class="reviews__header">
                                <span class="reviews__author reviews__author--{{ $review->checkAuthorStatus() }}">
                                    @if($comment->user && ($comment->user->ngo_profile || $comment->user->issetEdrpou($comment->entity_id)))
                                        {{ $comment->showAuthorName() }}
                                    @else
                                        @if($comment->is_anon)
                                            {{t('reviews.comment_author_is_anon')}}
                                        @else
                                            {{ $comment->showAuthorName() }}
                                        @endif
                                    @endif
                                </span><span class="reveiw__date">{{ $comment->date->format('d.m.Y H:i') }}</span>
                                @if($is_admin)
                                    <span class="reveiw__date" style="margin-right:20px;">{{ $comment->object_id}}</span>
                                @endif
                                @if($comment->showGroup())
                                    <div class="label_custom_reviews2" style="{{ $comment->userGroup->color ? 'background:'.$comment->userGroup->color : ''}}">{{ $comment->userGroup->name }}</div>
                                @elseif($comment->user && empty($comment->userGroup) && $comment->is_customer)
                                    <span class="label_custom_reviews2">
                                        {{t('reviews.answer_customer')}}
                                    </span>
                                @elseif($comment->is_customer)
                                    <span class="label_custom_reviews2">
                                        {{t('reviews.answer_customer')}}
                                    </span>
                                @endif
                            </div>
                            <div class="reviews__body">
                                <div class="reviews__text">
                                    <p>{!! auto_format($comment->json->comment) !!}</p>
                                </div>
                            </div>
                            @if($comment->user && $comment->user->ngo_profile)
                                <em>{{ $comment->user->ngo_profile->title }}</em>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>
</div>
