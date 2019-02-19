@extends('layouts/app')

@section('content')
    <div class="tender">
        <div class="tender-header-wrap">
            @if(!$multy)
                @include('partials/blocks/tender/header', ['tender_page' => true])
            @endif
    @if ($user->ngo)
        <div class="@if($form != "F204"){{'block_form_register_defied'}}@else{{'block_form_finish_work'}}@endif bg_grey add-ngo-review-form" id="ngo_review_form" data-formjs="submit_ngo_action" data-schema="{{ $form }}" data-return-back="{{ $return_back }}"
             @if(!$multy)
             data-tender-id="{{ $item->id }}" data-tender-public-id="{{ $item->tenderID }}"
             @else
             data-tender-public-id="{{ $tender_ids }}" data-multy
             @endif

             data-selected="{{ @$selected }}"
        >
            <div class="container">
                <div class="row">
                    <div class="col-md-12">

                        <h3>@include('pages/forms/'.$form.'_title')</h3>

                <div class="form-add-ngo" form-container>
                    <form action="/jsonforms" novalidate="true" class="@if($form != "F204"){{'form_register_defied'}}@else{{'form_finish_work'}}@endif">

                        @if(isset($parents) && !empty($parents))
                            @foreach($parents AS $id)
                                <input class="input-parents" value="{{$id}}" type="hidden">
                            @endforeach
                        @endif

                        @if(!empty($edit))
                            <input class="input-edit" value="{{$edit}}" type="hidden">
                        @endif

                        @if($form != 'F204')
                            @foreach($ngo_form_data as $form_code=>$form_item)
                                <div class="ngo-form-accordeon ngo-form-{{ $form_code }}">
                                    @foreach($form_item as $_code=>$_item)
                                        <div class="form-holder control-group">
                                            <div class="form-checkbox inline-layout">
                                                <div class="checkbox">
                                                    @if(empty($edit))
                                                    <input type="checkbox" id="checkbox{{$_code}}">
                                                    @else
                                                    <input type="radio" name="form_code" id="checkbox{{$_code}}">
                                                    @endif
                                                    <label for="checkbox{{$_code}}">{{ $_item }}</label>
                                                </div>

                                                <div class="open_form_reviews js_open_form_reviews">
                                                    {{ t('tender.ngo.'.$form.'_add_comment') }}
                                                </div>
                                            </div>
                                            <div class="form-reviews js_form_reviews">
                                                @if($form != 'F201')
                                                    <div class="form-holder main_form_reviews">
                                                        <div class="row">
                                                            <div class="form_input col-md-4">
                                                                <label>{{t('tender.ngo.form.file_name')}}</label>
                                                                <input name="file_name" type="text">
                                                            </div>
                                                            <div class="form_input col-md-4">
                                                                <label>{{t('tender.ngo.form.file_link')}}</label>
                                                                @if (config('filesystems.disks.localstorage.root'))
                                                                    <input name="file_link" type="hidden">
                                                                    <div class="dropzone"></div>
                                                                    <div>{{t('tender.ngo.form.max_comment_10mb')}}</div>
                                                                @else
                                                                    <input name="file_link" type="text">
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <textarea name="{{$_code}}"></textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                            <div class="form-holder main_form_reviews other_main_form_reviews">
                                @if($form != 'F201')
                                <div class="row">
                                    <div class="form_input col-md-4">
                                        <label>{{t('tender.ngo.form.file_name')}}</label>
                                        <input name="file_name" type="text">
                                    </div>
                                    <div class="form_input col-md-4">
                                        <label>{{t('tender.ngo.form.file_link')}}</label>
                                        @if (config('filesystems.disks.localstorage.root'))
                                            <input name="file_link" type="hidden">
                                            <div class="dropzone"></div>
                                            <div>{{t('tender.ngo.form.max_comment_10mb')}}</div>
                                        @else
                                            <input name="file_link" type="text">
                                        @endif
                                    </div>
                                </div>
                                @endif
                                <div class="form_textarea">
                                    <label id="label">{{t('tender.ngo.form.comment_for_all')}}</label>
                                    <textarea for="label">@if(!empty($edit)){{ $comment }}@endif</textarea>
                                </div>
                                <div class="inline-layout" id="for-spinner1">
                                    <button type="submit" submit-action>{{ t('tender.submit_your_ngo_review') }}</button>
                                    @if(empty($edit))
                                    <a href="{{ $return_back }}" class="link_back">{{t('tender.ngo.form.cancel')}}</a>
                                    @endif
                                </div>
                            </div>
                        @elseif($form == 'F204')
                            <div class="control-group">
                                <textarea name="reasonComment" class="none"></textarea>
                                <p>{{t('tender.ngo.form.result')}}</p>
                                <div class="form-radio">
                                    <input type="radio" value="defeat" name="reason" id="radio1" checked class="status-input">
                                    <label for="radio1" class="inline-layout">
                                        <span class="label_status betrayal">{{t('tender.ngo.form.defeat')}}</span>
                                        <span>{{t('tender.ngo.form.bad')}}</span>
                                    </label>
                                </div>
                                <div class="form-radio">
                                    <input type="radio" value="succes" name="reason" id="radio2" class="status-input">
                                    <label for="radio2" class="inline-layout">
                                        <span class="label_status victory">{{t('tender.ngo.form.success')}}</span>
                                        <span>{{t('tender.ngo.form.good')}}</span>
                                    </label>
                                </div>
                                <div class="form-radio">
                                    <input type="radio" value="cancel" name="reason" id="radio3" class="status-input">
                                    <label for="radio3" class="inline-layout">
                                        <span class="label_status give_up">{{t('tender.ngo.form.cancel')}}</span>
                                        <span>{{t('tender.ngo.form.not_good')}}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="block_button inline-layout" id="for-spinner1">
                                <button type="submit" submit-action>{{ t('tender.submit_your_ngo_review') }}</button>
                                @if(empty($edit))
                                <a href="{{ $return_back }}" class="link_back">{{t('tender.ngo.form.cancel')}}</a>
                                @endif
                            </div>
                        @endif
                    </form>
                </div>
                <br>
                <div class="thanks" hidden>
                    {{t('tender.thanks.hidden')}}
                </div>
                <div class="success" hidden form-success>
                    {{t('tender.thanks.success')}}
                </div>
                <div class="error" hidden form-error>
                    {{t('tender.thanks.error')}}
                </div>
            </div>
        </div>
            </div>
        </div>
    @endif
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function(){
            var opts = {
                lines: 13, length: 28, width: 4, radius: 3, scale: 1, corners: 1, color: '#000', opacity: 0.25, rotate: 0, direction: 1, speed: 1, trail: 60, fps: 20, zIndex: 2e9, className: 'spinner', top: '50%', left: '40%', shadow: false, hwaccel: false, position: 'relative'
            }

            var target = document.getElementById('for-spinner1')
            var spinner = new Spinner(opts).spin(target);

            $('.spinner').hide();
        });
    </script>
@endpush
