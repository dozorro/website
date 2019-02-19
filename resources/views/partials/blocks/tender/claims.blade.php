
@if (!empty($item->__complaints_claims))
    <div class="container margin-bottom-more">

            <div class="block_title">
                <h3>{{t('tender.claims_title')}}</h3>
            </div>

            <div class="row questions">
                <div class="description-wr questions-block">
                    @foreach($item->__complaints_claims as $k=>$complaint)
                        <div class="questions-row{{--$k>1?' none':' visible'--}}" style="margin-bottom:45px">
                            <h4>{{ t('tender.phone_requirements') }}: {{!empty($complaint->complaintID)?$complaint->complaintID:$complaint->id}}</h4>
                            <h4>{{ t('tender.status') }}: <div class="marked">{{$complaint->__status_name}}</div></h4>

                            <div class="list_date inline-layout">
                                @if (!empty($complaint->author->identifier->id))
                                    <div class="item_date">
                                        {{ t('tender.participant') }}: {{!empty($complaint->author->identifier->legalName) ? $complaint->author->identifier->legalName : $complaint->author->name}}, Код ЄДРПОУ:{{$complaint->author->identifier->id}}<br>
                                    </div>
                                @endif

                                @if(!empty($complaint->dateSubmitted))
                                    <div class="item_date">{{ t('tender.filing_date') }}: {{date('d.m.Y H:i', strtotime($complaint->dateSubmitted))}}</div>
                                @endif
                            </div>

                            
                            <!--<div class="margin-bottom margin-top"><strong>{{$complaint->title}}</strong></div>-->
                            
                            @if (!empty($complaint->description))
                                <div class="m-10 description-wr{{mb_strlen($complaint->description)>350?' croped':' open'}}">
                                    <div class="description">
                                        {!!nl2br($complaint->description)!!}
                                    </div>
                                    @if (mb_strlen($complaint->description)>350)
                                        <a class="search-form--open">
                                            <span>{{t('interface.expand')}}</span>
                                            <span>{{t('interface.collapse')}}</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                            
                            @if(!empty($complaint->__documents_owner))
                                <div class="m-10"><a href="" class="document-link" data-id="{{$complaint->id}}-owner-complaint">{{t('tender.bids_documents')}}</a></div>
                            @endif
                            
                            @if(in_array($complaint->status, ['cancelled']))
                                <div class="m-10">
                                    <div><strong>Скасована</strong></div>
                                    @if(!empty($complaint->dateCanceled))
                                        <div class="grey-light size12 question-date">{{ t('tender.date') }}: {{date('d.m.Y H:i', strtotime($complaint->dateCanceled))}}</div>
                                        {{ t('tender.reason') }}: {{$complaint->cancellationReason}}
                                    @endif
                                </div>
                            @endif
                            
                            @if(in_array($complaint->status, ['claim']))
                                <h4>
                                    {{ t('tender.decision_customer') }}: {{ t('tender.expected') }}
                                </h4>
                            @endif
                            
                            @if(!empty($complaint->resolutionType) || !empty($complaint->tendererActionDate))
                                <h4>
                                    @if($complaint->resolutionType=='invalid')
                                        {{ t('tender.decision_customer') }}: {{ t('tender.demand_rejected') }}
                                    @elseif($complaint->resolutionType=='resolved')
                                        {{ t('tender.decision_customer') }}: {{ t('tender.requirement_satisfied') }}
                                    @elseif($complaint->resolutionType=='declined')
                                        {{ t('tender.decision_customer') }}: {{ t('tender.demand_not_satisfied') }}
                                    @else(empty($complaint->resolutionType))
                                        {{ t('tender.answer_customer') }}:
                                    @endif
                                </h4>
                            @endif


                            <div class="list_date inline-layout">
                                @if (!empty($complaint->dateAnswered))
                                    <div class="item_date">{{date('d.m.Y H:i', strtotime($complaint->dateAnswered))}}</div>
                                @endif
                            </div>

                            
                            @if (!empty($complaint->tendererAction))
                                <div class="m-10 description-wr margin-bottom{{mb_strlen($complaint->tendererAction)>350?' croped':' open'}}">
                                    <div class="description">
                                        {!!nl2br($complaint->tendererAction)!!}
                                    </div>
                                    @if (mb_strlen($complaint->tendererAction)>350)
                                        <a class="search-form--open"><i class="sprite-arrow-down"></i>
                                            <span>{{t('interface.expand')}}</span>
                                            <span>{{t('interface.collapse')}}</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                            
                            @if (!empty($complaint->resolution))
                                <div class="m-10">{!!nl2br($complaint->resolution)!!}</div>
                            @endif
                            
                            @if(!empty($complaint->__documents_tender_owner))
                                <div class="m-10"><a href="" class="document-link" style="margin-top:5px; display:block" data-id="{{$complaint->id}}-tender-complaint">{{t('tender.bids_documents')}}</a></div>
                            @endif
                            
                            @if(property_exists($complaint, 'satisfied'))
                                <div class="margin-top">
                                    @if($complaint->satisfied)
                                        <strong>{{ t('tender.customer_evaluation_complainant_solution') }}: {{ t('tender.satisfactorily') }}</strong>
                                    @else
                                        <strong>{{ t('tender.customer_evaluation_complainant_solution') }}: {{ t('tender.unsatisfactorily') }}</strong>
                                        @if(!empty($complaint->dateEscalated))
                                            <div class="grey-light size12 question-date">{{ t('tender.date_request_commission_review_applications') }}: {{date('d.m.Y H:i', strtotime($complaint->dateAnswered))}}</div>
                                        @endif
                                    @endif
                                </div>
                            @endif
                            {{--
                            <div style="margin-top:20px;">
                                <div><strong>Рішення замовника</strong></div>
                                @if(empty($complaint->resolutionType))
                                    <div>Очікується</div>
                                @else
                                    <div>{{$complaint->resolutionType}}</div>
                                    @if (!empty($complaint->tendererAction))
                                        <div class="description-wr margin-bottom{{mb_strlen($complaint->tendererAction)>350?' croped':' open'}}">
                                            <div class="description">
                                                {!!nl2br($complaint->tendererAction)!!}
                                            </div>
                                            @if (mb_strlen($complaint->tendererAction)>350)
                                                <a class="search-form--open"><i class="sprite-arrow-down"></i>
                                                    <span>{{trans('interface.expand')}}</span>
                                                    <span>{{trans('interface.collapse')}}</span>
                                                </a>
                                            @endif
                                        </div>                                    
                                    @elseif ($complaint->resolution)
                                        <div>{{$complaint->resolution}}</div>
                                    @endif
                                    <div class="grey-light size12 question-date">{{date('d.m.Y H:i', strtotime($complaint->dateAnswered))}}</div>
                                @endif
                            </div>
                            --}}
                        </div>
                    @endforeach
                    {{--
                    @if (sizeof($item->__complaints_claims)>2)
                        <a class="question--open"><i class="sprite-arrow-down"></i>
                            <span class="question-up">{{trans('tender.expand_claims')}}: {{sizeof($item->__complaints_claims)}}</span>
                            <span class="question-down">{{trans('tender.collapse_claims')}}</span>
                        </a>
                    @endif
                    --}}
                </div>
            </div>
            @if(!empty($item->__complaints_claims))
                <div class="overlay overlay-documents">
                    <div class="overlay-close overlay-close-layout"></div>
                    <div class="overlay-box">
                        @foreach($item->__complaints_claims as $complaint)
                            @if(!empty($complaint->__documents_owner))
                                <div class="tender--offers documents" data-id="{{$complaint->id}}-owner-complaint">
                                    <h4 class="overlay-title">
                                        {{ t('tender.documents_submitted_by_complainant') }}
                                    </h4>
                                    @foreach($complaint->__documents_owner as $document)
                                        <div class="document-info">
                                            <div class="document-date">{{date('d.m.Y H:i', strtotime($document->datePublished))}}</div>
                                            <a href="{{$document->url}}" target="_blank" class="document-name">{{$document->title}}</a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if(!empty($complaint->__documents_tender_owner))
                                <div class="tender--offers documents" data-id="{{$complaint->id}}-tender-complaint">
                                    <h4 class="overlay-title">
                                        {{ t('tender.documents') }}
                                    </h4>
                                    @foreach($complaint->__documents_tender_owner as $document)
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
