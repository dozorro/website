@if(!@$isCustomer && $review->issetComment)
    <div class="icon-table-ngo">
        <span data-form="{{$review->schema.'-'.$modal_id}}" class="comment_number" data-formjs="ngo_open_modal"></span>
        <?php $comment_file = $review->link_in_comment(); ?>
        @if(is_array($comment_file))
            <a target="_blank" href="{{str_replace('{DOWNLOAD_URL}', config('services.localstorage.url'), $comment_file['file_link'])}}" class="attachment" title="{{$comment_file['file_name']}}"></a>
        @endif
    </div>

    <div data-form-modal="{{$review->schema.'-'.$modal_id}}" class="none">
        <div id="overlay" class="overlay2" data-form-modal="{{$review->schema.'-'.$modal_id}}"></div>
        <div class="modal_div show welcome-modal" data-form-modal="{{$review->schema.'-'.$modal_id}}" style="overflow: scroll;">
            <div class="modal_close"></div>
            <div class="content-holder">
                <h3>{{t('ngo.form.modal_window.title')}}</h3>
                <div class="desc-modal">
                    {{t('ngo.form.modal_window.description')}}
                </div>

                <div style="text-align: left;">
                    <h4>{{t('ngo.form.modal_window.f201_block')}}</h4>

                    <p>
                        @if($review->schema == 'F201')
                        <b>{!! !empty($review->json->overallScoreComment) ? $review->json->generalName : $review->json->abuseName !!} (201)</b>
                        <br>
                        {!! !empty($review->json->overallScoreComment) ? $review->json->overallScoreComment : $review->json->abuseComment !!}
                        @elseif($review->schema == 'F202')
                        <p>
                            <b>{{ $review->json->actionName }} (202)</b>
                            <br>
                            {!! $review->showComment() !!}
                            <div>
                            @if($review->closed && $review->f204)
                                @if($review->f204->json->reason == 'succes')
                                    <span class="label_status victory">{{t('tender.ngo.status_success')}}</span>
                                @elseif($review->f204->json->reason == 'defeat')
                                    <span class="label_status betrayal">{{t('tender.ngo.status_defeat')}}</span>
                                @elseif($review->f204->json->reason == 'cancel')
                                    <span class="label_status give_up">{{t('tender.ngo.status_cancel')}}</span>
                                @endif
                            @endif
                            </div>
                        </p>
                        @elseif($review->schema == 'F203')
                        <p>
                            <b>{{ $review->json->resultName }} (203)</b>
                            <br>
                            {!! $review->showComment() !!}
                        </p>
                        @endif
                    </p>

                </div>
                <br><br>
            </div>
        </div>
    </div>
@elseif(@$isCustomer)
    <div data-form-modal="{{$modal_id}}" class="none">
        <div id="overlay" class="overlay2" data-form-modal="{{$modal_id}}"></div>
        <div class="modal_div show welcome-modal" data-form-modal="{{$modal_id}}" style="overflow: scroll;">
            <div class="modal_close"></div>
            <div class="content-holder">
                <h3>{{t('ngo.form.modal_window.title')}}</h3>
                <div class="desc-modal">
                    {{t('ngo.form.modal_window.description')}}
                </div>
                <div style="text-align: left;">
                    <h4>{{t('ngo.form.modal_window.comments_block')}}</h4>
                    <p>
                        @foreach($comments as $review)
                        <p>
                            {!! $review->json->comment !!}
                        </p>
                        @endforeach
                    </p>
                </div>
                <br><br>
            </div>
        </div>
    </div>
@endif
