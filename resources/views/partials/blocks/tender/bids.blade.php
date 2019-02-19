@if (!empty($item->__bids) && $item->procurementMethod=='open')
    <div class="margin-bottom-more">

            <div class="block_title">
                <h3>{{ t('tender.register_suggestions') }}</h3>
            </div>
            
            @if(!empty($item->auctionPeriod->endDate))
                <p class="table-date">{{t('tender.bids_open_time')}}: {{date('d.m.Y H:i', strtotime($item->auctionPeriod->endDate))}}</p>
            @elseif(!empty($item->tenderPeriod->endDate))
                <p class="table-date">{{t('tender.bids_open_time')}}: {{date('d.m.Y H:i', strtotime($item->tenderPeriod->endDate))}}</p>
            @endif

            <div class="overflow-table">

                <table>
                    <thead>
                        <tr>
                            <th>{{t('tender.bids_participant')}}</th>
                            <th>{{t('tender.bids_start_bid')}}</th>
                            <th>{{t('tender.bids_last_bid')}}</th>
                            @if($item->__features_price<1)
                                <th>{{t('tender.bids_coef')}}</th>
                                <th>{{t('tender.bids_price')}}</th>
                            @endif
                            <th>{{t('tender.bids_documents')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->__bids as $k=>$bid)
                            <tr>
                                <td>
                                    @if(!empty($bid->tenderers[0]->identifier->legalName))
                                        <div class="bid-contacts">
                                            <span style="float: left;margin-right: 10px;">
                                                {{$bid->tenderers[0]->identifier->legalName}}
                                            </span>
                                            <div class="info">
                                                <span class="info_icon"></span>
                                                <div class="info_text" style="margin-left: -140px;">
                                                    <div>
                                                        {{ $bid->__contactPoint }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif(!empty($bid->tenderers[0]->name))
                                        <div class="bid-contacts">
                                            <span style="float: left;margin-right: 10px;">
                                                {{$bid->tenderers[0]->name}}
                                            </span>
                                            <div class="info">
                                                <span class="info_icon"></span>
                                                <div class="info_text" style="margin-left: -140px;">
                                                    <div>
                                                        {{ $bid->__contactPoint }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        {{ t('tender.participant') }}
                                    @endif
                                    @if(!empty($profileAccess) || !empty($user->is_profile_links))
                                        <br><a target="_blank" class="profile-role2" href="{{ route('page.profile_by_id', ['scheme'=>$bid->tenderers[0]->identifier->scheme.'-'.$bid->tenderers[0]->identifier->id,'tpl'=>$profileRole2TplId,'role'=>'role2']) }}">{{ t('dozorro_profile') }}</a>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($item->__initial_bids[$bid->id]))
                                        {{str_replace('.00', '', number_format($item->__initial_bids[$bid->id], 2, '.', ' '))}}
                                        {{$bid->value->currency}}{{$bid->value->valueAddedTaxIncluded?t('tender.vat'):''}}
                                    @elseif(!empty($bid->value))
                                        {{str_replace('.00', '', number_format($bid->value->amount, 2, '.', ' '))}}
                                        {{$bid->value->currency}}{{$bid->value->valueAddedTaxIncluded?t('tender.vat'):''}}
                                    @elseif(!empty($item->bids_values[$k]->value))
                                        {{str_replace('.00', '', number_format($item->bids_values[$k]->value->amount, 2, '.', ' '))}}
                                        {{$item->bids_values[$k]->value->currency}}{{$item->bids_values[$k]->value->valueAddedTaxIncluded?t('tender.vat'):''}}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($bid->value))
                                        {{str_replace('.00', '', number_format($bid->value->amount, 2, '.', ' '))}}
                                        {{$bid->value->currency}}{{$bid->value->valueAddedTaxIncluded?t('tender.vat'):''}}
                                    @elseif(!empty($item->bids_values[$k]->value))
                                        {{str_replace('.00', '', number_format($item->bids_values[$k]->value->amount, 2, '.', ' '))}}
                                        {{$item->bids_values[$k]->value->currency}}{{$item->bids_values[$k]->value->valueAddedTaxIncluded?t('tender.vat'):''}}
                                    @endif
                                </td>
                                @if($item->__features_price<1)
                                    <td>{{$bid->__featured_coef}}</td>
                                    <td class="1">{{$bid->__featured_price}}</td>
                                @endif
                                <td>
                                    @if(!empty($bid->documents))
                                        <a href="" class="document-link2" data-id="{{$bid->id}}">{{t('tender.bids_documents')}}</a>
                                    @else
                                        {{t('tender.no_documents')}}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="overlay overlay-documents">
                <div class="overlay-close overlay-close-layout"></div>
                <div class="overlay-box">
                    @foreach($item->__bids as $bid)
                        <div class="tender--offers documents" data-id="{{$bid->id}}">
                            @if(!empty($bid->__documents_public))
                                <h4 class="overlay-title">
                                    Публічні документи
                                </h4>
                                @foreach($bid->__documents_public as $document)
                                    <div class="document-info">
                                        <div class="document-date" style="{{ !empty($document->stroked) ? "padding-left:20px;text-decoration: line-through;":"" }}">{!! \App\Helpers::parseDate($document->dateModified, false, false) !!}</div>
                                        <a href="{{$document->url}}" target="_blank" class="word-break document-name" style="{{ !empty($document->stroked) ? "padding-left:10px;text-decoration: line-through;":"" }}">{{$document->title}}</a>
                                    </div>
                                @endforeach
                            @endif
                            @if(!empty($bid->__documents_confident))
                                <h4 class="overlay-title">
                                    Конфіденційні документи
                                </h4>
                                @foreach($bid->__documents_confident as $document)
                                    <div class="document-info">
                                        <div class="document-date">{!! \App\Helpers::parseDate($document->dateModified) !!}</div>
                                        <div>{{$document->title}}</div>
                                        <p style="font-size:80%;margin-top:10px;margin-bottom:4px;color:#AAA">Обгрунтування конфіденційності</p>
                                        @if(!empty($document->confidentialityRationale))
                                            <p style="font-size:80%;">{{$document->confidentialityRationale}}</p>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                    <div class="overlay-close"><i class="sprite-close-grey"></i></div>
                </div>
            </div>

    </div>
@endif
{{--
@if (!empty($item->__eu_bids) && $item->procurementMethod=='open' && $item->procurementMethodType=='aboveThresholdEU')
    <div class="container wide-table">
        <div class="tender--offers margin-bottom-xl">
            <h3>Реєстр пропозицій</h3>

            <table class="table table-striped margin-bottom small-text">
                <thead>
                    <tr>
                        <th>{{trans('tender.bids_participant')}}</th>
                        <th>{{trans('tender.bids_documents')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($item->__eu_bids as $k=>$bid)
                        <tr>
                            <td>Учасник {{$k+1}}</td>
                            <td>
                                @if(!empty($bid->documents))
                                    <a href="" class="document-link" data-id="{{$bid->id}}">{{trans('tender.bids_documents')}}</a>
                                @else
                                    {{trans('tender.no_documents')}}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="overlay overlay-documents">
                <div class="overlay-close overlay-close-layout"></div>
                <div class="overlay-box">
                    @foreach($item->__eu_bids as $bid)
                        <div class="tender--offers documents" data-id="{{$bid->id}}">
                            @if(!empty($bid->__documents_public))
                                <div class="margin-bottom">
                                    <h4 class="overlay-title">
                                        Публічні документи
                                    </h4>
                                    @foreach($bid->__documents_public as $document)
                                        <div class="document-info">
                                            <div class="document-date">{{date('d.m.Y H:i', strtotime($document->dateModified))}}</div>
                                            <a href="{{$document->url}}" target="_blank" class="document-name">{{$document->title}}</a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif    
                            @if(!empty($bid->__documents_confident))
                                <div class="margin-bottom">
                                    <h4 class="overlay-title">
                                        Конфіденційні документи
                                    </h4>
                                    @foreach($bid->__documents_confident as $document)
                                        <div class="document-info">
                                            <div class="document-date">{{date('d.m.Y H:i', strtotime($document->dateModified))}}</div>
                                            <div>{{$document->title}}</div>
                                            <p style="font-size:80%;margin-top:10px;margin-bottom:4px;color:#AAA">Обгрунтування конфіденційності</p>
                                            <p style="font-size:80%;">{{$document->confidentialityRationale}}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                    <div class="overlay-close"><i class="sprite-close-grey"></i></div>
                </div>
            </div>
        </div>
    </div>
@endif
--}}