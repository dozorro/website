@if(in_array($item->status, ['cancelled', 'unsuccessful']))
    <div class="">
        <div class="container margin-bottom-xl">
            <div class="col-sm-9">
                <h3>{{ t($what.'.information_cancellation') }}</h3>
                @if($what == 'lot')
                <h4>{{ $item->title }}</h4>
                @endif

                @if($item->status=='cancelled' && !empty($item->__cancellations))
                    @foreach($item->__cancellations as $cancellation)
                        @if($cancellation->status != 'pending')
                        <div class="row">
                            <div class="col-md-12 margin-bottom">
                                <strong>{{ t('tender.date_cancellation') }}</strong>
                                <div>{{date('d.m.Y H:i', strtotime($cancellation->date))}}</div>
                            </div>
                            @if(!empty($cancellation->reason))
                                <div class="col-md-12 margin-bottom">
                                    <strong>{{ t('tender.reason_cancellation') }}</strong>
                                    <div>{{$cancellation->reason}}</div>
                                </div>
                            @endif
                            <div class="col-md-12 margin-bottom">
                                <strong>Документи</strong>
                                @if (!empty($cancellation->documents))
                                    <table class="tender--customer">
                                        <tbody>
                                            @foreach($cancellation->documents as $k=>$document)
                                                <tr>
                                                    <td class="col-sm-2" style="padding-left:0px;">{{!empty($document->dateModified) ? date('d.m.Y H:i', strtotime($document->dateModified)) : t('tender.no_date')}}</td>
                                                    <td class="col-sm-6"><a href="{{$document->url}}" target="_blank" class="word-break{{!empty($document->stroked) ? ' stroked': ''}}">{{$document->title}}</a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="tender--customer padding-td">{{t('tender.no_documents')}}</div>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endforeach
                @elseif(in_array($item->procurementMethodType, ['aboveThresholdEU', 'aboveThresholdUA', 'aboveThresholdUA.defense', 'competitiveDialogueUA.stage2', 'competitiveDialogueEU.stage2']) && !empty($item->__unsuccessful_awards))
                    <div class="row">
                        <div class="col-md-12 margin-bottom">
                            <strong>{{ t('tender.date_cancellation') }}</strong>
                            <div>{{!empty($item->awardPeriod->endDate) ? date('d.m.Y H:i', strtotime($item->awardPeriod->endDate)) : 'не вказано'}}</div>
                        </div>
                        <div class="col-md-12">
                            <strong>{{ t('tender.reason_cancellation') }}</strong>
                            <div>{{ t('tender.reject_all_tenders_pursuant_Law_on_public_procurement') }}</div>
                            _
                        </div>
                    </div>
                @else
                    <?php
                        $numberOfBids=0;

                        if(!empty($item->__bids))
                        {
                            $numberOfBids=array_where($item->__bids, function($key, $bid){
                                return !empty($bid->status) && ($bid->status=='active' || $bid->status=='unsuccessful');
                            });
                        
                            $numberOfBids=$numberOfBids ? sizeof($numberOfBids) : 0;
                        }

                        $numberOfQualifications=0;

                        if(!empty($item->__qualifications))
                        {
                            $numberOfQualifications=array_where($item->__qualifications, function($key, $qualification){
                                return !empty($qualification->status) && $qualification->status=='active';
                            });
                            
                            $numberOfQualifications=$numberOfQualifications ? sizeof($numberOfQualifications) : 0;
                        }
                    ?>
                    @if($item->status=='unsuccessful')
                        @if(in_array($item->procurementMethodType, ['aboveThresholdUA', 'competitiveDialogueUA.stage2', 'competitiveDialogueEU.stage2']))
                            <div class="row">
                                <div class="col-md-12 margin-bottom">
                                    <strong>{{ t('tender.date_cancellation') }}</strong>
                                    <div>{{date('d.m.Y H:i', strtotime($tenderPeriod->endDate))}}</div>
                                </div>
                                <div class="col-md-12">
                                    <strong>{{ t('tender.reason_cancellation') }}</strong>
                                    <div>{{ t('tender.submission_bid_less_than_two_tender_offers') }}</div>
                                </div>
                            </div>
                        @elseif(in_array($item->procurementMethodType, ['competitiveDialogueUA', 'competitiveDialogueEU']))
                            <div class="row">
                                <div class="col-md-12 margin-bottom">
                                    <strong>{{ t('tender.date_cancellation') }}</strong>
                                    <div>{{date('d.m.Y H:i', strtotime($tenderPeriod->endDate))}}</div>
                                </div>
                                <div class="col-md-12">
                                    <strong>{{ t('tender.reason_cancellation') }}</strong>
                                    <div>{{ t('tender.submission_bid_least_three_tenders') }}</div>
                                </div>
                            </div>
                        @elseif(in_array($item->procurementMethodType, ['aboveThresholdUA.defense']))
                            <div class="row">
                                <div class="col-md-12 margin-bottom">
                                    <strong>{{ t('tender.date_cancellation') }}</strong>
                                    <div>{{date('d.m.Y H:i', strtotime($tenderPeriod->endDate))}}</div>
                                </div>
                                <div class="col-md-12">
                                    <strong>{{ t('tender.reason_cancellation') }}</strong>
                                    <div>{{ t('tender.not_submitting _bid_any_tender_offer') }}</div>
                                </div>
                            </div>
                        @elseif($item->procurementMethodType=='belowThresholdUA')
                            <div class="row">
                                <div class="col-md-12 margin-bottom">
                                    <strong>{{ t('tender.date_cancellation') }}</strong>
                                    <div>{{date('d.m.Y H:i', strtotime($tenderPeriod->endDate))}}</div>
                                </div>
                                <div class="col-md-12">
                                    <strong>{{ t('tender.reason_cancellation') }}</strong>
                                    <div>{{ t('tender.absence_tenders') }}</div>
                                </div>
                            </div>
                        @elseif(in_array($item->procurementMethodType, ['aboveThresholdEU', 'competitiveDialogueEU.stage2']) && $numberOfBids < 2)
                            <div class="row">
                                <div class="col-md-12 margin-bottom">
                                    <strong>{{ t('tender.date_cancellation') }}</strong>
                                    <div>{{date('d.m.Y H:i', strtotime($tenderPeriod->endDate))}}</div>
                                </div>
                                <div class="col-md-12">
                                    <strong>{{ t('tender.reason_cancellation') }}</strong>
                                    <div>{{ t('tender.submission_bid_less_than_two_tender_offers') }}</div>
                                </div>
                            </div>                        
                        @elseif(in_array($item->procurementMethodType, ['aboveThresholdEU', 'competitiveDialogueEU.stage2']) && $numberOfQualifications < 2)
                            <div class="row">
                                <div class="col-md-12 margin-bottom">
                                    <strong>{{ t('tender.date_cancellation') }}</strong>
                                    <div>
                                        @if(!empty($qualificationPeriod->endDate))
                                            {{date('d.m.Y H:i', strtotime($qualificationPeriod->endDate))}}
                                        @else
                                            {{ t('tender.none3') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <strong>{{ t('tender.reason_cancellation') }}</strong>
                                    <div>{{ t('tender.admission_evaluation_least_two_tenders') }}</div>
                                </div>
                            </div>
                        @elseif(in_array($item->procurementMethodType, ['competitiveDialogueEU', 'competitiveDialogueUA']) && $numberOfQualifications < 3)
                            <div class="row">
                                <div class="col-md-12 margin-bottom">
                                    <strong>{{ t('tender.date_cancellation') }}</strong>
                                    <div>
                                        @if(!empty($qualificationPeriod->endDate))
                                            {{date('d.m.Y H:i', strtotime($qualificationPeriod->endDate))}}
                                        @else
                                            {{ t('tender.none3') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <strong>{{ t('tender.reason_cancellation') }}</strong>
                                    <div>{{ t('tender.admission_negotiation_least_three_tenders') }}</div>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-12 margin-bottom">
                                    <strong>{{ t('tender.trades_canceled') }}</strong>
                                </div>
                                <div class="col-md-12">
                                    <strong>{{ t('tender.reason_cancellation') }}</strong>
                                    <div>{{ t('tender.no_suggestions') }}</div>
                                </div>
                            </div>
                        @endif
                    @endif
                @endif
            </div>
        </div>
    </div>
@endif