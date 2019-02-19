@if(count($f200_reviews))
    <div class="@if(count($f200_reviews)){{'is-show'}}@else{{'none'}}@endif" tab-content id="ngo-tab">
        @foreach ($f200_reviews as $ngo_id => $data)
            <?php $accessToNgo = !empty($user->ngo) && $user->ngo->id == $ngo_id; ?>
            <?php $accessToForm = !empty($accessToNgo) || !empty($user->superadmin); ?>
            @if(array_first($data['data'], function($dk, $dv) { return $dv->schema == 'F201'; }))
                <div class="container">
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="tender-tabs__wrap">
                                <div class="ngo_title_status inline-layout clearfix">
                                    <span class="name_company">{{t('tender.ngo.position_text')}} <a href="{{route('page.ngo', ['slug' => $data['ngo']->slug])}}">{{$data['ngo']->title}}</a></span>

                                    @if($user && $user->user->issetEdrpou($item->procuringEntity->identifier->id))
                                        <div style="padding: 0 10px;">
                                            <div data-thread="{{ $data['last_f201']->object_id }}" form-comment style="float:right">
                                                <a href=""
                                                   class="open-comment__button form_ngo_link"
                                                   data-formjs="jsonForm"
                                                   data-form="comment"
                                                   data-form-title="{{t('tender.ngo.your_comment')}}"
                                                   data-submit-button="{{t('tender.ngo.add_comment')}}"
                                                   data-model="comment"
                                                   data-validate="comment"
                                                   data-init="comment">
                                                    {{ t('tender.ngo.customer_reaction') }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    @if(isset($data['f204']) && $data['f204'])
                                        @if($data['f204']->json->reason == 'succes')
                                            <span class="label_status victory">{{t('tender.ngo.status_success')}}</span>
                                        @elseif($data['f204']->json->reason == 'defeat')
                                            <span class="label_status betrayal">{{t('tender.ngo.status_defeat')}}</span>
                                        @elseif($data['f204']->json->reason == 'cancel')
                                            <span class="label_status give_up">{{t('tender.ngo.status_cancel')}}</span>
                                        @endif
                                    @endif
                                    <div style="float: right;" class="likely" data-title="{{ $data['ngo']->title }}" data-url="{{ route('page.tender_by_id', ['id' => $item->tenderID]) }}">
                                        <div class="facebook">Share</div>
                                        <div class="twitter">Tweet</div>
                                    </div>
                                    @if(!$data['ngo']->badges->isEmpty())
                                        @include('partials/badges', ['badges' => $data['ngo']->badges, 'featureBadges' => false])
                                    @endif
                                </div>
                                <form class="form_ngo" method="get" action="">
                                <input type="hidden" class="hidden-input">
                                <div class="overflow-table">
                                    <table>
                                        <tr>
                                            <th width="40%">{{t('tender.ngo.violations')}}</th>
                                            <th>{{t('tender.ngo.inform')}}</th>
                                            <th>{{t('tender.ngo.answers')}}</th>
                                        </tr>

                                        @foreach ($data['data'] as $k => $review_f201)
                                            @if($review_f201->schema=='F201' && (!empty($review_f201->json->abuseCode) || !empty($review_f201->json->abuseName)))

                                                <?php
                                                $schemas=[];
                                                $rowspan = 0;

                                                $review_f201->moderation = $review_f201->status == 0;

                                                foreach($data['data'] as $review)
                                                {
                                                    if($review->schema=='F202' && $review_f201->object_id==$review->jsonParentForm) {
                                                        $schemas[$review->schema][]=$review;
                                                    }
                                                }

                                                if(isset($schemas['F202'])) {
                                                    $rowspan = count($schemas['F202']);
                                                }

                                                if(isset($schemas['F202'])) {
                                                    foreach($data['data'] as $review)
                                                    {
                                                        if($review->schema=='F203') {
                                                            foreach($schemas['F202'] AS $k => $r) {
                                                                if($r->object_id==$review->jsonParentForm) {
                                                                    $schemas['F202-203'][$r->id][] = $review;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }

                                                foreach($data['data'] as $review)
                                                {
                                                    if($review->schema=='F203' && $review_f201->object_id==$review->jsonParentForm) {
                                                        $schemas['F203'][] = $review;
                                                    }
                                                }

                                                if(isset($schemas['F203'])) {
                                                    $rowspan++;
                                                }

                                                if(!empty($schemas['F202'])) {
                                                    $verified = array_where($schemas['F202'], function($fk, $fv) {
                                                       return $fv->status == 1;
                                                    });

                                                    if(empty($verified) && (empty($user->superadmin) && !$accessToNgo)) {
                                                        continue;
                                                    } elseif(empty($verified) && (!empty($user->superadmin) || $accessToNgo)) {
                                                        $review_f201->closed = true;
                                                        $review_f201->moderation = true;
                                                    }
                                                }

                                                ?>

                                                <tr>
                                                    <td @if($rowspan > 0) rowspan="{{ $rowspan }} @endif">
                                                        <div class="inline-layout">
                                                            @if($accessToNgo && !$review_f201->closed && $review_f201->status == 1)
                                                                <div class="checkbox @if($review_f201->issetComment){{'table-ngo-text'}}@endif">
                                                                    <input id="f201-{{$ngo_id.$review_f201->id}}" data-schema="F201" data-min-schema="202" type="checkbox" value="{{$review_f201->object_id}}" name="parents[]">
                                                                    <label for="f201-{{$ngo_id.$review_f201->id}}">
                                                                        {{ $review_f201->json->abuseName }}
                                                                    </label>
                                                                </div>
                                                            @else
                                                                <div class="@if($review_f201->issetComment){{'table-ngo-text'}}@endif">
                                                                    {{ $review_f201->json->abuseName }}
                                                                </div>
                                                                @if($review_f201->status != 1 && $accessToForm)
                                                                    <br><strong>{{ $review_f201->statusName }}</strong>
                                                                    <br><span>{{ $review_f201->admin_comment }}</span>
                                                                @elseif(!empty($review_f201->moderation))
                                                                    <br><strong>{{ t('ngo.form.status.moderation') }}</strong>
                                                                @endif
                                                            @endif
                                                            @include('partials/blocks/tender/modal', ['review' => $review_f201, 'modal_id' => $ngo_id.$review_f201->id])
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if(isset($schemas['F202'][0]))
                                                            <div class="inline-layout">
                                                                @if($accessToNgo && !$schemas['F202'][0]->closed)
                                                                    <div class="checkbox @if($schemas['F202'][0]->issetComment){{'table-ngo-text'}}@endif">
                                                                        @if($schemas['F202'][0]->status == 1)
                                                                        <input id="f202-{{$ngo_id.$review_f201->id.$schemas['F202'][0]->id}}" data-schema="F202" data-min-schema="203" type="checkbox" value="{{$schemas['F202'][0]->object_id}}" name="parents[]">
                                                                        @endif
                                                                        <label for="f202-{{$ngo_id.$review_f201->id.$schemas['F202'][0]->id}}">
                                                                            {{ $schemas['F202'][0]->json->actionName }}
                                                                        </label>
                                                                        <br>
                                                                        {{ $schemas['F202'][0]->date->format('d.m.Y') }}

                                                                        @if($schemas['F202'][0]->status != 1)
                                                                            <br><strong>{{ $schemas['F202'][0]->statusName }}</strong>
                                                                            <br><span>{{ $schemas['F202'][0]->admin_comment }}</span>
                                                                        @endif
                                                                    </div>
                                                                    @include('partials/blocks/tender/modal', ['review' => $schemas['F202'][0], 'modal_id' => $ngo_id.$review_f201->id.$schemas['F202'][0]->id])
                                                                @elseif($schemas['F202'][0]->status == 1 || $accessToForm)
                                                                    <div class="@if($schemas['F202'][0]->issetComment){{'table-ngo-text'}}@endif">
                                                                        {{ $schemas['F202'][0]->json->actionName }}
                                                                        <br>
                                                                        {{ $schemas['F202'][0]->date->format('d.m.Y') }}

                                                                        @if($schemas['F202'][0]->status != 1 && $accessToForm)
                                                                            <br><strong>{{ $schemas['F202'][0]->statusName }}</strong>
                                                                            <br><span>{{ $schemas['F202'][0]->admin_comment }}</span>
                                                                        @endif
                                                                    </div>
                                                                    @include('partials/blocks/tender/modal', ['review' => $schemas['F202'][0], 'modal_id' => $ngo_id.$review_f201->id.$schemas['F202'][0]->id])
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($schemas['F202'][0]))
                                                            @if(isset($schemas['F202-203'][$schemas['F202'][0]->id]))
                                                                @foreach($schemas['F202-203'][$schemas['F202'][0]->id] AS $schema)
                                                                    @if($schema->status == 1 || $accessToForm)
                                                                    <div>
                                                                        - {{ $schema->json->resultName }}

                                                                        @if($schema->status != 1 && $accessToForm)
                                                                            <br><strong>{{ $schema->statusName }}</strong>
                                                                            <br><span>{{ $schema->admin_comment }}</span>
                                                                        @endif

                                                                        @include('partials/blocks/tender/modal', ['review' => $schema, 'modal_id' => $ngo_id.$review_f201->id.$schemas['F202'][0]->id.$schema->id])
                                                                    </div>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>

                                                @if($rowspan)
                                                    @for($i = 1; $i < $rowspan; $i++)
                                                        @if(isset($schemas['F202'][$i]))
                                                            <tr>
                                                                <td>
                                                                    @if(isset($schemas['F202'][$i]) && $schemas['F202'][$i]->json->actionName)
                                                                        <div class="inline-layout">
                                                                            @if($accessToNgo && !$schemas['F202'][$i]->closed)
                                                                                <div class="checkbox @if($schemas['F202'][$i]->issetComment){{'table-ngo-text'}}@endif">
                                                                                    @if($schemas['F202'][$i]->status == 1)
                                                                                    <input id="f202-{{$ngo_id.$review_f201->id.$schemas['F202'][$i]->id}}" data-schema="F202" data-min-schema="203" type="checkbox" value="{{$schemas['F202'][$i]->object_id}}" name="parents[]">
                                                                                    @endif
                                                                                    <label for="f202-{{$ngo_id.$review_f201->id.$schemas['F202'][$i]->id}}">
                                                                                        {{ $schemas['F202'][$i]->json->actionName }}
                                                                                    </label>
                                                                                    <br>
                                                                                    {{ $schemas['F202'][$i]->date->format('d.m.Y') }}

                                                                                    @if($schemas['F202'][$i]->status != 1 && $accessToForm)
                                                                                        <br><strong>{{ $schemas['F202'][$i]->statusName }}</strong>
                                                                                        <br><span>{{ $schemas['F202'][$i]->admin_comment }}</span>
                                                                                    @endif
                                                                                </div>
                                                                                @include('partials/blocks/tender/modal', ['review' => $schemas['F202'][$i], 'modal_id' => $ngo_id.$review_f201->id.$schemas['F202'][$i]->id])
                                                                            @elseif($schemas['F202'][$i]->status == 1 || $accessToForm)
                                                                                <div class="@if($schemas['F202'][$i]->issetComment){{'table-ngo-text'}}@endif">
                                                                                    {{ $schemas['F202'][$i]->json->actionName }}
                                                                                    <br>
                                                                                    {{ $schemas['F202'][$i]->date->format('d.m.Y') }}

                                                                                    @if($schemas['F202'][$i]->status != 1 && $accessToForm)
                                                                                        <br><strong>{{ $schemas['F202'][$i]->statusName }}</strong>
                                                                                        <br><span>{{ $schemas['F202'][$i]->admin_comment }}</span>
                                                                                    @endif
                                                                                </div>
                                                                                @include('partials/blocks/tender/modal', ['review' => $schemas['F202'][$i], 'modal_id' => $ngo_id.$review_f201->id.$schemas['F202'][$i]->id])
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                                @if(isset($schemas['F202-203'][$schemas['F202'][$i]->id]))
                                                                    <td>
                                                                        @foreach($schemas['F202-203'][$schemas['F202'][$i]->id] AS $schema)
                                                                            @if($schema->status == 1 || $accessToForm)
                                                                            <div>
                                                                                - {{ $schema->json->resultName }}

                                                                                @if($schema->status != 1 && $accessToForm)
                                                                                    <br><strong>{{ $schema->statusName }}</strong>
                                                                                    <br><span>{{ $schema->admin_comment }}</span>
                                                                                @endif

                                                                                @include('partials/blocks/tender/modal', ['review' => $schema, 'modal_id' => $ngo_id.$review_f201->id.$schemas['F202'][$i]->id.$schema->id])
                                                                            </div>
                                                                            @endif
                                                                        @endforeach
                                                                    </td>
                                                                @else
                                                                    <td></td>
                                                                @endif
                                                            </tr>
                                                        @endif
                                                    @endfor
                                                @endif
                                                @if(isset($schemas['F203']))
                                                    <tr>
                                                        @if($rowspan <= 1)
                                                            <td>
                                                            </td>
                                                        @endif
                                                        <td>
                                                        </td>
                                                        <td>
                                                            @foreach($schemas['F203'] AS $schema)
                                                                @if($schema->status == 1 || $accessToForm)
                                                                <div>
                                                                    - {{ $schema->json->resultName }}

                                                                    @if($schema->status != 1 && $accessToForm)
                                                                        <br><strong>{{ $schema->statusName }}</strong>
                                                                        <br><span>{{ $schema->admin_comment }}</span>
                                                                    @endif

                                                                    @include('partials/blocks/tender/modal', ['review' => $schema, 'modal_id' => $ngo_id.$review_f201->id.$schema->id])
                                                                </div>
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endif
                                        @endforeach

                                        @if ($accessToNgo)
                                            @if(!$data['close_all'])
                                                <tr>
                                                    <td><a class="form_ngo_link @if(in_array($last_form, ['F202','F203','F204'])){{'grey_link'}}@endif" data-formjs="tender_open_form" data-schema="202" data-href="{{ route('page.tender_form', ['id' => $item->tenderID, 'form' => 'F202']) }}">{{t('tender.ngo.inform_violence')}}</a></td>
                                                    <td><a class="form_ngo_link @if(in_array($last_form, ['F201','F203'])){{'grey_link'}}@endif" data-formjs="tender_open_form" data-schema="203" data-href="{{ route('page.tender_form', ['id' => $item->tenderID, 'form' => 'F203']) }}">{{t('tender.ngo.set_answers')}}</a></td>
                                                    <td colspan="2">
                                                        <a class="form_ngo_link @if(in_array($last_form, ['F201','F202','F204'])){{'grey_link'}}@endif" data-formjs="tender_open_form" data-schema="204" data-href="{{ route('page.tender_form', ['id' => $item->tenderID, 'form' => 'F204']) }}">{{t('tender.ngo.the_end')}}</a>
                                                        <div style="display: inline-block;" class="info">
                                                            <span class="info_icon"></span>
                                                            <div class="info_text">
                                                                <div>
                                                                    {{t('tender.ngo.f204_tip')}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endif
                                    </table>

                                </div>
                                @if(!$data['comments']->isEmpty())
                                    <div class="ngo_text_w50" >

                                        <h4>{{t('ngo.form.modal_window.comments_block')}}</h4>
                                        @foreach($data['comments'] as $review)
                                            <p>
                                                {!! $review->json->comment !!}
                                            </p>
                                        @endforeach

                                    </div>
                                @endif
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
        @if($user)
        <div class="container">
            <div class="row">
                <div class="col-sm-9">
                    <div class="tender-tabs__wrap">
                        <form style="display:none;" class="form_ngo" method="post" action="{{ route('ngo.review.save') }}">
                            <div class="ngo_title_status inline-layout clearfix">
                                <span class="name_company">{{t('tender.ngo.reviews_form_title')}}</span>
                            </div>
                            {{ csrf_field() }}
                            <input name="tender_id" type="hidden" value="{{ $item->tenderID }}">
                            @if(count($f200_reviews) > 1)
                            <select name="ngo_profile_id">
                                @foreach ($f200_reviews as $ngo_id => $data)
                                    <option value="{{ $data['ngo']->id }}">{{$data['ngo']->title}}</option>
                                @endforeach
                            </select>
                            @else
                                @foreach ($f200_reviews as $ngo_id => $data)
                                    <input name="ngo_profile_id" type="hidden" value="{{ $data['ngo']->id }}">
                                @endforeach
                            @endif
                            <textarea name="text" style="width:100%;height: 150px;" required></textarea>
                            <button type="submit" class="link_back">{{ t('ngo.submit_review') }}</button>
                            <button type="submit" class="link_back cancel">{{ t('ngo.cancel_submit_review') }}</button>
                        </form>
                        <button type="submit" class="link_back submit">{{ t('ngo.show_feedback_form') }}</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
@endif