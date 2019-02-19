@if (!empty($item->__complaints_complaints))
    <div class="container margin-bottom-xl margin-bottom-more" id="block_complaints{{ $lot_id  ? '_lot' : '' }}">

            {{--@if($item->procurementMethodType=='belowThreshold')
                <h2>{{ t('tender.appeal_review_commission') }}</h2>
            @else
                <h2>{{$title}}</h2>
            @endif --}}

            <div class="block_title">
                <h3>Скарги до процедури</h3>
            </div>
            
            <div class="row questions">
                <div class="description-wr questions-block">
                    @foreach(array_values($item->__complaints_complaints) as $k=>$complaint)
                        <div class="questions-row{{--$k>1?' none':' visible'--}}" style="margin-bottom:45px">
                            <h4>{{ t('tender.number_of_complaints') }}: {{!empty($complaint->complaintID)?$complaint->complaintID:$complaint->id}}</h4>
                            <h4>{{ t('tender.status') }}: <div class="marked">{{$complaint->__status_name}}</div></h4>

                            <div class="list_date inline-layout">
                                <div class="item_date">
                                    @if (!empty($complaint->author->identifier->id))
                                        {{ t('tender.complainant') }}: {{!empty($complaint->author->identifier->legalName) ? $complaint->author->identifier->legalName : $complaint->author->name}}, Код ЄДРПОУ:{{$complaint->author->identifier->id}}
                                    @endif
                                    @if(!empty($profileAccess) || !empty($user->is_profile_links))
                                        <br><a target="_blank" class="profile-role2" href="{{ route('page.profile_by_id', ['scheme'=>$complaint->author->identifier->scheme.'-'.$complaint->author->identifier->id,'tpl'=>$profileRole2TplId,'role'=>'role2']) }}">{{ t('dozorro_profile') }}</a>
                                    @endif
                                </div>
                                <div class="item_date">
                                    @if($item->procurementMethodType!='belowThreshold')
                                        {{ t('tender.filing_date') }}: {{!empty($complaint->dateSubmitted) ? date('d.m.Y H:i', strtotime($complaint->dateSubmitted)) : 'відсутня'}}
                                    @else
                                        {{ t('tender.filing_date') }}: {{!empty($complaint->dateEscalated) ? date('d.m.Y H:i', strtotime($complaint->dateEscalated)) : 'відсутня'}}
                                    @endif
                                </div>
                            </div>

                            
                            <div class="margin-bottom" >
                                <div class="complaints-info inline-layout">
                                    <div class="complaints-desc">
                                        <h4>{{$complaint->title}}</h4>

                                        @if (!empty($complaint->description))
                                            <div class="description-wr{{mb_strlen($complaint->description)>350?' croped':' open'}}">
                                                <div class="description">
                                                    {!!$complaint->description!!}
                                                </div>
                                                @if (mb_strlen($complaint->description)>350)
                                                    <a class="search-form--open">
                                                        <span>{{t('interface.expand')}}</span>
                                                        <span>{{t('interface.collapse')}}</span>
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>


                                    @if(!empty($complaint->__documents_owner))
                                        <a href="" class="document-link" data-id="{{$complaint->id}}-owner-complaint">
                                            <img src="/assets/images/doc-tender.svg" alt="{{t('tender.bids_documents')}}"/>
                                        </a>
                                    @endif
                                </div>

                                
                                @if(!empty($complaint->dateAnswered))
                                    <h4>
                                        @if($complaint->resolutionType=='invalid')
                                            {{ t('tender.decision_customer') }}: {{ t('tender.requirement_left_without_consideration') }}
                                        @elseif($complaint->resolutionType=='resolved')
                                            {{ t('tender.decision_customer') }}: {{ t('tender.requirement_is_satisfied') }}
                                        @elseif($complaint->resolutionType=='declined')
                                            {{ t('tender.decision_customer') }}: {{ t('tender.requirement_is_not_satisfied') }}
                                        @endif
                                    </h4>
                                    <div class="list_date inline-layout">
                                        <div class="item_date">{{ t('tender.date') }}: {{date('d.m.Y H:i', strtotime($complaint->dateAnswered))}}</div>
                                    </div>

                                    <div>{!!nl2br($complaint->resolution)!!}</div>
                                @endif

                                @if(property_exists($complaint, 'satisfied'))
                                    <div style="margin-top:20px">
                                        @if($complaint->satisfied)
                                            <strong>{{ t('tender.evaluation_complainant') }}: {{ t('tender.decision_customer_satisfied') }}</strong>
                                        @else
                                            <strong>{{ t('tender.evaluation_complainant') }}: {{ t('tender.decision_customer_is_not_satisfied') }}</strong>
                                            @if(!empty($complaint->dateEscalated))
                                                <div class="grey-light size12 question-date">{{ t('tender.date_request_commission_review_applications') }}: {{date('d.m.Y H:i', strtotime($complaint->dateEscalated))}}</div>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="margin-bottom">
                                <h4>

                                        @if($complaint->status=='mistaken')
                                            {{ t('tender.appeal_body_decisions') }}: {{ t('tender.returned_as_directed_by_mistake') }}
                                        @elseif($complaint->status=='stopped')
                                            {{ t('tender.appeal_body_decisions') }}: {{ t('tender.consideration_suspended') }}
                                        @elseif($complaint->status=='stopping')
                                            @if(!empty($complaint->dateAccepted))
                                                <div>{{ t('tender.appeal_body_decisions') }} {{ t('tender.expected') }}</div>
                                            @endif
                                                {{ t('tender.retracted_complainant') }}
                                        @elseif($complaint->status=='pre_stopping')
                                            {{ t('tender.appeal_body_decisions') }}:
                                        @elseif($complaint->status=='cancelled')
                                            {{ t('tender.canceled') }}
                                        @else
                                            @if($item->procurementMethodType=='belowThreshold')
                                                {{ t('tender.commission_decision') }}:
                                            @else
                                                {{ t('tender.appeal_body_decisions') }}:
                                            @endif
                                            @if(in_array($complaint->status, ['pending', 'stopping']))
                                                    {{ t('tender.expected') }}
                                            @elseif($complaint->status=='stopped')
                                                    {{ t('tender.consideration_suspended') }}
                                            @elseif($complaint->status=='invalid')
                                                    {{ t('tender.left_without_consideration') }}
                                            @elseif($complaint->status=='satisfied')
                                                    {{ t('tender.satisfied') }}
                                            @elseif($complaint->status=='declined')
                                                    {{ t('tender.not_satisfied') }}
                                            @endif
                                            @if($item->procurementMethodType!='belowThreshold')
                                                @if($complaint->status=='resolved')
                                                        {{ t('tender.satisfied') }}
                                                @endif
                                            @endif
                                        @endif

                                </h4>
                                <div class="list_date inline-layout">
                                    @if(!empty($complaint->dateAccepted))
                                        <div class="item_date">{{ t('tender.it_is_for_consideration') }}: {{date('d.m.Y H:i', strtotime($complaint->dateAccepted))}}</div>
                                    @endif
                                    @if(in_array($complaint->status, ['declined', 'satisfied']))
                                        @if(!empty($complaint->dateDecision))
                                            <div class="item_date" style="margin-top: -10px;">{{ t('tender.date_of_decision') }}: {{date('d.m.Y H:i', strtotime($complaint->dateDecision))}}</div>
                                        @endif
                                    @endif
                                </div>
                                @if(in_array($complaint->status, ['cancelled', 'stopping', 'stopped', 'invalid']))
                                    @if(!empty($complaint->dateCanceled))
                                        @if(in_array($complaint->status, ['invalid']))
                                            <div><strong>{{ t('tender.retracted_complainant') }}</strong></div>
                                        @endif
                                        @if(!empty($complaint->cancellationReason))
                                            Причина: {{$complaint->cancellationReason}}
                                        @endif
                                        <div class="grey-light size12 question-date">{{ t('tender.date') }}: {{date('d.m.Y H:i', strtotime($complaint->dateCanceled))}}</div>
                                    @endif
                                @endif
                            </div>

                            @if(!empty($complaint->__documents_reviewer))
                                <div class="list_documents_reviewer">
                                    @foreach($complaint->__documents_reviewer as $document)
                                        <div class="item inline-layout">
                                            <div class="name_doc">
                                                <a href="{{$document->url}}" target="_blank">{{$document->title}}</a>
                                            </div>
                                            <div class="date"><!--{{ t('tender.date_of_publication') }}: -->{{date('d.m.Y H:i', strtotime($document->datePublished))}}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    @endforeach
                    {{--
                    @if (sizeof($item->__complaints_complaints)>2)
                        <a class="question--open"><i class="sprite-arrow-down"></i>
                            <span class="question-up">{{trans('tender.expand_complaints')}}: {{sizeof($item->__complaints_complaints)}}</span>
                            <span class="question-down">{{trans('tender.collapse_complaints')}}</span>
                        </a>
                    @endif
                    --}}
                </div>
            </div>
            @if(!empty($item->__complaints_complaints))
                <div class="overlay overlay-documents">
                    <div class="overlay-close overlay-close-layout"></div>
                    <div class="overlay-box">
                        @foreach($item->__complaints_complaints as $complaint)
                            @if(!empty($complaint->__documents_owner))
                                <div class="tender--offers documents" data-id="{{$complaint->id}}-owner-complaint">
                                    <h4 class="overlay-title">
                                        {{ t('tender.documents_submitted_by_ complainant') }}
                                    </h4>
                                    @foreach($complaint->__documents_owner as $document)
                                        <div class="document-info">
                                            <div class="document-date">{{date('d.m.Y H:i', strtotime($document->datePublished))}}</div>
                                            <a href="{{$document->url}}" target="_blank" class="document-name">{{$document->title}}</a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if(!empty($complaint->__documents_reviewer))
                                <div class="tender--offers documents" data-id="{{$complaint->id}}-reviewer-complaint">
                                    <h4 class="overlay-title">
                                        {{ t('tender.documents_of_appeal') }}
                                    </h4>
                                    @foreach($complaint->__documents_reviewer as $document)
                                        <div class="document-info">
                                            <div class="document-date">{{date('d.m.Y H:i', strtotime($document->datePublished))}}</div>
                                            <a href="{{$document->url}}" target="_blank" class="document-name">{{$document->title}}</a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                        <div class="overlay-close"><i class="sprite-close-grey"></i></div>
                    </div>
                </div>
            @endif

    </div>
@endif