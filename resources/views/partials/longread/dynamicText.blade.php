
@if($block->value->position !== 'full')
    <div class="col-md-6">
@endif
    <div class="block_table">
        <div class="list_reviews_profile">
            @if(!empty($block->data['data']->title))
            <div class="title-container">
                <h3>{{ $block->data['data']->title }}</h3>

            </div>
            @endif
            @if(!empty($block->value->show_link))
            <div class="title">
                <a target="_blank" href="{{ route('page.tenders') }}?edrpou={{ $edrpou }}">{{ t('profile.reviews') }}</a>
            </div>
            <br>
            @endif

            @if(!empty($block->data['results']))
                <div @if(count($block->data['results']) > 1)class="slick-dynamic-text" style="display: none;"@endif>

                @foreach($block->data['results'] as $fields)
                    <?php $iter = 0; ?>
                    <div>
                        <div class="title-container" style="margin-bottom: 15px;margin-left: 0;">
                        @if(!empty($fields->tender->reaction))
                            <div class="label_review_profile ">
                                <span class="image-holder">
                                    <img src="/assets/images/user_w.png">
                                </span>

                                <span class="name">{{ t('tender.reaction_yes') }}</span>
                            </div>
                        @endif
                        @if(!empty($fields->tender->f201_count))
                            <div class="label_review_profile ">
                                <span class="image-holder">
                                    <img src="/assets/images/GO_mark.png">
                                </span>
                                <span class="name">{{ t('tender.f201_yes') }}</span>
                            </div>
                        @endif
                        </div>

                        @if(!empty($fields->tender) && is_object($fields->tender) && $fields->tender->schema == 'F101')
                            <div class="label_review_profile ">
                                <div class="reviews__stars">
                                    <span class="name">{{t('reviews.conditions_of_purchase')}}:</span>
                                    <ul class="tender-stars tender-stars--{{ $fields->tender->json->overallScore }}">
                                        <li></li><li></li><li></li><li></li><li></li>
                                    </ul>
                                </div>
                            </div>
                        @endif

                        @foreach($fields as $field => $text)
                            @if($field != 'tender')
                                @if($iter == 0)
                                    <h4 class="field-{{ $field }}">
                                        @if(strpos($text, 'tender/') === 0)
                                            <a target="_blank" href="{!! route('page.tender_by_id', ['id' => explode('/', $text)[1]]) !!}">{!! explode('/', $text)[1] !!}</a>
                                        @else
                                            {!! $text !!}
                                        @endif
                                    </h4>
                                @elseif($iter == 1)
                                    <div class="field-{{ $field }}">
                                        @if(strpos($text, 'tender/') === 0)
                                            <a target="_blank" href="{!! route('page.tender_by_id', ['id' => explode('/', $text)[1]]) !!}">{!! explode('/', $text)[1] !!}</a>
                                        @else
                                            {!! $text !!}
                                        @endif
                                    </div>
                                @elseif($iter == 2)
                                    <div class=" short_desc field-{{ $field }}">
                                        @if(strpos($text, 'tender/') === 0)
                                            <a target="_blank" href="{!! route('page.tender_by_id', ['id' => explode('/', $text)[1]]) !!}">{!! explode('/', $text)[1] !!}</a>
                                        @else
                                            {!! $text !!}
                                        @endif
                                    </div>
                                @else
                                    <div class="field-{{ $field }}">
                                        @if(strpos($text, 'tender/') === 0)
                                            <a target="_blank" href="{!! route('page.tender_by_id', ['id' => explode('/', $text)[1]]) !!}">{!! explode('/', $text)[1] !!}</a>
                                        @else
                                            {!! $text !!}
                                        @endif
                                    </div>
                                @endif
                                <?php $iter++; ?>
                            @endif
                        @endforeach
                    </div>
                @endforeach
                </div>
            @endif

    	</div>
    </div>
@if($block->value->position !== 'full')
    </div>
@endif

@push('scripts')
<script>
    $(document).ready(function(){
        $('.slick-dynamic-text').slick({
            dots: false,
            speed: 500,
            adaptiveHeight: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: true,
			dots: true,
        });
        $('.slick-dynamic-text').find('.slick-next.slick-arrow').text('{{ t('profile.dynamic_text.next') }}');
        $('.slick-dynamic-text').find('.slick-prev.slick-arrow').text('{{ t('profile.dynamic_text.prev') }}');
        $('.slick-dynamic-text').show();
    });
</script>
@endpush
